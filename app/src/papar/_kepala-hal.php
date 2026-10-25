<?php
// Kepala halaman rujukan (rujukan pantas, formula, rujukan fungsi/ciri): latar aksen lembut + grid + cahaya statik, jubin ikon,
// eyebrow subjek, tajuk, pengenalan, angka dikira. Perlu: $kp = ['ikon', 'eyebrow', 'tajuk' (HTML selamat), 'intro' (HTML selamat),
// 'angka' => [[nilai, label], …], 'aksi' (HTML pilihan, cth butang cetak)].
?>
<section class="relative overflow-hidden border-b border-line bg-accent-soft/30">
  <div class="grid-aksen absolute inset-0 -z-10" aria-hidden="true"></div>
  <div class="cahaya absolute -z-10 -top-48 -right-40 w-[36rem] h-[36rem] pointer-events-none" aria-hidden="true"></div>
  <div class="bekas pt-10 pb-10 sm:pt-14 sm:pb-12">
    <div class="flex items-center gap-3.5">
      <span class="jubin shadow-kad"><?= Papar::ikon($kp['ikon'], 'w-5 h-5') ?></span>
      <p class="eyebrow-aksen"><?= Papar::e($kp['eyebrow']) ?></p>
    </div>
    <div class="mt-6 flex flex-wrap items-end justify-between gap-4">
      <h1 class="text-h1 tracking-[-0.03em]"><?= $kp['tajuk'] ?></h1>
      <?= $kp['aksi'] ?? '' ?>
    </div>
    <p class="mt-4 text-ink-2 text-[1.0625rem] max-w-[62ch] leading-relaxed"><?= $kp['intro'] ?></p>
    <?php if (!empty($kp['angka'])): ?>
    <ul class="mt-7 flex flex-wrap gap-2.5">
      <?php foreach ($kp['angka'] as [$n, $label]): ?>
      <li class="inline-flex items-baseline gap-2 h-9 px-3.5 rounded-lg bg-surface border border-line shadow-kad">
        <span class="font-mono font-semibold tabular-nums text-accent"><?= Papar::e(Bahasa::nombor($n)) ?></span>
        <span class="text-[0.8125rem] text-ink-2"><?= Papar::e($label) ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
</section>
