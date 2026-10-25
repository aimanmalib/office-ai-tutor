<?php

declare(strict_types=1);

/**
 * Gerbang kandungan dwibahasa (docs/REVAMP.md, docs/GAYA-TULISAN.md). Dijalankan oleh ujian/uji.py dan oleh sesiapa yang
 * menyunting kandungan:  php alat/semak-kandungan.php [excel|word|powerpoint|formulas ...]   (tiada hujah = semua)
 * Menyemak: fail EN wujud untuk setiap fail BM; struktur sama (id, susunan, kunci, jawapan); perkataan dilarang;
 * tiada emoji dalam teks; nilai `ikon` = nama dalam src/Ikon.php. Keluar 1 bila ada masalah.
 */

require __DIR__ . '/../src/Ikon.php';
require __DIR__ . '/../src/Latihan.php';
require __DIR__ . '/../src/Kandungan.php';

$AKAR = dirname(__DIR__);
$masalah = [];
$m = function (string $f, string $s) use (&$masalah) { $masalah[] = "$f: $s"; };

const DILARANG_SEMUA = ['unlock', 'supercharge', 'seamless', 'seamlessly', 'elevate', 'empower', 'journey', 'dive in', 'deep dive',
    'game-changer', 'game changer', 'effortless', 'effortlessly', 'revolutionize', 'revolutionise', 'harness', 'unleash',
    "in today's", 'look no further', 'next level', 'next-level', 'magic', 'ultimate', 'made easy', 'at your fingertips',
    'cutting-edge', 'cutting edge', 'boost', 'jom', 'best', 'power', 'rahsia', 'jadi senang', 'di hujung jari', 'secara pintar',
    'revolusi', 'tahap seterusnya', 'wajib hafal', 'paling lengkap', 'cincai', 'gempak', 'terbaik', 'whether you'];
const PASAR = ['tak', 'nak', 'kalau', 'pasal', 'macam mana', 'dah', 'je', 'takde', 'tapi kalau', 'camne', 'korang', 'awak'];
// Pengecualian kata yang sah dalam konteks Excel/Office (nama fungsi, nama menu)
const BENAR = ['power query', 'power pivot', 'powerpoint', 'power bi', 'power()', 'power(', '=power', 'best fit', 'powershell'];

function teks(mixed $d, string $laluan = ''): Generator
{
    if (is_string($d)) {
        yield $laluan => $d;
    } elseif (is_array($d)) {
        foreach ($d as $k => $v) {
            if (in_array($k, ['icon', 'accept', 'canonical', 'keys', 'syntax', 'example', 'id', 'cat', 'highlight', 'mode', 'color'], true)) {
                continue;   // bukan prosa
            }
            yield from teks($v, $laluan . '/' . $k);
        }
    }
}

function semakTeks(string $f, mixed $data, bool $ms, callable $m): void
{
    foreach (teks($data) as $laluan => $s) {
        $polos = mb_strtolower(strip_tags($s));
        $tanpaKod = preg_replace('~<code>.*?</code>~s', '', mb_strtolower($s));
        foreach (BENAR as $b) {
            $tanpaKod = str_replace($b, '', $tanpaKod);
        }
        foreach (DILARANG_SEMUA as $w) {
            if (preg_match('/(?<![\p{L}])' . preg_quote($w, '/') . '(?![\p{L}])/u', $tanpaKod)) {
                $m($f, "perkataan dilarang \"$w\" di $laluan: " . mb_substr($polos, 0, 90));
            }
        }
        if ($ms) {
            foreach (PASAR as $w) {
                if (preg_match('/(?<![\p{L}])' . preg_quote($w, '/') . '(?![\p{L}])/u', $tanpaKod)) {
                    $m($f, "bahasa pasar \"$w\" di $laluan: " . mb_substr($polos, 0, 90));
                }
            }
        }
        if (preg_match('/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B50}\x{2705}\x{274C}\x{FE0F}]/u', $s)) {
            $m($f, "emoji di $laluan: " . mb_substr($polos, 0, 60));
        }
        if (substr_count($s, '!') > 0 && !preg_match('/[=!]=|<>|!\$|![A-Z]|\w!\w|#/', $s)) {
            $m($f, "tanda seru di $laluan: " . mb_substr($polos, 0, 60));
        }
    }
}

function semakIkon(string $f, mixed $d, callable $m, string $laluan = ''): void
{
    if (!is_array($d)) {
        return;
    }
    foreach ($d as $k => $v) {
        if ($k === 'ikon' && is_string($v) && !Ikon::ada($v)) {
            $m($f, "ikon \"$v\" tiada dalam src/Ikon.php ($laluan)");
        }
        if (is_array($v)) {
            semakIkon($f, $v, $m, "$laluan/$k");
        }
    }
}

