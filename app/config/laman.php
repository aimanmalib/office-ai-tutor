<?php

declare(strict_types=1);

/**
 * Site identity — copy and edit for your own deployment (no names or numbers are hard-coded in templates).
 * Used by src/papar/_bingkai.php (brand, canonical URL, footer), utama.php (instructor card) and the sitemap.
 */
return [
    'url' => 'https://example.com',
    'jenama' => 'Office Tutor',
    'pemilik' => 'Your name',
    'warna_tema' => ['cerah' => '#fafaf9', 'gelap' => '#0a0a0c'],   // browser UI colour: light / dark theme
    'sumber' => '',   // link to your source code (required by AGPL if you modify and host it); empty = hidden
    'pengajar' => [
        'nama' => 'Instructor name',
        'peranan' => 'Instructor',
        'foto' => '/assets/img/pengajar.svg',
        'whatsapp' => '',   // international number without "+" (e.g. 60123456789); empty = WhatsApp button hidden
        'bio' => ['ms' => '', 'en' => ''],
    ],
];
