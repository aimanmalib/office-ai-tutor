<?php

declare(strict_types=1);

require_once __DIR__ . '/Latihan.php';

/**
 * Pemapar halaman awam: templat src/papar/<nama>.php menetapkan $tajuk/$huraian dan mencetak badan; _bingkai.php
 * membalutnya (head, nav, kaki). Templat menerima $subjekId (null = laman utama), $halaman, dan pembantu di bawah.
 */
final class Papar
{
    public static function halaman(string $templat, array $v): void
    {
        extract($v, EXTR_SKIP);
        $tajuk = '';            // <title> (tanpa nama jenama — _bingkai menambahnya)
        $huraian = '';          // meta description
        $skrip = [];            // [laluan JS] dimuat defer selepas badan
        $data = [];             // window.DATA (JSON) untuk JS halaman
        $widget = true;         // butang tutor terapung
        $noindex = false;
        ob_start();
        require __DIR__ . "/papar/$templat.php";
        $badan = (string) ob_get_clean();
        require __DIR__ . '/papar/_bingkai.php';
    }

    public static function e(mixed $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Teks kandungan: hanya <strong> <code> <em> <br> tanpa atribut (Latihan::html). */
    public static function f(mixed $s): string
    {
        return Latihan::html((string) $s);
    }

    /** Teks biasa (untuk <title>, meta, data-cari): tag + entiti dibuang. */
    public static function polos(mixed $s): string
    {
        return trim(html_entity_decode(strip_tags(Latihan::html((string) $s)), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    /** ?v=<cincangan> untuk aset (CF menyimpan aset ikut Cache-Control; nama sama = versi lama di telefon). */
    public static function aset(string $laluan): string
    {
        $f = __DIR__ . '/../public' . $laluan;
        return $laluan . '?v=' . substr((string) @md5_file($f), 0, 8);
    }

    public static function ikon(string $n, string $kelas = 'w-4 h-4'): string
    {
        return Ikon::svg($n, $kelas);
    }

    /** Bilangan kandungan sebenar (dikira, bukan ditaip) untuk kad subjek dan halaman gambaran. */
    public static function kiraan(string $s, string $b): array
    {
        require_once __DIR__ . '/Fungsi.php';
        $lat = Kandungan::baca($s, 'latihan', $b);
        $n = 0;
        foreach ($lat as $set) {
            $n += count($set['questions'] ?? []);
        }
        $cs = Kandungan::baca($s, 'cheatsheet', $b);
        $pint = 0;
        foreach ($cs['bahagian'] ?? [] as $bh) {
            $pint += ($bh['jenis'] ?? '') === 'pintasan' ? count($bh['item'] ?? []) : 0;
        }
        return ['latihan' => $n, 'set' => count($lat), 'pintasan' => $pint, 'bahagian' => count($cs['bahagian'] ?? []),
            'rujukan' => $s === 'excel' ? count(Fungsi::semua($b)) : count(Kandungan::baca($s, 'rujukan', $b)['ciri'] ?? []),
            'formula' => $s === 'excel' ? count(Kandungan::baca($s, 'formula', $b)['formula'] ?? []) : 0];
    }

    /** Kekunci pintasan: ['Ctrl','Alt','C'] → <kbd>…</kbd>+<kbd>…</kbd> */
    public static function kekunci(array $keys): string
    {
        return implode('<span class="text-muted text-xs px-0.5" aria-hidden="true">+</span>',
            array_map(fn($k) => '<kbd>' . self::e($k) . '</kbd>', $keys));
    }

    /** "Ctrl+Alt+C / Ctrl+Alt+V (Word 2019: …)" → kbd untuk bahagian kekunci, teks selebihnya biasa. */
    public static function pintasanTeks(string $s): string
    {
        return (string) preg_replace_callback('~(?<![\w])((?:Ctrl|Shift|Alt|Win|Fn|Cmd|⌘|Option)(?:\s*\+\s*[A-Za-z0-9⌘]+|\s*\+\s*(?:F\d{1,2}|Enter|Space|Tab|Esc|Delete|Backspace|Home|End|PgUp|PgDn|Up|Down|Left|Right|[=;:,.\-\[\]\'`/\\\\]))+|F\d{1,2})(?![\w])~u',
            fn($m) => self::kekunci(array_map('trim', explode('+', $m[1]))), self::e($s));
    }
}
