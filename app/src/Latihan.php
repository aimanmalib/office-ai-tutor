<?php

declare(strict_types=1);

/**
 * Soalan latihan: semak jawapan pelajar + sahkan soalan yang ditulis pengajar di admin (atau ditampal sebagai JSON dari AI).
 * Mod: formula | result (taip jawapan, dipadan dengan corak) · choice (pilih satu) · pintasan (kombinasi kekunci,
 * cth "Ctrl+Alt+1" — Ctrl ≡ ⌘ Mac, susunan modifier tidak penting).
 * Pengajar menulis JAWAPAN ("=SUM(B2:B4)"), bukan regex: corak dijana dari bentuk ternormal jawapan itu.
 */
final class Latihan
{
    public const MOD = ['formula', 'result', 'choice', 'pintasan'];

    /**
     * Ruang di luar petikan dibuang, huruf kecil. Juga di luar petikan: `;` → `,` (Excel tetapan Eropah), dan — bila
     * $dropDollar — `$` rujukan mutlak dibuang. Petikan pintar papan kekunci telefon (iOS) menjadi ".
     */
    public static function normal(string $s, bool $dropDollar = false): string
    {
        $s = str_replace(["\u{201C}", "\u{201D}", "\u{201E}", "\u{2033}", "\u{FF02}"], '"', trim($s));
        $out = '';
        $quote = false;
        $n = strlen($s);
        for ($i = 0; $i < $n; $i++) {
            $c = $s[$i];
            if ($c === '"') {
                $quote = !$quote;
                $out .= $c;
            } elseif (!$quote && ctype_space($c)) {
                continue;
            } elseif (!$quote && $c === ';') {
                $out .= ',';
            } elseif (!$quote && $dropDollar && $c === '$') {
                continue;
            } else {
                $out .= $c;
            }
        }
        return strtolower($out);
    }

