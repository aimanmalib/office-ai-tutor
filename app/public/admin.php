<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Env.php';
require_once __DIR__ . '/../src/Groq.php';
require_once __DIR__ . '/../src/GroqPool.php';
require_once __DIR__ . '/../src/ChatStatus.php';
require_once __DIR__ . '/../src/RateLimit.php';
require_once __DIR__ . '/../src/AdminAuth.php';
require_once __DIR__ . '/../src/ClientIp.php';
require_once __DIR__ . '/../src/Stats.php';
require_once __DIR__ . '/../src/Kandungan.php';
require_once __DIR__ . '/../src/Latihan.php';
require_once __DIR__ . '/../src/Subjek.php';
require_once __DIR__ . '/../src/Ikon.php';

Env::load(__DIR__ . '/../config/.env');

header('Cache-Control: no-store');
AdminAuth::startSession(__DIR__ . '/../data/sessions');

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Logout (pautan membawa token sesi — laman lain tidak boleh melog keluar pentadbir)
if (($_GET['action'] ?? '') === 'logout') {
    if (AdminAuth::csrfValid((string) ($_GET['t'] ?? ''))) {
        $_SESSION = [];
        session_destroy();
    }
    header('Location: /admin.php');
    exit;
}

// Login POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['password']) && empty($_SESSION['admin'])) {
    $loginError = AdminAuth::login((string) $_POST['password'], __DIR__ . '/../data/login-attempts');
    if ($loginError === null) {
        header('Location: /admin.php');
        exit;
    }
}

$loggedIn = !empty($_SESSION['admin']);

// Mutations (only when logged in, with the session's form token)
if ($loggedIn && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!AdminAuth::csrfValid((string) ($_POST['_token'] ?? ''))) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Borang tamat tempoh. Muat semula halaman dan cuba lagi.'];
        header('Location: /admin.php');
        exit;
    }
    if (($_POST['action'] ?? '') === 'tukar_kata_laluan') {
        $ralat = AdminAuth::tukarKataLaluan((string) ($_POST['semasa'] ?? ''), (string) ($_POST['baru'] ?? ''),
            (string) ($_POST['ulang'] ?? ''), __DIR__ . '/../config/.env');
        $_SESSION['flash'] = $ralat === null
            ? ['type' => 'success', 'msg' => 'Kata laluan ditukar. Gunakan yang baharu pada log masuk seterusnya.']
            : ['type' => 'error', 'msg' => $ralat];
        header('Location: /admin.php' . ($ralat === null ? '' : '#akaun'));
        exit;
    }
    if (str_starts_with((string) ($_POST['action'] ?? ''), 'kandungan_')) {
        require __DIR__ . '/../src/admin/kandungan-aksi.php';   // keluar sendiri (redirect)
    }
    try {
        $pool = new GroqPool(
            keysFile: __DIR__ . '/../config/groq-keys.json',
            usageFile: __DIR__ . '/../data/groq-usage.json',
            model: (string) Env::get('GROQ_MODEL', 'openai/gpt-oss-120b'),
            baseUrl: (string) Env::get('GROQ_BASE_URL', Groq::BASE_URL),
        );
        if (($_POST['action'] ?? '') === 'add_key') {
            $pool->addKey((string) ($_POST['new_key'] ?? ''), (string) ($_POST['new_label'] ?? ''));
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Key ditambah.'];
            header('Location: /admin.php');
            exit;
        }
        if (($_POST['action'] ?? '') === 'remove_key') {
            $pool->removeKey((string) ($_POST['key_id'] ?? ''));
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Key dipadam.'];
            header('Location: /admin.php');
            exit;
        }
        if (($_POST['action'] ?? '') === 'toggle_chat') {
            $cs = new ChatStatus(__DIR__ . '/../data/chat-status.json');
            $current = $cs->read();
            $cs->setEnabled(!$current['enabled'], (string) ($_POST['message'] ?? $current['message']));
            $_SESSION['flash'] = [
                'type' => 'success',
                'msg' => !$current['enabled'] ? 'Chat dihidupkan.' : 'Chat dimatikan.',
            ];
            header('Location: /admin.php');
            exit;
        }
        if (($_POST['action'] ?? '') === 'update_chat_message') {
            $cs = new ChatStatus(__DIR__ . '/../data/chat-status.json');
            $current = $cs->read();
            $cs->setEnabled($current['enabled'], (string) ($_POST['message'] ?? ''));
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Mesej dikemaskini.'];
            header('Location: /admin.php');
            exit;
        }
        if (($_POST['action'] ?? '') === 'update_models') {
            $catalog = require __DIR__ . '/../config/models.php';
            $validIds = array_column($catalog, 'id');
            $textId = (string) ($_POST['text_model'] ?? '');
            $visionId = (string) ($_POST['vision_model'] ?? '');
            if (!in_array($textId, $validIds, true)) {
                throw new InvalidArgumentException('Text model tak sah.');
            }
            $live = $pool->availableModels(__DIR__ . '/../data/groq-models.json');
            $isLive = fn(string $id): bool => $live === null || isset($live[$id]);
            if (!$isLive($textId)) {
                throw new InvalidArgumentException('Text model ini sudah tiada di Groq. Pilih model lain.');
            }
            // vision_model can be empty (when image disabled)
            $imageEnabled = !empty($_POST['image_upload_enabled']);
            if ($imageEnabled) {
                if (!in_array($visionId, $validIds, true)) {
                    throw new InvalidArgumentException('Vision model tak sah.');
                }
                // also verify the chosen vision model actually supports vision
                $vrow = null;
                foreach ($catalog as $row) if ($row['id'] === $visionId) { $vrow = $row; break; }
                if (!$vrow || empty($vrow['vision'])) {
                    throw new InvalidArgumentException('Model yang dipilih untuk vision tak support image.');
                }
                if (!$isLive($visionId)) {
                    throw new InvalidArgumentException('Vision model ini sudah tiada di Groq. Pilih model lain.');
                }
            }
            $cs = new ChatStatus(__DIR__ . '/../data/chat-status.json');
            $cs->setModels($textId, $imageEnabled ? $visionId : '', $imageEnabled);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Model AI dikemas kini.'];
            header('Location: /admin.php');
            exit;
        }
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => $e->getMessage()];
        header('Location: /admin.php');
        exit;
    }
}

