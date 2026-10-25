<?php

declare(strict_types=1);

require_once __DIR__ . '/Groq.php';

/**
 * Manages a pool of Groq API keys. Picks the key with the most
 * remaining quota for each call, falls back on 429 / 5xx / network errors
 * (a 400 is the request's own fault — retrying it on every key only burns time),
 * and tracks per-key per-day usage to disk. A key that hit 429 stays cooling
 * across requests (data/groq-cooling.json) so the next student does not wait on it.
 */
final class GroqPool
{
    private string $keysFile;
    private string $usageFile;
    /** @var array<int,array{id:string,key:string,label:string}> */
    private array $keys;
    /** @var array<string,array{last_remaining_req:?int,last_remaining_tok:?int,cooling_until:int}> */
    private array $runtime = [];

    public function __construct(
        string $keysFile,
        string $usageFile,
        private readonly string $model,
        private readonly int $maxTokens = 1024,
        private readonly float $temperature = 0.5,
        private readonly ?string $visionModel = null,
        private readonly string $baseUrl = Groq::BASE_URL,
    ) {
        $this->keysFile = $keysFile;
        $this->usageFile = $usageFile;
        $this->keys = $this->loadKeys();   // boleh kosong: admin mesti masih boleh MENAMBAH key pertama
        $this->loadUsage();
    }

    /** Cloudflare memutuskan permintaan selepas 100 s; berhenti mencuba kunci baharu selepas tempoh ini. */
    private const TIME_BUDGET_SECONDS = 55;

    /**
     * @param array<int,array{role:string,content:string|array}> $messages
     */
    public function chat(array $messages): string
    {
        if (!$this->keys) {
            throw new RuntimeException('Tiada API key dikonfigurasi. Sila tambah di /admin.php.');
        }
        $tried = [];
        $lastError = null;

        // Auto-detect vision: if any message has array-content with image_url part, use vision model.
        $useVision = false;
        foreach ($messages as $m) {
            if (is_array($m['content'] ?? null)) {
                foreach ($m['content'] as $part) {
                    if (($part['type'] ?? '') === 'image_url') {
                        $useVision = true;
                        break 2;
                    }
                }
            }
        }
        $modelToUse = ($useVision && $this->visionModel) ? $this->visionModel : $this->model;

        $candidates = $this->pickOrder();
        $started = time();

        foreach ($candidates as $cand) {
            if ($tried && time() - $started > self::TIME_BUDGET_SECONDS) {
                break;
            }
            $id = $cand['id'];
            $tried[] = $id;

            try {
                $groq = new Groq(
                    apiKey: $cand['key'],
                    model: $modelToUse,
                    maxTokens: $this->maxTokens,
                    temperature: $this->temperature,
                    baseUrl: $this->baseUrl,
                );
                $result = $groq->chatWithMeta($messages);

                // Update runtime cache + on-disk usage
                $this->runtime[$id] = [
                    'last_remaining_req' => $result['remaining_req'],
                    'last_remaining_tok' => $result['remaining_tok'],
                    'cooling_until' => 0,
                ];
                $this->recordUsage($id, $result['total_tokens'], $result['remaining_req'], $result['remaining_tok']);

                return $result['reply'];
            } catch (GroqRateLimitException $e) {
                // Cool this key down until its reset
                $until = time() + max(5, $e->retryAfterSeconds);
                $this->runtime[$id] = [
                    'last_remaining_req' => 0,
                    'last_remaining_tok' => 0,
                    'cooling_until' => $until,
                ];
                $this->saveCooling($id, $until);
                $lastError = $e;
                continue;
            } catch (GroqRetryableException $e) {
                $lastError = $e;
                continue;
            }
            // Any other error (400 too large, 404 model, …) is the request's fault: rethrow, don't try other keys.
        }

        throw new RuntimeException(
            'Semua API key sedang busy. Cuba lagi dalam seminit. (' .
            ($lastError ? $lastError->getMessage() : 'no candidate') . ')'
        );
    }

