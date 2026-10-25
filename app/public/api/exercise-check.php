<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function jsonOut(int $code, array $body): never
{
    http_response_code($code);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../../src/Kandungan.php';
require_once __DIR__ . '/../../src/Latihan.php';
require_once __DIR__ . '/../../src/Subjek.php';

try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        jsonOut(405, ['success' => false, 'error' => 'Method not allowed']);
    }

    $raw = file_get_contents('php://input');
    $input = json_decode((string) $raw, true);
    if (!is_array($input)) {
        jsonOut(400, ['success' => false, 'error' => 'Permintaan tak sah']);
    }

    $id = (string) ($input['id'] ?? '');
    $setId = (string) ($input['set'] ?? 'basics');
    $answer = mb_substr((string) ($input['answer'] ?? ''), 0, 500);   // jawapan sebenar < 200 aksara

    $subjek = (string) ($input['subjek'] ?? 'excel');
    if (!Subjek::bolehLihat($subjek)) {
        jsonOut(404, ['success' => false, 'error' => 'Subjek tidak dijumpai']);
    }
    $bahasa = ($input['bahasa'] ?? 'ms') === 'en' ? 'en' : 'ms';
    $exercises = Kandungan::baca($subjek, 'latihan', $bahasa);
    if (!isset($exercises[$setId]['questions'])) {
        jsonOut(400, ['success' => false, 'error' => 'Set latihan tak dijumpai']);
    }
    $q = null;
    foreach ($exercises[$setId]['questions'] as $row) {
        if (($row['id'] ?? '') === $id) { $q = $row; break; }
    }
    if (!$q) {
        jsonOut(400, ['success' => false, 'error' => 'Soalan tak dijumpai']);
    }

    if ($q['mode'] !== 'choice' && trim($answer) === '') {
        jsonOut(400, ['success' => false, 'error' => 'Jawapan kosong']);
    }
    $correct = Latihan::betul($q, $answer, isset($input['choice_index']) ? (int) $input['choice_index'] : -1);
    // Hasil sama dengan fungsi yang sama (dikira pelayar, assets/js/formula-enjin.js) — hanya untuk soalan yang membenarkannya.
    // Ini latihan: pelayar dipercayai; yang dilindungi ialah soalan yang menguji cara ($ rujukan mutlak).
    $setara = false;
    if (!$correct && !empty($input['setara']) && Latihan::bolehSetara($q) && preg_match('/^=.*[A-Z]{1,3}\$?\d/i', trim($answer))) {
        $correct = $setara = true;
    }

    require_once __DIR__ . '/../../src/Stats.php';
    $stats = new Stats(__DIR__ . '/../../data/statistik.db');
    // setara = cubaan KEDUA bagi jawapan yang sama (pelayar menyemak semula): betulkan rekod "salah" tadi, jangan tambah satu lagi
    $setara ? $stats->betulkanTerakhir('latihan', $subjek, $setId . '/' . $id)
            : $stats->rekod('latihan', $subjek, $setId . '/' . $id, $correct ? '1' : '0');

    jsonOut(200, [
        'success' => true,
        'correct' => $correct,
        'canonical' => $q['canonical'] ?? '',
        'explanation' => Latihan::html((string) ($q['explanation'] ?? '')),
    ]);
} catch (Throwable $e) {
    error_log('[exercise-check] ' . $e->getMessage());
    jsonOut(500, ['success' => false, 'error' => 'Server error']);
}
