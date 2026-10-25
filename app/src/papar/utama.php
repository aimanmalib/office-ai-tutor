<?php
/**
 * Laman utama (arah "buku kerja yang hidup", docs/REKA.md): wira = tetingkap hamparan hidup (formula ditaip, sel hasil
 * berkelip sekali) di atas cahaya tiga subjek, angka dikira dari kandungan, jubin subjek tiga warna, cara bernombor, pengajar.
 */
$e = [Papar::class, 'e'];
$bhs = Bahasa::kod();
$laman = require __DIR__ . '/../../config/laman.php';
$tajuk = '';
$huraian = t('utama.meta');
$widget = false;
$hidup = array_filter(Subjek::semua(), fn($x, $id) => Subjek::hidup($id), ARRAY_FILTER_USE_BOTH);
$pg = Kandungan::benih('excel', 'playground', $bhs) ?: Kandungan::benih('excel', 'playground');
$demo = reset($pg);
$data = ['demo' => ['grid' => array_slice($demo['grid'], 0, 8), 'formula' => $demo['cadangan'][2] ?? $demo['cadangan'][0]]];
$skrip = ['/assets/vendor/formula.min.js', '/assets/js/formula-enjin.js', '/assets/js/demo.js'];
$pj = $laman['pengajar'];
$kira = array_map(fn($sid) => Papar::kiraan($sid, $bhs), array_combine(array_keys($hidup), array_keys($hidup)));
// tajuk: nama setiap subjek dalam warna subjeknya (teks dari config/bahasa, nama dari config/subjek.php)
$h1 = te('utama.tajuk');
foreach ($hidup as $sid => $si) {
    $n = $e($si['nama']);
    $h1 = (string) preg_replace('/(?<![\w>])' . preg_quote($n, '/') . '(?!\w)/u', '<span data-subjek="' . $sid . '" class="text-accent">' . $n . '</span>', $h1, 1);
}
$angka = array_filter([
    ['utama.angka.fungsi', $kira['excel']['rujukan'] ?? 0],
    ['utama.angka.latihan', array_sum(array_column($kira, 'latihan'))],
    ['utama.angka.pintasan', array_sum(array_column($kira, 'pintasan'))],
    ['utama.angka.bahasa', 2],
], fn($a) => $a[1] > 0);
?>
<section class="relative overflow-hidden border-b border-line">
  <div class="absolute inset-0 -z-10 [background-image:linear-gradient(rgb(var(--line)/.6)_1px,transparent_1px),linear-gradient(90deg,rgb(var(--line)/.6)_1px,transparent_1px)] [background-size:40px_40px] [mask-image:radial-gradient(ellipse_70%_60%_at_70%_40%,black_20%,transparent_75%)]" aria-hidden="true"></div>
  <div class="bekas pt-14 pb-20 sm:pt-20 sm:pb-28 lg:pt-24 lg:pb-32 grid lg:grid-cols-[1.02fr_1fr] gap-14 lg:gap-16 items-center">
    <div class="min-w-0">
      <p class="inline-flex items-center gap-3 h-8 pl-1.5 pr-3.5 rounded-full border border-line bg-surface shadow-kad text-[0.8125rem] text-ink-2">
        <span class="flex -space-x-1">
          <?php foreach ($hidup as $sid => $si): ?><span data-subjek="<?= $sid ?>" class="jubin w-5 h-5 rounded-full ring-2 ring-surface"><?= Papar::ikon($si['ikon'], 'w-3 h-3') ?></span><?php endforeach; ?>
        </span>
        <?= $e(implode(' · ', array_column($hidup, 'nama'))) ?>
      </p>
      <h1 class="text-display mt-6 max-w-[16ch] text-balance"><?= $h1 ?></h1>
      <p class="mt-6 text-[1.0625rem] sm:text-lg text-ink-2 max-w-[50ch] leading-relaxed"><?= te('utama.ringkas') ?></p>
      <div class="mt-9 flex flex-col sm:flex-row gap-3">
        <a href="<?= Laluan::subjek('excel') ?>" class="btn btn-lg btn-utama"><?= te('utama.cta') ?><?= Papar::ikon('arrow-right') ?></a>
        <a href="#subjek" class="btn btn-lg btn-kedua"><?= te('utama.cta2') ?></a>
      </div>
      <dl class="mt-12 grid grid-cols-4 gap-x-3 sm:gap-x-6 max-w-xl">
        <?php foreach ($angka as $i => [$k, $n]): ?>
        <div class="min-w-0 flex flex-col-reverse justify-end <?= $i ? 'border-l border-line pl-3 sm:pl-6' : '' ?>">
          <dt class="mt-1.5 text-xs text-muted leading-snug"><?= te($k) ?></dt>
          <dd class="angka"><?= Bahasa::nombor($n) ?></dd>
        </div>
        <?php endforeach; ?>
      </dl>
    </div>

    <div class="relative min-w-0">
      <div class="absolute -inset-x-10 -inset-y-16 -z-10 pointer-events-none" aria-hidden="true">
        <div data-subjek="excel" class="cahaya absolute inset-0"></div>
        <div data-subjek="word" class="cahaya absolute -top-6 right-0 w-1/2 h-1/2"></div>
        <div data-subjek="powerpoint" class="cahaya absolute bottom-0 left-0 w-1/2 h-1/2"></div>
      </div>
      <figure class="tingkap" data-subjek="excel" aria-labelledby="demo-tajuk">
        <div class="tingkap-bar">
          <span class="tingkap-titik" aria-hidden="true"><i></i><i></i><i></i></span>
          <span id="demo-tajuk" class="flex items-center gap-2 text-[0.8125rem] font-medium min-w-0 truncate"><?= Papar::ikon('file-spreadsheet') ?><?= $e($demo['nama']) ?></span>
          <span class="ml-auto text-xs font-medium whitespace-nowrap hidden sm:inline"><?= te('utama.demo.label') ?></span>
        </div>
        <div class="flex items-stretch border-b border-line bg-surface">
          <span id="demo-sel" class="w-14 shrink-0 grid place-items-center border-r border-line font-mono text-xs text-muted">F2</span>
          <span class="px-3 grid place-items-center border-r border-line text-accent italic font-serif text-sm font-semibold" aria-hidden="true">fx</span>
          <label class="sr-only" for="demo-formula"><?= te('utama.demo.formula') ?></label>
          <input id="demo-formula" class="flex-1 min-w-0 h-12 px-3 bg-transparent font-mono text-[0.875rem] text-ink focus:outline-none" spellcheck="false" autocomplete="off" autocapitalize="off"
                 value="<?= $e($data['demo']['formula']) ?>">
        </div>
        <div class="tatal-x" tabindex="0" role="region" aria-label="<?= te('utama.demo.grid') ?>">
          <table class="hamparan w-full" id="demo-grid"></table>
        </div>
        <figcaption class="flex items-center justify-between gap-3 px-4 h-12 border-t border-line bg-surface-2/70 text-[0.8125rem]">
          <span class="flex items-center gap-2 text-muted"><span class="w-1.5 h-1.5 rounded-full bg-accent" aria-hidden="true"></span><?= te('utama.demo.hasil') ?></span>
          <output id="demo-hasil" class="font-mono text-base font-semibold text-accent tabular-nums" aria-live="polite">…</output>
        </figcaption>
      </figure>
    </div>
  </div>
