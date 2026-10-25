<?php
/**
 * Admin → Kandungan (Fasa 2, docs/PELAN.md): soalan latihan (tambah/ubah/buang/susun, import JSON dari AI), set latihan,
 * arahan AI tutor, Sejarah. Tindakan POST = src/admin/kandungan-aksi.php. Teks pengguna disimpan; kod tidak disentuh.
 */
$subjekK = (string) ($_GET['subjek'] ?? 'excel');
$subjekK = Subjek::sah($subjekK) ? $subjekK : 'excel';
$infoK = Subjek::semua()[$subjekK];
// Bahasa kandungan yang disunting (BM dan EN berasingan sepenuhnya, docs/REVAMP.md)
$bahasaK = ($_GET['bahasa'] ?? 'ms') === 'en' ? 'en' : 'ms';
$latihanK = Kandungan::baca($subjekK, 'latihan', $bahasaK);
// Ikon set latihan = nama Lucide (laman awam); senarai pilihan ringkas
$ikonSet = ['list-checks', 'book-open', 'chart-column', 'keyboard', 'file-text', 'layout-template', 'sigma', 'search', 'calendar', 'type',
    'banknote', 'table', 'square-function', 'filter', 'presentation', 'palette', 'layers', 'mail', 'image', 'graduation-cap'];
$bukaSet = (string) ($_GET['set'] ?? '');
$ubahId = (string) ($_GET['ubah'] ?? '');
$tambah = isset($_GET['tambah']);
$borang = $_SESSION['borang'] ?? null;   // isian yang ditolak (kekal sekali)
unset($_SESSION['borang']);
// Mesej ralat dipapar DI SEBELAH borang yang ditolak (di telefon, mesej di atas halaman berada 5000 px di luar skrin)
$ralatBorang = function (string $aksi, ?string $set = null) use ($borang, $flash): void {
    if (($flash['type'] ?? '') === 'error' && ($borang['action'] ?? '') === $aksi && ($set === null || ($borang['set'] ?? '') === $set)) {
        echo '<p role="alert" class="px-4 py-3 rounded-2xl bg-rose-50 text-rose-800 border border-rose-200 text-sm">' . htmlspecialchars($flash['msg']) . '</p>';
    }
};
$sub = '<input type="hidden" name="subjek" value="' . htmlspecialchars($subjekK) . '"><input type="hidden" name="bahasa" value="' . $bahasaK . '">';
$qsK = 'subjek=' . rawurlencode($subjekK) . '&amp;bahasa=' . $bahasaK . '&amp;';
$tok = htmlspecialchars(AdminAuth::csrfToken());
$h = fn($s) => htmlspecialchars((string) $s);
$kelasInput = 'w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white border-2 border-gray-100 focus:border-brand-purple outline-none text-sm transition';
$kelasLabel = 'block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5';
$kelasKecil = 'px-2.5 py-1.5 rounded-lg text-xs font-semibold transition';
$pilihIkon = function (string $semasa) use ($ikonSet, $h, $kelasInput): string {
    $o = '';
    foreach (array_unique(array_merge(Ikon::ada($semasa) ? [$semasa] : [], $ikonSet)) as $n) {
        $o .= '<option value="' . $h($n) . '"' . ($n === $semasa ? ' selected' : '') . '>' . $h($n) . '</option>';
    }
    return '<select name="ikon" aria-label="Ikon set" class="' . $kelasInput . ' cursor-pointer">' . $o . '</select>';
};
$modLabel = ['formula' => 'Formula', 'result' => 'Jawapan', 'choice' => 'Pilihan', 'pintasan' => 'Pintasan'];

