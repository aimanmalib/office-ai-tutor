<?php
/** Rujukan pantas / cheat sheet: config/pek/<subjek>/[en/]cheatsheet.php — bahagian pintasan | kad | jadual | kesilapan.
 *  Sauh sisi menanda bahagian aktif (assets/js/sauh.js); kepala halaman = _kepala-hal.php. */
$e = [Papar::class, 'e'];
$f = [Papar::class, 'f'];
$bhs = Bahasa::kod();
$c = Kandungan::baca($subjekId, 'cheatsheet', $bhs);
$info = Subjek::semua()[$subjekId];
$tajuk = Papar::polos($c['tajuk'] ?? t('hal.cheatsheet'));
$huraian = Papar::polos($c['intro'] ?? '');
$skrip = ['/assets/js/sauh.js'];
$pint = 0;
foreach ($c['bahagian'] ?? [] as $b) {
    $pint += $b['jenis'] === 'pintasan' ? count($b['item'] ?? []) : 0;
}
$kp = ['ikon' => 'list', 'eyebrow' => $info['nama'], 'tajuk' => $f($c['tajuk'] ?? t('hal.cheatsheet')), 'intro' => $f($c['intro'] ?? ''),
    'angka' => array_values(array_filter([[count($c['bahagian'] ?? []), t('kira.bahagian')], [$pint, t('kira.pintasan')]], fn($a) => $a[0] > 0)),
    'aksi' => '<button type="button" class="btn btn-kedua btn-sm" onclick="window.print()" data-cetak="sorok">' . Papar::ikon('printer') . te('cs.cetak') . '</button>'];
require __DIR__ . '/_kepala-hal.php';
$bil = 0;
?>

