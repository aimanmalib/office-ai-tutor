<?php

declare(strict_types=1);

final class GroqRateLimitException extends RuntimeException
{
    public function __construct(string $message, public readonly int $retryAfterSeconds)
    {
        parent::__construct($message);
    }
}

/** Ralat yang patut dicuba dengan kunci lain (5xx, rangkaian, key tidak dikonfigurasi). 4xx lain = permintaan itu sendiri salah. */
final class GroqRetryableException extends RuntimeException {}

final class Groq
{
    public const BASE_URL = 'https://api.groq.com/openai/v1';

    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
        private readonly int $maxTokens = 1024,
        private readonly float $temperature = 0.5,
        private readonly string $baseUrl = self::BASE_URL,
    ) {
        if ($apiKey === '' || str_starts_with($apiKey, 'gsk_your_')) {
            throw new GroqRetryableException('Groq API key tidak dikonfigurasi.');   // key LAIN mungkin sah
        }
    }

    /**
     * @param array<int,array{role:string,content:string}> $messages
     */
    public function chat(array $messages): string
    {
        return $this->chatWithMeta($messages)['reply'];
    }

    /**
     * @param array<int,array{role:string,content:string}> $messages
     * @return array{reply:string,total_tokens:int,prompt_tokens:int,completion_tokens:int,remaining_req:?int,remaining_tok:?int,reset_req:?string,reset_tok:?string}
     */
    public function chatWithMeta(array $messages): array
    {
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'max_completion_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ];
        // Model penaakulan: token "berfikir" dikira dalam had yang sama. Jangan pulangkan penaakulan sebagai jawapan.
        if (str_starts_with($this->model, 'openai/gpt-oss')) {
            $payload['reasoning_effort'] = 'low';
            $payload['include_reasoning'] = false;
        } elseif (str_starts_with($this->model, 'qwen/')) {
            $payload['reasoning_format'] = 'hidden';
        }

        $headersIn = [];

        $ch = curl_init(rtrim($this->baseUrl, '/') . '/chat/completions');
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HEADERFUNCTION => function ($ch, $header) use (&$headersIn) {
                $len = strlen($header);
                $pos = strpos($header, ':');
                if ($pos !== false) {
                    $name = strtolower(trim(substr($header, 0, $pos)));
                    $value = trim(substr($header, $pos + 1));
                    $headersIn[$name] = $value;
                }
                return $len;
            },
        ];

        curl_setopt_array($ch, $opts);

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new GroqRetryableException('Groq cURL error: ' . $err);
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode((string) $response, true);

        $remainingReq = isset($headersIn['x-ratelimit-remaining-requests']) ? (int) $headersIn['x-ratelimit-remaining-requests'] : null;
        $remainingTok = isset($headersIn['x-ratelimit-remaining-tokens']) ? (int) $headersIn['x-ratelimit-remaining-tokens'] : null;
        $resetReq = $headersIn['x-ratelimit-reset-requests'] ?? null;
        $resetTok = $headersIn['x-ratelimit-reset-tokens'] ?? null;

        if ($httpCode === 429) {
            $retryAfter = isset($headersIn['retry-after']) ? (int) $headersIn['retry-after'] : 60;
            $msg = $data['error']['message'] ?? 'Rate limit hit';
            throw new GroqRateLimitException($msg, max(5, $retryAfter));
        }

        if ($httpCode !== 200) {
            $msg = $data['error']['message'] ?? 'Unknown error';
            $msg = "Groq API error ({$httpCode}): {$msg}";
            // 401/403 = key INI sahaja rosak/digantung: sejukkan 10 minit, cuba key lain. 5xx = Groq sendiri.
            if (in_array($httpCode, [401, 403], true)) {
                throw new GroqRateLimitException($msg, 600);
            }
            throw $httpCode >= 500 ? new GroqRetryableException($msg) : new RuntimeException($msg);
        }

        $reply = trim((string) ($data['choices'][0]['message']['content'] ?? ''));
        if ($reply === '') {
            // Bukan salah key: soalan sama akan kosong pada setiap key (cth token penaakulan habis) — jangan bakar kuota semua key
            throw new RuntimeException('Groq returned empty response (finish_reason: ' . ($data['choices'][0]['finish_reason'] ?? '?') . ')');
        }

        $usage = $data['usage'] ?? [];

        return [
            'reply' => $reply,
            'total_tokens' => (int) ($usage['total_tokens'] ?? 0),
            'prompt_tokens' => (int) ($usage['prompt_tokens'] ?? 0),
            'completion_tokens' => (int) ($usage['completion_tokens'] ?? 0),
            'remaining_req' => $remainingReq,
            'remaining_tok' => $remainingTok,
            'reset_req' => $resetReq,
            'reset_tok' => $resetTok,
        ];
    }
}