function muat(string $f): mixed
{
    return require $f;
}

$pilih = array_slice($argv, 1) ?: ['excel', 'word', 'powerpoint', 'formulas'];

foreach (array_diff($pilih, ['formulas']) as $s) {
    $dir = "$AKAR/config/pek/$s";
    foreach (glob("$dir/*.php") as $fms) {
        $jenis = basename($fms, '.php');
        if (in_array($jenis, ['playground', '_soalan'], true)) {
            continue;
        }
        $fen = "$dir/en/$jenis.php";
        $rel = "pek/$s/$jenis";
        if (!is_file($fen)) {
            $m($rel, 'tiada versi EN (en/' . $jenis . '.php)');
            continue;
        }
        $ms = Kandungan::benih($s, $jenis, 'ms');
        $en = Kandungan::benih($s, $jenis, 'en');   // fail EN bentuk tindih = digabung atas BM
        if ($jenis !== 'prompt') {
            semakTeks("$rel (ms)", $ms, true, $m);
            semakTeks("$rel (en)", $en, false, $m);
            semakIkon("$rel (ms)", $ms, $m);
            semakIkon("$rel (en)", $en, $m);
        }
        if ($jenis === 'prompt') {
            if (!is_string($en) || mb_strlen($en) < 200) {
                $m($rel, 'prompt EN kosong/pendek');
            }
        } elseif ($jenis === 'cheatsheet') {
            $a = array_map(fn($b) => [$b['id'], $b['jenis'], count($b['item'] ?? $b['baris'] ?? [])], $ms['bahagian']);
            $b = array_map(fn($b) => [$b['id'], $b['jenis'], count($b['item'] ?? $b['baris'] ?? [])], $en['bahagian']);
            if ($a !== $b) {
                $m($rel, 'bahagian ms ≠ en (id/jenis/bilangan item): ' . json_encode(array_map(null, $a, $b)));
            }
            foreach ($ms['bahagian'] as $i => $bh) {
                if ($bh['jenis'] === 'pintasan') {
                    foreach ($bh['item'] as $j => $it) {
                        if (($en['bahagian'][$i]['item'][$j]['keys'] ?? null) !== $it['keys']) {
                            $m($rel, "kekunci berbeza: {$bh['id']} #$j");
                        }
                    }
                }
                if (!in_array($bh['jenis'], ['pintasan', 'kad', 'jadual', 'kesilapan'], true)) {
                    $m($rel, "jenis bahagian tidak dikenali: {$bh['jenis']}");
                }
            }
        } elseif ($jenis === 'rujukan') {
            if (array_keys($ms['kategori']) !== array_keys($en['kategori'])) {
                $m($rel, 'kategori ms ≠ en');
            }
            $a = array_map(fn($c) => [$c['nama'], $c['cat'], $c['pintasan'] ?? ''], $ms['ciri']);
            $b = array_map(fn($c) => [$c['nama'], $c['cat'], $c['pintasan'] ?? ''], $en['ciri']);
            if ($a !== $b) {
                $m($rel, 'ciri ms ≠ en (nama/cat/pintasan, susunan sama): ' . json_encode(array_values(array_filter(array_map(null, $a, $b), fn($p) => $p[0] !== $p[1]))));
            }
        } elseif ($jenis === 'formula') {
            // contoh boleh berbeza HANYA pada literal teks ("Tinggi" / "High"); rujukan sel + fungsi mesti sama
            $rangka = fn($x) => preg_replace('/"[^"]*"/', '""', (string) $x);
            $a = array_map(fn($c) => [$c['name'], $c['cat'], $c['syntax'], $rangka($c['example'])], $ms['formula']);
            $b = array_map(fn($c) => [$c['name'], $c['cat'], $c['syntax'], $rangka($c['example'])], $en['formula']);
            if ($a !== $b) {
                $m($rel, 'formula ms ≠ en (name/cat/syntax/rangka contoh mesti sama)');
            }
            if (array_keys($ms['kategori']) !== array_keys($en['kategori'])) {
                $m($rel, 'kategori ms ≠ en');
            }
        } elseif ($jenis === 'latihan') {
            if (array_keys($ms) !== array_keys($en)) {
                $m($rel, 'set ms ≠ en');
            }
            foreach ($ms as $sid => $set) {
                $qe = $en[$sid]['questions'] ?? [];
                $tetap = fn($q) => [$q['id'], $q['mode'], $q['level'], $q['highlight'] ?? null, $q['correct_index'] ?? null, count($q['options'] ?? [])];
                if (array_map($tetap, $set['questions']) !== array_map($tetap, $qe)) {
                    $m($rel, "set $sid: soalan ms ≠ en (id/mode/level/highlight/correct_index/bil. pilihan)");
                    continue;
                }
                foreach (['name', 'tagline', 'description'] as $k) {
                    if (($set[$k] ?? '') !== '' && ($en[$sid][$k] ?? '') === $set[$k] && preg_match('/\p{L}{4}/u', $set[$k])) {
                        $m($rel, "set $sid: '$k' EN sama dengan BM (belum diterjemah)");
                    }
                }
                foreach ($qe as $j => $q) {
                    foreach (['prompt', 'hint', 'explanation'] as $k) {
                        // sama + cukup panjang + bukan sekadar laluan menu/pintasan = terlupa diterjemah
                        $teksQ = strip_tags((string) ($q[$k] ?? ''));
                        if ($teksQ !== '' && $q[$k] === ($set['questions'][$j][$k] ?? null) && mb_strlen($teksQ) >= 60
                            && !preg_match('/^[^.]*(→|=)[^.]*\.?$/u', $teksQ)) {
                            $m($rel, "set $sid {$q['id']}: '$k' EN sama dengan BM (belum diterjemah)");
                        }
                    }
                    if ($q['mode'] !== 'choice' && isset($q['canonical']) && !Latihan::betul($q, (string) $q['canonical'], -1)) {
                        $m($rel, "set $sid {$q['id']}: jawapan contoh EN tidak diterima oleh accept EN");
                    }
                    if (isset($q['grid'], $set['questions']) ) {
                        $gm = null;
                        foreach ($set['questions'] as $x) { if ($x['id'] === $q['id']) { $gm = $x['grid'] ?? null; } }
                        if (is_array($gm) && (count($gm) !== count($q['grid']) || array_map('count', $gm) !== array_map('count', $q['grid']))) {
                            $m($rel, "set $sid {$q['id']}: bentuk grid EN ≠ BM (rujukan sel akan salah)");
                        }
                    }
                }
            }
        } elseif ($jenis === 'utama') {
            $k = fn($d) => array_keys($d);
            if ($k($ms) !== $k($en)) {
                $m($rel, 'kunci utama ms ≠ en');
            }
        }
    }
}

