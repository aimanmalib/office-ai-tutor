<?php

declare(strict_types=1);

// Teks subjek Excel (gambaran /excel/, kad laman utama, tutor). Gaya: docs/GAYA-TULISAN.md. EN = en/utama.php (kunci sama).
return [
    'ringkas' => 'Formula, carian data, jumlah bersyarat dan pintasan yang digunakan setiap hari dalam kerja kewangan.',
    'meta' => 'Rujukan pantas, formula penting, rujukan semua fungsi, latihan bersemak automatik dan tutor AI untuk Microsoft Excel, dalam Bahasa Melayu.',
    'alat' => [
        'cheatsheet' => 'Pintasan, rujukan sel, operator dan kesilapan biasa dalam satu halaman.',
        'formulas' => 'Formula yang paling kerap digunakan dalam kerja kewangan, dengan contoh yang boleh disalin.',
        'reference' => 'Fungsi Excel dengan sintaks, contoh dan versi yang memperkenalkannya. Boleh dicari.',
        'practice' => 'Tulis formula atas data sebenar. Jawapan disemak serta-merta dan formula yang setara diterima.',
        'playground' => 'Grid dalam pelayar yang mengira formula. Tulis formula dan lihat hasilnya tanpa membuka Excel.',
        'tutor' => 'Tanya tentang formula, ralat atau tangkapan skrin lembaran kerja anda.',
    ],
    'popular' => ['SUM', 'IF', 'XLOOKUP', 'VLOOKUP', 'SUMIFS', 'COUNTIFS', 'IFERROR', 'INDEX', 'MATCH', 'AVERAGE', 'ROUND', 'TEXT', 'TEXTJOIN', 'DATE', 'EOMONTH', 'NETWORKDAYS', 'PMT', 'UNIQUE'],   // susunan = paparan /excel/
    'chat' => [
        'salam' => 'Apa yang anda mahu tahu tentang Excel?',
        'intro' => 'Tanya tentang formula, fungsi, ralat atau cara menyusun data. Tutor menjawab dalam bahasa soalan anda.',
        'lampiran' => 'Anda boleh melampirkan tangkapan skrin lembaran kerja.',
        'cadangan' => ['Apakah beza VLOOKUP dan XLOOKUP?', 'Bagaimana menggunakan SUMIFS dengan dua kriteria?', 'Bila perlu menggunakan $ dalam formula?', 'Bagaimana mengira peratus pertumbuhan bulanan?'],
        'placeholder' => 'Tanya tentang Excel',
        'meta' => 'Tutor AI untuk Microsoft Excel: formula, fungsi, ralat dan susunan data, dalam Bahasa Melayu atau Inggeris.',
        'nota' => 'AI boleh tersilap. Uji formula dalam Excel sebelum menggunakannya.',
        'nota_gambar' => 'Gambar dikecilkan dalam pelayar sebelum dihantar. AI boleh tersilap. Uji formula dalam Excel.',
    ],
];
