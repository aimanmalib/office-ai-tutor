<?php
/**
 * Gambaran satu subjek (arah "buku kerja yang hidup", docs/REKA.md): wira diwarnai aksen subjek + panel angka berbentuk sel
 * hamparan, kad alat timbul berjubin (bilangan dikira), set latihan dengan kemajuan pelayar (tapak.js), fungsi popular (Excel).
 */
$e = [Papar::class, 'e'];
$bhs = Bahasa::kod();
$info = Subjek::semua()[$subjekId];
$u = Kandungan::baca($subjekId, 'utama', $bhs);
$k = Papar::kiraan($subjekId, $bhs);
$tajuk = $info['nama'];
$huraian = $u['meta'] ?? ($u['ringkas'] ?? '');
$skrip = ['/assets/js/kemajuan.js'];
$hal = Laluan::halaman($subjekId);
$ikonHal = ['cheatsheet' => 'list', 'formulas' => 'square-function', 'reference' => 'book-marked', 'practice' => 'square-check-big',
    'playground' => 'flask-conical', 'tutor' => 'message-square'];
$metaHal = [
    'cheatsheet' => t('kira.bahagian_n', ['n' => $k['bahagian']]),
    'formulas' => t('kira.formula_n', ['n' => $k['formula']]),
    'reference' => t($subjekId === 'excel' ? 'kira.fungsi_n' : 'kira.ciri_n', ['n' => Bahasa::nombor($k['rujukan'])]),
    'practice' => t('kira.latihan_n', ['n' => $k['latihan'], 's' => $k['set']]),
    'playground' => t('kira.langsung'), 'tutor' => t('kira.bahasa_dua'),
];
$labelHal = fn(string $h) => t($h === 'reference' ? ($subjekId === 'excel' ? 'hal.reference.excel' : 'hal.reference.ciri') : "hal.$h");
$set = Kandungan::baca($subjekId, 'latihan', $bhs);
$popular = [];
if ($subjekId === 'excel') {
    require_once __DIR__ . '/../Fungsi.php';
    $ada = array_column(Fungsi::semua($bhs), null, 'name');
    $popular = array_values(array_filter(array_map(fn($n) => $ada[$n] ?? null, $u['popular'] ?? [])));   // dipilih tangan: config/pek/excel/utama.php
}
// panel wira: empat angka dikira, dilabel seperti sel A1..B2 (hiasan; label sebenar di bawah setiap angka)
$angka = array_values(array_filter([
    [$subjekId === 'excel' ? 'kira.fungsi' : 'kira.ciri', $k['rujukan']],
    ['kira.latihan', $k['latihan']],
    ['subjek.set_n', $k['set']],
    ['kira.pintasan', $k['pintasan']],
], fn($a) => $a[1] > 0));
$bil = 0;
?>
<section class="relative overflow-hidden border-b border-line bg-accent-soft/30">
  <div class="absolute inset-0 -z-10 [background-image:linear-gradient(rgb(var(--accent)/.10)_1px,transparent_1px),linear-gradient(90deg,rgb(var(--accent)/.10)_1px,transparent_1px)] [background-size:32px_32px] [mask-image:radial-gradient(ellipse_80%_70%_at_80%_30%,black_10%,transparent_70%)]" aria-hidden="true"></div>
  <div class="cahaya absolute -z-10 -top-40 -right-32 w-[44rem] h-[44rem] pointer-events-none" aria-hidden="true"></div>
  <div class="bekas pt-12 pb-14 sm:pt-20 sm:pb-20 grid lg:grid-cols-[1.15fr_1fr] gap-12 lg:gap-16 items-center">
    <div class="min-w-0">
      <div class="flex items-center gap-4">
        <span class="jubin jubin-lg shadow-kad"><?= Papar::ikon($info['ikon'], 'w-7 h-7') ?></span>
        <p class="eyebrow-aksen"><?= te('subjek.eyebrow') ?></p>
      </div>
      <h1 class="text-display mt-7"><?= $e($info['nama']) ?></h1>
      <p class="mt-5 text-[1.0625rem] sm:text-lg text-ink-2 max-w-[54ch] leading-relaxed"><?= $e($u['ringkas'] ?? '') ?></p>
      <div class="mt-9 flex flex-col sm:flex-row gap-3">
        <a href="<?= Laluan::subjek($subjekId, 'practice') ?>" class="btn btn-lg btn-aksen"><?= te('subjek.mula') ?><?= Papar::ikon('arrow-right') ?></a>
        <a href="<?= Laluan::subjek($subjekId, 'cheatsheet') ?>" class="btn btn-lg btn-kedua"><?= Papar::ikon('list') ?><?= $e($labelHal('cheatsheet')) ?></a>
      </div>
    </div>

    <?php if ($angka): ?>
    <figure class="tingkap min-w-0" aria-labelledby="ringkasan-tajuk">
      <div class="tingkap-bar">
        <span class="tingkap-titik" aria-hidden="true"><i></i><i></i><i></i></span>
        <span id="ringkasan-tajuk" class="text-[0.8125rem] font-medium truncate"><?= te('subjek.ringkasan', ['subjek' => $info['nama']]) ?></span>
      </div>
      <dl class="grid grid-cols-2">
        <?php foreach ($angka as $i => [$kunci, $n]): ?>
        <div class="sel-angka min-w-0 <?= $i % 2 ? '' : 'border-r border-line' ?> <?= $i < 2 && count($angka) > 2 ? 'border-b border-line' : '' ?>" data-sel="<?= chr(65 + $i % 2) . (intdiv($i, 2) + 1) ?>">
          <dt class="order-last mt-2 text-[0.8125rem] text-ink-2"><?= te($kunci) ?></dt>
          <dd class="angka text-[1.75rem] sm:text-[2rem] text-accent"><?= Bahasa::nombor($n) ?></dd>
        </div>
        <?php endforeach; ?>
      </dl>
    </figure>
    <?php endif; ?>
  </div>
