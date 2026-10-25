<?php

declare(strict_types=1);

/**
 * Kandungan setiap subjek (docs/PELAN.md): benih dalam git config/pek/<subjek>/[en/]<jenis>.php; suntingan admin dalam
 * data/kandungan/<subjek>/[en/]<jenis>.json MENANG. BM dan EN berasingan sepenuhnya (docs/REVAMP.md). Setiap simpanan merekod keadaan SEBELUM dalam sejarah/ (pulih = satu klik).
 * Jenis: 'latihan' (array set → soalan), 'prompt' (rentetan arahan AI tutor); 'utama', 'cheatsheet', 'rujukan' = teks halaman
 * subjek (benih sahaja buat masa ini — diubah dalam config/pek, belum dari admin).
 */
final class Kandungan
{
    public const JENIS = ['latihan', 'prompt', 'utama', 'cheatsheet', 'rujukan', 'formula', 'playground'];
    private const SEJARAH_SIMPAN = 60;

    public static function akar(): string
    {
        return __DIR__ . '/../data/kandungan';
    }

    /** @return mixed */
    public static function baca(string $subjek, string $jenis, string $bahasa = 'ms')
    {
        self::semakNama($subjek, $jenis);
        $f = self::akar() . '/' . self::rel($subjek, $bahasa) . "/$jenis.json";
        if (is_file($f)) {
            $d = json_decode((string) file_get_contents($f), true);
            if ($d !== null) {
                return $d;
            }
            error_log("[Kandungan] $f rosak — guna benih");
        }
        return self::benih($subjek, $jenis, $bahasa);
    }

    /** @return mixed */
    public static function benih(string $subjek, string $jenis, string $bahasa = 'ms')
    {
        self::semakNama($subjek, $jenis);
        $f = __DIR__ . '/../config/pek/' . self::rel($subjek, $bahasa) . "/$jenis.php";
        $d = is_file($f) ? require $f : ($jenis === 'prompt' ? '' : []);
        // Fail EN bentuk TINDIH ('_tindih' => true): hanya medan yang berbeza, digabung atas benih BM (jawapan, grid,
        // id tidak disalin — tidak boleh hanyut antara bahasa). Soalan latihan dipadan ikut id.
        if (is_array($d) && !empty($d['_tindih'])) {
            unset($d['_tindih']);
            $d = self::tindih(self::benih($subjek, $jenis, 'ms'), $d);
        }
        return $d;
    }

    public static function disunting(string $subjek, string $jenis, string $bahasa = 'ms'): bool
    {
        return is_file(self::akar() . '/' . self::rel($subjek, $bahasa) . "/$jenis.json");
    }

    /** Simpan (atomik, dikunci) + rekod keadaan sebelum dalam sejarah dengan label tindakan. */
    public static function simpan(string $subjek, string $jenis, $data, string $label, string $bahasa = 'ms'): void
    {
        self::semakNama($subjek, $jenis);
        self::denganKunci(fn() => self::simpanTerus($subjek, $jenis, $data, $label, $bahasa));
    }

    /**
     * Baca → ubah → simpan dalam SATU kunci: dua peranti yang menyimpan serentak tidak saling menindih
     * (diukur: 30 tambahan serentak dari 2 sesi tanpa ini = 18 tersimpan). $fn(data) → [data baharu|null, label].
     */
    public static function ubah(string $subjek, string $jenis, callable $fn, string $bahasa = 'ms'): void
    {
        self::semakNama($subjek, $jenis);
        self::denganKunci(function () use ($subjek, $jenis, $fn, $bahasa) {
            [$baru, $label] = $fn(self::baca($subjek, $jenis, $bahasa));
            if ($baru !== null) {
                self::simpanTerus($subjek, $jenis, $baru, $label, $bahasa);
            }
        });
    }