function maskKey(string $k): string
{
    if (strlen($k) < 12) return str_repeat('•', strlen($k));
    return substr($k, 0, 7) . str_repeat('•', 6) . substr($k, -4);
}

function fmtTokens(int $n): string
{
    if ($n >= 1_000_000) return number_format($n / 1_000_000, 1) . 'M';
    if ($n >= 1_000) return number_format($n / 1_000, 1) . 'k';
    return (string) $n;
}

?><!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin · Sasa Teguh Academy</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= @filemtime(__DIR__ . '/assets/css/style.css') ?: '1' ?>">
    <link rel="stylesheet" href="/assets/css/tw-admin.css?v=<?= @filemtime(__DIR__ . '/assets/css/tw-admin.css') ?: '1' ?>">
    <script src="/assets/js/modal.js?v=<?= @filemtime(__DIR__ . '/assets/js/modal.js') ?: '1' ?>" defer></script>
</head>
<body class="bg-bg min-h-screen font-sans text-ink antialiased">

<?php if (!$loggedIn): ?>
    <!-- LOGIN -->
    <main class="min-h-screen flex items-center justify-center px-5">
        <form method="POST" class="w-full max-w-sm bg-white rounded-3xl p-8 shadow-lg border border-purple-100">
            <div class="text-center mb-6">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-brand-purple to-brand-pink flex items-center justify-center text-white text-2xl font-extrabold shadow-lg"><?= Ikon::svg('lock', 'w-6 h-6') ?></div>
                <h1 class="text-xl font-extrabold mt-3">Log masuk admin</h1>
                <p class="text-xs text-gray-500 mt-1">Sasa Teguh Academy</p>
            </div>
            <label for="login-password" class="block text-xs font-semibold text-gray-600 mb-1">Kata laluan</label>
            <input type="password" id="login-password" name="password" autocomplete="current-password" required autofocus
                   class="w-full px-4 py-3 rounded-2xl bg-gray-50 focus:bg-white border-2 border-transparent focus:border-brand-purple outline-none text-sm transition">
            <?php if (!empty($loginError)): ?>
                <p class="mt-3 text-xs text-rose-600 bg-rose-50 px-3 py-2 rounded-xl"><?= htmlspecialchars($loginError) ?></p>
            <?php endif; ?>
            <button class="mt-4 w-full px-6 py-3 bg-gradient-to-r from-brand-purple to-brand-pink text-white font-bold rounded-2xl hover:scale-[1.02] transition shadow-md">Masuk</button>
            <a href="/" class="block text-center text-xs text-gray-500 hover:text-brand-purple mt-4">← Kembali ke laman</a>
        </form>
    </main>