$borangSoalan = function (string $setId, ?array $q) use ($borang, $tok, $h, $kelasInput, $kelasLabel, $ralatBorang, $sub, $qsK, $subjekK, $infoK): void {
    // Soalan baharu: Excel = formula; Word/PowerPoint = pilihan (tiada formula untuk ditulis)
    $v = ($borang['action'] ?? '') === 'kandungan_soalan_simpan' ? $borang
        : ($q ? Latihan::keBorang($q) : ['mod' => $subjekK === 'excel' ? 'formula' : 'choice', 'tahap' => 1, 'ikon' => '']);
    $mod = $v['mod'] ?? 'formula';
    ?>
    <form method="POST" id="borang-soalan" class="mt-4 p-4 sm:p-5 rounded-2xl bg-purple-50/60 border border-purple-100 space-y-4 scroll-mt-24">
        <input type="hidden" name="_token" value="<?= $tok ?>">
        <input type="hidden" name="action" value="kandungan_soalan_simpan"><?= $sub ?>
        <input type="hidden" name="set" value="<?= $h($setId) ?>">
        <input type="hidden" name="id" value="<?= $h($q['id'] ?? '') ?>">
        <p class="font-bold text-gray-800 text-sm"><?= $q ? 'Ubah soalan' : 'Soalan baharu' ?></p>
        <?php $ralatBorang('kandungan_soalan_simpan', $setId); ?>
        <div>
            <label class="<?= $kelasLabel ?>" for="f-soalan">Soalan</label>
            <textarea id="f-soalan" name="soalan" rows="3" required class="<?= $kelasInput ?>"><?= $h($v['soalan'] ?? '') ?></textarea>
            <p class="text-[11px] text-gray-500 mt-1">Boleh guna &lt;strong&gt;tebal&lt;/strong&gt; dan &lt;code&gt;formula&lt;/code&gt;.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <label class="<?= $kelasLabel ?>" for="f-mod">Jenis jawapan</label>
                <select id="f-mod" name="mod" class="<?= $kelasInput ?> cursor-pointer" data-mod>
                    <option value="formula" <?= $mod === 'formula' ? 'selected' : '' ?>>Tulis formula</option>
                    <option value="result" <?= $mod === 'result' ? 'selected' : '' ?>>Tulis jawapan (nombor/teks)</option>
                    <option value="choice" <?= $mod === 'choice' ? 'selected' : '' ?>>Pilih satu jawapan</option>
                    <option value="pintasan" <?= $mod === 'pintasan' ? 'selected' : '' ?>>⌨️ Tekan pintasan (shortcut)</option>
                </select>
            </div>
            <div>
                <label class="<?= $kelasLabel ?>" for="f-tahap">Tahap</label>
                <select id="f-tahap" name="tahap" class="<?= $kelasInput ?> cursor-pointer">
                    <?php foreach ([1 => '● Mudah', 2 => '●● Sederhana', 3 => '●●● Sukar'] as $n => $t): ?>
                        <option value="<?= $n ?>" <?= (int) ($v['tahap'] ?? 1) === $n ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="ikon" value="<?= $h($v['ikon'] ?? '') ?>">
        </div>
        <div>
            <label class="<?= $kelasLabel ?>" for="f-kategori">Kategori</label>
            <input id="f-kategori" name="kategori" maxlength="40" value="<?= $h($v['kategori'] ?? '') ?>" placeholder="cth Lookup, Aggregation" class="<?= $kelasInput ?>">
        </div>
        <div data-untuk="formula result choice" class="<?= $mod === 'pintasan' ? 'hidden' : '' ?> space-y-4">
        <div>
            <label class="<?= $kelasLabel ?>" for="f-grid">Data jadual (pilihan)</label>
            <textarea id="f-grid" name="grid" rows="4" class="<?= $kelasInput ?> font-mono" placeholder="Bulan | Revenue&#10;Jan | 1200&#10;Feb | 1500&#10;Total | ?"><?= $h($v['grid'] ?? '') ?></textarea>
            <p class="text-[11px] text-gray-500 mt-1">Satu baris = satu baris Excel (baris 1, 2, 3…). Pisahkan sel dengan <code>|</code>. Maksimum 20 × 8.</p>
        </div>
        <div>
            <label class="<?= $kelasLabel ?>" for="f-sel">Sel jawapan (pilihan)</label>
            <input id="f-sel" name="sel" maxlength="3" value="<?= $h($v['sel'] ?? '') ?>" placeholder="cth B5" class="<?= $kelasInput ?> font-mono uppercase">
        </div>
        </div>
        <div data-untuk="formula result pintasan" class="<?= $mod === 'choice' ? 'hidden' : '' ?>">
            <label class="<?= $kelasLabel ?>" for="f-jawapan">Jawapan yang diterima</label>
            <textarea id="f-jawapan" name="jawapan" rows="3" class="<?= $kelasInput ?> font-mono" placeholder="=SUM(B2:B4)&#10;=B2+B3+B4"><?= $h($v['jawapan'] ?? '') ?></textarea>
            <p class="text-[11px] text-gray-500 mt-1">Satu jawapan satu baris; yang pertama = jawapan contoh. Huruf besar/kecil, ruang, <code>;</code> dan <code>$</code> diterima sendiri (kecuali soalan yang menguji <code>$</code>). Pintasan: tulis cth <code>Ctrl+Alt+1</code> (⌘ Mac diterima sendiri).</p>
            <details class="mt-2">
                <summary class="cursor-pointer text-[11px] text-gray-500 hover:text-brand-purple font-semibold">Corak lanjutan (regex)</summary>
                <textarea name="corak" rows="2" class="<?= $kelasInput ?> font-mono mt-2"><?= $h($v['corak'] ?? '') ?></textarea>
            </details>
        </div>
        <div data-untuk="choice" class="<?= $mod === 'choice' ? '' : 'hidden' ?> grid sm:grid-cols-[1fr_8rem] gap-3">
            <div>
                <label class="<?= $kelasLabel ?>" for="f-pilihan">Pilihan (satu baris satu)</label>
                <textarea id="f-pilihan" name="pilihan" rows="4" class="<?= $kelasInput ?>"><?= $h($v['pilihan'] ?? '') ?></textarea>
            </div>
            <div>
                <label class="<?= $kelasLabel ?>" for="f-betul">Jawapan betul</label>
                <input id="f-betul" name="betul" type="number" min="1" max="6" value="<?= $h($v['betul'] ?? '') ?>" placeholder="1" class="<?= $kelasInput ?>">
                <p class="text-[11px] text-gray-500 mt-1">Nombor baris pilihan.</p>
            </div>
        </div>
        <div>
            <label class="<?= $kelasLabel ?>" for="f-petunjuk">Petunjuk</label>
            <textarea id="f-petunjuk" name="petunjuk" rows="2" class="<?= $kelasInput ?>"><?= $h($v['petunjuk'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="<?= $kelasLabel ?>" for="f-penerangan">Penerangan (selepas menjawab)</label>
            <textarea id="f-penerangan" name="penerangan" rows="3" class="<?= $kelasInput ?>"><?= $h($v['penerangan'] ?? '') ?></textarea>
        </div>
        <div class="flex flex-wrap justify-end gap-2">
            <a href="/admin.php?<?= $qsK ?>set=<?= rawurlencode($setId) ?>#set-<?= $h($setId) ?>" class="px-5 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm">Batal</a>
            <button class="px-6 py-3 rounded-xl bg-gradient-to-r from-brand-purple to-brand-pink text-white font-bold text-sm shadow-sm hover:scale-[1.02] transition">Simpan soalan</button>
        </div>
    </form>
    <?php
};
?>
        <!-- Kandungan -->
        <section id="kandungan" class="bg-white rounded-3xl p-6 border border-purple-100 shadow-sm scroll-mt-24">
            <div class="flex flex-wrap items-baseline justify-between gap-2 mb-1">
                <h2 class="text-lg font-extrabold">Kandungan</h2>
                <span class="text-xs text-gray-500"><?= Kandungan::disunting($subjekK, 'latihan', $bahasaK) ? 'Latihan disunting di admin' : 'Kandungan asal' ?></span>
            </div>
            <nav class="flex flex-wrap gap-2 my-4" aria-label="Subjek">
                <?php foreach (Subjek::semua() as $sid => $si): ?>
                <a href="/admin.php?subjek=<?= $h($sid) ?>&amp;bahasa=<?= $bahasaK ?>#kandungan" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold <?= $sid === $subjekK ? 'bg-gradient-to-r from-brand-purple to-brand-pink text-white' : 'bg-gray-100 text-gray-700 hover:bg-purple-100' ?>">
                    <?= Ikon::svg($si['ikon'], 'w-4 h-4') ?><?= $h($si['nama']) ?><?= Subjek::hidup($sid) ? '' : ' · draf' ?></a>
                <?php endforeach; ?>
                <span class="ml-auto inline-flex rounded-xl bg-gray-100 p-1" role="group" aria-label="Bahasa kandungan">
                    <?php foreach (['ms' => 'BM', 'en' => 'EN'] as $kb => $lb): ?>
                    <a href="/admin.php?subjek=<?= $h($subjekK) ?>&amp;bahasa=<?= $kb ?>#kandungan" aria-current="<?= $kb === $bahasaK ? 'true' : 'false' ?>" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $kb === $bahasaK ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' ?>"><?= $lb ?></a>
                    <?php endforeach; ?>
                </span>
            </nav>
            <p class="text-xs text-gray-500 -mt-2 mb-4">Menyunting kandungan <strong><?= $bahasaK === 'en' ? 'English (/en/)' : 'Bahasa Melayu' ?></strong>. Versi BM dan EN berasingan; ubah kedua-duanya bila menambah soalan.</p>
            <?php if ($subjekK !== 'excel'): $hidupK = Subjek::hidup($subjekK); ?>
            <div class="mb-5 p-4 rounded-2xl <?= $hidupK ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' ?> flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm <?= $hidupK ? 'text-emerald-800' : 'text-amber-900' ?>"><?= $hidupK ? 'Diterbitkan: pelajar nampak ' . $h($infoK['nama']) . ' di laman.' : 'Draf: hanya admin nampak. Semak kandungan dahulu, kemudian terbitkan.' ?>
                    <a href="<?= Subjek::url($subjekK, '', $bahasaK) ?>" class="font-semibold underline">Buka <?= $hidupK ? '' : 'pratonton ' ?>→</a></p>
                <form method="POST" data-confirm="<?= $hidupK ? 'Pelajar tidak lagi nampak halaman ' . $h($infoK['nama']) . '.' : 'Halaman ' . $h($infoK['nama']) . ' (cheat sheet, rujukan, latihan, chat) kelihatan kepada semua pelajar dan Google.' ?>"
                      data-confirm-title="<?= $hidupK ? 'Sorok semula?' : 'Terbitkan ' . $h($infoK['nama']) . '?' ?>" data-confirm-ok="<?= $hidupK ? 'Ya, sorok' : 'Ya, terbitkan' ?>" data-confirm-danger="<?= $hidupK ? '1' : '0' ?>">
                    <input type="hidden" name="_token" value="<?= $tok ?>"><input type="hidden" name="action" value="kandungan_status"><?= $sub ?>
                    <input type="hidden" name="status" value="<?= $hidupK ? 'draf' : 'hidup' ?>">
                    <button class="px-5 py-3 rounded-xl font-bold text-sm <?= $hidupK ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-gradient-to-r from-brand-purple to-brand-pink text-white' ?>"><?= $hidupK ? 'Sorok (draf)' : 'Terbitkan' ?></button>
                </form>
            </div>
            <?php endif; ?>
            <p class="text-xs text-gray-500 mb-5">Latihan <?= $h($infoK['nama']) ?>: ubah soalan tanpa kod. Setiap perubahan terus kelihatan di <a href="<?= Subjek::url($subjekK, 'latihan', $bahasaK) ?>" class="text-brand-purple font-semibold hover:underline">halaman Latihan</a> dan boleh dipulih dari Sejarah di bawah.</p>

            <div class="space-y-3">
            <?php foreach ($latihanK as $setId => $set): $qs = $set['questions']; ?>
                <details id="set-<?= $h($setId) ?>" class="group rounded-2xl border border-purple-100 scroll-mt-24" <?= $bukaSet === $setId ? 'open' : '' ?>>
                    <summary class="cursor-pointer list-none flex items-center justify-between gap-3 p-4 min-h-[3rem]">
                        <span class="font-bold text-gray-800 inline-flex items-center gap-2"><?= Ikon::svg(Ikon::ada((string) $set['icon']) ? $set['icon'] : 'list-checks', 'w-4 h-4') ?><?= $h($set['name']) ?> <span class="font-normal text-xs text-gray-500">· <?= count($qs) ?> soalan</span></span>
                        <span class="text-xs text-brand-purple font-semibold group-open:hidden">Buka ▾</span>
                    </summary>
                    <div class="px-4 pb-4">
                        <ol class="divide-y divide-purple-50">
                        <?php foreach ($qs as $i => $q): ?>
                            <li id="s-<?= $h($q['id']) ?>" class="py-3 flex flex-wrap items-start gap-x-3 gap-y-2 scroll-mt-24">
                                <span class="text-xs font-mono text-gray-500 w-6 pt-0.5"><?= $i + 1 ?>.</span>
                                <span class="flex-1 min-w-[12rem] text-sm text-gray-800"><?= $h(mb_strimwidth(trim(html_entity_decode(strip_tags($q['prompt']), ENT_QUOTES, 'UTF-8')), 0, 110, '…')) ?>
                                    <span class="block text-[11px] text-gray-500 mt-0.5"><?= $modLabel[$q['mode']] ?? $h($q['mode']) ?> · <?= $h($q['category']) ?> · <?= str_repeat('●', (int) $q['level']) ?></span></span>
                                <span class="flex items-center gap-1">
                                    <?php foreach (['naik' => '▲', 'turun' => '▼'] as $arah => $simbol):
                                        $mati = ($arah === 'naik' && $i === 0) || ($arah === 'turun' && $i === count($qs) - 1); ?>
                                        <form method="POST"><input type="hidden" name="_token" value="<?= $tok ?>"><input type="hidden" name="action" value="kandungan_soalan_alih"><?= $sub ?>
                                            <input type="hidden" name="set" value="<?= $h($setId) ?>"><input type="hidden" name="id" value="<?= $h($q['id']) ?>"><input type="hidden" name="arah" value="<?= $arah ?>">
                                            <button <?= $mati ? 'disabled' : '' ?> aria-label="Alih <?= $arah ?>" class="<?= $kelasKecil ?> min-w-[2.75rem] min-h-[2.75rem] bg-gray-100 hover:bg-purple-100 text-gray-600 disabled:opacity-30"><?= $simbol ?></button></form>
                                    <?php endforeach; ?>
                                    <a href="/admin.php?<?= $qsK ?>set=<?= rawurlencode($setId) ?>&amp;ubah=<?= rawurlencode($q['id']) ?>#borang-soalan" class="<?= $kelasKecil ?> min-h-[2.75rem] inline-flex items-center bg-purple-100 text-brand-purple hover:bg-purple-200">Ubah</a>
                                    <form method="POST" data-confirm="Soalan ini dibuang dari latihan. Boleh dipulih dari Sejarah." data-confirm-title="Buang soalan?" data-confirm-ok="Ya, buang">
                                        <input type="hidden" name="_token" value="<?= $tok ?>"><input type="hidden" name="action" value="kandungan_soalan_buang"><?= $sub ?>
                                        <input type="hidden" name="set" value="<?= $h($setId) ?>"><input type="hidden" name="id" value="<?= $h($q['id']) ?>">
                                        <button class="<?= $kelasKecil ?> min-h-[2.75rem] bg-rose-50 text-rose-600 hover:bg-rose-100">Buang</button></form>
                                </span>
                                <?php if ($bukaSet === $setId && $ubahId === $q['id']) { echo '<div class="w-full">'; $borangSoalan($setId, $q); echo '</div>'; } ?>
                            </li>
                        <?php endforeach; ?>
                        </ol>
                        <?php if ($bukaSet === $setId && $tambah): $borangSoalan($setId, null); else: ?>
                            <a href="/admin.php?<?= $qsK ?>set=<?= rawurlencode($setId) ?>&amp;tambah=1#borang-soalan" class="mt-3 inline-flex items-center px-4 py-3 rounded-xl bg-gradient-to-r from-brand-purple to-brand-pink text-white font-bold text-sm shadow-sm">Tambah soalan</a>
                        <?php endif; ?>

                        <?php $ralatSet = ($borang['action'] ?? '') === 'kandungan_set_simpan' && ($borang['set'] ?? '') === $setId; ?>
                        <details class="mt-4" <?= $ralatSet ? 'open' : '' ?>>
                            <summary class="cursor-pointer text-xs text-gray-500 hover:text-brand-purple font-semibold py-3">Ubah set ini</summary>
                            <?php $ralatBorang('kandungan_set_simpan', $setId); ?>
                            <?php $vs = ($borang && ($borang['action'] ?? '') === 'kandungan_set_simpan' && ($borang['set'] ?? '') === $setId) ? $borang
                                : ['nama' => $set['name'], 'ikon' => $set['icon'], 'tagline' => $set['tagline'], 'penerangan' => $set['description'], 'warna' => $set['color']]; ?>
                            <form method="POST" class="mt-3 grid sm:grid-cols-2 gap-3">
                                <input type="hidden" name="_token" value="<?= $tok ?>"><input type="hidden" name="action" value="kandungan_set_simpan"><?= $sub ?><input type="hidden" name="set" value="<?= $h($setId) ?>">
                                <input name="nama" maxlength="40" value="<?= $h($vs['nama']) ?>" aria-label="Nama set" class="<?= $kelasInput ?>">
                                <?= $pilihIkon((string) $vs['ikon']) ?>
                                <input name="tagline" maxlength="80" value="<?= $h($vs['tagline']) ?>" aria-label="Tagline" class="<?= $kelasInput ?> sm:col-span-2">
                                <textarea name="penerangan" rows="2" maxlength="300" aria-label="Penerangan set" class="<?= $kelasInput ?> sm:col-span-2"><?= $h($vs['penerangan']) ?></textarea>
                                <select name="warna" aria-label="Warna" class="<?= $kelasInput ?> cursor-pointer">
                                    <option value="purple" <?= $vs['warna'] === 'purple' ? 'selected' : '' ?>>Ungu</option>
                                    <option value="amber" <?= $vs['warna'] === 'amber' ? 'selected' : '' ?>>Oren</option>
                                </select>
                                <button class="px-5 py-3 rounded-xl bg-gradient-to-r from-brand-purple to-brand-pink text-white font-bold text-sm">Simpan set</button>
                            </form>
                            <?php if (!$qs): ?>
                            <form method="POST" class="mt-2" data-confirm="Set kosong ini dibuang." data-confirm-title="Buang set?" data-confirm-ok="Ya, buang">
                                <input type="hidden" name="_token" value="<?= $tok ?>"><input type="hidden" name="action" value="kandungan_set_buang"><?= $sub ?><input type="hidden" name="set" value="<?= $h($setId) ?>">
                                <button class="<?= $kelasKecil ?> bg-rose-50 text-rose-600 hover:bg-rose-100">Buang set kosong ini</button>
                            </form>
                            <?php endif; ?>
                        </details>
                    </div>
                </details>
            <?php endforeach; ?>
            </div>

            <?php $vb = ($borang['action'] ?? '') === 'kandungan_set_simpan' && ($borang['set'] ?? '') === '' ? $borang : []; ?>
            <details id="set-baru" class="mt-4 rounded-2xl border border-purple-100 p-4 scroll-mt-24" <?= $vb ? 'open' : '' ?>>
                <summary class="cursor-pointer font-bold text-sm text-gray-800 py-2">Set latihan baharu</summary>
                <?php $ralatBorang('kandungan_set_simpan', ''); ?>
                <form method="POST" class="mt-3 grid sm:grid-cols-2 gap-3">
                    <input type="hidden" name="_token" value="<?= $tok ?>"><input type="hidden" name="action" value="kandungan_set_simpan"><?= $sub ?><input type="hidden" name="set" value="">
                    <input name="nama" maxlength="40" required placeholder="Nama set (cth Pivot Table)" aria-label="Nama set" value="<?= $h($vb['nama'] ?? '') ?>" class="<?= $kelasInput ?>">
                    <?= $pilihIkon((string) ($vb['ikon'] ?? 'list-checks')) ?>
                    <input name="tagline" maxlength="80" placeholder="Tagline ringkas" aria-label="Tagline" value="<?= $h($vb['tagline'] ?? '') ?>" class="<?= $kelasInput ?> sm:col-span-2">
                    <textarea name="penerangan" rows="2" maxlength="300" placeholder="Penerangan" aria-label="Penerangan set" class="<?= $kelasInput ?> sm:col-span-2"><?= $h($vb['penerangan'] ?? '') ?></textarea>
                    <select name="warna" aria-label="Warna" class="<?= $kelasInput ?> cursor-pointer"><option value="purple">Ungu</option><option value="amber">Oren</option></select>
                    <button class="px-5 py-3 rounded-xl bg-gradient-to-r from-brand-purple to-brand-pink text-white font-bold text-sm">Cipta set</button>
                </form>
            </details>

            <details id="import-json" class="mt-3 rounded-2xl border border-purple-100 p-4 scroll-mt-24" <?= ($borang['action'] ?? '') === 'kandungan_json' ? 'open' : '' ?>>
                <summary class="cursor-pointer font-bold text-sm text-gray-800 py-2">Tambah banyak soalan dari AI (tampal JSON)</summary>
                <?php $ralatBorang('kandungan_json'); ?>
                <p class="text-xs text-gray-600 mt-3 mb-2">1. Salin arahan di bawah ke ChatGPT/Gemini/Claude, tukar topik + bilangan. 2. Tampal jawapannya di kotak kedua. Setiap soalan disemak dahulu : jika satu salah, tiada yang disimpan dan nombor soalan itu dipaparkan.</p>
                <?php if ($subjekK === 'excel'): ?>
                <textarea readonly rows="7" onclick="this.select()" aria-label="Arahan untuk AI" class="<?= $kelasInput ?> font-mono text-[11px]">Jana 10 soalan latihan Microsoft Excel tentang [TOPIK] untuk pelajar kelas Excel, dalam <?= $bahasaK === 'en' ? 'English' : 'Bahasa Melayu Malaysia' ?> (istilah Excel kekal English). Balas dengan JSON SAHAJA, tanpa teks lain, dalam bentuk:
[{"soalan": "Tulis formula untuk ... (boleh guna <strong> dan <code>)", "mod": "formula", "kategori": "Lookup", "tahap": 2,
  "grid": [["Bulan","Revenue"],["Jan",1200],["Feb",1500],["Total","?"]], "sel": "B4",
  "jawapan": ["=SUM(B2:B3)", "=B2+B3"], "petunjuk": "...", "penerangan": "..."},
 {"soalan": "...", "mod": "choice", "pilihan": ["A", "B", "C", "D"], "betul": 2, "kategori": "...", "tahap": 1, "petunjuk": "...", "penerangan": "..."}]
Peraturan: "mod" = "formula" (pelajar tulis formula), "result" (pelajar tulis nombor/teks), "choice" (pilih satu; "betul" = nombor pilihan bermula 1) atau "pintasan" (pelajar tekan shortcut; "jawapan" cth ["Ctrl+B"]; elak Ctrl+N/T/W). "jawapan" = SEMUA bentuk formula yang betul. "grid" baris pertama = tajuk, rujukan sel mesti sepadan dengan grid (baris 1 = tajuk). "sel" = sel yang pelajar isi. "tahap" 1–3.</textarea>
                <?php else: ?>
                <textarea readonly rows="7" onclick="this.select()" aria-label="Arahan untuk AI" class="<?= $kelasInput ?> font-mono text-[11px]">Jana 10 soalan latihan Microsoft <?= $h($infoK['nama']) ?> (Microsoft 365, Windows) tentang [TOPIK] untuk pelajar kelas <?= $h($infoK['nama']) ?>, dalam <?= $bahasaK === 'en' ? 'English' : 'Bahasa Melayu Malaysia' ?> (istilah menu <?= $h($infoK['nama']) ?> kekal English, cth Insert → Table of Contents). Balas dengan JSON SAHAJA, tanpa teks lain, dalam bentuk:
[{"soalan": "Anda mahu ... Menu mana yang betul? (boleh guna <strong> dan <code>)", "mod": "choice", "pilihan": ["Home → ...", "Insert → ...", "Layout → ...", "View → ..."], "betul": 2,
  "kategori": "Format", "tahap": 1, "petunjuk": "...", "penerangan": "..."},
 {"soalan": "Tekan pintasan untuk ...", "mod": "pintasan", "jawapan": ["Ctrl+B"], "kategori": "Pintasan", "tahap": 1, "petunjuk": "...", "penerangan": "..."}]
Peraturan: "mod" = "choice" (pilih satu; "betul" = nombor pilihan bermula 1, 3–4 pilihan yang munasabah) atau "pintasan" (pelajar tekan shortcut; "jawapan" = pintasan Windows yang PASTI wujud dalam <?= $h($infoK['nama']) ?>, cth ["Ctrl+B"]; elak Ctrl+N/T/W kerana pelayar mengambilnya). Jangan reka nama menu atau pintasan — kalau tidak pasti, buat soalan "choice". "tahap" 1–3.</textarea>
                <?php endif; ?>
                <form method="POST" class="mt-3 space-y-3">
                    <input type="hidden" name="_token" value="<?= $tok ?>"><input type="hidden" name="action" value="kandungan_json"><?= $sub ?>
                    <select name="set" aria-label="Set sasaran" class="<?= $kelasInput ?> cursor-pointer">
                        <?php foreach ($latihanK as $sid => $s): ?><option value="<?= $h($sid) ?>" <?= ($borang['set'] ?? $bukaSet) === $sid ? 'selected' : '' ?>>Tambah ke: <?= $h('' . $s['name']) ?></option><?php endforeach; ?>
                    </select>
                    <textarea name="json" rows="6" required placeholder="Tampal jawapan AI di sini" aria-label="JSON dari AI" class="<?= $kelasInput ?> font-mono text-xs"><?= $h(($borang['action'] ?? '') === 'kandungan_json' ? ($borang['json'] ?? '') : '') ?></textarea>
                    <div class="flex justify-end"><button class="px-6 py-3 rounded-xl bg-gradient-to-r from-brand-purple to-brand-pink text-white font-bold text-sm">Semak &amp; tambah</button></div>
                </form>
            </details>

            <details id="arahan-ai" class="mt-3 rounded-2xl border border-purple-100 p-4 scroll-mt-24" <?= ($borang['action'] ?? '') === 'kandungan_prompt' ? 'open' : '' ?>>
                <summary class="cursor-pointer font-bold text-sm text-gray-800 py-2">Arahan AI tutor</summary>
                <?php $ralatBorang('kandungan_prompt'); ?>
                <p class="text-xs text-gray-600 mt-3 mb-2">Cara AI menjawab pelajar: bahasa, gaya, skop, topik kelas semasa. Terpakai pada soalan seterusnya.</p>
                <form method="POST" class="space-y-3">
                    <input type="hidden" name="_token" value="<?= $tok ?>"><input type="hidden" name="action" value="kandungan_prompt"><?= $sub ?>
                    <textarea name="prompt" rows="12" maxlength="6000" aria-label="Arahan AI tutor" class="<?= $kelasInput ?> text-xs leading-relaxed"><?= $h(($borang['action'] ?? '') === 'kandungan_prompt' ? ($borang['prompt'] ?? '') : Kandungan::baca($subjekK, 'prompt', $bahasaK)) ?></textarea>
                    <div class="flex justify-end"><button class="px-6 py-3 rounded-xl bg-gradient-to-r from-brand-purple to-brand-pink text-white font-bold text-sm">Simpan arahan</button></div>
                </form>
            </details>

            <details id="sejarah" class="mt-3 rounded-2xl border border-purple-100 p-4 scroll-mt-24">
                <summary class="cursor-pointer font-bold text-sm text-gray-800 py-2">Sejarah perubahan</summary>
                <?php $sej = Kandungan::sejarah($subjekK, 15, $bahasaK); if (!$sej): ?>
                    <p class="text-xs text-gray-500 mt-3">Belum ada perubahan.</p>
                <?php else: ?>
                <ul class="mt-3 divide-y divide-purple-50">
                    <?php foreach ($sej as $r): ?>
                    <li class="py-2 flex flex-wrap items-center justify-between gap-2 text-sm">
                        <span class="min-w-0"><?= $h($r['label']) ?> <span class="block text-[11px] text-gray-500"><?= date('d M Y, H:i', $r['masa']) ?></span></span>
                        <form method="POST" data-confirm="Kandungan dikembalikan kepada keadaan SEBELUM perubahan ini. Keadaan sekarang disimpan dalam Sejarah juga." data-confirm-title="Pulih?" data-confirm-ok="Ya, pulih" data-confirm-danger="0">
                            <input type="hidden" name="_token" value="<?= $tok ?>"><input type="hidden" name="action" value="kandungan_pulih"><?= $sub ?><input type="hidden" name="fail" value="<?= $h($r['fail']) ?>">
                            <button class="<?= $kelasKecil ?> min-h-[2.75rem] bg-gray-100 hover:bg-purple-100 text-gray-700">↩ Pulih ke sebelum ini</button>
                        </form>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </details>
        </section>
        <script>
            document.querySelectorAll('[data-mod]').forEach(function (sel) {
                var form = sel.closest('form');
                function tukar() {
                    form.querySelectorAll('[data-untuk]').forEach(function (el) {
                        el.classList.toggle('hidden', el.dataset.untuk.split(' ').indexOf(sel.value) < 0);
                    });
                }
                sel.addEventListener('change', tukar);
            });
        </script>
