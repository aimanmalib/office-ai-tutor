<?php

declare(strict_types=1);

require_once __DIR__ . '/Bahasa.php';
require_once __DIR__ . '/Subjek.php';

/**
 * URL laman (revamp v2): [/en]/ · [/en]/<subjek>/ · [/en]/<subjek>/<halaman>. Halaman setiap subjek = config/subjek.php
 * 'halaman'. URL lama (/formula.php, /word/rujukan …) dialih 301 oleh lama() — pautan dalam nota/WhatsApp pelajar kekal.
 */
final class Laluan
{
    /** halaman → templat src/papar */
    public const TEMPLAT = ['' => 'subjek', 'cheatsheet' => 'cheatsheet', 'formulas' => 'formula', 'reference' => 'rujukan',
        'practice' => 'latihan', 'playground' => 'playground', 'tutor' => 'tutor'];

    public static function utama(?string $bahasa = null): string
    {
        return Bahasa::awalan($bahasa) . '/';
    }

    public static function subjek(string $subjek, string $halaman = '', ?string $bahasa = null): string
    {
        return Bahasa::awalan($bahasa) . '/' . $subjek . '/' . $halaman;
    }

    /** @return list<string> halaman yang subjek ini ada (tanpa '' = gambaran keseluruhan) */
    public static function halaman(string $subjek): array
    {
        return Subjek::semua()[$subjek]['halaman'] ?? [];
    }

    /**
     * Laluan lama → baharu (301). null = bukan URL lama.
     */
    public static function lama(string $laluan): ?string
    {
        static $peta = ['/index.php' => '/', '/cheatsheet.php' => '/excel/cheatsheet', '/formula.php' => '/excel/formulas',
            '/formula-lengkap.php' => '/excel/reference', '/reference.php' => '/excel/reference', '/latihan.php' => '/excel/practice',
            '/playground.php' => '/excel/playground', '/chat.php' => '/excel/tutor'];
        if (isset($peta[$laluan])) {
            return $peta[$laluan];
        }
        if (preg_match('~^(/en)?/([a-z]{2,20})/(rujukan|latihan|chat)$~', $laluan, $m)) {
            return $m[1] . '/' . $m[2] . '/' . ['rujukan' => 'reference', 'latihan' => 'practice', 'chat' => 'tutor'][$m[3]];
        }
        return null;
    }

    /**
     * Huraikan laluan permintaan. @return array{bahasa:string,subjek:?string,halaman:string}|array{alih:string}|null
     * null = 404.
     */
    public static function hurai(string $laluan): ?array
    {
        if (($baru = self::lama($laluan)) !== null) {
            return ['alih' => $baru];
        }
        $bahasa = 'ms';
        if ($laluan === '/en') {
            return ['alih' => '/en/'];
        }
        if (str_starts_with($laluan, '/en/')) {
            $bahasa = 'en';
            $laluan = substr($laluan, 3);
        }
        if ($laluan === '/') {
            return ['bahasa' => $bahasa, 'subjek' => null, 'halaman' => ''];
        }
        if (!preg_match('~^/([a-z]{2,20})(/([a-z]{2,20})?)?/?$~', $laluan, $m)) {
            return null;
        }
        $subjek = $m[1];
        $halaman = $m[3] ?? '';
        if (!Subjek::sah($subjek)) {
            return null;
        }
        if (!isset($m[2])) {
            return ['alih' => Bahasa::awalan($bahasa) . "/$subjek/"];          // /excel → /excel/
        }
        if ($halaman !== '' && str_ends_with($laluan, '/')) {
            return ['alih' => Bahasa::awalan($bahasa) . "/$subjek/$halaman"];  // /excel/cheatsheet/ → tanpa garis miring
        }
        if ($halaman !== '' && !in_array($halaman, self::halaman($subjek), true)) {
            return null;
        }
        return ['bahasa' => $bahasa, 'subjek' => $subjek, 'halaman' => $halaman];
    }
}