<?php else:
    // Dashboard
    try {
        $pool = new GroqPool(
            keysFile: __DIR__ . '/../config/groq-keys.json',
            usageFile: __DIR__ . '/../data/groq-usage.json',
            model: (string) Env::get('GROQ_MODEL', 'openai/gpt-oss-120b'),
            baseUrl: (string) Env::get('GROQ_BASE_URL', Groq::BASE_URL),
        );
        $liveModels = $pool->availableModels(__DIR__ . '/../data/groq-models.json');
        $keys = $pool->listKeys();
        if (!$keys) {
            $poolError = 'Tiada API key dikonfigurasi. Tambah key di bawah — chat student gagal selagi tiada key.';
        }
        $today = $pool->todayUsage();
        $recent = $pool->recentUsage(7);
    } catch (Throwable $e) {
        $keys = [];
        $liveModels = null;
        $today = [];
        $recent = [];
        $poolError = $e->getMessage();
    }

    $totalReqToday = 0; $totalTokToday = 0;
    foreach ($today as $u) {
        $totalReqToday += (int) ($u['requests'] ?? 0);
        $totalTokToday += (int) ($u['tokens'] ?? 0);
    }
    $tokenCounts = array_column($recent, 'tokens');
    $maxDailyTok = $tokenCounts ? max(1, max($tokenCounts)) : 1;

    $chatStatusObj = new ChatStatus(__DIR__ . '/../data/chat-status.json');
    $chatStatus = $chatStatusObj->read();
    $modelCatalog = require __DIR__ . '/../config/models.php';
    $textModels = array_values(array_filter($modelCatalog, fn($m) => !$m['vision']));
    $visionModels = array_values(array_filter($modelCatalog, fn($m) => $m['vision']));
    $defaultTextModel = (string) Env::get('GROQ_MODEL', 'openai/gpt-oss-120b');
    $defaultVisionModel = (string) Env::get('GROQ_VISION_MODEL', 'qwen/qwen3.8-27b');
    $selectedText = $chatStatus['text_model'] !== '' ? $chatStatus['text_model'] : $defaultTextModel;
    $selectedVision = $chatStatus['vision_model'] !== '' ? $chatStatus['vision_model'] : $defaultVisionModel;
    // Model yang sedang dipakai tetapi sudah ditutup Groq = setiap soalan pelajar gagal. Beritahu admin terus.
    $deadModels = [];
    if ($liveModels !== null) {
        foreach (array_unique(array_filter([$selectedText, $chatStatus['image_upload_enabled'] ? $selectedVision : ''])) as $id) {
            if (!isset($liveModels[$id])) {
                $deadModels[] = $id;
            }
        }
    }
    $modelGone = fn(string $id): string => ($liveModels !== null && !isset($liveModels[$id])) ? ' · tiada di Groq' : '';

    function tierBadge(int $tier): string {
        $coins = str_repeat('$', max(1, $tier));
        $labels = [1 => 'Murah', 2 => 'Sederhana', 3 => 'Premium'];
        $label = $labels[$tier] ?? '';
        return "$coins $label";
    }
