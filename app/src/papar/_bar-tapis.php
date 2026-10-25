<?php
// Bar carian + kategori melekat (desktop) untuk halaman rujukan. Perlu: $katTapis [kunci => label], $bil [kunci => n],
// $jumlah, $tempatCari. Kelakuan: assets/js/tapis.js.
?>
<div class="md:sticky md:top-16 z-30 py-3 bg-bg" data-cetak="sorok">
  <div class="flex flex-col gap-3">
    <div class="relative">
      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted"><?= Papar::ikon('search') ?></span>
      <input type="search" data-cari-input class="medan pl-9 pr-16" placeholder="<?= Papar::e($tempatCari) ?>" aria-label="<?= te('tapis.cari') ?>" autocomplete="off" spellcheck="false">
      <span class="absolute right-2.5 top-1/2 -translate-y-1/2 hidden md:inline-flex"><kbd>/</kbd></span>
    </div>
    <div class="flex items-center gap-3">
      <div class="flex gap-1.5 overflow-x-auto md:overflow-visible md:flex-wrap tanpa-bar -mx-1 px-1 flex-1 [mask-image:linear-gradient(90deg,#000_92%,transparent)] md:[mask-image:none]" role="group" aria-label="<?= te('tapis.kategori') ?>">
        <button type="button" class="cip shrink-0" data-kat="all" aria-pressed="true"><?= te('tapis.semua') ?> <span class="cip-bil" data-kat-bil><?= $jumlah ?></span></button>
        <?php foreach ($katTapis as $k => $label): if (empty($bil[$k])) continue; ?>
        <button type="button" class="cip shrink-0" data-kat="<?= Papar::e($k) ?>" aria-pressed="false"><?= Papar::f($label) ?> <span class="cip-bil" data-kat-bil><?= $bil[$k] ?></span></button>
        <?php endforeach; ?>
      </div>
      <p class="hidden sm:block text-xs text-muted tabular-nums shrink-0" data-bil aria-live="polite"></p>
    </div>
  </div>
</div>
