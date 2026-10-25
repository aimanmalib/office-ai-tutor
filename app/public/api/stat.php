<?php

declare(strict_types=1);

// Suar statistik dari pelayar (assets/js/stat.js). Tiada IP/kuki disimpan; IP hanya untuk had kadar (fail sementara).
// Jawapan sentiasa 204 — pelayar tidak menunggu apa-apa, dan penghantar sampah tidak belajar apa-apa.
require_once __DIR__ . '/../../src/Stats.php';
require_once __DIR__ . '/../../src/RateLimit.php';
require_once __DIR__ . '/../../src/ClientIp.php';
require_once __DIR__ . '/../../src/Subjek.php';

http_response_code(204);
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    exit;
}
$in = json_decode((string) file_get_contents('php://input', false, null, 0, 2048), true);
if (!is_array($in)) {
    exit;
}
$dir = __DIR__ . '/../../data/stat-kadar';
if (!is_dir($dir)) {
    @mkdir($dir, 0700, true);
}
if ((new RateLimit($dir . '/' . RateLimit::bucket(ClientIp::get()) . '.json', 3600))->take(300, time()) === null) {
    exit;
}
$stats = new Stats(__DIR__ . '/../../data/statistik.db');
$subjek = (string) ($in['s'] ?? 'excel');
$subjek = Subjek::sah($subjek) ? $subjek : 'excel';
$jenis = (string) ($in['j'] ?? '');
if ($jenis === 'halaman') {
    $p = (string) ($in['p'] ?? '');
    if (preg_match('#^/[a-z0-9/_-]*(\.php)?$#', $p)) {   // laluan laman sahaja (bukan pertanyaan / serpihan)
        $stats->rekod('halaman', $subjek, $p);
    }
} elseif ($jenis === 'cari') {
    $q = mb_strtolower(trim(preg_replace('/\s+/u', ' ', (string) ($in['q'] ?? ''))));
    $n = (int) ($in['n'] ?? -1);
    if (mb_strlen($q) >= 2 && mb_strlen($q) <= 60 && $n >= 0) {
        $stats->rekod('cari', $subjek, $q, (string) min($n, 999));
    }
}
