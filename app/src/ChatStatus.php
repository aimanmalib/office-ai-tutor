<?php

declare(strict_types=1);

/**
 * Read/write chat enabled state + model selection.
 * Storage: a single JSON file outside web root.
 */
final class ChatStatus
{
    public function __construct(private readonly string $file) {}

    /**
     * @return array{
     *   enabled:bool, message:string, updated_at:string,
     *   text_model:string, vision_model:string, image_upload_enabled:bool
     * }
     */
    public function read(): array
    {
        $default = [
            'enabled' => true,
            'message' => 'Chat AI sedang tidak aktif. Akan dibuka semasa kelas training nanti.',
            'updated_at' => '',
            'text_model' => '',
            'vision_model' => '',
            'image_upload_enabled' => true,
        ];
        if (!is_file($this->file)) {
            return $default;
        }
        $raw = @file_get_contents($this->file);
        $data = json_decode((string) $raw, true);
        if (!is_array($data)) {
            return $default;
        }
        return [
            'enabled' => (bool) ($data['enabled'] ?? true),
            'message' => (string) ($data['message'] ?? $default['message']),
            'updated_at' => (string) ($data['updated_at'] ?? ''),
            'text_model' => (string) ($data['text_model'] ?? ''),
            'vision_model' => (string) ($data['vision_model'] ?? ''),
            'image_upload_enabled' => (bool) ($data['image_upload_enabled'] ?? true),
        ];
    }

    public function isEnabled(): bool
    {
        return $this->read()['enabled'];
    }

    public function setEnabled(bool $enabled, ?string $message = null): void
    {
        $current = $this->read();
        $this->write([
            'enabled' => $enabled,
            'message' => $message !== null ? trim($message) : $current['message'],
            'text_model' => $current['text_model'],
            'vision_model' => $current['vision_model'],
            'image_upload_enabled' => $current['image_upload_enabled'],
        ]);
    }

    public function setModels(string $textModel, string $visionModel, bool $imageUploadEnabled): void
    {
        $current = $this->read();
        $this->write([
            'enabled' => $current['enabled'],
            'message' => $current['message'],
            'text_model' => $textModel,
            'vision_model' => $visionModel,
            'image_upload_enabled' => $imageUploadEnabled,
        ]);
    }

    private function write(array $data): void
    {
        $msg = trim((string) ($data['message'] ?? ''));
        if ($msg === '') {
            $msg = 'Chat AI sedang tidak aktif. Akan dibuka semasa kelas training nanti.';
        }
        $payload = [
            'enabled' => (bool) ($data['enabled'] ?? true),
            'message' => $msg,
            'text_model' => trim((string) ($data['text_model'] ?? '')),
            'vision_model' => trim((string) ($data['vision_model'] ?? '')),
            'image_upload_enabled' => (bool) ($data['image_upload_enabled'] ?? true),
            'updated_at' => date('c'),
        ];
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        // tmp + rename: chat yang membaca serentak tidak pernah nampak fail kosong (dan jatuh ke nilai lalai)
        $tmp = $this->file . '.tmp' . bin2hex(random_bytes(3));
        if (file_put_contents($tmp, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            throw new RuntimeException('Gagal menyimpan status chat.');
        }
        rename($tmp, $this->file);
    }
}