?>
    <nav class="bg-bg sticky top-0 z-40 border-b border-purple-100">
        <div class="max-w-6xl mx-auto px-5 sm:px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2 text-lg font-extrabold">
                <?php require __DIR__ . '/../src/papar/_logo.php'; ?>
                <span class="bg-gradient-to-r from-brand-purple to-brand-pink bg-clip-text text-transparent">Admin</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="/" class="px-3 py-2 rounded-xl text-xs sm:text-sm text-gray-600 hover:bg-purple-50">← Laman</a>
                <a href="/admin.php?action=logout&amp;t=<?= htmlspecialchars(AdminAuth::csrfToken()) ?>" class="px-3 py-2 rounded-xl text-xs sm:text-sm text-rose-600 hover:bg-rose-50 font-semibold">Log keluar</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-5 sm:px-6 py-8 space-y-6">

        <?php if ($flash): $ralatFlash = $flash['type'] === 'error'; ?>
            <div id="toast" role="<?= $ralatFlash ? 'alert' : 'status' ?>" data-auto="<?= $ralatFlash ? '0' : '1' ?>"
                 class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[calc(100%-2rem)] max-w-md flex items-start gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm <?= $ralatFlash ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' ?>">
                <span class="flex-1"><?= htmlspecialchars($flash['msg']) ?></span>
                <button type="button" aria-label="Tutup" onclick="this.parentElement.remove()" class="-m-2 w-10 h-10 flex-shrink-0 rounded-full hover:bg-black/5 grid place-items-center"><?= Ikon::svg('x', 'w-4 h-4') ?></button>
            </div>
            <script>setTimeout(() => { const t = document.getElementById('toast'); if (t && t.dataset.auto === '1') t.remove(); }, 6000);</script>
        <?php endif; ?>

        <nav aria-label="Bahagian admin" class="flex flex-wrap gap-2 text-xs sm:text-sm">
            <?php foreach ([['#kandungan', 'Kandungan'], ['#statistik', 'Statistik'], ['#model', 'Model AI'], ['#kunci', 'Kunci API'], ['#akaun', 'Kata laluan']] as [$u, $l]): ?>
            <a href="<?= $u ?>" class="px-3 py-2 rounded-xl bg-white border border-purple-100 text-gray-700 hover:text-brand-purple hover:bg-purple-50 font-semibold"><?= $l ?></a>
            <?php endforeach; ?>
        </nav>

        <?php if ($deadModels): ?>
            <div class="px-4 py-3 rounded-2xl bg-amber-50 text-amber-900 border border-amber-200 text-sm">
                Model <?= htmlspecialchars(implode(', ', $deadModels)) ?> sudah tiada di Groq — chat student akan gagal. Pilih model lain di bahagian Model AI dan tekan Simpan.
            </div>
        <?php endif; ?>

        <?php if (!empty($poolError)): ?>
            <div class="px-4 py-3 rounded-2xl bg-amber-50 text-amber-900 border border-amber-200 text-sm">
                <?= htmlspecialchars($poolError) ?>
            </div>
        <?php endif; ?>

        <!-- Chat status toggle -->
        <section class="bg-white rounded-3xl p-6 border border-purple-100 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-md <?= $chatStatus['enabled'] ? 'bg-ok-soft text-ok' : 'bg-surface-3 text-muted' ?>">
                        <?= Ikon::svg($chatStatus['enabled'] ? 'circle-check' : 'clock', 'w-6 h-6') ?>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold">Status tutor AI</h2>
                        <p class="text-sm <?= $chatStatus['enabled'] ? 'text-emerald-700' : 'text-gray-500' ?>">
                            <?= $chatStatus['enabled'] ? 'Aktif: pelajar boleh bertanya' : 'Tidak aktif: pelajar melihat mesej rehat' ?>
                        </p>
                    </div>
                </div>
                <form method="POST" data-confirm="<?= $chatStatus['enabled'] ? 'Matikan chat? Student tak boleh hantar message sampai diaktifkan semula.' : 'Hidupkan chat? Student boleh terus guna AI tutor.' ?>"
                      data-confirm-title="<?= $chatStatus['enabled'] ? 'Matikan Chat AI?' : 'Hidupkan Chat AI?' ?>"
                      data-confirm-ok="<?= $chatStatus['enabled'] ? 'Ya, matikan' : 'Ya, hidupkan' ?>"
                      data-confirm-danger="<?= $chatStatus['enabled'] ? '1' : '0' ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(AdminAuth::csrfToken()) ?>">
                <input type="hidden" name="action" value="toggle_chat">
                    <input type="hidden" name="message" value="<?= htmlspecialchars($chatStatus['message']) ?>">
                    <button class="px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition <?= $chatStatus['enabled'] ? 'bg-err text-white hover:scale-[1.02]' : 'bg-ok text-white hover:scale-[1.02]' ?>">
                        <?= $chatStatus['enabled'] ? 'Matikan chat' : 'Hidupkan chat' ?>
                    </button>
                </form>
            </div>

            <details class="mt-4 group">
                <summary class="cursor-pointer text-xs text-gray-500 hover:text-brand-purple font-semibold inline-flex items-center gap-1">
                    <svg class="w-3 h-3 group-open:rotate-90 transition" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    Ubah mesej semasa tutor berehat
                </summary>
                <form method="POST" class="mt-3 flex flex-col sm:flex-row gap-2">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(AdminAuth::csrfToken()) ?>">
                <input type="hidden" name="action" value="update_chat_message">
                    <input type="text" name="message" aria-label="Mesej bila chat dimatikan" value="<?= htmlspecialchars($chatStatus['message']) ?>" maxlength="200"
                           class="flex-1 px-4 py-2.5 rounded-xl bg-gray-50 focus:bg-white border-2 border-transparent focus:border-brand-purple outline-none text-sm">
                    <button class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-purple-50 text-gray-700 hover:text-brand-purple font-semibold text-sm transition">Simpan mesej</button>
                </form>
                <p class="text-[10px] text-gray-500 mt-2">Pelajar melihat mesej ini apabila tutor dimatikan.</p>
            </details>
        </section>

        <!-- Model AI selector -->
        <section id="model" class="bg-white rounded-3xl p-6 border border-purple-100 shadow-sm scroll-mt-24">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-purple to-brand-pink flex items-center justify-center text-white text-xl shadow-md"><?= Ikon::svg('settings', 'w-5 h-5') ?></div>
                <div>
                    <h2 class="text-lg font-extrabold">Model AI</h2>
                    <p class="text-xs text-gray-500">Tetapkan sekali. Sistem memilih model mengikut jenis soalan pelajar.</p>
                </div>
            </div>

            <!-- How it works diagram -->
            <div class="mb-5 p-4 rounded-2xl bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-100">
                <p class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-3">Cara ia berfungsi</p>
                <div class="grid sm:grid-cols-2 gap-3 text-xs">
                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-purple-100">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-lg flex-shrink-0"><?= Ikon::svg('message-square', 'w-4 h-4') ?></div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-800">Soalan teks sahaja</p>
                            <p class="text-gray-500">→ <strong class="text-ink">model teks</strong></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-purple-100">
                        <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center text-lg flex-shrink-0"><?= Ikon::svg('paperclip', 'w-4 h-4') ?></div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-800">Soalan dengan gambar</p>
                            <p class="text-gray-500">→ <strong class="text-ink">model gambar</strong></p>
                        </div>
                    </div>
                </div>
                <p class="text-[11px] text-blue-800 mt-3 leading-relaxed">
                    <strong>Kenapa dua model?</strong> Model teks terbaik (cth GPT-OSS 120B) tidak menerima gambar, dan model yang menerima gambar lebih kecil. Dengan kedua-duanya, setiap jenis soalan dijawab oleh model yang paling sesuai.
                </p>
            </div>

            <form method="POST" class="space-y-5" id="model-form">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(AdminAuth::csrfToken()) ?>">
                <input type="hidden" name="action" value="update_models">

                <!-- Image upload toggle -->
                <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-100">
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Benarkan muat naik gambar</p>
                        <p class="text-xs text-gray-600 mt-0.5">Jika dimatikan, butang lampiran hilang daripada halaman pelajar dan model gambar tidak digunakan.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="image_upload_enabled" id="image-upload-toggle" aria-label="Benarkan muat naik gambar" value="1" <?= $chatStatus['image_upload_enabled'] ? 'checked' : '' ?> class="sr-only peer">
                        <div class="w-12 h-7 bg-gray-300 peer-checked:bg-ok rounded-full peer-focus:ring-2 peer-focus:ring-brand-purple/30 transition relative">
                            <div class="absolute top-0.5 left-0.5 bg-white w-6 h-6 rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                </div>

                <!-- Text Model -->
                <div>
                    <div class="flex flex-wrap items-baseline justify-between mb-2">
                        <label for="text-select" class="text-xs font-bold text-gray-700 uppercase tracking-wider">Model teks <span class="text-rose-500">*</span></label>
                        <span class="text-[10px] text-gray-500">soalan biasa (paling kerap)</span>
                    </div>
                    <select name="text_model" id="text-select" class="w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white border-2 border-gray-100 focus:border-brand-purple outline-none text-sm font-mono transition cursor-pointer">
                        <?php foreach ($textModels as $m): ?>
                            <option value="<?= htmlspecialchars($m['id']) ?>" <?= $selectedText === $m['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['name']) ?> · Teks · <?= tierBadge($m['tier']) ?><?= $modelGone($m['id']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-2" id="text-model-note"><?php
                        foreach ($textModels as $m) if ($m['id'] === $selectedText) { echo htmlspecialchars($m['note']); break; }
                    ?></p>
                </div>

                <!-- Vision Model (hidden when image off) -->
                <div id="vision-block" class="<?= $chatStatus['image_upload_enabled'] ? '' : 'hidden' ?>">
                    <div class="flex flex-wrap items-baseline justify-between mb-2">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-wider">Model gambar</label>
                        <span class="text-[10px] text-gray-500">apabila pelajar melampirkan gambar</span>
                    </div>
                    <select name="vision_model" id="vision-select" class="w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white border-2 border-gray-100 focus:border-brand-purple outline-none text-sm font-mono transition cursor-pointer">
                        <?php foreach ($visionModels as $m): ?>
                            <option value="<?= htmlspecialchars($m['id']) ?>" <?= $selectedVision === $m['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['name']) ?> · Gambar · <?= tierBadge($m['tier']) ?><?= $modelGone($m['id']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-2" id="vision-model-note"><?php
                        foreach ($visionModels as $m) if ($m['id'] === $selectedVision) { echo htmlspecialchars($m['note']); break; }
                    ?></p>
                </div>

                <!-- Legend -->
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <p class="text-[11px] text-gray-500 leading-relaxed">
                        <strong class="text-gray-700">Tahap kos:</strong>
                        $ Murah (kecil, pantas) ·
                        $$ Sederhana ·
                        $$$ Premium (besar, perlahan, kualiti tertinggi)
                    </p>
                </div>

                <div class="flex justify-end">
                    <button class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-purple to-brand-pink text-white font-bold text-sm shadow-sm hover:scale-[1.02] transition">
                        Simpan model
                    </button>
                </div>
            </form>

            <script>
                (function () {
                    const toggle = document.getElementById('image-upload-toggle');
                    const visionBlock = document.getElementById('vision-block');
                    const visionSelect = document.getElementById('vision-select');
                    toggle?.addEventListener('change', () => {
                        if (toggle.checked) {
                            visionBlock.classList.remove('hidden');
                        } else {
                            visionBlock.classList.add('hidden');
                        }
                    });

                    const notes = <?= json_encode(array_combine(array_column($modelCatalog, 'id'), array_column($modelCatalog, 'note'))) ?>;
                    document.querySelector('select[name="text_model"]').addEventListener('change', (e) => {
                        document.getElementById('text-model-note').textContent = notes[e.target.value] || '';
                    });
                    visionSelect?.addEventListener('change', (e) => {
                        document.getElementById('vision-model-note').textContent = notes[e.target.value] || '';
                    });
                })();
            </script>
        </section>

        <!-- Today summary -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Kunci aktif</p>
                <p class="text-3xl font-extrabold text-brand-purple"><?= count($keys) ?></p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Soalan hari ini</p>
                <p class="text-3xl font-extrabold text-brand-pink"><?= number_format($totalReqToday) ?></p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Token hari ini</p>
                <p class="text-3xl font-extrabold text-brand-amber"><?= fmtTokens($totalTokToday) ?></p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Hari</p>
                <p class="text-3xl font-extrabold text-brand-emerald"><?= date('j') . ' ' . ['Jan','Feb','Mac','Apr','Mei','Jun','Jul','Ogo','Sep','Okt','Nov','Dis'][(int) date('n') - 1] ?></p>
                <p class="text-[10px] text-gray-500 mt-0.5"><?= ['Ahad','Isnin','Selasa','Rabu','Khamis','Jumaat','Sabtu'][(int) date('w')] ?></p>
            </div>
        </section>

        <!-- API Keys -->
        <section id="kunci" class="bg-white rounded-3xl p-6 border border-purple-100 shadow-sm scroll-mt-24">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-extrabold">Kunci API (<?= count($keys) ?>)</h2>
            </div>

            <?php if (empty($keys)): ?>
                <p class="text-sm text-gray-500 py-6 text-center">Belum ada key. Tambah di bawah.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 font-bold border-b border-gray-100">
                                <th class="text-left py-2 px-2">Label</th>
                                <th class="text-left py-2 px-2">Kunci</th>
                                <th class="text-right py-2 px-2">Soalan hari ini</th>
                                <th class="text-right py-2 px-2">Token hari ini</th>
                                <th class="text-right py-2 px-2">Baki</th>
                                <th class="py-2 px-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($keys as $k):
                                $u = $today[$k['id']] ?? null;
                                $req = (int) ($u['requests'] ?? 0);
                                $tok = (int) ($u['tokens'] ?? 0);
                                $remReq = $u['last_remaining_req'] ?? null;
                                $remTok = $u['last_remaining_tok'] ?? null;
                                $status = 'idle';
                                if ($remTok !== null) {
                                    if ($remTok < 1000) $status = 'low';
                                    elseif ($remTok < 4000) $status = 'mid';
                                    else $status = 'ok';
                                }
                            ?>
                            <tr>
                                <td class="py-3 px-2 font-semibold"><?= htmlspecialchars($k['label']) ?></td>
                                <td class="py-3 px-2 font-mono text-xs text-gray-600"><?= htmlspecialchars(maskKey($k['key'])) ?></td>
                                <td class="py-3 px-2 text-right font-mono"><?= number_format($req) ?></td>
                                <td class="py-3 px-2 text-right font-mono"><?= fmtTokens($tok) ?></td>
                                <td class="py-3 px-2 text-right font-mono">
                                    <?php if ($remTok !== null): ?>
                                        <?= fmtTokens((int) $remTok) ?> tok
                                        <span class="ml-1 inline-block w-2 h-2 rounded-full align-middle <?= $status==='low' ? 'bg-rose-500' : ($status==='mid' ? 'bg-amber-400' : 'bg-emerald-500') ?>"></span>
                                    <?php else: ?>
                                        <span class="text-gray-500 text-xs">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-2 text-right">
                                    <form method="POST" class="inline"
                                          data-confirm="Padam key <?= htmlspecialchars($k['label']) ?> dari pool?"
                                          data-confirm-title="Padam API key?"
                                          data-confirm-ok="Ya, padam"
                                          data-confirm-danger="1">
                                        <input type="hidden" name="_token" value="<?= htmlspecialchars(AdminAuth::csrfToken()) ?>">
                <input type="hidden" name="action" value="remove_key">
                                        <input type="hidden" name="key_id" value="<?= htmlspecialchars($k['id']) ?>">
                                        <button class="px-3 min-h-[2.75rem] rounded-lg text-xs text-rose-600 hover:bg-rose-50 font-semibold">Padam</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <form method="POST" class="mt-6 grid sm:grid-cols-[1fr_auto_auto] gap-2 items-stretch">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(AdminAuth::csrfToken()) ?>">
                <input type="hidden" name="action" value="add_key">
                <input type="text" name="new_key" placeholder="gsk_..." aria-label="API key Groq baharu" required pattern="gsk_.{16,}"
                       class="px-4 py-2.5 rounded-xl bg-gray-50 focus:bg-white border-2 border-transparent focus:border-brand-purple outline-none text-sm font-mono">
                <input type="text" name="new_label" aria-label="Label key" placeholder="Label (cth: backup1)" maxlength="40"
                       class="px-4 py-2.5 rounded-xl bg-gray-50 focus:bg-white border-2 border-transparent focus:border-brand-purple outline-none text-sm sm:w-48">
                <button class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-purple to-brand-pink text-white font-semibold text-sm hover:scale-[1.02] transition shadow-sm">Tambah kunci</button>
            </form>
            <p class="text-xs text-gray-500 mt-2">Kunci API percuma boleh didapati di console.groq.com. Lebih banyak kunci bermakna lebih banyak kuota untuk kelas.</p>
        </section>

        <!-- Recent days -->
        <section class="bg-white rounded-3xl p-6 border border-purple-100 shadow-sm">
            <h2 class="text-lg font-extrabold mb-5">7 hari lepas</h2>
            <div class="space-y-2">
                <?php foreach ($recent as $date => $stats):
                    $pct = $maxDailyTok > 0 ? ($stats['tokens'] / $maxDailyTok) * 100 : 0;
                    $isToday = $date === date('Y-m-d');
                ?>
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-20 sm:w-28 text-xs font-mono text-gray-500 flex-shrink-0"><?= htmlspecialchars($date) ?><?= $isToday ? ' •' : '' ?></div>
                    <div class="flex-1 bg-gray-100 rounded-full h-6 relative overflow-hidden">
                        <div class="h-full bg-ink/15 rounded-full transition-all" style="width: <?= max(2, $pct) ?>%"></div>
                        <div class="absolute inset-0 flex items-center px-3 text-xs font-mono text-gray-700">
                            <?= number_format($stats['requests']) ?> soalan · <?= fmtTokens($stats['tokens']) ?> token
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <?php $statsObj = new Stats(__DIR__ . '/../data/statistik.db'); require __DIR__ . '/../src/admin/statistik.php'; ?>

        <?php require __DIR__ . '/../src/admin/kandungan.php'; ?>

        <section id="akaun" class="bg-white rounded-3xl p-6 border border-purple-100 shadow-sm scroll-mt-24">
            <h2 class="text-lg font-extrabold mb-1">Tukar kata laluan admin</h2>
            <p class="text-xs text-gray-500 mb-4">Sekurang-kurangnya 12 aksara. Frasa beberapa perkataan (cth tiga perkataan rawak) lebih kuat dan lebih mudah diingat.</p>
            <form method="POST" class="grid sm:grid-cols-3 gap-3 items-end">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(AdminAuth::csrfToken()) ?>">
                <input type="hidden" name="action" value="tukar_kata_laluan">
                <?php foreach ([['semasa', 'Kata laluan semasa', 'current-password'], ['baru', 'Kata laluan baharu', 'new-password'], ['ulang', 'Ulang kata laluan baharu', 'new-password']] as [$n, $l, $ac]): ?>
                <div>
                    <label for="kl-<?= $n ?>" class="block text-xs font-semibold text-gray-600 mb-1"><?= $l ?></label>
                    <input type="password" id="kl-<?= $n ?>" name="<?= $n ?>" autocomplete="<?= $ac ?>" required <?= $n !== 'semasa' ? 'minlength="12"' : '' ?>
                           class="w-full px-4 py-2.5 rounded-xl bg-gray-50 focus:bg-white border-2 border-transparent focus:border-brand-purple outline-none text-sm">
                </div>
                <?php endforeach; ?>
                <button class="sm:col-span-3 justify-self-start px-5 py-2.5 rounded-xl bg-gray-800 text-white font-semibold text-sm hover:bg-gray-700 transition">Tukar kata laluan</button>
            </form>
        </section>

        <p class="text-center text-xs text-gray-500 py-4">Muat semula halaman untuk angka terkini.</p>

    </main>
<?php endif; ?>

</body>
</html>
