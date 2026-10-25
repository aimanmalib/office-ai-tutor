<?php

declare(strict_types=1);

/**
 * Pendaftaran subjek (docs/PELAN.md). Satu baris = satu subjek; kandungannya dalam config/pek/<id>/.
 * status lalai: 'hidup' (awam) / 'draf' (tersorok — 404 kepada awam, pratonton untuk admin yang log masuk, tiada dalam
 * nav/sitemap). Admin menerbitkan/menyorok tanpa kod: pilihan disimpan dalam data/kandungan/status.json dan menang.
 * URL: [/en]/<id>/<halaman> (src/Laluan.php; penghala public/laman.php).
 * Subjek baharu = satu baris di sini + folder config/pek/<id>/ (utama, cheatsheet, rujukan, latihan, prompt) — tiada kod.
 */
// 'ikon' = nama Lucide (src/Ikon.php); 'halaman' = halaman subjek mengikut susunan nav (src/Laluan.php::TEMPLAT).
return [
    'excel' => ['nama' => 'Excel', 'ikon' => 'file-spreadsheet', 'status' => 'hidup',
                'halaman' => ['cheatsheet', 'formulas', 'reference', 'practice', 'playground', 'tutor']],
    'word' => ['nama' => 'Word', 'ikon' => 'file-text', 'status' => 'hidup',
               'halaman' => ['cheatsheet', 'reference', 'practice', 'tutor']],
    'powerpoint' => ['nama' => 'PowerPoint', 'ikon' => 'presentation', 'status' => 'hidup',
                     'halaman' => ['cheatsheet', 'reference', 'practice', 'tutor']],
];
