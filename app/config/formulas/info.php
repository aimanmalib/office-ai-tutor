<?php

declare(strict_types=1);

// Sebahagian rujukan formula lengkap; dimuat oleh config/formulas-complete.php.
return [
    // ============================================================
    // INFORMATION (~20)
    // ============================================================
    ['name'=>'CELL', 'cat'=>'info', 'syntax'=>'CELL(info_type, [reference])', 'desc'=>'Memulangkan maklumat tentang format, lokasi atau kandungan sel, mengikut jenis maklumat yang diminta.', 'example'=>'=CELL("address",A1)', 'sample'=>'"$A$1"', 'version'=>'2007'],
    ['name'=>'ERROR.TYPE', 'cat'=>'info', 'syntax'=>'ERROR.TYPE(error_val)', 'desc'=>'Memulangkan nombor yang mewakili jenis ralat (1 = <code>#NULL!</code>, 2 = <code>#DIV/0!</code>, dan seterusnya), atau <code>#N/A</code> jika tiada ralat.', 'example'=>'=ERROR.TYPE(A1)', 'version'=>'2007'],
    ['name'=>'INFO', 'cat'=>'info', 'syntax'=>'INFO(type_text)', 'desc'=>'Memulangkan maklumat tentang persekitaran operasi semasa, seperti sistem pengendalian atau bilangan lembaran kerja aktif.', 'example'=>'=INFO("system")', 'sample'=>'"pcdos"/"mac"', 'version'=>'2007'],
    ['name'=>'ISBLANK', 'cat'=>'info', 'syntax'=>'ISBLANK(value)', 'desc'=>'Memulangkan TRUE jika sel kosong.', 'example'=>'=ISBLANK(A1)', 'usecase'=>'Menyemak sama ada medan wajib dalam borang pendaftaran sudah diisi.', 'version'=>'2007', 'popular'=>true],
    ['name'=>'ISERR', 'cat'=>'info', 'syntax'=>'ISERR(value)', 'desc'=>'Memulangkan TRUE jika nilai ialah sebarang ralat kecuali <code>#N/A</code>.', 'example'=>'=ISERR(A1/B1)', 'version'=>'2007'],
    ['name'=>'ISERROR', 'cat'=>'info', 'syntax'=>'ISERROR(value)', 'desc'=>'Memulangkan TRUE jika nilai ialah sebarang jenis ralat, termasuk <code>#N/A</code>.', 'example'=>'=ISERROR(VLOOKUP(...))', 'usecase'=>'Mengesan formula yang gagal supaya mesej yang jelas dapat dipaparkan sebagai ganti ralat.', 'version'=>'2007', 'popular'=>true],
    ['name'=>'ISEVEN', 'cat'=>'info', 'syntax'=>'ISEVEN(number)', 'desc'=>'Memulangkan TRUE jika nombor ialah nombor genap.', 'example'=>'=ISEVEN(4)', 'sample'=>'TRUE', 'version'=>'2007'],
    ['name'=>'ISFORMULA', 'cat'=>'info', 'syntax'=>'ISFORMULA(reference)', 'desc'=>'Memulangkan TRUE jika sel yang dirujuk mengandungi formula.', 'example'=>'=ISFORMULA(B2)', 'version'=>'2013'],
    ['name'=>'ISLOGICAL', 'cat'=>'info', 'syntax'=>'ISLOGICAL(value)', 'desc'=>'Memulangkan TRUE jika nilai ialah nilai logik (TRUE atau FALSE).', 'example'=>'=ISLOGICAL(A1)', 'version'=>'2007'],
    ['name'=>'ISNA', 'cat'=>'info', 'syntax'=>'ISNA(value)', 'desc'=>'Memulangkan TRUE jika nilai ialah ralat <code>#N/A</code>.', 'example'=>'=ISNA(VLOOKUP(...))', 'version'=>'2007'],
    ['name'=>'ISNONTEXT', 'cat'=>'info', 'syntax'=>'ISNONTEXT(value)', 'desc'=>'Memulangkan TRUE jika nilai bukan teks, termasuk sel kosong.', 'example'=>'=ISNONTEXT(A1)', 'version'=>'2007'],
    ['name'=>'ISNUMBER', 'cat'=>'info', 'syntax'=>'ISNUMBER(value)', 'desc'=>'Memulangkan TRUE jika nilai ialah nombor.', 'example'=>'=ISNUMBER(A1)', 'usecase'=>'Memastikan lajur harga hanya mengandungi nombor sebelum dijumlahkan.', 'version'=>'2007', 'popular'=>true],
    ['name'=>'ISODD', 'cat'=>'info', 'syntax'=>'ISODD(number)', 'desc'=>'Memulangkan TRUE jika nombor ialah nombor ganjil.', 'example'=>'=ISODD(7)', 'sample'=>'TRUE', 'version'=>'2007'],
    ['name'=>'ISOMITTED', 'cat'=>'info', 'syntax'=>'ISOMITTED(argument)', 'desc'=>'Memulangkan TRUE jika argumen dalam fungsi <code>LAMBDA</code> tidak diberikan nilai.', 'example'=>'=LAMBDA(x,IF(ISOMITTED(x),0,x))', 'version'=>'365'],
    ['name'=>'ISREF', 'cat'=>'info', 'syntax'=>'ISREF(value)', 'desc'=>'Memulangkan TRUE jika nilai ialah rujukan sel atau julat.', 'example'=>'=ISREF(A1)', 'version'=>'2007'],
    ['name'=>'ISTEXT', 'cat'=>'info', 'syntax'=>'ISTEXT(value)', 'desc'=>'Memulangkan TRUE jika nilai ialah teks.', 'example'=>'=ISTEXT(A1)', 'version'=>'2007'],
    ['name'=>'N', 'cat'=>'info', 'syntax'=>'N(value)', 'desc'=>'Menukar nilai kepada nombor: nombor kekal, tarikh menjadi nombor siri, TRUE menjadi 1, ralat dipulangkan seperti asal, dan nilai lain menjadi 0.', 'example'=>'=N("text")', 'sample'=>'0', 'version'=>'2007'],
    ['name'=>'NA', 'cat'=>'info', 'syntax'=>'NA()', 'desc'=>'Memulangkan ralat <code>#N/A</code> secara sengaja, contohnya untuk menandakan data yang belum tersedia.', 'example'=>'=NA()', 'version'=>'2007'],
    ['name'=>'SHEET', 'cat'=>'info', 'syntax'=>'SHEET([value])', 'desc'=>'Memulangkan nombor sheet bagi sheet yang dirujuk, atau sheet semasa jika tiada argumen.', 'example'=>'=SHEET()', 'version'=>'2013'],
    ['name'=>'SHEETS', 'cat'=>'info', 'syntax'=>'SHEETS([reference])', 'desc'=>'Memulangkan bilangan sheet dalam rujukan, atau bilangan semua sheet dalam buku kerja jika tiada argumen.', 'example'=>'=SHEETS()', 'version'=>'2013'],
    ['name'=>'TYPE', 'cat'=>'info', 'syntax'=>'TYPE(value)', 'desc'=>'Memulangkan kod jenis nilai: 1 nombor, 2 teks, 4 nilai logik, 16 ralat, 64 tatasusunan, 128 data majmuk.', 'example'=>'=TYPE(A1)', 'version'=>'2007'],
];
