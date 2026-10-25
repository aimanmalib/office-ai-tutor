<?php

declare(strict_types=1);

/**
 * Had kadar tingkap gelongsor dalam satu fail JSON (senarai cap masa), dikunci flock untuk baca-ubah-tulis:
 * permintaan serentak dari IP yang sama tidak boleh semuanya lulus semakan sebelum ada yang menulis.
 */
final class RateLimit
{
    public function __construct(private readonly string $file, private readonly int $window) {}

    /**
     * Kunci fail untuk satu IP. IPv6 dikira ikut /64: satu sambungan rumah/telefon memegang 2^64 alamat,
     * jadi had ikut alamat penuh boleh dipintas dengan menukar alamat.
     */
    public static function bucket(string $ip): string
    {
        $bin = @inet_pton($ip);
        if ($bin !== false && strlen($bin) === 16) {
            $ip = bin2hex(substr($bin, 0, 8)) . '::/64';
        }
        return md5($ip);
    }

    /** Ambil satu slot. Pulangkan cap masa hit (untuk refund) atau null bila had sudah penuh. */
    public function take(int $limit, int $now): ?int
    {
        return $this->update(function (array $hits) use ($limit, $now): array {
            if (count($hits) >= $limit) {
                return [$hits, null];
            }
            $hits[] = $now;
            return [$hits, $now];
        }, $now);
    }

    /** Pulangkan slot bila permintaan gagal (ralat AI bukan salah pelajar). */
    public function refund(int $hitAt): void
    {
        $this->update(function (array $hits) use ($hitAt): array {
            $i = array_search($hitAt, $hits, true);
            if ($i !== false) {
                array_splice($hits, (int) $i, 1);
            }
            return [$hits, null];
        }, time());
    }

    public function remaining(int $limit, int $now): int
    {
        $hits = json_decode((string) @file_get_contents($this->file), true);
        $hits = is_array($hits) ? array_filter($hits, fn($t) => is_int($t) && $t > $now - $this->window) : [];
        return max(0, $limit - count($hits));
    }

    /** @param callable(array<int,int>):array{0:array<int,int>,1:?int} $fn */
    private function update(callable $fn, int $now): ?int
    {
        $fp = @fopen($this->file, 'c+');
        if ($fp === false) {
            return $now;   // gagal-terbuka: jangan sekat pelajar kerana cakera
        }
        try {
            flock($fp, LOCK_EX);
            $stored = json_decode((string) stream_get_contents($fp), true);
            $hits = is_array($stored)
                ? array_values(array_filter($stored, fn($t) => is_int($t) && $t > $now - $this->window))
                : [];
            [$hits, $result] = $fn($hits);
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, (string) json_encode(array_values($hits)));
            return $result;
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
}
