<?php
/**
 * Admin → Statistik pelajar (30 hari). Dimuat oleh public/admin.php selepas log masuk. Tanpa identiti pelajar (src/Stats.php).
 * @var Stats $statsObj
 */
$st = $statsObj->ringkasan(30);
$jumlahHalaman = array_sum($st['halaman_harian']);
$maxHari = $st['halaman_harian'] ? max($st['halaman_harian']) : 1;
$soalanLatihan = [];
foreach (Subjek::semua() as $sid => $_) {
    foreach (Kandungan::baca($sid, 'latihan') as $setId => $set) {
        foreach ($set['questions'] as $q) {
            $soalanLatihan[$sid . '/' . $setId . '/' . $q['id']] = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($q['prompt']))));
        }
    }
}
$senarai = function (array $baris, string $kosong, callable $label, callable $angka): void {
    if (!$baris) {
        echo '<p class="text-sm text-gray-500">' . htmlspecialchars($kosong) . '</p>';
        return;
    }
    echo '<ul class="space-y-1.5">';
    foreach ($baris as $b) {
        echo '<li class="flex items-start justify-between gap-3 text-sm"><span class="min-w-0 break-words">' . $label($b)
            . '</span><span class="flex-shrink-0 text-xs font-mono text-gray-500">' . htmlspecialchars($angka($b)) . '</span></li>';
    }
    echo '</ul>';
};
?>
        <!-- Statistik pelajar -->
        <section id="statistik" class="bg-white rounded-3xl p-6 border border-purple-100 shadow-sm scroll-mt-24">
            <div class="flex flex-wrap items-baseline justify-between gap-2 mb-1">
                <h2 class="text-lg font-extrabold">Statistik pelajar · 30 hari</h2>
                <span class="text-xs text-gray-500"><?= number_format($jumlahHalaman) ?> paparan halaman</span>
            </div>
            <p class="text-xs text-gray-500 mb-5">Tanpa nama, IP atau kuki pelajar. Bot tidak dikira. Teks soalan AI dipadam selepas 60 hari.</p>

            <?php if ($st['halaman_harian']): ?>
            <div class="flex items-end gap-1 h-24 mb-1" aria-label="Paparan halaman sehari">
                <?php foreach ($st['halaman_harian'] as $d => $n): ?>
                    <div class="flex-1 bg-ink/70 rounded-t min-w-[3px] max-w-[2.5rem]" style="height: <?= max(4, round($n / $maxHari * 100)) ?>%" title="<?= htmlspecialchars($d) ?>: <?= $n ?>"></div>
                <?php endforeach; ?>
            </div>
            <div class="flex justify-between text-[10px] font-mono text-gray-500 mb-6">
                <span><?= htmlspecialchars(array_key_first($st['halaman_harian'])) ?></span><span><?= htmlspecialchars(array_key_last($st['halaman_harian'])) ?></span>
            </div>
            <?php endif; ?>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">Halaman paling dibuka</h3>
                    <?php $senarai($st['halaman_teratas'], 'Belum ada data — statistik bermula 23 Okt 2026.', fn($b) => '<code class="font-mono text-xs">' . htmlspecialchars($b[0]) . '</code>', fn($b) => number_format((int) $b[1])); ?>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">Dicari tetapi tiada hasil</h3>
                    <?php $senarai($st['cari_tiada'], 'Tiada — setiap carian jumpa sesuatu.', fn($b) => htmlspecialchars($b[0]), fn($b) => $b[1] . '×'); ?>
                    <p class="text-[11px] text-gray-500 mt-2">Calon formula/topik untuk ditambah atau diajar.</p>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">Carian paling kerap</h3>
                    <?php $senarai($st['cari_teratas'], 'Belum ada carian.', fn($b) => htmlspecialchars($b[0]), fn($b) => $b[1] . '×'); ?>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">Soalan latihan paling kerap salah <span class="font-normal text-gray-500">· % cubaan salah · bil. cubaan</span></h3>
                    <?php $senarai($st['latihan_sukar'], 'Belum cukup jawapan (minimum 3 cubaan satu soalan).',
                        fn($b) => htmlspecialchars(mb_strimwidth($soalanLatihan[$b[0]] ?? $b[0], 0, 90, '…')),
                        fn($b) => round(100 * $b[2] / max(1, $b[1])) . '% · ' . $b[1]); ?>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-bold text-gray-700 mb-2">Soalan terkini kepada AI
                    <span class="font-normal text-gray-500">· <?= $st['chat']['ok'] ?> dijawab<?= $st['chat']['gagal'] ? ' · ' . $st['chat']['gagal'] . ' gagal' : '' ?></span></h3>
                <?php $senarai($st['soalan'], 'Belum ada soalan.', fn($b) => htmlspecialchars($b[1]), fn($b) => date('d M H:i', (int) $b[0])); ?>
            </div>
        </section>