    /**
     * Model yang Groq hidangkan SEKARANG (GET /models dengan kunci pertama yang menjawab), dicache 1 jam.
     * Groq menutup model tanpa amaran dalam laman ini (5 daripada 7 model asal ditutup Mac–Ogos 2026).
     * @return array<string,array{vision:bool}>|null  null = tidak dapat disemak (laman tetap berfungsi)
     */
    public function availableModels(string $cacheFile, int $ttl = 3600): ?array
    {
        $cached = json_decode((string) @file_get_contents($cacheFile), true);
        if (is_array($cached) && ($cached['at'] ?? 0) > time() - $ttl && is_array($cached['models'] ?? null)) {
            return $cached['models'];
        }
        foreach ($this->pickOrder() as $cand) {
            $ch = curl_init(rtrim($this->baseUrl, '/') . '/models');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $cand['key']],
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);
            $raw = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $data = $code === 200 ? json_decode((string) $raw, true) : null;
            if (!is_array($data['data'] ?? null)) {
                continue;
            }
            $models = [];
            foreach ($data['data'] as $m) {
                if (is_string($m['id'] ?? null)) {
                    $models[$m['id']] = ['vision' => in_array('image', (array) ($m['input_modalities'] ?? []), true)];
                }
            }
            $tmp = $cacheFile . '.tmp' . bin2hex(random_bytes(3));
            if (@file_put_contents($tmp, json_encode(['at' => time(), 'models' => $models])) !== false) {
                @rename($tmp, $cacheFile);
            }
            return $models;
        }
        return is_array($cached['models'] ?? null) ? $cached['models'] : null;
    }

    /** @return array<int,array{id:string,key:string,label:string}> */
    public function listKeys(): array
    {
        return $this->keys;
    }

    public function addKey(string $apiKey, string $label): void
    {
        $apiKey = trim($apiKey);
        $label = trim($label);
        if (!str_starts_with($apiKey, 'gsk_') || strlen($apiKey) < 20) {
            throw new InvalidArgumentException('API key tak sah (mesti mula dengan gsk_).');
        }
        foreach ($this->keys as $k) {
            if ($k['key'] === $apiKey) {
                throw new InvalidArgumentException('Key dah ada dalam pool.');
            }
        }
        $this->keys[] = [
            'id' => bin2hex(random_bytes(4)),
            'key' => $apiKey,
            'label' => $label !== '' ? $label : 'key-' . (count($this->keys) + 1),
        ];
        $this->saveKeys();
    }

    public function removeKey(string $id): void
    {
        $this->keys = array_values(array_filter($this->keys, fn($k) => $k['id'] !== $id));
        $this->saveKeys();
    }

    /** @return array<string,array{requests:int,tokens:int,last_remaining_req:?int,last_remaining_tok:?int}> */
    public function todayUsage(): array
    {
        $today = date('Y-m-d');
        $data = $this->readUsageFile();
        return $data[$today] ?? [];
    }

    /** @return array<string,array{requests:int,tokens:int}> Last N days summary */
    public function recentUsage(int $days = 7): array
    {
        $data = $this->readUsageFile();
        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $day = $data[$d] ?? [];
            $req = 0; $tok = 0;
            foreach ($day as $k) {
                $req += (int) ($k['requests'] ?? 0);
                $tok += (int) ($k['tokens'] ?? 0);
            }
            $out[$d] = ['requests' => $req, 'tokens' => $tok];
        }
        return $out;
    }

    /** @return array<string,array{last_remaining_req:?int,last_remaining_tok:?int,cooling_until:int}> */
    public function runtimeState(): array
    {
        return $this->runtime;
    }

    // -------- internals --------

    /** @return array<int,array{id:string,key:string,label:string}> */
    private function pickOrder(): array
    {
        $now = time();
        $scored = [];
        foreach ($this->keys as $k) {
            $rt = $this->runtime[$k['id']] ?? null;
            $coolingUntil = $rt['cooling_until'] ?? 0;
            $isCooling = $coolingUntil > $now;
            $remaining = $rt['last_remaining_tok'] ?? PHP_INT_MAX;
            $scored[] = [
                'key' => $k,
                'cooling' => $isCooling,
                'remaining' => $remaining,
            ];
        }
        // sort: not-cooling first, then by remaining DESC
        usort($scored, function ($a, $b) {
            if ($a['cooling'] !== $b['cooling']) {
                return $a['cooling'] ? 1 : -1;
            }
            return $b['remaining'] <=> $a['remaining'];
        });
        return array_map(fn($s) => $s['key'], $scored);
    }

    /** @return array<int,array{id:string,key:string,label:string}> */
    private function loadKeys(): array
    {
        if (!is_file($this->keysFile)) {
            return [];
        }
        $raw = file_get_contents($this->keysFile);
        $data = json_decode((string) $raw, true);
        if (!is_array($data)) {
            return [];
        }
        $out = [];
        foreach ($data as $row) {
            if (isset($row['id'], $row['key']) && is_string($row['id']) && is_string($row['key'])) {
                $out[] = [
                    'id' => $row['id'],
                    'key' => $row['key'],
                    'label' => (string) ($row['label'] ?? ''),
                ];
            }
        }
        return $out;
    }

    private function saveKeys(): void
    {
        $dir = dirname($this->keysFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $json = json_encode($this->keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        // tmp + rename: pembaca serentak (chat) tidak pernah nampak fail separuh ditulis / kosong
        $tmp = $this->keysFile . '.tmp' . bin2hex(random_bytes(3));
        if (file_put_contents($tmp, $json) === false) {
            throw new RuntimeException('Gagal menyimpan senarai key.');
        }
        @chmod($tmp, 0600);
        rename($tmp, $this->keysFile);
    }

    private function loadUsage(): void
    {
        // populate runtime cache from latest stored values for sensible initial ordering
        $today = date('Y-m-d');
        $data = $this->readUsageFile();
        $todayData = $data[$today] ?? [];
        $cooling = $this->readCooling();
        foreach ($this->keys as $k) {
            $u = $todayData[$k['id']] ?? null;
            $this->runtime[$k['id']] = [
                'last_remaining_req' => $u['last_remaining_req'] ?? null,
                'last_remaining_tok' => $u['last_remaining_tok'] ?? null,
                'cooling_until' => (int) ($cooling[$k['id']] ?? 0),
            ];
        }
    }

    private function coolingFile(): string
    {
        return dirname($this->usageFile) . '/groq-cooling.json';
    }

    /** @return array<string,int> key id → unix time until which the key is rate-limited */
    private function readCooling(): array
    {
        $data = json_decode((string) @file_get_contents($this->coolingFile()), true);
        return is_array($data) ? $data : [];
    }

    private function saveCooling(string $id, int $until): void
    {
        $fp = @fopen($this->coolingFile(), 'c+');
        if ($fp === false) {
            return;
        }
        try {
            flock($fp, LOCK_EX);
            $data = json_decode((string) stream_get_contents($fp), true);
            $data = is_array($data) ? $data : [];
            $now = time();
            $data = array_filter($data, fn($t) => (int) $t > $now);
            $data[$id] = $until;
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, (string) json_encode($data));
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    private function recordUsage(string $id, int $totalTokens, ?int $remainingReq, ?int $remainingTok): void
    {
        $dir = dirname($this->usageFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $fp = fopen($this->usageFile, 'c+');
        if ($fp === false) {
            return;
        }
        try {
            flock($fp, LOCK_EX);
            rewind($fp);
            $content = stream_get_contents($fp) ?: '';
            $data = json_decode($content, true);
            if (!is_array($data)) {
                $data = [];
            }

            $today = date('Y-m-d');
            $data[$today] ??= [];
            $data[$today][$id] ??= ['requests' => 0, 'tokens' => 0, 'last_remaining_req' => null, 'last_remaining_tok' => null];
            $data[$today][$id]['requests']++;
            $data[$today][$id]['tokens'] += $totalTokens;
            if ($remainingReq !== null) $data[$today][$id]['last_remaining_req'] = $remainingReq;
            if ($remainingTok !== null) $data[$today][$id]['last_remaining_tok'] = $remainingTok;

            // prune entries older than 30 days
            $cutoff = strtotime('-30 days');
            foreach (array_keys($data) as $day) {
                if (strtotime((string) $day) < $cutoff) {
                    unset($data[$day]);
                }
            }

            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, $json);
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    private function readUsageFile(): array
    {
        if (!is_file($this->usageFile)) {
            return [];
        }
        $raw = file_get_contents($this->usageFile);
        $data = json_decode((string) $raw, true);
        return is_array($data) ? $data : [];
    }
}
