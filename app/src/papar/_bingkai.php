<?php
/**
 * Bingkai setiap halaman awam: head (SEO, hreflang, tema tanpa kelip), nav, kaki. Dipanggil oleh Papar::halaman.
 * Tiada backdrop-filter pada header: ia menjadikan header blok pengandung position:fixed dan memerangkap menu telefon.
 */
$e = [Papar::class, 'e'];
$laman = require __DIR__ . '/../../config/laman.php';
$bhs = Bahasa::kod();
$subjekInfo = $subjekId ? Subjek::semua()[$subjekId] : null;
$jenama = $laman['jenama'];
$tajukPenuh = $tajuk !== '' ? "$tajuk · $jenama" : $jenama;
$laluanTanpaBahasa = $subjekId === null && ($halaman ?? '') === '' ? '/' : (string) preg_replace('~^/en(?=/)~', '', (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH));
$urlBahasa = fn(string $b) => Bahasa::awalan($b) . $laluanTanpaBahasa;
$kanonik = $laman['url'] . $urlBahasa($bhs);
$ogFail = $subjekId && is_file(__DIR__ . "/../../public/og/$subjekId-$bhs.jpg") ? "/og/$subjekId-$bhs.jpg" : "/og/utama-$bhs.jpg";
$ogImej = $laman['url'] . Papar::aset($ogFail);
$labelHal = fn(string $hal) => t($hal === 'reference' ? ($subjekId === 'excel' ? 'hal.reference.excel' : 'hal.reference.ciri') : "hal.$hal");
$subjekHidup = array_filter(Subjek::semua(), fn($x, $id) => Subjek::hidup($id) || $id === $subjekId, ARRAY_FILTER_USE_BOTH);
$draf = $subjekId && !Subjek::hidup($subjekId);
$teksSubjek = $subjekId ? Kandungan::baca($subjekId, 'utama', $bhs) : [];
?><!DOCTYPE html>
<html lang="<?= $bhs ?>" data-subjek="<?= $e($subjekId ?? 'utama') ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $e($tajukPenuh) ?></title>
<meta name="description" content="<?= $e($huraian) ?>">
<?php if ($noindex || $draf): ?><meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<link rel="canonical" href="<?= $e($kanonik) ?>">
<link rel="alternate" hreflang="ms" href="<?= $e($laman['url'] . $urlBahasa('ms')) ?>">
<link rel="alternate" hreflang="en" href="<?= $e($laman['url'] . $urlBahasa('en')) ?>">
<link rel="alternate" hreflang="x-default" href="<?= $e($laman['url'] . $urlBahasa('ms')) ?>">
<meta name="theme-color" content="<?= $e($laman['warna_tema']['cerah']) ?>" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="<?= $e($laman['warna_tema']['gelap']) ?>" media="(prefers-color-scheme: dark)">
<link rel="icon" type="image/svg+xml" href="<?= Papar::aset('/favicon.svg') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= Papar::aset('/favicon-32.png') ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?= Papar::aset('/apple-touch-icon.png') ?>">
<link rel="manifest" href="/site.webmanifest">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= $e($jenama) ?>">
<meta property="og:locale" content="<?= Bahasa::LOKAL[$bhs] ?>">
<meta property="og:locale:alternate" content="<?= Bahasa::LOKAL[Bahasa::lain()] ?>">
<meta property="og:title" content="<?= $e($tajukPenuh) ?>">
<meta property="og:description" content="<?= $e($huraian) ?>">
<meta property="og:url" content="<?= $e($kanonik) ?>">
<meta property="og:image" content="<?= $e($ogImej) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?= $e($subjekInfo ? t('og.alt.subjek', ['subjek' => $subjekInfo['nama']]) : t('og.alt.utama')) ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="preload" href="/assets/fonts/geist.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= Papar::aset('/assets/css/tw.css') ?>">
<script>
// tema sebelum lukisan pertama (tiada kelip putih dalam mod gelap): pilihan pengguna → sistem.
// html.gerak = bahagian muncul semasa tatal (tapak.js); hanya bila IO ada + tiada prefers-reduced-motion, dan dibuang
// selepas 4 s jika tapak.js tidak berjalan (kandungan tidak boleh kekal tersorok).
(function () { var t, h = document.documentElement; try { t = localStorage.getItem('tema'); } catch (e) {}
  if (t !== 'cerah' && t !== 'gelap') t = matchMedia('(prefers-color-scheme: dark)').matches ? 'gelap' : 'cerah';
  h.setAttribute('data-tema', t);
  if ('IntersectionObserver' in window && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    h.classList.add('gerak'); setTimeout(function () { if (!window.__gerak) h.classList.remove('gerak'); }, 4000); } })();
