<?php // Jalur penutup halaman rujukan: ke tutor AI subjek ini (hanya bila subjek ada halaman tutor). ?>
<?php if (in_array('tutor', Laluan::halaman($subjekId), true)): ?>
<div class="bekas" data-cetak="sorok">
  <div class="kad-timbul p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-6" data-cta-tutor>
    <span class="hiasan" aria-hidden="true">
      <span class="absolute inset-y-0 left-0 w-1 bg-accent"></span>
      <span class="cahaya absolute -right-24 -top-28 w-80 h-80"></span>
    </span>
    <span class="jubin relative"><?= Papar::ikon('message-square', 'w-5 h-5') ?></span>
    <div class="relative flex-1 min-w-0">
      <h2 class="text-h3"><?= te('cta.tajuk') ?></h2>
      <p class="mt-1 text-[0.9375rem] text-ink-2 leading-relaxed"><?= te('cta.teks', ['subjek' => Subjek::semua()[$subjekId]['nama']]) ?></p>
    </div>
    <a href="<?= Laluan::subjek($subjekId, 'tutor') ?>" class="relative btn btn-lg btn-aksen shrink-0 self-start sm:self-auto"><?= Papar::ikon('message-square') ?><?= te('cta.butang') ?></a>
  </div>
</div>
<?php endif; ?>