</section>

<section id="subjek" class="bahagian bahagian-alt scroll-mt-16">
  <div class="bekas">
    <div class="max-w-2xl" data-muncul>
      <p class="eyebrow-aksen">01 · <?= te('nav.subjek_semua') ?></p>
      <h2 class="tajuk-bahagian"><?= te('utama.subjek.tajuk') ?></h2>
      <p class="mt-4 text-ink-2 text-[1.0625rem] leading-relaxed"><?= te('utama.subjek.ringkas') ?></p>
    </div>
    <div class="mt-12 grid gap-5 md:grid-cols-3" data-muncul>
      <?php foreach ($hidup as $sid => $si): $k = $kira[$sid]; $u = Kandungan::baca($sid, 'utama', $bhs); ?>
      <a href="<?= Laluan::subjek($sid) ?>" data-subjek="<?= $sid ?>" class="kad-timbul group min-w-0 p-6 sm:p-7 flex flex-col">
        <span class="hiasan" aria-hidden="true">
          <span class="absolute inset-x-0 top-0 h-1 bg-accent"></span>
          <span class="cahaya absolute -top-24 -right-24 w-64 h-64"></span>
          <span class="absolute -right-5 top-8 text-accent opacity-[.08]"><?= Papar::ikon($si['ikon'], 'w-32 h-32') ?></span>
        </span>
        <span class="jubin jubin-lg relative"><?= Papar::ikon($si['ikon'], 'w-6 h-6') ?></span>
        <h3 class="relative mt-6 text-[1.375rem] font-semibold tracking-tight"><?= $e($si['nama']) ?></h3>
        <p class="relative mt-2 text-[0.9375rem] text-ink-2 leading-relaxed flex-1"><?= $e($u['ringkas'] ?? '') ?></p>
        <dl class="relative mt-7 pt-5 border-t border-line grid grid-cols-3 gap-3">
          <div class="min-w-0 flex flex-col-reverse"><dt class="mt-1.5 text-xs text-muted"><?= te($sid === 'excel' ? 'kira.fungsi' : 'kira.ciri') ?></dt><dd class="angka text-[1.25rem]"><?= Bahasa::nombor($k['rujukan']) ?></dd></div>
          <div class="min-w-0 flex flex-col-reverse"><dt class="mt-1.5 text-xs text-muted"><?= te('kira.latihan') ?></dt><dd class="angka text-[1.25rem]"><?= Bahasa::nombor($k['latihan']) ?></dd></div>
          <div class="min-w-0 flex flex-col-reverse"><dt class="mt-1.5 text-xs text-muted"><?= te('kira.pintasan') ?></dt><dd class="angka text-[1.25rem]"><?= Bahasa::nombor($k['pintasan']) ?></dd></div>
        </dl>
        <span class="relative mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-accent"><?= te('utama.subjek.buka', ['subjek' => $si['nama']]) ?><span class="transition-transform group-hover:translate-x-1"><?= Papar::ikon('arrow-right') ?></span></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="bahagian">
  <div class="bekas">
    <div class="max-w-2xl" data-muncul>
      <p class="eyebrow-aksen">02 · <?= te('utama.cara.label') ?></p>
      <h2 class="tajuk-bahagian"><?= te('utama.cara.tajuk') ?></h2>
      <p class="mt-4 text-ink-2 text-[1.0625rem] leading-relaxed"><?= te('utama.cara.ringkas') ?></p>
    </div>
    <ol class="mt-12 grid gap-5 md:grid-cols-3" data-muncul>
      <?php foreach ([['book-open', 'rujuk', 'word'], ['square-check-big', 'latih', 'excel'], ['message-square', 'tanya', 'powerpoint']] as $i => [$ik, $k, $warna]): ?>
      <li class="kad-timbul min-w-0 p-6 sm:p-7 flex flex-col" data-subjek="<?= $warna ?>">
        <div class="flex items-center justify-between">
          <span class="jubin"><?= Papar::ikon($ik, 'w-5 h-5') ?></span>
          <span class="font-mono text-[2rem] font-semibold leading-none tabular-nums text-accent/25 tracking-tight" aria-hidden="true">0<?= $i + 1 ?></span>
        </div>
        <h3 class="text-h3 mt-6"><?= te("utama.cara.$k.tajuk") ?></h3>
        <p class="mt-2 text-[0.9375rem] text-ink-2 leading-relaxed flex-1"><?= te("utama.cara.$k.teks") ?></p>
        <div class="mt-6 rounded-lg bg-surface-2 border border-line p-3.5 min-h-[3.75rem] flex items-center" aria-hidden="true">
          <?php if ($k === 'rujuk'): ?>
          <div class="flex flex-wrap items-center gap-2 text-sm"><?= Papar::kekunci(['Ctrl', 'Shift', 'L']) ?><span class="text-muted"><?= te('utama.cara.rujuk.contoh') ?></span></div>
          <?php elseif ($k === 'latih'): ?>
          <div class="flex items-center gap-2 w-full min-w-0">
            <span class="font-serif italic text-accent text-sm font-semibold">fx</span>
            <span class="flex-1 min-w-0 truncate rounded-md bg-surface border border-line px-2.5 h-8 leading-8 font-mono text-[0.8125rem]">=SUM(B2:B4)</span>
            <span class="grid place-items-center w-7 h-7 rounded-full bg-ok-soft text-ok"><?= Papar::ikon('check') ?></span>
          </div>
          <?php else: ?>
          <div class="inline-flex max-w-full rounded-2xl rounded-bl-md bg-accent-soft text-accent-ink px-3.5 py-2 text-[0.8125rem]"><?= te('utama.cara.tanya.contoh') ?></div>
          <?php endif; ?>
        </div>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="bahagian bahagian-alt">
  <div class="bekas" data-muncul>
    <div class="kad-timbul p-6 sm:p-10 grid gap-8 md:grid-cols-[auto_1fr_auto] items-center">
      <span class="hiasan" aria-hidden="true">
        <span class="jalur absolute inset-x-0 top-0"><i class="jalur-x"></i><i class="jalur-w"></i><i class="jalur-p"></i></span>
        <span class="cahaya absolute -left-24 -top-24 w-72 h-72"></span>
      </span>
      <?php if (!empty($pj['foto'])): ?>
      <img src="<?= Papar::aset($pj['foto']) ?>" alt="<?= $e($pj['nama']) ?>" width="112" height="112" loading="lazy" decoding="async" class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover ring-1 ring-line shadow-kad">
      <?php endif; ?>
      <div class="relative min-w-0">
        <p class="eyebrow-aksen">03 · <?= te('utama.pengajar.label') ?></p>
        <h2 class="tajuk-bahagian mt-3"><?= $e($pj['nama']) ?></h2>
        <p class="mt-3 text-ink-2 text-[1.0625rem] leading-relaxed max-w-[60ch]"><?= $e($pj['bio'][$bhs] ?? '') ?></p>
      </div>
      <?php if (!empty($pj['whatsapp'])): ?>
      <a href="https://wa.me/<?= $e($pj['whatsapp']) ?>" target="_blank" rel="noopener" class="relative btn btn-lg btn-kedua justify-self-start md:justify-self-end"><?= Papar::ikon('message-circle') ?><?= te('utama.pengajar.hubungi') ?></a>
      <?php endif; ?>
    </div>
  </div>
</section>
