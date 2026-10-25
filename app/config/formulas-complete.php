<?php

declare(strict_types=1);

/**
 * Complete Excel function reference — 500+ functions.
 *
 * Per-entry fields:
 *   name     — function name
 *   cat      — category: math, stat, text, date, lookup, logic, info, finance, engr, db, cube, web, dyn, compat
 *   syntax   — function syntax (Microsoft official)
 *   desc     — description in Bahasa Melayu (English technical terms preserved)
 *   example  — example formula (optional but recommended)
 *   sample   — sample data / context (optional)
 *   usecase  — practical use case (optional)
 *   version  — Excel version introduced: 2007, 2010, 2013, 2016, 2019, 2021, 365, legacy
 *   popular  — boolean — mark for ⭐ badge on UI (~50 most-used)
 *
 * Data dipecah ikut kategori dalam config/formulas/<cat>.php; susunan di bawah = susunan paparan.
 */

$senarai = [];
foreach (['math', 'stat', 'text', 'date', 'lookup', 'logic', 'info', 'finance', 'engr', 'db', 'cube', 'web', 'compat'] as $cat) {
    array_push($senarai, ...require __DIR__ . '/formulas/' . $cat . '.php');
}
return $senarai;
