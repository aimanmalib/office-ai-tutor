<?php
/**
 * Rujukan: Excel = semua fungsi (src/Fungsi.php, dikumpul ikut kategori, butiran dalam <details>);
 * Word/PowerPoint = ciri (config/pek/<subjek>/[en/]rujukan.php) dengan laluan ribbon + pintasan.
 */
$e = [Papar::class, 'e'];
$f = [Papar::class, 'f'];
$bhs = Bahasa::kod();
$info = Subjek::semua()[$subjekId];
$skrip = ['/assets/js/tapis.js'];
$slug = fn(string $n) => strtolower(trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $n), '-'));
$langkah = fn(string $s) => implode('<span class="text-accent mx-1" aria-hidden="true">→</span>', array_map(fn($x) => '<span>' . $f(trim($x)) . '</span>', preg_split('/\s*→\s*/u', $s)));

if ($subjekId === 'excel'):
    require_once __DIR__ . '/../Fungsi.php';
    $semua = Fungsi::semua($bhs);
    $katTapis = [];
    $susunan = ['lookup', 'logic', 'math', 'stat', 'text', 'date', 'finance', 'info', 'engr', 'db', 'cube', 'web', 'compat'];
    foreach ($susunan as $k) {
        $katTapis[$k] = t("fn.kat.$k");
    }
    $bil = array_count_values(array_column($semua, 'cat'));
    $jumlah = count($semua);
    $tempatCari = t('fn.cari');
    $tajuk = t('hal.reference.excel');
    $huraian = t('fn.meta', ['n' => $jumlah]);
    $popular = te('fn.popular');
    $lama = t('fn.lama');
    $salin = te('js.salin');
    $kegunaan = te('fx.kegunaan');
    $ikonSalin = Papar::ikon('copy');
    $ikut = [];
    foreach ($semua as $r) {
        $ikut[$r['cat']][] = $r;
    }
?>
<?php
$kp = ['ikon' => 'book-marked', 'eyebrow' => 'Excel', 'tajuk' => te('hal.reference.excel'), 'intro' => te('fn.intro', ['n' => Bahasa::nombor($jumlah)]),
    'angka' => [[$jumlah, t('kira.fungsi')], [count($ikut), t('kira.kategori')], [count(array_filter(array_column($semua, 'popular'))), t('fn.popular')]]];
require __DIR__ . '/_kepala-hal.php';
$ikonKat = ['lookup' => 'search', 'logic' => 'braces', 'math' => 'calculator', 'stat' => 'chart-column', 'text' => 'type', 'date' => 'calendar',
    'finance' => 'banknote', 'info' => 'info', 'engr' => 'settings', 'db' => 'table', 'cube' => 'layers', 'web' => 'globe', 'compat' => 'refresh-cw'];
?>
<div class="bekas pt-4 sm:pt-6">
  <?php require __DIR__ . '/_bar-tapis.php'; ?>
  <div class="mt-8 space-y-12">
    <?php foreach ($susunan as $k): if (empty($ikut[$k])) continue; ?>
    <section data-kumpulan>
      <h2 class="flex items-center gap-3 text-h3 mb-4"><span class="jubin jubin-sm"><?= Papar::ikon($ikonKat[$k], 'w-[1.125rem] h-[1.125rem]') ?></span><?= $e($katTapis[$k]) ?><span class="lencana-angka"><?= count($ikut[$k]) ?></span></h2>
      <div class="kad-timbul divide-y divide-line overflow-hidden">
        <?php foreach ($ikut[$k] as $r): $id = 'fn-' . $slug($r['name']); ?>
        <details id="<?= $e($id) ?>" data-item data-cat="<?= $e($k) ?>" data-nama="<?= $e($r['name']) ?>" class="fn">
          <summary><span class="fn-n"><?= $e($r['name']) ?><?= empty($r['popular']) ? '' : '<i title="' . $popular . '"></i>' ?></span><span class="fn-d teks"><?= $f($r['desc']) ?></span><?php
            if (!empty($r['version']) && $r['version'] !== '2007'): ?><span class="lencana"><?= $e($r['version'] === '365' ? 'Microsoft 365' : ($r['version'] === 'legacy' ? $lama : 'Excel ' . $r['version'])) ?></span><?php endif; ?></summary>
          <div class="fn-b">
            <p class="fn-s"><?= $e($r['syntax']) ?></p>
            <?php if (!empty($r['example'])): ?><div class="fn-c"><code><?= $e($r['example']) ?></code><?php if (!empty($r['sample'])): ?><span>→ <?= $e($r['sample']) ?></span><?php endif; ?><button type="button" class="btn-ikon" data-salin="<?= $e($r['example']) ?>" aria-label="<?= $salin ?>"><?= $ikonSalin ?></button></div><?php endif; ?>
            <?php if (!empty($r['usecase'])): ?><p class="teks"><b><?= $kegunaan ?></b> <?= $f($r['usecase']) ?></p><?php endif; ?>
          </div>
        </details>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>
  </div>
  <?php require __DIR__ . '/_kosong.php'; ?>
