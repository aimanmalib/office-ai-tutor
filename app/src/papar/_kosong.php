<div data-kosong hidden class="py-20 text-center">
  <span class="jubin jubin-lg mx-auto"><?= Papar::ikon('search', 'w-6 h-6') ?></span>
  <p class="mt-5 text-h3"><?= te('tapis.kosong') ?></p>
  <p class="mt-1.5 text-[0.9375rem] text-ink-2"><?= te('tapis.kosong_teks') ?></p>
  <?php if ($subjekId && in_array('tutor', Laluan::halaman($subjekId), true)): ?>
  <a href="<?= Laluan::subjek($subjekId, 'tutor') ?>" class="btn btn-kedua mt-6"><?= Papar::ikon('message-square') ?><?= te('cta.butang') ?></a>
  <?php endif; ?>
</div>