</section>

<section class="bahagian">
  <div class="bekas">
    <div class="max-w-2xl" data-muncul>
      <p class="eyebrow-aksen"><?= sprintf('%02d', ++$bil) ?> · <?= te('subjek.alat') ?></p>
      <h2 class="tajuk-bahagian"><?= te('subjek.alat.tajuk', ['subjek' => $info['nama']]) ?></h2>
      <p class="mt-4 text-ink-2 text-[1.0625rem] leading-relaxed"><?= te('subjek.alat.ringkas') ?></p>
    </div>
    <div class="mt-12 grid gap-5 sm:grid-cols-2 <?= count($hal) % 3 === 0 ? 'lg:grid-cols-3' : '' ?>" data-muncul>
      <?php foreach ($hal as $h): ?>
      <a href="<?= Laluan::subjek($subjekId, $h) ?>" class="kad-timbul group min-w-0 p-6 sm:p-7 flex flex-col">
        <div class="flex items-start justify-between gap-3">
          <span class="jubin"><?= Papar::ikon($ikonHal[$h], 'w-5 h-5') ?></span>
          <span class="lencana-angka"><?= $e($metaHal[$h]) ?></span>
        </div>
        <h3 class="text-h3 mt-6"><?= $e($labelHal($h)) ?></h3>
        <p class="mt-2 text-[0.9375rem] text-ink-2 leading-relaxed flex-1"><?= $e($u['alat'][$h] ?? '') ?></p>
        <span class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-accent"><?= $e($labelHal($h)) ?><span class="transition-transform group-hover:translate-x-1"><?= Papar::ikon('arrow-right') ?></span></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($set): ?>
