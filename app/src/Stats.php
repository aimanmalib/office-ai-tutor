<?php

declare(strict_types=1);

/**
 * Statistik pengajar TANPA identiti pelajar: tiada IP, tiada kuki, tiada nama. SQLite data/statistik.db (WAL).
 * Jenis peristiwa (lajur `jenis`): halaman (kunci = laluan), cari (kunci = kata carian, nilai = bilangan hasil),
 * latihan (kunci = set/id, nilai = 1 betul / 0 salah), chat (kunci = 'ok'/'gagal', nilai = soalan dipotong 200 aksara).
 * Simpanan: peristiwa 180 hari; TEKS soalan chat dikosongkan selepas 60 hari (pelajar mungkin menaip maklumat peribadi).
 */
final class Stats
{
    public const JENIS = ['halaman', 'cari', 'latihan', 'chat'];
    private const SIMPAN_HARI = 180;
    private const SIMPAN_TEKS_HARI = 60;

    private ?PDO $db = null;

    public function __construct(private readonly string $file) {}

    private function db(): PDO
    {
        if ($this->db === null) {
            $this->db = new PDO('sqlite:' . $this->file, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $this->db->exec('PRAGMA busy_timeout = 3000; PRAGMA journal_mode = WAL;');
            $this->db->exec('CREATE TABLE IF NOT EXISTS peristiwa (id INTEGER PRIMARY KEY, masa INTEGER NOT NULL,
                jenis TEXT NOT NULL, subjek TEXT NOT NULL, kunci TEXT NOT NULL, nilai TEXT)');
            $this->db->exec('CREATE INDEX IF NOT EXISTS peristiwa_jenis_masa ON peristiwa (jenis, masa)');
        }
        return $this->db;
    }

    /** Rekod satu peristiwa. Statistik TIDAK boleh mematahkan laman: setiap ralat ditelan (dilog sahaja). */
    public function rekod(string $jenis, string $subjek, string $kunci, ?string $nilai = null): void
    {
        if (!in_array($jenis, self::JENIS, true) || ($_SERVER['HTTP_X_UJI'] ?? '') !== '' || isset($_COOKIE['excel_admin'])) {
            return;   // X-Uji: semakan deploy/pemantau; kuki admin: pengajar (termasuk pratonton draf) — bukan pelajar
        }
        try {
            $this->db()->prepare('INSERT INTO peristiwa (masa, jenis, subjek, kunci, nilai) VALUES (?, ?, ?, ?, ?)')
                ->execute([time(), $jenis, mb_substr($subjek, 0, 20), mb_substr($kunci, 0, 120), $nilai === null ? null : mb_substr($nilai, 0, 200)]);
            if (random_int(1, 200) === 1) {
                $this->pangkas();
            }
        } catch (Throwable $e) {
            error_log('[Stats] ' . $e->getMessage());
        }
    }

    /** Tukar rekod "0" terbaharu (≤ 2 minit) bagi kunci ini kepada "1" — jawapan setara yang disahkan semula. */
    public function betulkanTerakhir(string $jenis, string $subjek, string $kunci): void
    {
        if (($_SERVER['HTTP_X_UJI'] ?? '') !== '' || isset($_COOKIE['excel_admin'])) {
            return;
        }
        try {
            $this->db()->prepare("UPDATE peristiwa SET nilai = '1' WHERE id = (SELECT id FROM peristiwa WHERE jenis = ? AND subjek = ?
                AND kunci = ? AND nilai = '0' AND masa >= ? ORDER BY id DESC LIMIT 1)")->execute([$jenis, $subjek, mb_substr($kunci, 0, 120), time() - 120]);
        } catch (Throwable $e) {
            error_log('[Stats] ' . $e->getMessage());
        }
    }

    public function pangkas(): void
    {
        $db = $this->db();
        $db->prepare('DELETE FROM peristiwa WHERE masa < ?')->execute([time() - self::SIMPAN_HARI * 86400]);
        $db->prepare("UPDATE peristiwa SET nilai = NULL WHERE jenis = 'chat' AND nilai IS NOT NULL AND masa < ?")
            ->execute([time() - self::SIMPAN_TEKS_HARI * 86400]);
    }

    /**
     * Ringkasan untuk admin bagi $hari terakhir.
     * @return array{halaman_harian:array<string,int>, halaman_teratas:array<int,array{0:string,1:int}>,
     *   cari_tiada:array<int,array{0:string,1:int}>, cari_teratas:array<int,array{0:string,1:int}>,
     *   latihan_sukar:array<int,array{0:string,1:int,2:int}>, chat:array{ok:int,gagal:int}, soalan:array<int,array{0:int,1:string}>}
     */
    public function ringkasan(int $hari = 30): array
    {
        $kosong = ['halaman_harian' => [], 'halaman_teratas' => [], 'cari_tiada' => [], 'cari_teratas' => [],
                   'latihan_sukar' => [], 'chat' => ['ok' => 0, 'gagal' => 0], 'soalan' => []];
        if (!is_file($this->file)) {
            return $kosong;
        }
        try {
            $db = $this->db();
            $dari = time() - $hari * 86400;
            $q = function (string $sql, array $p = []) use ($db, $dari): array {
                $st = $db->prepare($sql);
                $st->execute([$dari, ...$p]);
                return $st->fetchAll(PDO::FETCH_NUM);
            };
            $out = $kosong;
            // Hari ikut zon waktu PHP (Asia/Kuala_Lumpur, php.ini) — 'localtime' SQLite ikut TZ kontena (UTC)
            $off = (int) (new DateTime())->getOffset();
            foreach ($q("SELECT date(masa + $off, 'unixepoch') d, COUNT(*) FROM peristiwa WHERE masa >= ? AND jenis = 'halaman' GROUP BY d ORDER BY d") as [$d, $n]) {
                $out['halaman_harian'][$d] = (int) $n;
            }
            $out['halaman_teratas'] = $q("SELECT kunci, COUNT(*) n FROM peristiwa WHERE masa >= ? AND jenis = 'halaman' GROUP BY kunci ORDER BY n DESC LIMIT 10");
            $out['cari_tiada'] = $q("SELECT kunci, COUNT(*) n FROM peristiwa WHERE masa >= ? AND jenis = 'cari' AND nilai = '0' GROUP BY kunci ORDER BY n DESC LIMIT 15");
            $out['cari_teratas'] = $q("SELECT kunci, COUNT(*) n FROM peristiwa WHERE masa >= ? AND jenis = 'cari' AND nilai <> '0' GROUP BY kunci ORDER BY n DESC LIMIT 15");
            $out['latihan_sukar'] = $q("SELECT subjek || '/' || kunci, COUNT(*) n, SUM(nilai = '0') salah FROM peristiwa WHERE masa >= ? AND jenis = 'latihan'
                GROUP BY subjek, kunci HAVING n >= 3 ORDER BY 1.0 * salah / n DESC, n DESC LIMIT 10");
            foreach ($q("SELECT kunci, COUNT(*) FROM peristiwa WHERE masa >= ? AND jenis = 'chat' GROUP BY kunci") as [$k, $n]) {
                $out['chat'][$k === 'ok' ? 'ok' : 'gagal'] = (int) $n;
            }
            $out['soalan'] = $q("SELECT masa, nilai FROM peristiwa WHERE masa >= ? AND jenis = 'chat' AND nilai IS NOT NULL ORDER BY masa DESC LIMIT 30");
            return $out;
        } catch (Throwable $e) {
            error_log('[Stats] ' . $e->getMessage());
            return $kosong;
        }
    }
}
