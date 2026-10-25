<?php
/**
 * Admin → Kandungan: tindakan POST (action kandungan_*). Dimuat oleh public/admin.php SELEPAS semakan log masuk + token
 * borang. Perubahan latihan = Kandungan::ubah (baca-ubah-tulis dalam satu kunci; sejarah = boleh dipulih).
 * Ralat = isian borang dikekalkan + mesej dipapar DI SEBELAH borang itu (src/admin/kandungan.php), bukan di atas halaman.
 */
$subjek = (string) ($_POST['subjek'] ?? 'excel');
$subjek = Subjek::sah($subjek) ? $subjek : 'excel';
$bahasa = ($_POST['bahasa'] ?? 'ms') === 'en' ? 'en' : 'ms';
$aksi = (string) ($_POST['action'] ?? '');
$setId = (string) ($_POST['set'] ?? '');
$qs = 'subjek=' . rawurlencode($subjek) . '&bahasa=' . $bahasa;
$keSet = fn(string $s, string $sauh = '') => "/admin.php?$qs&set=" . rawurlencode($s) . '#' . ($sauh !== '' ? $sauh : 'set-' . rawurlencode($s));
$kembali = "/admin.php?$qs#kandungan";
$ringkas = fn(string $t) => mb_strimwidth(trim(strip_tags(html_entity_decode($t))), 0, 50, '…');
$idSemua = fn(array $l) => array_merge([], ...array_map(fn($s) => array_column($s['questions'], 'id'), array_values($l)));
$indeks = function (array $latihan, string $id) use ($setId): int {
    if (!isset($latihan[$setId])) {
        throw new InvalidArgumentException('Set latihan tidak dijumpai.');
    }
    foreach ($latihan[$setId]['questions'] as $i => $q) {
        if ($q['id'] === $id) {
            return $i;
        }
    }
    throw new InvalidArgumentException('Soalan tidak dijumpai (mungkin sudah dibuang di peranti lain).');
};
$mesej = '';

