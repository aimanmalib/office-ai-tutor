<?php

declare(strict_types=1);

/**
 * IP pelawat sebenar di belakang Cloudflare → traefik → Apache.
 * mod_remoteip menyalin CF-Connecting-IP ke REMOTE_ADDR untuk SETIAP permintaan dari traefik — termasuk yang dihantar
 * terus ke IP hos (bukan melalui Cloudflare) dengan pengepala palsu. Rakan sebenar yang traefik nampak = entri TERAKHIR
 * X-Forwarded-For: hanya bila rakan itu Cloudflare, REMOTE_ADDR boleh dipercayai; selain itu rakan itulah pelawatnya.
 * Tanpa traefik (ujian tempatan, nginx dengan real_ip) tiada XFF yang sah → REMOTE_ADDR.
 */
final class ClientIp
{
    public static function get(): string
    {
        $remote = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        $parts = explode(',', (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''));
        $peer = trim((string) end($parts));
        if (filter_var($peer, FILTER_VALIDATE_IP) && !self::isCloudflare($peer)) {
            return $peer;
        }
        return filter_var($remote, FILTER_VALIDATE_IP) ? $remote : '0.0.0.0';
    }

    public static function isCloudflare(string $ip): bool
    {
        $addr = @inet_pton($ip);
        if ($addr === false) {
            return false;
        }
        foreach (require __DIR__ . '/../config/cloudflare-ips.php' as $cidr) {
            [$net, $bits] = explode('/', $cidr, 2);
            $netAddr = @inet_pton($net);
            if ($netAddr === false || strlen($netAddr) !== strlen($addr)) {
                continue;
            }
            $bits = (int) $bits;
            $mask = str_repeat("\xff", intdiv($bits, 8))
                . ($bits % 8 ? chr((0xff << (8 - $bits % 8)) & 0xff) : '');
            $mask = str_pad($mask, strlen($addr), "\x00");
            if (($addr & $mask) === ($netAddr & $mask)) {
                return true;
            }
        }
        return false;
    }
}