    /** "ctrl + alt + 1", "⌘⇧C", "Control+Shift+c" → "ctrl+shift+c" (susunan tetap: ctrl, alt, shift, kekunci). */
    public static function normalPintasan(string $s): string
    {
        $s = strtolower(trim($s));
        $s = strtr($s, ['⌘' => ' ctrl ', '⌥' => ' alt ', '⇧' => ' shift ', '⌃' => ' ctrl ']);
        // Kekunci "+" itu sendiri (Microsoft: "Ctrl+Plus sign") — "Ctrl++", "Ctrl + +", "Ctrl+Shift++" — sebelum memisah pada "+"
        $s = (string) preg_replace(['/\b(plus|minus|equal|equals)\s+sign\b/', '/\s*\(.\)\s*$/', '/\+\s*\+/', '/^\s*\+\s*$/'], ['$1', '', '+plus', 'plus'], $s);
        $bahagian = preg_split('/\s*\+\s*|\s+/', $s, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $sinonim = ['control' => 'ctrl', 'ctl' => 'ctrl', 'cmd' => 'ctrl', 'command' => 'ctrl', 'meta' => 'ctrl', 'win' => 'ctrl',
                    'option' => 'alt', 'opt' => 'alt', 'spacebar' => 'space', 'return' => 'enter', 'esc' => 'escape', 'del' => 'delete',
                    'plus' => '=', 'equal' => '=', 'equals' => '=', 'minus' => '-', 'hyphen' => '-'];   // + dan = = kekunci fizikal sama
        $mod = [];
        $kekunci = [];
        foreach ($bahagian as $b) {
            $b = $sinonim[$b] ?? $b;
            if (in_array($b, ['ctrl', 'alt', 'shift'], true)) {
                $mod[$b] = true;
            } else {
                $kekunci[] = $b;
            }
        }
        // Simbol yang perlukan Shift = kekunci fizikalnya + Shift (perakam latihan.js merekod kekunci fizikal: Ctrl+Shift+4, bukan $)
        $shift = ['!' => '1', '@' => '2', '#' => '3', '$' => '4', '%' => '5', '^' => '6', '&' => '7', '*' => '8', '(' => '9', ')' => '0',
                  '_' => '-', ':' => ';', '"' => "'", '<' => ',', '>' => '.', '?' => '/', '~' => '`', '{' => '[', '}' => ']', '|' => '\\'];
        if (count($kekunci) === 1 && isset($shift[$kekunci[0]])) {
            $kekunci[0] = $shift[$kekunci[0]];
            $mod['shift'] = true;
        }
        if (count($kekunci) !== 1) {
            return '';   // tiada kekunci ("Ctrl+" belum siap) atau lebih daripada satu = bukan pintasan
        }
        $susun = array_values(array_filter(['ctrl', 'alt', 'shift'], fn($m) => isset($mod[$m])));
        return implode('+', array_merge($susun, $kekunci));
    }

    /** @param array<string,mixed> $q */
    public static function betul(array $q, string $jawapan, int $pilihan): bool
    {
        if ($q['mode'] === 'choice') {
            return $pilihan === (int) $q['correct_index'];
        }
        if ($q['mode'] === 'pintasan') {
            $j = self::normalPintasan($jawapan);
            return $j !== '' && in_array($j, array_map([self::class, 'normalPintasan'], $q['jawapan'] ?? []), true);
        }
        $patterns = $q['accept'] ?? [];
        // Soalan yang menguji $ (rujukan mutlak) mempunyai \$ dalam coraknya — di situ $ mesti kekal.
        $testsDollar = (bool) array_filter($patterns, fn($p) => str_contains($p, '\\$'));
        $calon = $testsDollar ? [self::normal($jawapan)] : array_unique([self::normal($jawapan), self::normal($jawapan, true)]);
        foreach ($calon as $c) {
            foreach ($patterns as $p) {
                if (@preg_match($p, $c) === 1) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Boleh diterima ikut HASIL (latihan.js mengira formula atas grid; fungsi mesti sama dengan jawapan contoh)?
     * Tidak untuk soalan yang menguji $ — =A3*B1 memberi hasil sama dengan =A3*$B$1 tetapi itulah yang diuji.
     */
    public static function bolehSetara(array $q): bool
    {
        return $q['mode'] === 'formula' && !empty($q['grid']) && ($q['setara'] ?? true) !== false
            && !array_filter($q['accept'] ?? [], fn($p) => str_contains($p, '\\$'));
    }

    /** Corak untuk satu jawapan tepat yang ditulis pengajar. */
    public static function corak(string $jawapan): string
    {
        return '/^' . preg_quote(self::normal($jawapan), '/') . '$/';
    }

    /** Bentuk yang dihantar ke pelayar: TANPA corak jawapan / indeks betul / penerangan. */
    public static function awam(array $q): array
    {
        return [
            'id' => $q['id'], 'category' => $q['category'], 'icon' => $q['icon'] ?? '📝', 'level' => $q['level'],
            'prompt' => self::html((string) $q['prompt']), 'mode' => $q['mode'], 'grid' => $q['grid'] ?? null, 'highlight' => $q['highlight'] ?? null,
            'options' => isset($q['options']) ? array_map(fn($o) => self::html((string) $o), $q['options']) : null, 'hint' => self::html((string) ($q['hint'] ?? '')),
            'setara' => self::bolehSetara($q),
            // salah satu jawapan diterima TANPA fungsi (cth =B2+B3+B4) → susunan lain tanpa fungsi juga setara
            'setara_bebas' => (bool) array_filter($q['accept'] ?? [], fn($p) => !str_contains($p, '\\(')),
        ];
    }

    /**
     * Soalan dari borang admin atau JSON AI → bentuk tersimpan. Medan (nama BM, lihat Panduan AI di admin):
     * soalan, mod, kategori, ikon, tahap 1-3, grid (baris; sel dipisah |), sel (cth B5), jawapan (senarai), corak (lanjutan),
     * pilihan (senarai), betul (nombor pilihan bermula 1), petunjuk, penerangan.
     * @param array<string,mixed> $in
     * @param array<int,string> $idWujud
     */
    public static function sahkan(array $in, array $idWujud, ?string $id = null): array
    {
        foreach (['soalan', 'kategori', 'ikon', 'grid', 'sel', 'petunjuk', 'penerangan', 'jawapan', 'corak', 'pilihan', 'mod'] as $k) {
            if (isset($in[$k]) && !is_scalar($in[$k]) && !(is_array($in[$k]) && in_array($k, ['grid', 'jawapan', 'corak', 'pilihan'], true))) {
                throw new InvalidArgumentException("Medan \"$k\" mesti teks.");
            }
        }
        $teks = fn(string $k, int $max) => self::html(mb_substr((string) ($in[$k] ?? ''), 0, $max));
        $senarai = fn(string $k): array => array_values(array_filter(array_map(fn($x) => trim((string) $x),
            is_array($in[$k] ?? null) ? $in[$k] : (preg_split('/\R/', (string) ($in[$k] ?? '')) ?: [])), fn($x) => $x !== ''));

        $q = ['id' => $id ?? self::idBaru($idWujud)];
        $q['category'] = trim(strip_tags(html_entity_decode(mb_substr((string) ($in['kategori'] ?? ''), 0, 40)))) ?: 'Umum';
        $q['icon'] = mb_substr(trim((string) ($in['ikon'] ?? '')), 0, 4) ?: '📝';
        $q['level'] = max(1, min(3, (int) ($in['tahap'] ?? 1)));
        $q['prompt'] = $teks('soalan', 1000);
        if ($q['prompt'] === '') {
            throw new InvalidArgumentException('Soalan kosong.');
        }
        $q['mode'] = in_array($in['mod'] ?? '', self::MOD, true) ? $in['mod'] : 'formula';

        $grid = [];
        foreach (is_array($in['grid'] ?? null) ? $in['grid'] : (preg_split('/\R/', trim((string) ($in['grid'] ?? ''))) ?: []) as $baris) {
            $sel = is_array($baris) ? $baris : explode('|', (string) $baris);
            if (count($sel) === 1 && trim((string) $sel[0]) === '') {
                continue;
            }
            $grid[] = array_map(fn($c) => is_int($c) || is_float($c) ? $c : mb_substr(trim((string) $c), 0, 40), $sel);
        }
        if (count($grid) > 20 || ($grid && max(array_map('count', $grid)) > 8)) {
            throw new InvalidArgumentException('Grid terlalu besar (maksimum 20 baris × 8 lajur).');
        }
        $lebar = $grid ? max(array_map('count', $grid)) : 0;
        $q['grid'] = $grid ? array_map(fn($r) => array_pad($r, $lebar, ''), $grid) : null;
        $sel = strtoupper(trim((string) ($in['sel'] ?? '')));
        if ($sel !== '' && !preg_match('/^[A-H]([1-9]|1\d|20)$/', $sel)) {
            throw new InvalidArgumentException("Sel \"$sel\" tidak sah (cth B5).");
        }
        $q['highlight'] = $sel !== '' ? $sel : null;

        if ($q['mode'] === 'choice') {
            $pilihan = array_map(fn($p) => mb_substr($p, 0, 200), $senarai('pilihan'));   // teks mentah: dipapar dengan escapeHtml
            if (count($pilihan) < 2 || count($pilihan) > 6) {
                throw new InvalidArgumentException('Soalan pilihan perlu 2 hingga 6 pilihan.');
            }
            $betul = (int) ($in['betul'] ?? 0);
            if ($betul < 1 || $betul > count($pilihan)) {
                throw new InvalidArgumentException('Pilih jawapan betul (nombor 1–' . count($pilihan) . ').');
            }
            $q['options'] = $pilihan;
            $q['correct_index'] = $betul - 1;
            $q['canonical'] = $pilihan[$betul - 1];
        } elseif ($q['mode'] === 'pintasan') {
            $jawapan = array_map(fn($j) => mb_substr($j, 0, 40), $senarai('jawapan'));
            if (!$jawapan || in_array('', array_map([self::class, 'normalPintasan'], $jawapan), true)) {
                throw new InvalidArgumentException('Tulis pintasan yang diterima, cth Ctrl+B (satu baris satu).');
            }
            $q['jawapan'] = $jawapan;
            $q['canonical'] = $jawapan[0];
        } else {
            $jawapan = array_map(fn($j) => mb_substr($j, 0, 200), $senarai('jawapan'));
            $corak = $senarai('corak');
            foreach ($corak as $c) {
                if (@preg_match($c, '') === false) {
                    throw new InvalidArgumentException("Corak lanjutan tidak sah: $c");
                }
            }
            if (!$jawapan && !$corak) {
                throw new InvalidArgumentException('Tulis sekurang-kurangnya satu jawapan yang diterima.');
            }
            $q['accept'] = array_values(array_unique(array_merge(array_map([self::class, 'corak'], $jawapan), $corak)));
            $q['canonical'] = $jawapan[0] ?? mb_substr(trim((string) ($in['kanonik'] ?? '')), 0, 200);
            $q['jawapan'] = $jawapan;   // disimpan untuk borang ubah (corak dijana semula daripadanya)
        }
        $q['hint'] = $teks('petunjuk', 600);
        $q['explanation'] = $teks('penerangan', 1200);
        return $q;
    }

    /** Medan borang untuk soalan sedia ada (termasuk soalan benih yang hanya ada corak). */
    public static function keBorang(array $q): array
    {
        $jawapan = $q['jawapan'] ?? [];
        $dijana = array_map([self::class, 'corak'], $jawapan);
        return [
            'soalan' => html_entity_decode($q['prompt'], ENT_QUOTES, 'UTF-8'), 'mod' => $q['mode'], 'kategori' => $q['category'], 'ikon' => $q['icon'] ?? '📝',
            'tahap' => $q['level'], 'sel' => $q['highlight'] ?? '',
            'grid' => implode("\n", array_map(fn($r) => implode(' | ', array_map('strval', $r)), $q['grid'] ?? [])),
            'jawapan' => implode("\n", $jawapan ?: ($q['mode'] === 'choice' ? [] : [$q['canonical'] ?? ''])),
            // pintasan: jawapan tidak dijana menjadi corak
            'corak' => implode("\n", array_values(array_diff($q['accept'] ?? [], $dijana))),
            'pilihan' => implode("\n", $q['options'] ?? []), 'betul' => isset($q['correct_index']) ? $q['correct_index'] + 1 : '',
            'petunjuk' => html_entity_decode($q['hint'] ?? '', ENT_QUOTES, 'UTF-8'),
            'penerangan' => html_entity_decode($q['explanation'] ?? '', ENT_QUOTES, 'UTF-8'),
        ];
    }

    /**
     * HTML soalan/petunjuk/penerangan dipapar dengan innerHTML (latihan.js): hanya tag format TANPA atribut.
     * strip_tags() sahaja mengekalkan atribut (<strong onclick=…>) — atribut dibuang di sini.
     */
    public static function html(string $s): string
    {
        // Lepaskan SEMUA dahulu (bukan strip_tags: ia memotong teks selepas "<" — "=IF(B2<50,…)" jadi "=IF(B2" — dan
        // mengekalkan atribut <strong onclick=…>), kemudian pulihkan hanya tag format, tanpa atribut. Entiti sedia ada
        // (&gt; dalam benih) tidak dikod dua kali.
        $s = htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
        return trim((string) preg_replace('#&lt;(/?)(strong|b|em|i|code|br)\s*/?&gt;#i', '<$1$2>', $s));
    }

    /** @param array<int,string> $wujud */
    public static function idBaru(array $wujud): string
    {
        do {
            $id = 'q' . bin2hex(random_bytes(3));
        } while (in_array($id, $wujud, true));
        return $id;
    }
}