// Teks antara muka (config/bahasa): kunci sama, gaya sama
$uiMs = muat("$AKAR/config/bahasa/ms.php");
$uiEn = muat("$AKAR/config/bahasa/en.php");
if (array_keys($uiMs) !== array_keys($uiEn)) {
    $m('bahasa', 'kunci ms.php ≠ en.php (susunan juga mesti sama)');
}
semakTeks('bahasa/ms', $uiMs, true, $m);
semakTeks('bahasa/en', $uiEn, false, $m);

if (in_array('formulas', $pilih, true)) {
    foreach (glob("$AKAR/config/formulas/*.php") as $fms) {
        $cat = basename($fms, '.php');
        $fen = "$AKAR/config/formulas/en/$cat.php";
        $rel = "formulas/$cat";
        $ms = muat($fms);
        semakTeks("$rel (ms)", array_map(fn($r) => ['desc' => $r['desc'] ?? '', 'usecase' => $r['usecase'] ?? '', 'sample' => $r['sample'] ?? ''], $ms), true, $m);
        if (!is_file($fen)) {
            $m($rel, 'tiada versi EN');
            continue;
        }
        $en = muat($fen);
        $nama = array_column($ms, 'name');
        if (array_keys($en) !== $nama) {
            $m($rel, 'nama fungsi EN ≠ BM (mesti sama + susunan sama): beza ' . json_encode(array_values(array_merge(array_diff($nama, array_keys($en)), array_diff(array_keys($en), $nama)))));
        }
        foreach ($ms as $r) {
            if (!empty($r['usecase']) && empty($en[$r['name']]['usecase'])) {
                $m($rel, "{$r['name']}: usecase EN tiada");
            }
            if (!empty($r['sample']) && preg_match('/\p{L}{3}/u', preg_replace('/"[^"]*"|[A-Z]{1,3}\d+/', '', $r['sample'])) && empty($en[$r['name']]['sample'])) {
                $m($rel, "{$r['name']}: sample EN tiada (sample BM ada perkataan)");
            }
            if (empty($en[$r['name']]['desc'])) {
                $m($rel, "{$r['name']}: desc EN tiada");
            }
        }
        semakTeks("$rel (en)", $en, false, $m);
    }
}

foreach ($masalah as $x) {
    fwrite(STDOUT, "  ✗ $x\n");
}
fwrite(STDOUT, $masalah ? count($masalah) . " masalah\n" : "kandungan: LULUS\n");
exit($masalah ? 1 : 0);
