<?php

declare(strict_types=1);

// Sebahagian rujukan formula lengkap; dimuat oleh config/formulas-complete.php.
return [
    // ============================================================
    // CUBE (~7)
    // ============================================================
    ['name'=>'CUBEKPIMEMBER', 'cat'=>'cube', 'syntax'=>'CUBEKPIMEMBER(connection, kpi_name, kpi_property, [caption])', 'desc'=>'Memulangkan sifat KPI, seperti nilai, matlamat atau status, daripada kiub dan memaparkan nama KPI dalam sel.', 'example'=>'=CUBEKPIMEMBER("Sales","KPI",1)', 'version'=>'2007'],
    ['name'=>'CUBEMEMBER', 'cat'=>'cube', 'syntax'=>'CUBEMEMBER(connection, member_expression, [caption])', 'desc'=>'Memulangkan ahli atau tuple daripada hierarki kiub, jika ia wujud.', 'example'=>'=CUBEMEMBER("Sales","[Product].[All]")', 'version'=>'2007'],
    ['name'=>'CUBEMEMBERPROPERTY', 'cat'=>'cube', 'syntax'=>'CUBEMEMBERPROPERTY(connection, member_expression, property)', 'desc'=>'Memulangkan nilai sifat bagi satu ahli dalam kiub.', 'example'=>'=CUBEMEMBERPROPERTY("Sales","[Product]","Color")', 'version'=>'2007'],
    ['name'=>'CUBERANKEDMEMBER', 'cat'=>'cube', 'syntax'=>'CUBERANKEDMEMBER(connection, set_expression, rank, [caption])', 'desc'=>'Memulangkan ahli ke-n dalam set kiub mengikut kedudukan, contohnya produk dengan jualan tertinggi.', 'example'=>'=CUBERANKEDMEMBER("Sales","[Top10Products]",1)', 'version'=>'2007'],
    ['name'=>'CUBESET', 'cat'=>'cube', 'syntax'=>'CUBESET(connection, set_expression, [caption], [sort_order], [sort_by])', 'desc'=>'Mentakrifkan set ahli atau tuple dengan menghantar ungkapan set kepada kiub, untuk digunakan oleh fungsi kiub lain.', 'example'=>'=CUBESET("Sales","[Product].MEMBERS")', 'version'=>'2007'],
    ['name'=>'CUBESETCOUNT', 'cat'=>'cube', 'syntax'=>'CUBESETCOUNT(set)', 'desc'=>'Memulangkan bilangan item dalam set yang ditakrifkan oleh <code>CUBESET</code>.', 'example'=>'=CUBESETCOUNT(A1)', 'version'=>'2007'],
    ['name'=>'CUBEVALUE', 'cat'=>'cube', 'syntax'=>'CUBEVALUE(connection, [member_expression1], ...)', 'desc'=>'Memulangkan nilai agregat daripada kiub bagi gabungan ahli yang diberikan.', 'example'=>'=CUBEVALUE("Sales","[Measures].[Sales]")', 'version'=>'2007'],
];
