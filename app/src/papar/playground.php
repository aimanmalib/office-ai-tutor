<?php
/** Makmal formula Excel: grid yang mengira formula (assets/js/playground.js + formula-enjin.js). Data = config/pek/excel/[en/]playground.php. */
$e = [Papar::class, 'e'];
$bhs = Bahasa::kod();
$pg = Kandungan::baca('excel', 'playground', $bhs) ?: Kandungan::baca('excel', 'playground');
$tajuk = t('hal.playground');
$huraian = t('pg.meta');
$data = ['playground' => $pg, 'kunci' => 'excel_playground_v1' . ($bhs === 'en' ? '_en' : '')];
$skrip = ['/assets/vendor/formula.min.js', '/assets/js/formula-enjin.js', '/assets/js/playground.js'];
$kp = ['ikon' => 'flask-conical', 'eyebrow' => 'Excel', 'tajuk' => te('hal.playground'), 'intro' => te('pg.intro'),
    'angka' => [[count($pg), t('pg.angka.data')], [array_sum(array_map(fn($d) => count($d['cadangan'] ?? []), $pg)), t('pg.angka.cuba')]]];
?>
<?php include __DIR__ . '/_kepala-hal.php'; ?>

<div class="bekas pt-10 sm:pt-12 pb-16" data-subjek="excel">
  <div class="tingkap overflow-clip">
    <div class="tingkap-bar h-auto min-h-[3.25rem] py-2 flex-wrap">
      <span class="tingkap-titik hidden sm:flex" aria-hidden="true"><i></i><i></i><i></i></span>
      <label for="pg-contoh" class="text-[0.8125rem] font-medium"><?= te('pg.data') ?></label>
      <select id="pg-contoh" class="medan sm:h-9 flex-1 sm:flex-none sm:w-auto min-w-[11rem] pr-8 text-sm">
        <?php foreach ($pg as $id => $d): ?><option value="<?= $e($id) ?>"><?= $e($d['nama']) ?></option><?php endforeach; ?>
      </select>
      <button id="pg-reset" type="button" class="btn btn-sm btn-kedua border-transparent sm:ml-auto shrink-0"><?= Papar::ikon('rotate-ccw') ?><?= te('pg.reset') ?></button>
    </div>
    <div class="sticky top-16 z-20 bg-surface border-b border-line">
      <div class="flex items-stretch">
        <span id="pg-nama" class="medan-fx-nama w-16 sm:w-20" aria-live="polite">A1</span>
        <span class="medan-fx-fx" aria-hidden="true">fx</span>
        <input id="pg-bar" type="text" autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" aria-label="<?= te('pg.bar') ?>"
               placeholder="<?= te('pg.bar_contoh') ?>" class="flex-1 min-w-0 h-12 sm:h-14 px-3.5 bg-transparent font-mono text-[0.9375rem] sm:text-base text-ink placeholder:text-muted focus:outline-none">
      </div>
      <p id="pg-hasil" class="px-4 py-1.5 font-mono text-xs empty:hidden text-muted border-t border-line bg-surface-2/40" aria-live="polite"></p>
    </div>
    <div class="tatal-x" tabindex="0" role="region" aria-label="<?= te('pg.grid') ?>"><table class="hamparan min-w-full" id="pg-grid"></table></div>
  </div>
  <p class="mt-4 flex gap-2 text-xs text-muted max-w-[90ch]"><?= Papar::ikon('lightbulb', 'w-3.5 h-3.5 mt-px shrink-0 text-accent') ?><?= te('pg.bantuan') ?></p>

  <section class="mt-14">
    <p class="eyebrow-aksen"><?= te('pg.angka.cuba') ?></p>
    <h2 class="text-h2 mt-3"><?= te('pg.cuba') ?></h2>
    <p class="mt-2 text-ink-2"><?= te('pg.cuba_teks') ?></p>
    <div id="pg-cadangan" class="mt-6 flex flex-wrap gap-2"></div>
  </section>
  <p class="nota-aksen mt-12 max-w-[80ch] text-xs"><?= Papar::ikon('sparkle', 'w-4 h-4 shrink-0 text-accent') ?><span><?= te('pg.sokongan') ?></span></p>
</div>
