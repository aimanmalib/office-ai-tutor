<?php
/** Latihan: senarai set + kawasan soalan (assets/js/latihan.js). Jawapan (accept/correct_index) TIDAK dihantar ke pelayar. */
$e = [Papar::class, 'e'];
$bhs = Bahasa::kod();
$info = Subjek::semua()[$subjekId];
$u = Kandungan::baca($subjekId, 'utama', $bhs);
$latihan = Kandungan::baca($subjekId, 'latihan', $bhs);
$tajuk = t('lat.tajuk', ['subjek' => $info['nama']]);
$huraian = $u['alat']['practice'] ?? '';
$awam = [];
foreach ($latihan as $sid => $s) {
    $awam[$sid] = ['name' => $s['name'], 'tagline' => $s['tagline'] ?? '', 'description' => $s['description'] ?? '',
        'questions' => array_map([Latihan::class, 'awam'], $s['questions'] ?? [])];
}
$data = ['sets' => $awam];
$bergrid = (bool) array_filter(array_merge(...array_values(array_map(fn($s) => $s['questions'], $awam)) ?: [[]]), fn($q) => $q['mode'] === 'formula' && $q['grid']);
$skrip = $bergrid ? ['/assets/vendor/formula.min.js', '/assets/js/formula-enjin.js', '/assets/js/latihan.js'] : ['/assets/js/latihan.js'];
$jumlahSoalan = array_sum(array_map(fn($s) => count($s['questions']), $awam));
$kp = ['ikon' => 'list-checks', 'eyebrow' => $info['nama'], 'tajuk' => te('hal.practice'), 'intro' => $e($huraian),
    'angka' => [[count($awam), t('kira.set')], [$jumlahSoalan, t('kira.soalan')]],
    'aksi' => in_array('playground', Laluan::halaman($subjekId), true)
        ? '<a class="btn btn-kedua" href="' . Laluan::subjek($subjekId, 'playground') . '">' . Papar::ikon('flask-conical') . te('lat.ke_makmal_btn') . '</a>' : ''];
?>
<div id="lat-kepala"><?php include __DIR__ . '/_kepala-hal.php'; ?></div>

