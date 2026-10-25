<?php
// Tutor terapung (halaman rujukan/latihan). Logik = assets/js/perbualan.js (dikongsi dengan halaman tutor).
require_once __DIR__ . '/../ChatStatus.php';
$wStatus = (new ChatStatus(__DIR__ . '/../../data/chat-status.json'))->read();
$wMati = !$wStatus['enabled'];
$wC = $teksSubjek['chat'] ?? [];
?>
<script src="<?= Papar::aset('/assets/vendor/marked.min.js') ?>" defer></script>
<script src="<?= Papar::aset('/assets/vendor/purify.min.js') ?>" defer></script>
<script src="<?= Papar::aset('/assets/js/perbualan.js') ?>" defer></script>
<div id="chat-widget" class="hidden sm:block fixed bottom-6 right-6 z-40" data-cetak="sorok">
  <section id="widget-panel" hidden class="fixed inset-0 z-50 rounded-none border-0 sm:inset-auto sm:absolute sm:bottom-[4.75rem] sm:right-0 sm:w-[24rem] sm:h-[34rem] sm:max-h-[calc(100dvh-8rem)] sm:border widget-panel flex flex-col overflow-hidden"
           aria-label="<?= te('tutor.tajuk', ['subjek' => $subjekInfo['nama']]) ?>">
    <div class="widget-kepala">
      <div class="flex items-center gap-2.5 min-w-0">
        <span class="jubin jubin-sm" aria-hidden="true"><?= Papar::ikon('sparkle') ?></span>
        <div class="min-w-0">
          <p class="text-sm font-semibold tracking-tight truncate"><?= te('tutor.tajuk', ['subjek' => $subjekInfo['nama']]) ?></p>
          <p class="text-[0.6875rem] text-ink-2 flex items-center gap-1.5"><span class="titik-status w-1.5 h-1.5 <?= $wMati ? 'rehat' : '' ?>"></span><?= te($wMati ? 'tutor.rehat' : 'tutor.hidup') ?></p>
        </div>
      </div>
      <div class="flex items-center">
        <a href="<?= Laluan::subjek($subjekId, 'tutor') ?>" class="btn-ikon h-9 w-9" aria-label="<?= te('tutor.skrin_penuh') ?>" title="<?= te('tutor.skrin_penuh') ?>"><?= Papar::ikon('external-link') ?></a>
        <?php if (!$wMati): ?><button type="button" class="btn-ikon h-9 w-9" data-c-baru aria-label="<?= te('tutor.baru') ?>" title="<?= te('tutor.baru') ?>"><?= Papar::ikon('rotate-ccw') ?></button><?php endif; ?>
        <button type="button" id="widget-close" class="btn-ikon h-11 w-11" aria-label="<?= te('nav.tutup') ?>"><?= Papar::ikon('x', 'w-5 h-5') ?></button>
      </div>
    </div>
    <?php if ($wMati): ?>
    <div class="flex-1 grid place-items-center p-6 text-center">
      <div><span class="jubin jubin-lg mx-auto bg-warn-soft text-warn"><?= Papar::ikon('clock', 'w-6 h-6') ?></span><p class="mt-4 font-medium"><?= te('tutor.rehat_tajuk') ?></p><p class="mt-2 text-sm text-ink-2"><?= Papar::e($wStatus['message']) ?></p></div>
    </div>
    <?php else: ?>
    <div id="widget-messages" data-c-mesej class="relative flex-1 overflow-y-auto px-4 py-4 space-y-5" aria-live="polite"></div>
    <form id="widget-form" data-c-borang class="m-2.5 mt-0 komposer gap-1 p-1 rounded-xl">
      <label for="widget-input" class="sr-only"><?= te('tutor.soalan') ?></label>
      <textarea id="widget-input" data-c-input rows="1" maxlength="2000" placeholder="<?= Papar::e($wC['placeholder'] ?? '') ?>" class="flex-1 min-w-0 resize-none bg-transparent px-2 py-2 text-sm leading-6 focus:outline-none max-h-[120px]"></textarea>
      <button type="submit" data-c-hantar class="btn btn-utama h-9 w-9 px-0 shrink-0" aria-label="<?= te('tutor.hantar') ?>"><?= Papar::ikon('arrow-up') ?></button>
    </form>
    <?php endif; ?>
  </section>
  <button id="widget-toggle" type="button" aria-expanded="false" aria-controls="widget-panel" aria-label="<?= te('tutor.tanya') ?>" class="btn btn-utama widget-togol h-14 w-14 px-0 rounded-full transition-transform duration-200" title="<?= te('tutor.tanya') ?>">
    <?= Papar::ikon('message-square', 'w-5 h-5') ?>
  </button>
</div>
