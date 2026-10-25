<?php

declare(strict_types=1);

final class Env
{
    /** @var array<string,string> */
    private static array $vars = [];
    private static bool $loaded = false;

    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }
        if (!is_file($path)) {
            throw new RuntimeException(".env file not found at: {$path}");
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (strlen($value) >= 2) {
                $first = $value[0];
                $last = $value[strlen($value) - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }
            self::$vars[$key] = $value;
        }
        self::$loaded = true;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return self::$vars[$key] ?? $default;
    }

    public static function int(string $key, int $default): int
    {
        $v = self::$vars[$key] ?? null;
        return $v !== null && $v !== '' ? (int) $v : $default;
    }

    public static function float(string $key, float $default): float
    {
        $v = self::$vars[$key] ?? null;
        return $v !== null && $v !== '' ? (float) $v : $default;
    }
}
