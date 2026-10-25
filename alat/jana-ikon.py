#!/usr/bin/env python3
"""Jana app/src/Ikon.php dari lucide-static (tailwind/node_modules, versi dipin dalam tailwind/package.json, lesen ISC).
Hanya ikon dalam SENARAI dimasukkan (laman tidak memuat 2000 ikon). Ikon baharu = tambah nama di sini → jalankan semula.
Nama ini juga = nilai sah untuk medan `ikon`/`icon` dalam kandungan (alat/semak-kandungan.php menyemaknya)."""
import pathlib, re, sys

SENARAI = """
keyboard table table-2 calculator sigma search filter calendar type hash link triangle-alert circle-check circle-x
file-text file-spreadsheet presentation layout-template layers image list list-ordered mail printer save share-2 palette
ruler columns-3 rows-3 grid-3x3 percent banknote lightbulb book-open book-marked graduation-cap message-square
message-circle play arrow-right arrow-left arrow-up-right chevron-right chevron-down chevron-left check x copy
external-link menu sun moon monitor languages globe user clock target braces scissors clipboard undo-2 align-left
align-justify bold heading pilcrow mouse-pointer-click timer video mic move group eye pen-line download upload
refresh-cw shuffle info circle-help settings log-out chart-column trending-up database code binary cpu box archive
square-function variable send paperclip trash-2 rotate-ccw notebook-pen flask-conical text-cursor-input spell-check
file-down file-up lock sparkle arrow-up list-checks square-check-big repeat arrow-down-up between-horizontal-start
""".split()

SINI = pathlib.Path(__file__).resolve().parent
SUMBER = SINI.parent / "tailwind" / "node_modules" / "lucide-static" / "icons"
if not SUMBER.is_dir():
    sys.exit(f"tiada {SUMBER} — jalankan tailwind/bina.sh dahulu (npm i)")
baris = []
for n in SENARAI:
    svg = (SUMBER / f"{n}.svg").read_text()
    dalam = re.search(r"<svg[^>]*>(.*)</svg>", svg, re.S).group(1)
    dalam = re.sub(r"\s*\n\s*", "", dalam).replace(" />", "/>").replace("'", "\\'")
    baris.append(f"        '{n}' => '{dalam}',")
php = """<?php

declare(strict_types=1);

/**
 * Ikon Lucide (ISC, https://lucide.dev) — DIJANA oleh alat/jana-ikon.py; jangan sunting tangan.
 * Ikon::svg('keyboard', 'w-4 h-4') → <svg> sebaris (currentColor, aria-hidden).
 */
final class Ikon
{
    private const P = [
%s
    ];

    public static function ada(string $n): bool
    {
        return isset(self::P[$n]);
    }

    public static function svg(string $n, string $kelas = 'w-4 h-4'): string
    {
        $p = self::P[$n] ?? self::P['circle-help'];
        return '<svg class="' . htmlspecialchars($kelas) . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
            . ' stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $p . '</svg>';
    }

    /** @return list<string> */
    public static function semua(): array
    {
        return array_keys(self::P);
    }
}
""" % "\n".join(baris)
(SINI.parent / "app" / "src" / "Ikon.php").write_text(php)
print(len(SENARAI), "ikon →", SINI.parent / "app" / "src" / "Ikon.php")
