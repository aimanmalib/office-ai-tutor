<?php
/** Tutor AI: perbualan skrin penuh (assets/js/perbualan.js). Status hidup/rehat + muat naik gambar = Admin (data/chat-status.json). */
require_once __DIR__ . '/../ChatStatus.php';
$e = [Papar::class, 'e'];
$bhs = Bahasa::kod();
$info = Subjek::semua()[$subjekId];
$c = Kandungan::baca($subjekId, 'utama', $bhs)['chat'] ?? [];
$status = (new ChatStatus(__DIR__ . '/../../data/chat-status.json'))->read();
$mati = !$status['enabled'];
$gambar = !$mati && $status['image_upload_enabled'];
$tajuk = t('tutor.tajuk', ['subjek' => $info['nama']]);
$huraian = $c['meta'] ?? '';
$widget = false;
$skrip = ['/assets/vendor/marked.min.js', '/assets/vendor/purify.min.js', '/assets/js/perbualan.js'];
?>
<div id="tutor" class="bekas max-w-3xl flex flex-col h-[calc(100dvh-4rem)] min-h-[32rem]">
  <div class="flex items-center justify-between gap-3 py-4 border-b border-line">
    <div class="min-w-0 flex items-center gap-3">
      <span class="jubin" aria-hidden="true"><?= Papar::ikon('sparkle', 'w-5 h-5') ?></span>
      <div class="min-w-0">
        <h1 class="font-semibold tracking-tight truncate"><?= te('tutor.tajuk', ['subjek' => $info['nama']]) ?></h1>
        <p class="text-xs text-muted flex items-center gap-2 mt-1">
          <span class="titik-status <?= $mati ? 'rehat' : '' ?>"></span><?= te($mati ? 'tutor.rehat' : 'tutor.hidup') ?>
        </p>
      </div>
    </div>
    <button type="button" class="btn btn-kedua btn-sm" data-c-baru aria-label="<?= te('tutor.baru') ?>" <?= $mati ? 'disabled' : '' ?>><?= Papar::ikon('rotate-ccw') ?><span class="hidden sm:inline"><?= te('tutor.baru') ?></span></button>
  </div>

  <div class="relative flex-1 min-h-0">
    <div id="messages" data-c-mesej class="h-full overflow-y-auto py-6 space-y-6" aria-live="polite"></div>
    <?php if ($mati): ?>
    <div class="absolute inset-0 grid place-items-center bg-bg/90 px-6">
      <div class="text-center max-w-sm">
        <span class="jubin jubin-lg mx-auto bg-warn-soft text-warn"><?= Papar::ikon('clock', 'w-6 h-6') ?></span>
        <h2 class="mt-4 text-h3"><?= te('tutor.rehat_tajuk') ?></h2>
        <p class="mt-2 text-sm text-ink-2"><?= $e($status['message']) ?></p>
        <div class="mt-6 flex flex-wrap justify-center gap-2">
          <a class="btn btn-kedua btn-sm" href="<?= Laluan::subjek($subjekId, 'cheatsheet') ?>"><?= te('hal.cheatsheet') ?></a>
          <a class="btn btn-kedua btn-sm" href="<?= Laluan::subjek($subjekId, 'reference') ?>"><?= te($subjekId === 'excel' ? 'hal.reference.excel' : 'hal.reference.ciri') ?></a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="pb-4 pt-2">
    <?php if ($gambar): ?>
    <div data-c-pratonton hidden class="mb-2 flex items-center gap-3 kad-timbul p-2">
      <img alt="" class="w-12 h-12 rounded object-cover border border-line">
      <span data-c-nama class="flex-1 min-w-0 truncate text-sm text-ink-2"></span>
      <button type="button" class="btn-ikon h-9 w-9" data-c-buang-gambar aria-label="<?= te('tutor.buang_gambar') ?>"><?= Papar::ikon('x') ?></button>
    </div>
    <?php endif; ?>
    <form id="chat-form" data-c-borang class="komposer <?= $mati ? 'opacity-60 pointer-events-none' : '' ?>">
      <?php if ($gambar): ?>
      <label class="btn-ikon shrink-0 cursor-pointer" aria-label="<?= te('tutor.lampir') ?>" title="<?= te('tutor.lampir') ?>">
        <?= Papar::ikon('paperclip', 'w-[1.125rem] h-[1.125rem]') ?>
        <input type="file" data-c-gambar id="image-input" accept="image/jpeg,image/png,image/webp" class="sr-only">
      </label>
      <?php endif; ?>
      <label for="chat-input" class="sr-only"><?= te('tutor.soalan') ?></label>
      <textarea id="chat-input" data-c-input rows="1" maxlength="2000" <?= $mati ? 'disabled' : '' ?>
                placeholder="<?= $e($c['placeholder'] ?? '') ?>" class="flex-1 min-w-0 resize-none bg-transparent px-2.5 py-2.5 text-[0.9375rem] leading-6 focus:outline-none max-h-[200px]"></textarea>
      <button type="submit" data-c-hantar class="btn btn-utama h-11 w-11 px-0 shrink-0" aria-label="<?= te('tutor.hantar') ?>" <?= $mati ? 'disabled' : '' ?>><?= Papar::ikon('arrow-up', 'w-[1.125rem] h-[1.125rem]') ?></button>
    </form>
    <p class="mt-2 text-xs text-muted text-center"><?= $e($gambar ? ($c['nota_gambar'] ?? $c['nota'] ?? '') : ($c['nota'] ?? '')) ?></p>
  </div>
</div>