try {
    switch ($aksi) {
        case 'kandungan_soalan_simpan':
            $id = (string) ($_POST['id'] ?? '');
            Kandungan::ubah($subjek, 'latihan', function (array $l) use ($setId, $id, $idSemua, $indeks, $ringkas) {
                if (!isset($l[$setId])) {
                    throw new InvalidArgumentException('Set latihan tidak dijumpai.');
                }
                $q = Latihan::sahkan($_POST, $idSemua($l), $id !== '' ? $id : null);
                if ($id !== '') {
                    $l[$setId]['questions'][$indeks($l, $id)] = $q;
                    return [$l, 'Ubah soalan: ' . $ringkas($q['prompt'])];
                }
                $l[$setId]['questions'][] = $q;
                return [$l, 'Tambah soalan: ' . $ringkas($q['prompt'])];
            }, $bahasa);
            $mesej = 'Soalan disimpan — terus kelihatan di halaman Latihan.';
            $kembali = $keSet($setId);
            break;

        case 'kandungan_soalan_buang':
            Kandungan::ubah($subjek, 'latihan', function (array $l) use ($setId, $indeks, $ringkas) {
                $i = $indeks($l, (string) ($_POST['id'] ?? ''));
                $label = 'Buang soalan: ' . $ringkas($l[$setId]['questions'][$i]['prompt']);
                array_splice($l[$setId]['questions'], $i, 1);
                return [$l, $label];
            }, $bahasa);
            $mesej = 'Soalan dibuang. Boleh dipulih dari Sejarah.';
            $kembali = $keSet($setId);
            break;

        case 'kandungan_soalan_alih':
            $id = (string) ($_POST['id'] ?? '');
            Kandungan::ubah($subjek, 'latihan', function (array $l) use ($setId, $id, $indeks, $ringkas) {
                $i = $indeks($l, $id);
                $j = $i + (($_POST['arah'] ?? '') === 'naik' ? -1 : 1);
                if ($j < 0 || $j >= count($l[$setId]['questions'])) {
                    return [null, ''];
                }
                $q = &$l[$setId]['questions'];
                [$q[$i], $q[$j]] = [$q[$j], $q[$i]];
                unset($q);
                return [$l, 'Susun soalan: ' . $ringkas($l[$setId]['questions'][$j]['prompt'])];
            }, $bahasa);
            $kembali = $keSet($setId, 's-' . rawurlencode($id));
            break;

        case 'kandungan_json':
            $teks = trim((string) ($_POST['json'] ?? ''));
            $teks = (string) preg_replace('/^```(?:json)?\s*|\s*```$/', '', $teks);   // pagar kod dari ChatGPT/Gemini
            $data = json_decode($teks, true);
            if (!is_array($data)) {
                throw new InvalidArgumentException('JSON tidak sah: ' . json_last_error_msg() . '. Salin semula keseluruhan jawapan AI.');
            }
            foreach (['soalan', 'questions'] as $balut) {
                if (isset($data[$balut]) && is_array($data[$balut])) {
                    $data = $data[$balut];
                    break;
                }
            }
            $data = array_is_list($data) ? $data : [$data];
            $n = 0;
            Kandungan::ubah($subjek, 'latihan', function (array $l) use ($setId, $data, $idSemua, &$n) {
                if (!isset($l[$setId])) {
                    throw new InvalidArgumentException('Set latihan tidak dijumpai.');
                }
                $wujud = $idSemua($l);
                foreach ($data as $k => $row) {
                    try {
                        if (!is_array($row)) {
                            throw new InvalidArgumentException('bukan objek JSON.');
                        }
                        $q = Latihan::sahkan($row, $wujud);
                    } catch (InvalidArgumentException $e) {
                        throw new InvalidArgumentException('Soalan #' . ($k + 1) . ': ' . $e->getMessage() . ' Tiada soalan disimpan.');
                    }
                    $wujud[] = $q['id'];
                    $l[$setId]['questions'][] = $q;
                    $n++;
                }
                if (!$n) {
                    throw new InvalidArgumentException('Tiada soalan dalam JSON.');
                }
                return [$l, "Import JSON: $n soalan ke " . $l[$setId]['name']];
            }, $bahasa);
            $mesej = "$n soalan ditambah.";
            $kembali = $keSet($setId);
            break;

        case 'kandungan_set_simpan':
            $nama = trim(strip_tags(mb_substr((string) ($_POST['nama'] ?? ''), 0, 40)));
            if ($nama === '') {
                throw new InvalidArgumentException('Nama set kosong.');
            }
            $baruSet = $setId === '';
            $id = $baruSet ? trim(substr(strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $nama)), 0, 20), '-') : $setId;
            Kandungan::ubah($subjek, 'latihan', function (array $l) use ($baruSet, $id, $nama) {
                if ($id === '' || ($baruSet && isset($l[$id])) || (!$baruSet && !isset($l[$id]))) {
                    throw new InvalidArgumentException($id === '' ? 'Nama set perlu huruf atau nombor.' : ($baruSet ? 'Set dengan nama itu sudah ada.' : 'Set latihan tidak dijumpai.'));
                }
                $l[$id] = array_merge($l[$id] ?? ['questions' => []], [
                    'name' => $nama,
                    'icon' => Ikon::ada((string) ($_POST['ikon'] ?? '')) ? (string) $_POST['ikon'] : 'list-checks',
                    'tagline' => trim(strip_tags(mb_substr((string) ($_POST['tagline'] ?? ''), 0, 80))),
                    'description' => trim(strip_tags(mb_substr((string) ($_POST['penerangan'] ?? ''), 0, 300))),
                    'color' => in_array($_POST['warna'] ?? '', ['purple', 'amber'], true) ? $_POST['warna'] : 'purple',
                ]);
                return [$l, ($baruSet ? 'Set baharu: ' : 'Ubah set: ') . $nama];
            }, $bahasa);
            $mesej = 'Set "' . $nama . '" disimpan.';
            $kembali = $keSet($id);
            break;

        case 'kandungan_set_buang':
            Kandungan::ubah($subjek, 'latihan', function (array $l) use ($setId) {
                if (!isset($l[$setId])) {
                    throw new InvalidArgumentException('Set latihan tidak dijumpai.');
                }
                if ($l[$setId]['questions']) {
                    throw new InvalidArgumentException('Set masih ada soalan — buang soalannya dahulu.');
                }
                if (count($l) === 1) {
                    throw new InvalidArgumentException('Set terakhir tidak boleh dibuang.');
                }
                $nama = $l[$setId]['name'];
                unset($l[$setId]);
                return [$l, 'Buang set: ' . $nama];
            }, $bahasa);
            $mesej = 'Set dibuang.';
            break;

        case 'kandungan_prompt':
            $p = trim(str_replace("\r\n", "\n", (string) ($_POST['prompt'] ?? '')));
            if (mb_strlen($p) < 50 || mb_strlen($p) > 6000) {
                throw new InvalidArgumentException('Arahan AI mesti 50–6000 aksara.');
            }
            Kandungan::simpan($subjek, 'prompt', $p, 'Ubah arahan AI tutor', $bahasa);
            $mesej = 'Arahan AI tutor disimpan — terpakai pada soalan seterusnya.';
            $kembali = "/admin.php?$qs#arahan-ai";
            break;

        case 'kandungan_pulih':
            $label = Kandungan::pulih($subjek, (string) ($_POST['fail'] ?? ''), $bahasa);
            $mesej = 'Dipulih ke keadaan sebelum: ' . $label . ' (pemulihan ini sendiri boleh diundur dari Sejarah).';
            $kembali = "/admin.php?$qs#sejarah";
            break;

        case 'kandungan_status':
            $status = ($_POST['status'] ?? '') === 'hidup' ? 'hidup' : 'draf';
            Subjek::tetapStatus($subjek, $status);
            $mesej = $status === 'hidup' ? '' . Subjek::semua()[$subjek]['nama'] . ' diterbitkan — kelihatan kepada semua pelajar.'
                : '' . Subjek::semua()[$subjek]['nama'] . ' disorok (draf) — hanya admin nampak.';
            break;

        default:
            throw new InvalidArgumentException('Tindakan tidak dikenali.');
    }
    if ($mesej !== '') {
        $_SESSION['flash'] = ['type' => 'success', 'msg' => $mesej];
    }
} catch (InvalidArgumentException | RuntimeException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => $e instanceof InvalidArgumentException ? $e->getMessage()
        : 'Tidak dapat menyimpan (ralat pelayan). Isian anda masih ada — cuba lagi sebentar.'];
    if (!$e instanceof InvalidArgumentException) {
        error_log('[kandungan] ' . $e->getMessage());
    }
    $_SESSION['borang'] = $_POST;   // isian dikekalkan bila ditolak
    unset($_SESSION['borang']['_token']);
    $id = (string) ($_POST['id'] ?? '');
    $kembali = match ($aksi) {
        'kandungan_soalan_simpan' => "/admin.php?$qs&set=" . rawurlencode($setId) . ($id !== '' ? '&ubah=' . rawurlencode($id) : '&tambah=1') . '#borang-soalan',
        'kandungan_json' => "/admin.php?$qs&set=" . rawurlencode($setId) . '#import-json',
        'kandungan_prompt' => "/admin.php?$qs#arahan-ai",
        'kandungan_set_simpan' => $setId === '' ? "/admin.php?$qs#set-baru" : $keSet($setId),
        default => $kembali,
    };
}
header('Location: ' . $kembali);
exit;
