<?php
/** Formula penting Excel: config/pek/excel/[en/]formula.php — kad dengan sintaks, contoh (butang salin), kegunaan. */
$e = [Papar::class, 'e'];
$f = [Papar::class, 'f'];
$bhs = Bahasa::kod();
$d = Kandungan::baca('excel', 'formula', $bhs);
$tajuk = Papar::polos($d['tajuk'] ?? t('hal.formulas'));
$huraian = Papar::polos($d['intro'] ?? '');
$skrip = ['/assets/js/tapis.js'];
$bil = array_count_values(array_column($d['formula'] ?? [], 'cat'));
$katTapis = array_map(fn($k) => $k['label'], $d['kategori'] ?? []);
$jumlah = count($d['formula'] ?? []);
$tempatCari = t('fx.cari');
?>
<?php
$kp = ['ikon' => 'square-function', 'eyebrow' => 'Excel', 'tajuk' => $f($d['tajuk'] ?? ''), 'intro' => $f($d['intro'] ?? ''),
    'angka' => [[$jumlah, t('kira.formula')], [count($bil), t('kira.kategori')]]];
require __DIR__ . '/_kepala-hal.php';
?>

<div class="bekas pt-4 sm:pt-6">
  <?php require __DIR__ . '/_bar-tapis.php'; ?>
  <div class="grid gap-4 md:grid-cols-2 mt-6">
    <?php foreach ($d['formula'] ?? [] as $r): $id = 'f-' . strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', $r['name'])); ?>
    <article id="<?= $e($id) ?>" data-item data-cat="<?= $e($r['cat']) ?>" data-nama="<?= $e($r['name']) ?>" class="kad-timbul p-5 sm:p-6 flex flex-col scroll-mt-28 min-w-0"
             data-cari="<?= $e(mb_strtolower(Papar::polos($r['name'] . ' ' . $r['desc'] . ' ' . ($r['usecase'] ?? '') . ' ' . ($r['sample'] ?? '') . ' ' . ($d['kategori'][$r['cat']]['label'] ?? '')))) ?>">
      <div class="flex items-start justify-between gap-3">
        <h2 class="font-mono text-[1.125rem] font-semibold tracking-tight text-accent"><a href="#<?= $e($id) ?>" class="hover:underline underline-offset-4"><?= $e($r['name']) ?></a></h2>
        <span class="lencana shrink-0"><?= $e($d['kategori'][$r['cat']]['label'] ?? $r['cat']) ?></span>
      </div>
      <p class="mt-1 font-mono text-xs text-muted break-words"><?= $e($r['syntax']) ?></p>
      <p class="mt-4 text-[0.9375rem] text-ink-2 leading-relaxed teks"><?= $f($r['desc']) ?></p>
      <div class="mt-4 bar-fx">
        <code class="flex-1 min-w-0 font-mono text-[0.8125rem] truncate"><?= $e($r['example']) ?></code>
        <button type="button" class="btn-ikon h-10 w-10 shrink-0" data-salin="<?= $e($r['example']) ?>" aria-label="<?= te('js.salin') ?>"><?= Papar::ikon('copy') ?></button>
      </div>
      <?php if (!empty($r['sample'])): ?><p class="mt-2 text-xs text-muted teks"><?= $f($r['sample']) ?></p><?php endif; ?>
      <?php if (!empty($r['usecase'])): ?>
      <span class="block mt-4"></span>
      <p class="mt-auto pt-4 border-t border-line text-sm text-ink-2 teks"><span class="font-semibold text-ink"><?= te('fx.kegunaan') ?></span> <?= $f($r['usecase']) ?></p>
      <?php endif; ?>
    </article>
    <?php endforeach; ?>
  </div>
  <?php require __DIR__ . '/_kosong.php'; ?>
</div>

<div class="mt-14"><?php require __DIR__ . '/_cta-tutor.php'; ?></div>
