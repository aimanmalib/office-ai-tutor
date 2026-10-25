<?php

declare(strict_types=1);

/**
 * Sesi, log masuk dan token borang untuk /admin.php.
 * - Kata laluan: ADMIN_PASSWORD_HASH (password_hash) dalam config/.env; ADMIN_PASSWORD teks biasa hanya sandaran lama.
 * - Had cubaan: 10 kata laluan salah / 15 minit setiap IP (IP sebenar: ClientIp).
 * - Token borang (CSRF): subdomain lain pada domain yang sama dikira "same-site", jadi SameSite=Lax sahaja tidak melindungi.
 */
final class AdminAuth
{
    private const MAX_FAILS = 10;
    private const FAIL_WINDOW = 900;

    public static function startSession(string $dir): void
    {
        if (!is_dir($dir)) {
            @mkdir($dir, 0700, true);
        }
        if (is_dir($dir) && is_writable($dir)) {
            session_save_path($dir);   // bukan /tmp kongsi pelayan
        }
        session_name('excel_admin');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'secure' => Env::get('APP_ENV') !== 'local',
            'samesite' => 'Lax',
        ]);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.gc_maxlifetime', '28800');
        session_start();
    }

    /** @return string|null  null = berjaya; selain itu mesej ralat untuk borang */
    public static function login(string $password, string $attemptDir): ?string
    {
        if (!is_dir($attemptDir)) {
            @mkdir($attemptDir, 0700, true);
        }
        $ip = ClientIp::get();
        $fails = new RateLimit($attemptDir . '/' . RateLimit::bucket($ip) . '.json', self::FAIL_WINDOW);
        // Slot diambil SEBELUM kata laluan disemak (atomik, flock): cubaan serentak tidak boleh semuanya lulus
        // semakan "masih ada baki" sebelum ada yang mencatat kegagalan. Berjaya = slot dipulangkan.
        $slot = $fails->take(self::MAX_FAILS, time());
        if ($slot === null) {
            return 'Terlalu banyak cubaan. Cuba lagi dalam 15 minit.';
        }
        if (self::passwordMatches($password)) {
            $fails->refund($slot);
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            return null;
        }
        return 'Kata laluan salah.';
    }

    private static function passwordMatches(string $password): bool
    {
        $hash = (string) Env::get('ADMIN_PASSWORD_HASH', '');
        if ($hash !== '') {
            return password_verify($password, $hash);
        }
        $plain = (string) Env::get('ADMIN_PASSWORD', '');
        return $plain !== '' && $plain !== 'changeme' && hash_equals($plain, $password);
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function csrfValid(string $token): bool
    {
        return !empty($_SESSION['csrf']) && $token !== '' && hash_equals($_SESSION['csrf'], $token);
    }

    /**
     * Tukar kata laluan dari panel: kata laluan semasa wajib betul, baharu ≥ 12 aksara.
     * Hash ditulis ke .env secara atomik (fail sementara dalam folder sama + rename, kekal 0600);
     * ADMIN_PASSWORD teks biasa lama dibuang. Sesi lain tidak dilog keluar (satu pentadbir).
     * @return string|null  null = berjaya; selain itu mesej ralat
     */
    public static function tukarKataLaluan(string $semasa, string $baru, string $ulang, string $envPath): ?string
    {
        if (!self::passwordMatches($semasa)) {
            return 'Kata laluan semasa salah.';
        }
        if (mb_strlen($baru) < 12) {
            return 'Kata laluan baharu mesti sekurang-kurangnya 12 aksara (frasa beberapa perkataan lebih mudah diingat).';
        }
        if ($baru !== $ulang) {
            return 'Kata laluan baharu dan ulangannya tidak sama.';
        }
        if (hash_equals($semasa, $baru)) {
            return 'Kata laluan baharu sama dengan yang lama.';
        }
        $baris = is_file($envPath) ? file($envPath, FILE_IGNORE_NEW_LINES) : false;
        if ($baris === false) {
            return 'Fail .env tidak boleh dibaca.';
        }
        $hash = password_hash($baru, PASSWORD_DEFAULT);
        $keluar = [];
        $ditulis = false;
        foreach ($baris as $b) {
            $k = trim(explode('=', $b, 2)[0]);
            if ($k === 'ADMIN_PASSWORD') {
                continue;
            }
            if ($k === 'ADMIN_PASSWORD_HASH') {
                if (!$ditulis) {
                    $keluar[] = "ADMIN_PASSWORD_HASH='" . $hash . "'";
                    $ditulis = true;
                }
                continue;
            }
            $keluar[] = $b;
        }
        if (!$ditulis) {
            $keluar[] = "ADMIN_PASSWORD_HASH='" . $hash . "'";
        }
        $tmp = dirname($envPath) . '/.env.tukar-' . bin2hex(random_bytes(4));
        $ok = @file_put_contents($tmp, implode("\n", $keluar) . "\n", LOCK_EX) !== false
            && @chmod($tmp, 0600) && @rename($tmp, $envPath);
        if (!$ok) {
            @unlink($tmp);
            return 'Gagal menulis .env (kebenaran fail). Tiada apa yang berubah.';
        }
        return null;
    }
}