<div class="bekas grid lg:grid-cols-[14rem_1fr] gap-10 lg:gap-14 pt-10 sm:pt-14 pb-16">
  <nav class="hidden lg:block" aria-label="<?= te('cs.kandungan') ?>" data-cetak="sorok">
    <div class="sticky top-24">
      <p class="eyebrow mb-3"><?= te('cs.kandungan') ?></p>
      <ul class="sauh text-sm" id="toc">
        <?php foreach ($c['bahagian'] ?? [] as $i => $b): ?>
        <li><a href="#<?= $e($b['id']) ?>"><span class="font-mono text-[0.6875rem] text-muted tabular-nums"><?= sprintf('%02d', $i + 1) ?></span><span class="min-w-0"><?= $f($b['tajuk']) ?></span></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </nav>

  <div class="min-w-0 space-y-16">
    <?php foreach ($c['bahagian'] ?? [] as $b): ?>
    <section id="<?= $e($b['id']) ?>" class="scroll-mt-24" data-sauh-bahagian>
      <div class="kepala-bahagian">
        <span class="jubin jubin-sm"><?= Papar::ikon(Ikon::ada($b['ikon'] ?? '') ? $b['ikon'] : 'list', 'w-[1.125rem] h-[1.125rem]') ?></span>
        <div class="min-w-0">
          <p class="font-mono text-[0.6875rem] font-medium text-accent tabular-nums tracking-[0.12em]"><?= sprintf('%02d', ++$bil) ?></p>
          <h2 class="text-h2 leading-tight"><?= $f($b['tajuk']) ?></h2>
        </div>
      </div>
      <?php if (!empty($b['sub'])): ?><p class="mt-3 text-ink-2 max-w-[70ch] teks"><?= $f($b['sub']) ?></p><?php endif; ?>

      <div class="mt-6">
      <?php if ($b['jenis'] === 'pintasan'): ?>
        <div class="kad-timbul dua-lajur overflow-hidden grid md:grid-cols-2 md:divide-x divide-line">
          <?php $sep = (int) ceil(count($b['item']) / 2); foreach ([array_slice($b['item'], 0, $sep), array_slice($b['item'], $sep)] as $lajur): ?>
          <dl class="divide-y divide-line min-w-0">
            <?php foreach ($lajur as $s): ?>
            <div class="flex items-center gap-4 px-4 py-3 baris-jalur">
              <dt class="w-[8.5rem] shrink-0 flex flex-wrap items-center gap-y-1"><?= Papar::kekunci($s['keys']) ?></dt>
              <dd class="min-w-0"><span class="block text-sm font-medium"><?= $f($s['name']) ?></span><span class="block text-[0.8125rem] text-ink-2 teks"><?= $f($s['desc'] ?? '') ?></span></dd>
            </div>
            <?php endforeach; ?>
          </dl>
          <?php endforeach; ?>
        </div>

      <?php elseif ($b['jenis'] === 'kad'): ?>
        <div class="grid gap-4 md:grid-cols-2">
          <?php foreach ($b['item'] as [$tj, $tk]): ?>
          <div class="kad-timbul p-5 sm:p-6 min-w-0 border-t-2 border-t-accent">
            <h3 class="font-semibold"><?= $f($tj) ?></h3>
            <p class="mt-1.5 text-sm text-ink-2 leading-relaxed teks"><?= $f($tk) ?></p>
          </div>
          <?php endforeach; ?>
        </div>

      <?php elseif ($b['jenis'] === 'jadual'): ?>
        <?php // telefon: setiap baris = kad bertindan (jadual 4 lajur tidak muat 350 px); sm+: jadual ?>
        <div class="sm:hidden kad-timbul overflow-hidden divide-y divide-line">
          <?php foreach ($b['baris'] as $r): ?>
          <dl class="px-4 py-3 text-sm space-y-1.5 baris-jalur">
            <dt class="font-medium teks"><?= $f($r[0]) ?></dt>
            <?php foreach (array_slice($r, 1, null, true) as $i => $sel): if (trim(strip_tags((string) $sel)) === '') continue; ?>
            <dd class="teks text-ink-2"><span class="text-xs text-muted block"><?= $f($b['kepala'][$i] ?? '') ?></span><?= $f($sel) ?></dd>
            <?php endforeach; ?>
          </dl>
          <?php endforeach; ?>
        </div>
        <div class="hidden sm:block kad-timbul tatal-x" tabindex="0" role="region" aria-label="<?= $e(Papar::polos($b['tajuk'])) ?>">
          <table class="w-full text-sm">
            <thead><tr class="bg-accent-soft/60 text-left">
              <?php foreach ($b['kepala'] as $kpl): ?><th scope="col" class="px-4 py-3 text-[0.8125rem] font-semibold text-accent-ink whitespace-nowrap border-b border-accent/20"><?= $f($kpl) ?></th><?php endforeach; ?>
            </tr></thead>
            <tbody class="divide-y divide-line">
              <?php foreach ($b['baris'] as $r): ?>
              <tr class="baris-jalur"><?php foreach ($r as $i => $sel): ?><td class="px-4 py-3 align-top teks <?= $i === 0 ? 'font-medium whitespace-nowrap' : 'text-ink-2' ?>"><?= $f($sel) ?></td><?php endforeach; ?></tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      <?php elseif ($b['jenis'] === 'kesilapan'): ?>
        <div class="kad-timbul divide-y divide-line overflow-hidden">
          <div class="hidden md:grid grid-cols-2 gap-6 px-5 py-3 text-[0.8125rem] font-semibold text-ink-2 bg-surface-2/70">
            <span><?= te('cs.masalah') ?></span><span><?= te('cs.betulkan') ?></span>
          </div>
          <?php foreach ($b['item'] as [$salah, $betul]): ?>
          <div class="grid md:grid-cols-2 gap-x-6 gap-y-2 px-5 py-4 text-sm baris-jalur">
            <p class="flex gap-2.5"><span class="text-err mt-0.5 shrink-0"><?= Papar::ikon('circle-x') ?></span><span class="teks font-medium"><?= $f($salah) ?></span></p>
            <p class="flex gap-2.5"><span class="text-ok mt-0.5 shrink-0"><?= Papar::ikon('circle-check') ?></span><span class="teks text-ink-2"><?= $f($betul) ?></span></p>
          </div>
          <?php endforeach; ?>
        </div>
        <?php if (!empty($b['amalan'])): ?>
        <div class="mt-6">
          <h3 class="font-medium mb-3"><?= te('cs.amalan') ?></h3>
          <ul class="grid gap-2 md:grid-cols-2 text-sm">
            <?php foreach ($b['amalan'] as $a): ?><li class="flex gap-2.5"><span class="text-ok mt-0.5 shrink-0"><?= Papar::ikon('check') ?></span><span class="teks text-ink-2"><?= $f($a) ?></span></li><?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      <?php endif; ?>
      </div>

      <?php if (!empty($b['nota'])): ?>
      <p class="nota-aksen mt-5 teks"><span class="text-accent mt-0.5 shrink-0"><?= Papar::ikon('lightbulb') ?></span><span><?= $f($b['nota']) ?></span></p>
      <?php endif; ?>
    </section>
    <?php endforeach; ?>
  </div>
</div>

<?php require __DIR__ . '/_cta-tutor.php'; ?>