<div class="bekas pt-10 sm:pt-12 pb-16">
  <!-- senarai set (keadaan kemajuan diisi latihan.js dari localStorage) -->
  <section id="set-picker" class="grid gap-5 md:grid-cols-2" aria-label="<?= te('lat.set') ?>">
    <?php foreach ($latihan as $sid => $s): $lv = array_map(fn($q) => (int) ($q['level'] ?? 1), $s['questions'] ?? []) ?: [1]; ?>
    <button type="button" class="set-card kad-timbul boleh-tekan group min-w-0 text-left p-6 sm:p-7 flex flex-col" data-set="<?= $e($sid) ?>">
      <span class="flex items-start gap-4 w-full">
        <span class="jubin" data-jubin><?= Papar::ikon(Ikon::ada($s['icon'] ?? '') ? $s['icon'] : 'list-checks', 'w-5 h-5') ?></span>
        <span class="flex-1 min-w-0">
          <span class="block text-h3"><?= $e($s['name']) ?></span>
          <span class="block mt-1 text-[0.9375rem] text-ink-2 leading-relaxed"><?= $e($s['tagline'] ?? '') ?></span>
        </span>
      </span>
      <span class="mt-6 flex flex-wrap items-center gap-2">
        <span class="lencana-angka"><?= te('kira.soalan_n', ['n' => count($lv)]) ?></span>
        <span class="lencana-angka"><?= te('subjek.tahap', ['a' => min($lv), 'b' => max($lv)]) ?></span>
      </span>
      <span class="mt-6 pt-5 border-t border-line flex items-end gap-4 w-full">
        <span class="flex-1 min-w-0">
          <span class="flex items-baseline justify-between gap-3">
            <span data-kemajuan class="text-xs text-muted truncate"><?= te('js.lat.belum') ?></span>
            <span data-kemajuan-angka class="font-mono text-xs font-semibold tabular-nums text-ink-2">0/<?= count($lv) ?></span>
          </span>
          <span class="trek mt-2"><span data-kemajuan-bar class="block h-full w-0 rounded-full bg-accent transition-[width]"></span></span>
        </span>
        <span class="shrink-0 inline-flex items-center gap-1.5 text-sm font-semibold text-accent"><span data-cta><?= te('subjek.set.buka') ?></span><span class="transition-transform group-hover:translate-x-1"><?= Papar::ikon('arrow-right') ?></span></span>
      </span>
    </button>
    <?php endforeach; ?>
  </section>

  <!-- bar kemajuan semasa sesi (melekat di bawah header): segmen = satu soalan (betul / belum betul / semasa) -->
  <div id="progress-bar-wrap" hidden class="sticky top-16 z-30 -mx-5 px-5 sm:mx-auto sm:px-0 max-w-4xl bg-bg py-3 border-b border-line sm:border-0">
    <div class="flex items-center gap-3">
      <button id="back-to-sets" type="button" class="btn-ikon h-9 w-9 -ml-2" aria-label="<?= te('lat.ke_senarai') ?>"><?= Papar::ikon('arrow-left') ?></button>
      <span id="set-label" class="text-sm font-semibold truncate max-w-[9rem] sm:max-w-[16rem]"></span>
      <span id="progress-text" class="lencana-angka shrink-0"></span>
      <span id="progress-seg" class="segmen flex-1" aria-hidden="true"></span>
      <span class="inline-flex items-center gap-1.5 shrink-0"><span class="w-2 h-2 rounded-full bg-ok" aria-hidden="true"></span><span id="score-text" class="text-sm font-medium tabular-nums"></span></span>
    </div>
  </div>

  <!-- skrin mula -->
  <section id="welcome-screen" hidden class="kad-timbul max-w-3xl p-6 sm:p-10">
    <span class="hiasan" aria-hidden="true"><span class="absolute inset-x-0 top-0 h-1 bg-accent"></span><span class="cahaya absolute -top-40 -right-32 w-96 h-96"></span></span>
    <div class="relative">
      <div class="flex items-center gap-4">
        <span id="welcome-jubin" class="jubin jubin-lg" aria-hidden="true"></span>
        <p id="welcome-count" class="eyebrow-aksen"></p>
      </div>
      <h2 id="welcome-title" class="text-h1 tracking-[-0.03em] mt-6"></h2>
      <p id="welcome-desc" class="mt-3 text-ink-2 text-[1.0625rem] max-w-[60ch] leading-relaxed"></p>
      <div id="welcome-kemajuan" hidden class="mt-6 max-w-md"><span id="welcome-seg" class="segmen" aria-hidden="true"></span></div>
      <label class="mt-7 inline-flex items-center gap-3 cursor-pointer select-none">
        <input type="checkbox" id="random-toggle" class="peer sr-only">
        <span class="relative w-9 h-5 rounded-full bg-surface-3 border border-line-2 transition-colors peer-checked:bg-accent peer-checked:border-accent peer-focus-visible:ring-2 peer-focus-visible:ring-accent after:absolute after:top-0.5 after:left-0.5 after:w-3.5 after:h-3.5 after:rounded-full after:bg-surface after:shadow after:transition-transform peer-checked:after:translate-x-4"></span>
        <span class="text-sm"><?= te('lat.rawak') ?></span>
      </label>
      <div class="mt-8 flex flex-wrap gap-3">
        <button id="start-btn" type="button" class="btn btn-lg btn-utama"><span></span><?= Papar::ikon('arrow-right') ?></button>
        <button id="back-from-welcome" type="button" class="btn btn-lg btn-kedua"><?= te('lat.set_lain') ?></button>
      </div>
      <p class="mt-6 text-xs text-muted"><?= te('lat.disimpan') ?> <button id="reset-btn" type="button" hidden class="underline underline-offset-2 hover:text-err ml-1"><?= te('lat.reset') ?></button></p>
    </div>
  </section>

  <!-- soalan: tetingkap (bar = kategori + tahap; isi dilukis latihan.js) -->
  <section id="question-area" hidden class="mt-4 max-w-4xl mx-auto">
    <article id="question-card" class="tingkap" aria-live="polite"></article>
    <button id="quit-btn" type="button" class="mt-4 text-sm text-muted hover:text-ink"><?= te('lat.keluar') ?></button>
  </section>

  <!-- selesai -->
  <section id="completion-screen" hidden class="kad-timbul max-w-3xl p-6 sm:p-10">
    <span class="hiasan" aria-hidden="true"><span class="absolute inset-x-0 top-0 h-1 bg-accent"></span><span class="cahaya absolute -top-40 -right-32 w-96 h-96"></span></span>
    <div class="relative">
      <p class="eyebrow-aksen"><?= te('lat.selesai') ?></p>
      <div class="mt-6 flex flex-wrap items-center gap-6 sm:gap-8">
        <span class="cincin relative grid place-items-center w-28 h-28 sm:w-32 sm:h-32 shrink-0">
          <svg viewBox="0 0 36 36" class="absolute inset-0 w-full h-full -rotate-90" aria-hidden="true">
            <circle cx="18" cy="18" r="15.9155" class="text-surface-3"></circle>
            <circle id="final-cincin" cx="18" cy="18" r="15.9155" class="isi text-accent" stroke-dasharray="0 100"></circle>
          </svg>
          <span id="final-pct" class="relative font-mono font-semibold text-xl tabular-nums"></span>
        </span>
        <div class="min-w-0">
          <p class="flex items-baseline gap-2"><span id="final-score" class="text-display tabular-nums">0</span><span id="final-total" class="text-h2 text-muted tabular-nums"></span></p>
          <p id="completion-msg" class="mt-2 text-ink-2 max-w-[48ch] leading-relaxed"></p>
        </div>
      </div>
      <div id="weak-areas" hidden class="mt-10 max-w-lg">
        <h3 class="text-sm font-semibold"><?= te('lat.ulang_kaji') ?></h3>
        <ul id="weak-list" class="mt-3 space-y-3 text-sm"></ul>
        <p class="mt-4 text-sm"><a class="pautan-aksen inline-flex items-center gap-1.5" href="<?= Laluan::subjek($subjekId, 'cheatsheet') ?>"><?= Papar::ikon('list') ?><?= te('lat.buka_cs') ?></a></p>
      </div>
      <div class="mt-8 flex flex-wrap gap-3">
        <button id="restart-btn" type="button" class="btn btn-lg btn-utama"><?= Papar::ikon('rotate-ccw') ?><?= te('lat.mula_semula') ?></button>
        <button id="back-from-completion" type="button" class="btn btn-lg btn-kedua"><?= te('lat.set_lain') ?></button>
      </div>
    </div>
  </section>
</div>