    private static function simpanTerus(string $subjek, string $jenis, $data, string $label, string $bahasa = 'ms'): void
    {
        (function () use ($subjek, $jenis, $data, $label, $bahasa) {
            $dir = self::akar() . '/' . self::rel($subjek, $bahasa);
            if (!is_dir("$dir/sejarah")) {
                mkdir("$dir/sejarah", 0700, true);
            }
            $sebelum = json_encode(self::baca($subjek, $jenis, $bahasa), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            // mikrosaat dalam nama: dua perubahan dalam saat yang sama mesti tersusun ikut masa (pulih memilih rekod yang betul)
            $nama = date('Ymd-His') . '-' . sprintf('%06d', (int) (fmod(microtime(true), 1) * 1e6)) . bin2hex(random_bytes(1)) . "-$jenis.json";
            file_put_contents("$dir/sejarah/$nama", json_encode(['label' => mb_substr($label, 0, 120), 'masa' => time(),
                'data' => json_decode((string) $sebelum, true)], JSON_UNESCAPED_UNICODE));
            $lama = glob("$dir/sejarah/*.json") ?: [];
            sort($lama);
            foreach (array_slice($lama, 0, max(0, count($lama) - self::SEJARAH_SIMPAN)) as $f) {
                @unlink($f);
            }
            self::tulisAtomik("$dir/$jenis.json", (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        })();
    }

    /** @return array<int,array{fail:string,label:string,masa:int,jenis:string}> terbaru dahulu */
    public static function sejarah(string $subjek, int $had = 15, string $bahasa = 'ms'): array
    {
        $out = [];
        $fail = glob(self::akar() . '/' . self::rel($subjek, $bahasa) . '/sejarah/*.json') ?: [];
        rsort($fail);
        foreach (array_slice($fail, 0, $had) as $f) {
            $d = json_decode((string) file_get_contents($f), true);
            if (is_array($d) && preg_match('/-([a-z]+)\.json$/', $f, $m)) {
                $out[] = ['fail' => basename($f), 'label' => (string) ($d['label'] ?? ''), 'masa' => (int) ($d['masa'] ?? 0), 'jenis' => $m[1]];
            }
        }
        return $out;
    }

    /** Pulihkan keadaan yang direkod dalam satu fail sejarah (keadaan semasa pula masuk sejarah — pulih boleh diundur). */
    public static function pulih(string $subjek, string $fail, string $bahasa = 'ms'): string
    {
        if (!preg_match('/^\d{8}-\d{6}-\d{6}[0-9a-f]{2}-([a-z]+)\.json$/', $fail, $m) || !in_array($m[1], self::JENIS, true)) {
            throw new InvalidArgumentException('Rekod sejarah tidak sah.');
        }
        $d = json_decode((string) @file_get_contents(self::akar() . '/' . self::rel($subjek, $bahasa) . "/sejarah/$fail"), true);
        if (!is_array($d) || !array_key_exists('data', $d)) {
            throw new InvalidArgumentException('Rekod sejarah tidak dijumpai.');
        }
        self::denganKunci(fn() => self::simpanTerus($subjek, $m[1], $d['data'], 'Pulih: ' . ($d['label'] ?? ''), $bahasa));
        return (string) ($d['label'] ?? '');
    }

    private static function tindih(array $asas, array $atas): array
    {
        foreach ($atas as $k => $v) {
            if ($k === 'questions' && is_array($v) && isset($asas['questions'])) {
                foreach ($asas['questions'] as $i => $q) {
                    if (isset($v[$q['id']])) {
                        $asas['questions'][$i] = array_replace($q, $v[$q['id']]);
                    }
                }
            } elseif (is_array($v) && isset($asas[$k]) && is_array($asas[$k]) && !array_is_list($v)) {
                $asas[$k] = self::tindih($asas[$k], $v);
            } else {
                $asas[$k] = $v;
            }
        }
        return $asas;
    }

    /** Folder relatif: BM = '<subjek>', EN = '<subjek>/en' (kandungan dan suntingan admin setiap bahasa berasingan). */
    private static function rel(string $subjek, string $bahasa): string
    {
        if (!in_array($bahasa, ['ms', 'en'], true)) {
            throw new InvalidArgumentException('Bahasa kandungan tidak sah.');
        }
        return $bahasa === 'en' ? "$subjek/en" : $subjek;
    }

    private static function semakNama(string $subjek, string $jenis): void
    {
        if (!preg_match('/^[a-z]{2,20}$/', $subjek) || !in_array($jenis, self::JENIS, true)) {
            throw new InvalidArgumentException('Subjek/jenis kandungan tidak sah.');
        }
    }

    private static function denganKunci(callable $fn): void
    {
        if (!is_dir(self::akar())) {
            mkdir(self::akar(), 0700, true);
        }
        $fp = fopen(self::akar() . '/.kunci', 'c');
        flock($fp, LOCK_EX);
        try {
            $fn();
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    public static function tulisAtomik(string $f, string $isi): void
    {
        $tmp = dirname($f) . '/.' . basename($f) . '.tmp' . bin2hex(random_bytes(3));
        if (file_put_contents($tmp, $isi) === false || !rename($tmp, $f)) {
            @unlink($tmp);
            throw new RuntimeException('Gagal menyimpan kandungan.');
        }
    }
}
