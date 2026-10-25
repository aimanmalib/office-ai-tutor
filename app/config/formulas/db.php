<?php

declare(strict_types=1);

// Sebahagian rujukan formula lengkap; dimuat oleh config/formulas-complete.php.
return [
    // ============================================================
    // DATABASE (~12)
    // ============================================================
    ['name'=>'DAVERAGE', 'cat'=>'db', 'syntax'=>'DAVERAGE(database, field, criteria)', 'desc'=>'Memulangkan purata nilai dalam satu medan bagi rekod pangkalan data yang memenuhi kriteria.', 'example'=>'=DAVERAGE(A1:E20,"Sales",G1:G2)', 'version'=>'2007'],
    ['name'=>'DCOUNT', 'cat'=>'db', 'syntax'=>'DCOUNT(database, field, criteria)', 'desc'=>'Mengira sel yang mengandungi nombor dalam satu medan bagi rekod pangkalan data yang memenuhi kriteria.', 'example'=>'=DCOUNT(A1:E20,"Sales",G1:G2)', 'version'=>'2007'],
    ['name'=>'DCOUNTA', 'cat'=>'db', 'syntax'=>'DCOUNTA(database, field, criteria)', 'desc'=>'Mengira sel yang tidak kosong dalam satu medan bagi rekod pangkalan data yang memenuhi kriteria.', 'example'=>'=DCOUNTA(A1:E20,"Name",G1:G2)', 'version'=>'2007'],
    ['name'=>'DGET', 'cat'=>'db', 'syntax'=>'DGET(database, field, criteria)', 'desc'=>'Memulangkan satu nilai daripada medan bagi satu-satunya rekod yang memenuhi kriteria, atau ralat jika tiada rekod atau lebih daripada satu rekod sepadan.', 'example'=>'=DGET(A1:E20,"Email",G1:G2)', 'version'=>'2007'],
    ['name'=>'DMAX', 'cat'=>'db', 'syntax'=>'DMAX(database, field, criteria)', 'desc'=>'Memulangkan nilai terbesar dalam satu medan bagi rekod pangkalan data yang memenuhi kriteria.', 'example'=>'=DMAX(A1:E20,"Sales",G1:G2)', 'version'=>'2007'],
    ['name'=>'DMIN', 'cat'=>'db', 'syntax'=>'DMIN(database, field, criteria)', 'desc'=>'Memulangkan nilai terkecil dalam satu medan bagi rekod pangkalan data yang memenuhi kriteria.', 'example'=>'=DMIN(A1:E20,"Sales",G1:G2)', 'version'=>'2007'],
    ['name'=>'DPRODUCT', 'cat'=>'db', 'syntax'=>'DPRODUCT(database, field, criteria)', 'desc'=>'Memulangkan hasil darab nilai dalam satu medan bagi rekod pangkalan data yang memenuhi kriteria.', 'example'=>'=DPRODUCT(A1:E20,"Qty",G1:G2)', 'version'=>'2007'],
    ['name'=>'DSTDEV', 'cat'=>'db', 'syntax'=>'DSTDEV(database, field, criteria)', 'desc'=>'Memulangkan anggaran sisihan piawai sampel bagi nilai dalam satu medan rekod yang memenuhi kriteria.', 'example'=>'=DSTDEV(A1:E20,"Score",G1:G2)', 'version'=>'2007'],
    ['name'=>'DSTDEVP', 'cat'=>'db', 'syntax'=>'DSTDEVP(database, field, criteria)', 'desc'=>'Memulangkan sisihan piawai populasi bagi nilai dalam satu medan rekod yang memenuhi kriteria.', 'example'=>'=DSTDEVP(A1:E20,"Score",G1:G2)', 'version'=>'2007'],
    ['name'=>'DSUM', 'cat'=>'db', 'syntax'=>'DSUM(database, field, criteria)', 'desc'=>'Memulangkan jumlah nilai dalam satu medan bagi rekod pangkalan data yang memenuhi kriteria.', 'example'=>'=DSUM(A1:E20,"Sales",G1:G2)', 'version'=>'2007'],
    ['name'=>'DVAR', 'cat'=>'db', 'syntax'=>'DVAR(database, field, criteria)', 'desc'=>'Memulangkan anggaran varians sampel bagi nilai dalam satu medan rekod yang memenuhi kriteria.', 'example'=>'=DVAR(A1:E20,"Score",G1:G2)', 'version'=>'2007'],
    ['name'=>'DVARP', 'cat'=>'db', 'syntax'=>'DVARP(database, field, criteria)', 'desc'=>'Memulangkan varians populasi bagi nilai dalam satu medan rekod yang memenuhi kriteria.', 'example'=>'=DVARP(A1:E20,"Score",G1:G2)', 'version'=>'2007'],
];
