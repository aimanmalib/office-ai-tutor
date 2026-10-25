<?php

declare(strict_types=1);

/**
 * Bahasa antara muka (revamp v2, docs/REVAMP.md): BM di akar, EN di /en/. Teks = config/bahasa/<kod>.php (kunci → teks);
 * kod tidak menyimpan teks. Kunci yang hilang dalam satu bahasa = gerbang ujian gagal (ujian/uji.py), bukan senyap.
 */
final class Bahasa
{
    public const SEMUA = ['ms', 'en'];
    public const LOKAL = ['ms' => 'ms_MY', 'en' => 'en_US'];

    private static string $kod = 'ms';
    /** @var array<string,array<string,string>> */
    private static array $teks = [];

    public static function tetap(string $kod): void
    {
        self::$kod = in_array($kod, self::SEMUA, true) ? $kod : 'ms';
    }

    public static function kod(): string
    {
        return self::$kod;
    }

    public static function lain(): string
    {
        return self::$kod === 'ms' ? 'en' : 'ms';
    }

    /** Awalan URL bahasa: '' (BM) atau '/en'. */
    public static function awalan(?string $kod = null): string
    {
        return ($kod ?? self::$kod) === 'en' ? '/en' : '';
    }

    /** @return array<string,string> */
    public static function senarai(?string $kod = null): array
    {
        $kod ??= self::$kod;
        if (!isset(self::$teks[$kod])) {
            self::$teks[$kod] = require __DIR__ . "/../config/bahasa/$kod.php";
        }
        return self::$teks[$kod];
    }

    /** t('nav.cheatsheet'), t('kira.fungsi', ['n' => 499]) → ganti {n}. */
    public static function t(string $kunci, array $ganti = []): string
    {
        $s = self::senarai()[$kunci] ?? null;
        if ($s === null) {
            error_log("[Bahasa] kunci tiada ({$kunci}, " . self::$kod . ')');
            $s = self::senarai('ms')[$kunci] ?? $kunci;
        }
        foreach ($ganti as $k => $v) {
            $s = str_replace('{' . $k . '}', (string) $v, $s);
        }
        return $s;
    }

    /** Nombor ikut bahasa: 1,234 (EN) / 1,234 (BM — Malaysia juga koma ribuan). */
    public static function nombor(int|float $n): string
    {
        return number_format($n);
    }
}

function t(string $kunci, array $ganti = []): string
{
    return Bahasa::t($kunci, $ganti);
}

/** t() yang sudah di-escape untuk HTML. */
function te(string $kunci, array $ganti = []): string
{
    return htmlspecialchars(Bahasa::t($kunci, $ganti), ENT_QUOTES);
}
