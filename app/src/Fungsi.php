<?php

declare(strict_types=1);

/**
 * Rujukan fungsi Excel: config/formulas/<cat>.php (data + teks BM) digabung dengan config/formulas/en/<cat>.php (teks EN
 * ikut nama). Susunan kategori = config/formulas-complete.php.
 */
final class Fungsi
{
    public const KATEGORI = ['math', 'stat', 'text', 'date', 'lookup', 'logic', 'info', 'finance', 'engr', 'db', 'cube', 'web', 'compat'];

    /** @return list<array{name:string,cat:string,syntax:string,desc:string,example?:string,sample?:string,usecase?:string,version?:string,popular?:bool}> */
    public static function semua(string $bahasa = 'ms'): array
    {
        static $cache = [];
        if (isset($cache[$bahasa])) {
            return $cache[$bahasa];
        }
        $out = [];
        foreach (self::KATEGORI as $cat) {
            $ms = require __DIR__ . "/../config/formulas/$cat.php";
            $en = $bahasa === 'en' && is_file($f = __DIR__ . "/../config/formulas/en/$cat.php") ? require $f : [];
            foreach ($ms as $r) {
                if (isset($en[$r['name']])) {
                    $r['desc'] = $en[$r['name']]['desc'] ?? $r['desc'];
                    foreach (['usecase', 'sample'] as $k) {
                        if (isset($r[$k], $en[$r['name']][$k])) {
                            $r[$k] = $en[$r['name']][$k];
                        }
                    }
                }
                $out[] = $r;
            }
        }
        return $cache[$bahasa] = $out;
    }
}