</div>

<?php else:
    $r = Kandungan::baca($subjekId, 'rujukan', $bhs);
    $katTapis = array_map(fn($k) => $k[0], $r['kategori'] ?? []);
    $bil = array_count_values(array_column($r['ciri'] ?? [], 'cat'));
    $jumlah = count($r['ciri'] ?? []);
    $tempatCari = t('ciri.cari');
    $tajuk = Papar::polos($r['tajuk'] ?? t('hal.reference.ciri'));
    $huraian = t('ciri.meta', ['n' => $jumlah, 'subjek' => $info['nama']]);
?>
<?php
$kp = ['ikon' => 'book-marked', 'eyebrow' => $info['nama'], 'tajuk' => $f($r['tajuk'] ?? t('hal.reference.ciri')),
    'intro' => te('ciri.intro', ['n' => $jumlah, 'subjek' => $info['nama']]), 'angka' => [[$jumlah, t('kira.ciri')], [count($bil), t('kira.kategori')]]];
require __DIR__ . '/_kepala-hal.php';
?>
<div class="bekas pt-4 sm:pt-6">
  <?php require __DIR__ . '/_bar-tapis.php'; ?>
  <div class="grid gap-4 md:grid-cols-2 mt-6">
    <?php foreach ($r['ciri'] ?? [] as $c): $id = 'ciri-' . $slug($c['nama']); ?>
    <article id="<?= $e($id) ?>" data-item data-cat="<?= $e($c['cat']) ?>" data-nama="<?= $e(Papar::polos($c['nama'])) ?>" class="kad-timbul p-5 sm:p-6 flex flex-col scroll-mt-40 min-w-0"
             data-cari="<?= $e(mb_strtolower(Papar::polos($c['nama'] . ' ' . $c['desc'] . ' ' . ($c['langkah'] ?? '') . ' ' . ($c['tip'] ?? '')))) ?>">
      <div class="flex items-start justify-between gap-3">
        <h2 class="text-[1.125rem] font-semibold tracking-tight"><a href="#<?= $e($id) ?>" class="hover:text-accent"><?= $f($c['nama']) ?></a></h2>
        <span class="lencana shrink-0"><?= $f($r['kategori'][$c['cat']][0] ?? $c['cat']) ?></span>
      </div>
      <p class="mt-2 text-[0.9375rem] text-ink-2 leading-relaxed teks"><?= $f($c['desc']) ?></p>
      <?php if (!empty($c['langkah'])): ?>
      <div class="mt-4 flex gap-2.5 rounded-lg bg-surface-2 border border-line px-3 py-2.5 text-[0.8125rem] leading-relaxed teks"><span class="text-accent mt-px shrink-0"><?= Papar::ikon('mouse-pointer-click') ?></span><span class="min-w-0"><?= $langkah($c['langkah']) ?></span></div>
      <?php endif; ?>
      <?php if (!empty($c['pintasan'])): ?>
      <p class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1.5 text-sm"><span class="text-muted"><?= Papar::ikon('keyboard') ?></span><?= Papar::pintasanTeks($c['pintasan']) ?></p>
      <?php endif; ?>
      <?php if (!empty($c['tip'])): ?>
      <span class="block mt-4"></span>
      <p class="nota-aksen mt-auto teks"><span class="text-accent mt-0.5 shrink-0"><?= Papar::ikon('lightbulb') ?></span><span><?= $f($c['tip']) ?></span></p>
      <?php endif; ?>
    </article>
    <?php endforeach; ?>
  </div>
  <?php require __DIR__ . '/_kosong.php'; ?>
</div>
<?php endif; ?>

<div class="mt-14"><?php require __DIR__ . '/_cta-tutor.php'; ?></div>