<section class="bahagian bahagian-alt">
  <div class="bekas">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4" data-muncul>
      <div class="max-w-2xl">
        <p class="eyebrow-aksen"><?= sprintf('%02d', ++$bil) ?> · <?= te('hal.practice') ?></p>
        <h2 class="tajuk-bahagian"><?= te('subjek.set') ?></h2>
        <p class="mt-4 text-ink-2 text-[1.0625rem] leading-relaxed"><?= te('subjek.set.ringkas') ?></p>
      </div>
      <a class="pautan-aksen text-sm font-medium shrink-0 inline-flex items-center gap-1.5" href="<?= Laluan::subjek($subjekId, 'practice') ?>"><?= te('subjek.semua_latihan') ?><?= Papar::ikon('arrow-right') ?></a>
    </div>
    <div class="mt-12 grid gap-5 md:grid-cols-2" data-muncul>
      <?php foreach ($set as $sid => $s):
          $n = count($s['questions'] ?? []);
          $lv = array_map(fn($q) => (int) ($q['level'] ?? 1), $s['questions'] ?? []) ?: [1]; ?>
      <a href="<?= Laluan::subjek($subjekId, 'practice') ?>?set=<?= rawurlencode($sid) ?>" class="kad-timbul group min-w-0 p-6 sm:p-7 flex flex-col"
         data-kemajuan-kunci="<?= $e($subjekId . '_latihan_v2_' . $sid) ?>" data-n="<?= $n ?>">
        <div class="flex items-start gap-4">
          <span class="jubin"><?= Papar::ikon(Ikon::ada($s['icon'] ?? '') ? $s['icon'] : 'list-checks', 'w-5 h-5') ?></span>
          <span class="flex-1 min-w-0">
            <span class="block text-h3"><?= $e($s['name']) ?></span>
            <span class="block mt-1 text-[0.9375rem] text-ink-2 leading-relaxed"><?= $e($s['tagline'] ?? '') ?></span>
          </span>
        </div>
        <div class="mt-6 flex flex-wrap items-center gap-2">
          <span class="lencana-angka"><?= te('kira.soalan_n', ['n' => $n]) ?></span>
          <span class="lencana-angka"><?= te('subjek.tahap', ['a' => min($lv), 'b' => max($lv)]) ?></span>
        </div>
        <div class="mt-6 pt-5 border-t border-line flex items-center gap-4">
          <span class="flex-1 min-w-0">
            <span class="flex items-baseline justify-between gap-3">
              <span data-kemajuan class="text-xs text-muted truncate"><?= te('js.lat.belum') ?></span>
              <span data-kemajuan-angka class="font-mono text-xs font-semibold tabular-nums text-ink-2">0/<?= $n ?></span>
            </span>
            <span class="trek mt-2"><span data-kemajuan-bar class="block h-full w-0 rounded-full bg-accent"></span></span>
          </span>
          <span class="shrink-0 inline-flex items-center gap-1.5 text-sm font-semibold text-accent"><?= te('subjek.set.buka') ?><span class="transition-transform group-hover:translate-x-1"><?= Papar::ikon('arrow-right') ?></span></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($popular): ?>
<section class="bahagian">
  <div class="bekas">
    <div class="max-w-2xl" data-muncul>
      <p class="eyebrow-aksen"><?= sprintf('%02d', ++$bil) ?> · <?= te('kira.fungsi') ?></p>
      <h2 class="tajuk-bahagian"><?= te('subjek.popular') ?></h2>
      <p class="mt-4 text-ink-2 text-[1.0625rem] leading-relaxed"><?= te('subjek.popular.ringkas') ?></p>
    </div>
    <div class="mt-10 flex flex-wrap gap-2.5" data-muncul>
      <?php foreach ($popular as $f): ?>
      <a href="<?= Laluan::subjek('excel', 'reference') ?>#fn-<?= $e(strtolower(str_replace('.', '-', $f['name']))) ?>" class="cip cip-aksen font-mono"><?= $e($f['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<div class="<?= $popular ? 'pb-16 sm:pb-24' : 'py-16 sm:py-24' ?>"><?php require __DIR__ . '/_cta-tutor.php'; ?></div>