window.LAMAN = <?= json_encode([
    'bahasa' => $bhs, 'subjek' => $subjekId, 'halaman' => $halaman,
    'nama' => $subjekInfo['nama'] ?? '', 'url_tutor' => $subjekId ? Laluan::subjek($subjekId, 'tutor') : '',
    'chat' => $teksSubjek['chat'] ?? null,
    'T' => array_filter(Bahasa::senarai(), fn($k) => str_starts_with($k, 'js.'), ARRAY_FILTER_USE_KEY),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
window.IKON = <?= json_encode(array_combine($ikonJs = ['check', 'lightbulb', 'message-square', 'arrow-right', 'triangle-alert', 'circle-check', 'circle-x', 'rotate-ccw', 'paperclip', 'sparkle', 'x'], array_map(fn($n) => Ikon::svg($n, ''), $ikonJs)), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;
<?php if ($data): ?>window.DATA = <?= json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
<?php endif; ?>
</script>
<script src="<?= Papar::aset('/assets/js/tapak.js') ?>" defer></script>
<script src="<?= Papar::aset('/assets/js/stat.js') ?>" defer></script>
<?php foreach ($skrip as $s): ?><script src="<?= Papar::aset($s) ?>" defer></script>
<?php endforeach; ?>
</head>
<body class="min-h-screen flex flex-col">
<a href="#isi" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] btn btn-utama"><?= te('nav.langkau') ?></a>

<header class="sticky top-0 z-40 bg-bg border-b border-line" data-cetak="sorok">
  <?php if ($subjekInfo): ?><div class="h-[3px] bg-accent" aria-hidden="true"></div>
  <?php else: ?><div class="jalur" aria-hidden="true"><i class="jalur-x"></i><i class="jalur-w"></i><i class="jalur-p"></i></div><?php endif; ?>
  <div class="bekas h-16 flex items-center gap-3">
    <a href="<?= Laluan::utama() ?>" class="flex items-center gap-2.5 shrink-0 rounded-md -ml-1 px-1 py-1" aria-label="<?= $e($jenama) ?>">
      <?php require __DIR__ . '/_logo.php'; ?>
      <span class="font-semibold tracking-tight text-[0.9375rem] <?= $subjekId ? 'hidden sm:inline' : '' ?>"><?= $e($jenama) ?></span>
    </a>
    <?php if ($subjekInfo): ?>
    <span class="text-line-2 select-none <?= $subjekId ? 'hidden sm:inline' : '' ?>" aria-hidden="true">/</span>
    <details class="relative" id="pilih-subjek">
      <summary class="list-none cursor-pointer flex items-center gap-1.5 h-9 px-2 rounded-md hover:bg-ink/5 font-medium text-[0.9375rem]">
        <span class="jubin w-7 h-7 rounded-md"><?= Papar::ikon($subjekInfo['ikon'], 'w-4 h-4') ?></span>
        <?= $e($subjekInfo['nama']) ?>
        <?= Papar::ikon('chevron-down', 'w-3.5 h-3.5 text-muted') ?>
      </summary>
      <div class="absolute left-0 mt-2 w-64 kad-timbul shadow-tingkap p-1.5 z-50" role="menu">
        <?php foreach ($subjekHidup as $sid => $si): ?>
        <a role="menuitem" href="<?= Laluan::subjek($sid) ?>" data-subjek="<?= $sid ?>" class="flex items-center gap-3 px-2.5 py-2 rounded-md hover:bg-surface-2 <?= $sid === $subjekId ? 'bg-surface-2' : '' ?>">
          <span class="jubin jubin-sm"><?= Papar::ikon($si['ikon'], 'w-4 h-4') ?></span>
          <span class="text-sm font-medium"><?= $e($si['nama']) ?></span>
          <?php if ($sid === $subjekId): ?><span class="ml-auto text-accent"><?= Papar::ikon('check') ?></span><?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>
    </details>
    <nav class="hidden lg:flex items-stretch self-stretch ml-2" aria-label="<?= te('nav.subjek') ?>">
      <?php foreach (Laluan::halaman($subjekId) as $hal): if ($hal === 'tutor') continue; ?>
      <a href="<?= Laluan::subjek($subjekId, $hal) ?>" <?= $halaman === $hal ? 'aria-current="page"' : '' ?>
         class="nav-pautan"><?= $e($labelHal($hal)) ?></a>
      <?php endforeach; ?>
    </nav>
    <?php endif; ?>

    <div class="ml-auto flex items-center gap-1">
      <a href="<?= $e($urlBahasa(Bahasa::lain())) ?>" hreflang="<?= Bahasa::lain() ?>" lang="<?= Bahasa::lain() ?>"
         class="hidden sm:inline-flex btn btn-hantu btn-sm font-mono text-xs tracking-wide" title="<?= te('nav.bahasa_lain') ?>"><?= Bahasa::lain() === 'en' ? 'EN' : 'BM' ?></a>
      <button type="button" class="btn-ikon hidden sm:inline-flex" data-togol-tema aria-label="<?= te('nav.tema') ?>">
        <span class="tema-cerah"><?= Papar::ikon('moon', 'w-[1.125rem] h-[1.125rem]') ?></span>
        <span class="tema-gelap hidden"><?= Papar::ikon('sun', 'w-[1.125rem] h-[1.125rem]') ?></span>
      </button>
      <?php if ($subjekInfo && in_array('tutor', Laluan::halaman($subjekId), true)): ?>
      <a href="<?= Laluan::subjek($subjekId, 'tutor') ?>" class="hidden md:inline-flex btn btn-sm <?= $halaman === 'tutor' ? 'btn-kedua' : 'btn-utama' ?> ml-1">
        <?= Papar::ikon('message-square', 'w-4 h-4') ?><?= te('hal.tutor') ?></a>
      <?php elseif (!$subjekInfo): ?>
      <a href="<?= Laluan::subjek('excel') ?>" class="hidden md:inline-flex btn btn-sm btn-utama ml-1"><?= te('utama.cta') ?></a>
      <?php endif; ?>
      <button type="button" class="btn-ikon lg:hidden -mr-2" data-buka-menu aria-expanded="false" aria-controls="menu-telefon" aria-label="<?= te('nav.menu') ?>">
        <?= Papar::ikon('menu', 'w-5 h-5') ?>
      </button>
    </div>
  </div>
</header>

<div id="menu-telefon" class="fixed inset-0 z-50 bg-bg hidden flex-col lg:hidden" role="dialog" aria-modal="true" aria-label="<?= te('nav.menu') ?>">
  <div class="bekas h-16 flex items-center justify-between border-b border-line shrink-0">
    <a href="<?= Laluan::utama() ?>" class="flex items-center gap-2.5"><?php require __DIR__ . '/_logo.php'; ?><span class="font-semibold"><?= $e($jenama) ?></span></a>
    <button type="button" class="btn-ikon -mr-2" data-tutup-menu aria-label="<?= te('nav.tutup') ?>"><?= Papar::ikon('x', 'w-5 h-5') ?></button>
  </div>
  <div class="bekas py-6 overflow-y-auto flex-1 space-y-8">
    <?php if ($subjekInfo): ?>
    <div>
      <p class="eyebrow-aksen mb-3"><?= $e($subjekInfo['nama']) ?></p>
      <ul class="space-y-0.5">
        <?php foreach (array_merge([''], Laluan::halaman($subjekId)) as $hal): ?>
        <li><a href="<?= Laluan::subjek($subjekId, $hal) ?>" <?= $halaman === $hal ? 'aria-current="page"' : '' ?>
               class="relative flex items-center h-12 px-3 -mx-3 rounded-md text-[1.0625rem] <?= $halaman === $hal ? 'bg-accent-soft text-accent-ink font-medium before:absolute before:left-0 before:top-2.5 before:bottom-2.5 before:w-[3px] before:rounded-full before:bg-accent' : 'text-ink-2' ?>"><?= $e($hal === '' ? t('hal.gambaran') : $labelHal($hal)) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
    <div>
      <p class="eyebrow-aksen mb-3"><?= te('nav.subjek_semua') ?></p>
      <ul class="grid grid-cols-1 gap-2">
        <?php foreach ($subjekHidup as $sid => $si): ?>
        <li><a href="<?= Laluan::subjek($sid) ?>" data-subjek="<?= $sid ?>" class="kad-timbul flex items-center gap-3 p-3">
          <span class="jubin"><?= Papar::ikon($si['ikon'], 'w-5 h-5') ?></span>
          <span class="font-medium"><?= $e($si['nama']) ?></span>
          <span class="ml-auto text-accent"><?= Papar::ikon('arrow-right') ?></span></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="flex items-center gap-2">
      <a href="<?= $e($urlBahasa(Bahasa::lain())) ?>" hreflang="<?= Bahasa::lain() ?>" class="btn btn-kedua flex-1"><?= Papar::ikon('languages') ?><?= te('nav.bahasa_lain') ?></a>
      <button type="button" class="btn btn-kedua flex-1" data-togol-tema>
        <span class="tema-cerah inline-flex items-center gap-2"><?= Papar::ikon('moon') ?><?= te('nav.tema_gelap') ?></span>
        <span class="tema-gelap hidden items-center gap-2"><?= Papar::ikon('sun') ?><?= te('nav.tema_cerah') ?></span>
      </button>
    </div>
  </div>
</div>

<?php if ($draf): ?>
<div class="bg-warn-soft text-warn border-b border-line text-sm" data-cetak="sorok">
  <div class="bekas py-2"><?= te('draf.lencana') ?> <a class="pautan" href="/admin.php?subjek=<?= $e($subjekId) ?>#kandungan"><?= te('draf.terbit') ?></a></div>
</div>
<?php endif; ?>

<main id="isi" class="flex-1"><?= $badan ?></main>

<footer class="<?= $subjekId === null && ($halaman ?? '') === '' ? '' : 'mt-24' ?> bg-surface-2/60 border-t border-line" data-cetak="sorok">
  <div class="jalur" aria-hidden="true"><i class="jalur-x"></i><i class="jalur-w"></i><i class="jalur-p"></i></div>
  <div class="bekas pt-14 pb-12 grid gap-10 md:grid-cols-[1.4fr_1fr_1fr]">
    <div class="space-y-4 max-w-sm">
      <a href="<?= Laluan::utama() ?>" class="inline-flex items-center gap-2.5"><?php require __DIR__ . '/_logo.php'; ?><span class="font-semibold text-[1.0625rem] tracking-tight"><?= $e($jenama) ?></span></a>
      <p class="text-sm text-ink-2 leading-relaxed"><?= te('kaki.ringkas') ?></p>
    </div>
    <div>
      <p class="eyebrow-aksen mb-4"><?= te('nav.subjek_semua') ?></p>
      <ul class="space-y-2.5 text-sm">
        <?php foreach (array_filter(Subjek::semua(), fn($x, $id) => Subjek::hidup($id), ARRAY_FILTER_USE_BOTH) as $sid => $si): ?>
        <li><a class="group inline-flex items-center gap-2.5 text-ink-2 hover:text-ink" href="<?= Laluan::subjek($sid) ?>" data-subjek="<?= $sid ?>"><span class="jubin w-6 h-6 rounded-md"><?= Papar::ikon($si['ikon'], 'w-3.5 h-3.5') ?></span><?= $e($si['nama']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="eyebrow-aksen mb-4"><?= te('kaki.tapak') ?></p>
      <ul class="space-y-2.5 text-sm text-ink-2">
        <li><a class="hover:text-ink" href="<?= $e($urlBahasa(Bahasa::lain())) ?>" hreflang="<?= Bahasa::lain() ?>"><?= te('nav.bahasa_lain') ?></a></li>
        <?php if (!empty($laman['pengajar']['whatsapp'])): ?>
        <li><a class="hover:text-ink" href="https://wa.me/<?= $e($laman['pengajar']['whatsapp']) ?>" rel="noopener" target="_blank"><?= te('kaki.hubungi', ['nama' => $laman['pengajar']['nama']]) ?></a></li>
        <?php endif; ?>
        <?php if (!empty($laman['sumber'])): ?>
        <li><a class="hover:text-ink" href="<?= $e($laman['sumber']) ?>" rel="noopener" target="_blank"><?= te('kaki.sumber') ?></a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  <div class="border-t border-line">
    <div class="bekas py-6 flex flex-col sm:flex-row gap-3 sm:items-center justify-between text-xs text-muted">
      <p>© <?= date('Y') ?> <?= $e($laman['pemilik'] ?? $laman['pengajar']['nama']) ?>. <?= te('kaki.hak') ?></p>
      <p class="max-w-xl sm:text-right"><?= te('kaki.tanda_dagangan') ?></p>
    </div>
  </div>
</footer>

<?php // tutor terapung hanya di halaman rujukan (latihan ada butang Tanya sendiri; gambaran + tutor ada kad/halaman sendiri)
if ($widget && $subjekId && in_array($halaman, ['cheatsheet', 'formulas', 'reference'], true) && in_array('tutor', Laluan::halaman($subjekId), true)) {
    require __DIR__ . '/_widget.php';
} ?>
</body>
</html>
