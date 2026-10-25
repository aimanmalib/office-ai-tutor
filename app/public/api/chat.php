<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../../src/Env.php';
require_once __DIR__ . '/../../src/Groq.php';
require_once __DIR__ . '/../../src/GroqPool.php';
require_once __DIR__ . '/../../src/ChatStatus.php';
require_once __DIR__ . '/../../src/RateLimit.php';
require_once __DIR__ . '/../../src/ClientIp.php';
require_once __DIR__ . '/../../src/Stats.php';
require_once __DIR__ . '/../../src/Kandungan.php';
require_once __DIR__ . '/../../src/Subjek.php';
require_once __DIR__ . '/../../src/Bahasa.php';

// Bahasa mesej ralat + arahan AI: ?bahasa=en (dihantar oleh chat.js/widget.js ikut halaman). Dibaca SEBELUM had kadar.
Bahasa::tetap((string) ($_GET['bahasa'] ?? 'ms'));

$rate = null;
$hitAt = null;

function jsonError(int $code, string $message): never
{
    global $rate, $hitAt;
    if ($rate !== null && $hitAt !== null && $code !== 429) {
        $rate->refund($hitAt);   // soalan yang gagal tidak dikira dalam had pelajar
    }
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    Env::load(__DIR__ . '/../../config/.env');

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        jsonError(405, 'Method not allowed.');
    }

    // Chat enable/disable gate (instructor-controlled via /admin.php)
    $status = (new ChatStatus(__DIR__ . '/../../data/chat-status.json'))->read();
    if (!$status['enabled']) {
        http_response_code(503);
        echo json_encode([
            'success' => false,
            'error' => $status['message'],
            'disabled' => true,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $imageUploadEnabled = $status['image_upload_enabled'];

    // Rate limit per IP pelajar sebenar (bukan IP pinggir Cloudflare yang dikongsi ramai, bukan pengepala palsu)
    $ip = ClientIp::get();
    $rateDir = __DIR__ . '/../../data/ratelimit';
    if (!is_dir($rateDir)) {
        @mkdir($rateDir, 0775, true);
    }
    $now = time();
    $window = 3600;
    $limit = Env::int('RATE_LIMIT_PER_HOUR', 20);
    if (random_int(1, 100) === 1) {
        // sapuan: satu fail setiap IP, tiada guna selepas tingkap tamat
        foreach (glob($rateDir . '/*.json') ?: [] as $f) {
            if (@filemtime($f) < $now - 2 * $window) {
                @unlink($f);
            }
        }
    }
    $rate = new RateLimit($rateDir . '/' . RateLimit::bucket($ip) . '.json', $window);
    // Hit direkod SEBELUM memanggil AI (permintaan serentak tidak boleh melepasi had) dan dipulangkan bila gagal.
    $hitAt = $rate->take($limit, $now);
    if ($hitAt === null) {
        jsonError(429, t('api.had', ['n' => $limit]));
    }

    // Parse input
    $raw = file_get_contents('php://input');
    $input = json_decode((string) $raw, true);
    if (!is_array($input)) {
        jsonError(400, t('api.tidak_sah'));
    }

    // Subjek: arahan AI + statistik ikut subjek. Draf = admin yang log masuk sahaja (pratonton).
    $subjek = (string) ($input['subjek'] ?? 'excel');
    if (!Subjek::bolehLihat($subjek)) {
        jsonError(404, t('api.subjek'));
    }

    $message = trim((string) ($input['message'] ?? ''));
    $imageData = (string) ($input['image'] ?? '');
    if ($message === '' && $imageData === '') {
        jsonError(400, t('api.kosong'));
    }
    if (mb_strlen($message) > 2000) {
        jsonError(400, t('api.panjang'));
    }

    // Validate image (optional)
    $imageDataUri = null;
    if ($imageData !== '') {
        if (!$imageUploadEnabled) {
            jsonError(400, t('api.gambar_mati'));
        }
        if (!preg_match('#^data:image/(jpeg|jpg|png|webp);base64,([A-Za-z0-9+/=]+)$#', $imageData, $m)) {
            jsonError(400, t('api.gambar_format'));
        }
        $rawBytes = base64_decode($m[2], true);
        if ($rawBytes === false) {
            jsonError(400, t('api.gambar_rosak'));
        }
        $maxBytes = 4 * 1024 * 1024;
        if (strlen($rawBytes) > $maxBytes) {
            jsonError(400, t('api.gambar_besar'));
        }
        $info = @getimagesizefromstring($rawBytes);
        if ($info === false || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            jsonError(400, t('api.gambar_format'));
        }
        if ($info[0] < 32 || $info[1] < 32) {
            jsonError(400, t('api.gambar_kecil'));
        }
        $imageDataUri = $imageData;
    }

    // Sanitize history. Strip past images — only current turn carries image
    // to keep token cost and request size manageable.
    // Had JUMLAH aksara (paling baharu dahulu): 10 × 4000 aksara melebihi had token seminit Groq peringkat
    // percuma, dan setiap soalan dalam perbualan panjang gagal sehingga pelajar menekan "Chat baru".
    $rawHistory = $input['history'] ?? [];
    $history = [];
    $historyBudget = 6000;
    if (is_array($rawHistory)) {
        foreach (array_slice($rawHistory, -10) as $m) {
            if (
                is_array($m)
                && isset($m['role'], $m['content'])
                && is_string($m['role'])
                && is_string($m['content'])
                && in_array($m['role'], ['user', 'assistant'], true)
                && mb_strlen($m['content']) <= 4000
            ) {
                $history[] = ['role' => $m['role'], 'content' => $m['content']];
            }
        }
        $kept = [];
        foreach (array_reverse($history) as $m) {
            $historyBudget -= mb_strlen($m['content']);
            if ($historyBudget < 0) {
                break;
            }
            array_unshift($kept, $m);
        }
        if ($kept && $kept[0]['role'] === 'assistant') {
            array_shift($kept);   // perbualan mesti bermula dengan soalan pelajar
        }
        $history = $kept;
    }

    $systemPrompt = (string) (Kandungan::baca($subjek, 'prompt', Bahasa::kod()) ?: Kandungan::baca($subjek, 'prompt'));

    // Build current user message — multi-part if image attached
    if ($imageDataUri !== null) {
        $userContent = [
            ['type' => 'text', 'text' => $message !== '' ? $message : t('api.gambar_sahaja', ['subjek' => Subjek::semua()[$subjek]['nama']])],
            ['type' => 'image_url', 'image_url' => ['url' => $imageDataUri]],
        ];
    } else {
        $userContent = $message;
    }

    $messages = array_merge(
        [['role' => 'system', 'content' => $systemPrompt]],
        $history,
        [['role' => 'user', 'content' => $userContent]]
    );

    // Model resolution: admin-selected (ChatStatus) overrides .env defaults
    $textModel = $status['text_model'] !== ''
        ? $status['text_model']
        : (string) Env::get('GROQ_MODEL', 'openai/gpt-oss-120b');
    $visionModel = $status['vision_model'] !== ''
        ? $status['vision_model']
        : (string) Env::get('GROQ_VISION_MODEL', 'qwen/qwen3.8-27b');

    $pool = new GroqPool(
        keysFile: __DIR__ . '/../../config/groq-keys.json',
        usageFile: __DIR__ . '/../../data/groq-usage.json',
        model: $textModel,
        maxTokens: Env::int('GROQ_MAX_TOKENS', 1024),
        temperature: Env::float('GROQ_TEMPERATURE', 0.5),
        visionModel: $visionModel,
        baseUrl: (string) Env::get('GROQ_BASE_URL', Groq::BASE_URL),
    );

    $stats = new Stats(__DIR__ . '/../../data/statistik.db');
    try {
        $reply = $pool->chat($messages);
    } catch (Throwable $e) {
        $stats->rekod('chat', $subjek, 'gagal', $message !== '' ? $message : '[gambar]');
        throw $e;
    }
    $stats->rekod('chat', $subjek, 'ok', ($imageDataUri !== null ? '[gambar] ' : '') . $message);

    echo json_encode([
        'success' => true,
        'reply' => $reply,
        'remaining' => $rate->remaining($limit, $now),
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('[chat.php] ' . $e->getMessage());
    jsonError(500, t('api.sibuk'));
}
