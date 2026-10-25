<?php
/** 404 (laluan tidak wujud / subjek draf). Status 404 ditetapkan oleh laman.php. Hiasan: sel hamparan dengan ralat #N/A
 *  (kod ralat Excel = sama dalam setiap bahasa, bukan teks UI). */
$tajuk = t('404.tajuk');
$huraian = t('404.teks');
$noindex = true;
$widget = false;
?>
<section class="relative overflow-hidden">
  <div class="absolute inset-0 -z-10 [background-image:linear-gradient(rgb(var(--line)/.6)_1px,transparent_1px),linear-gradient(90deg,rgb(var(--line)/.6)_1px,transparent_1px)] [background-size:40px_40px] [mask-image:radial-gradient(ellipse_60%_55%_at_50%_40%,black_15%,transparent_70%)]" aria-hidden="true"></div>
  <div class="cahaya absolute -z-10 left-1/2 top-10 -translate-x-1/2 w-[36rem] h-[28rem] pointer-events-none" aria-hidden="true"></div>
  <div class="bekas py-24 sm:py-32 text-center">
    <div class="tingkap mx-auto max-w-xs text-left" aria-hidden="true">
      <div class="flex items-stretch h-11 font-mono text-[0.8125rem]">
        <span class="w-14 grid place-items-center border-r border-line text-muted">A1</span>
        <span class="px-3 grid place-items-center border-r border-line text-accent italic font-serif font-semibold">fx</span>
        <span class="flex-1 px-3 flex items-center font-semibold text-err">#N/A</span>
      </div>
    </div>
    <p class="eyebrow-aksen mt-10">404</p>
    <h1 class="text-h1 mt-4"><?= te('404.tajuk') ?></h1>
    <p class="mt-4 text-ink-2 text-[1.0625rem] leading-relaxed max-w-md mx-auto"><?= te('404.teks') ?></p>
    <div class="mt-9 flex flex-wrap justify-center gap-3">
      <a href="<?= Laluan::utama() ?>" class="btn btn-lg btn-utama"><?= Papar::ikon('arrow-left') ?><?= te('404.utama') ?></a>
      <?php foreach (array_filter(Subjek::semua(), fn($x, $id) => Subjek::hidup($id), ARRAY_FILTER_USE_BOTH) as $sid => $si): ?>
      <a href="<?= Laluan::subjek($sid) ?>" data-subjek="<?= $sid ?>" class="btn btn-lg btn-kedua"><span class="jubin jubin-sm w-6 h-6 rounded-md"><?= Papar::ikon($si['ikon'], 'w-3.5 h-3.5') ?></span><?= Papar::e($si['nama']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
