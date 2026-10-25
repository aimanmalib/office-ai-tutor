<?php

declare(strict_types=1);

/** Pendaftaran subjek: config/subjek.php (benih) + data/kandungan/status.json (pilihan terbit/draf admin). */
final class Subjek
{
    /** @return array<string,array{nama:string,ikon:string,status:string,halaman:list<string>}> */
    public static function semua(): array
    {
        static $semua = null;
        if ($semua === null) {
            $semua = require __DIR__ . '/../config/subjek.php';
            $pilihan = json_decode((string) @file_get_contents(self::failStatus()), true);
            foreach (is_array($pilihan) ? $pilihan : [] as $id => $status) {
                if (isset($semua[$id]) && in_array($status, ['hidup', 'draf'], true)) {
                    $semua[$id]['status'] = $status;
                }
            }
        }
        return $semua;
    }

    public static function sah(string $id): bool
    {
        return isset(self::semua()[$id]);
    }

    public static function hidup(string $id): bool
    {
        return (self::semua()[$id]['status'] ?? '') === 'hidup';
    }

    /** URL halaman subjek dalam bahasa semasa (src/Laluan.php). Nama halaman lama (rujukan/latihan/chat) masih diterima. */
    public static function url(string $id, string $halaman = '', ?string $bahasa = null): string
    {
        require_once __DIR__ . '/Laluan.php';
        $halaman = ['rujukan' => 'reference', 'latihan' => 'practice', 'chat' => 'tutor'][$halaman] ?? $halaman;
        return Laluan::subjek($id, $halaman, $bahasa);
    }

    /**
     * Boleh dilihat oleh permintaan ini? Draf = admin yang log masuk sahaja (pratonton). Sesi hanya dibuka bila kuki
     * admin ADA — pelawat biasa tidak pernah mendapat fail sesi.
     */
    public static function bolehLihat(string $id): bool
    {
        if (!self::sah($id)) {
            return false;
        }
        if (self::hidup($id)) {
            return true;
        }
        if (!isset($_COOKIE['excel_admin'])) {
            return false;
        }
        $bukaDiSini = session_status() !== PHP_SESSION_ACTIVE;
        if ($bukaDiSini) {
            require_once __DIR__ . '/Env.php';
            require_once __DIR__ . '/AdminAuth.php';
            Env::load(__DIR__ . '/../config/.env');
            AdminAuth::startSession(__DIR__ . '/../data/sessions');
        }
        $admin = !empty($_SESSION['admin']);
        if ($bukaDiSini) {
            session_write_close();   // jangan pegang kunci sesi sepanjang panggilan AI (tab admin lain tergantung)
        }
        return $admin;
    }

    /** Tetapkan status (admin). */
    public static function tetapStatus(string $id, string $status): void
    {
        if (!self::sah($id) || $id === 'excel' || !in_array($status, ['hidup', 'draf'], true)) {
            throw new InvalidArgumentException('Status subjek tidak sah.');
        }
        $f = self::failStatus();
        if (!is_dir(dirname($f))) {
            mkdir(dirname($f), 0700, true);
        }
        $semasa = json_decode((string) @file_get_contents($f), true);
        $semasa = is_array($semasa) ? $semasa : [];
        $semasa[$id] = $status;
        $tmp = $f . '.tmp' . bin2hex(random_bytes(3));
        file_put_contents($tmp, json_encode($semasa));
        rename($tmp, $f);
    }

    public static function failStatus(): string
    {
        return __DIR__ . '/../data/kandungan/status.json';
    }
}
