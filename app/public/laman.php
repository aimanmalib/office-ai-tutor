<?php

declare(strict_types=1);

/**
 * Pengawal hadapan laman awam (revamp v2, docs/REVAMP.md). Apache menghantar setiap laluan yang bukan fail sebenar ke sini
 * (php/apache-excel.conf); dev/router.php meniru tingkah laku yang sama. api/*.php dan admin.php kekal fail sendiri.
 */
require_once __DIR__ . '/../src/Bahasa.php';
require_once __DIR__ . '/../src/Laluan.php';
require_once __DIR__ . '/../src/Subjek.php';
require_once __DIR__ . '/../src/Kandungan.php';
require_once __DIR__ . '/../src/Ikon.php';
require_once __DIR__ . '/../src/Papar.php';

$laluan = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$laluan = preg_replace('~/{2,}~', '/', $laluan) ?: '/';
$h = Laluan::hurai($laluan);

if (isset($h['alih'])) {
    $qs = (string) ($_SERVER['QUERY_STRING'] ?? '');
    header('Location: ' . $h['alih'] . ($qs !== '' ? "?$qs" : ''), true, 301);
    exit;
}
if ($laluan === '/sitemap.xml') {
    require __DIR__ . '/../src/papar/sitemap.php';
    exit;
}

$bahasa = $h['bahasa'] ?? (str_starts_with($laluan, '/en/') ? 'en' : 'ms');
Bahasa::tetap($bahasa);

if ($h === null || ($h['subjek'] !== null && !Subjek::bolehLihat($h['subjek']))) {
    http_response_code(404);
    Papar::halaman('404', ['subjekId' => null, 'halaman' => '404']);
    exit;
}

$subjekId = $h['subjek'];
$halaman = $h['halaman'];
Papar::halaman($subjekId === null ? 'utama' : Laluan::TEMPLAT[$halaman], ['subjekId' => $subjekId, 'halaman' => $halaman]);
