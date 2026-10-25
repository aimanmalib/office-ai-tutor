<?php

declare(strict_types=1);

// Sebahagian rujukan formula lengkap; dimuat oleh config/formulas-complete.php.
return [
    // ============================================================
    // WEB (~3)
    // ============================================================
    ['name'=>'ENCODEURL', 'cat'=>'web', 'syntax'=>'ENCODEURL(text)', 'desc'=>'Memulangkan teks dalam bentuk dikodkan URL, dengan aksara khas seperti ruang ditukar kepada kod peratus.', 'example'=>'=ENCODEURL("hello world")', 'sample'=>'"hello%20world"', 'version'=>'2013'],
    ['name'=>'FILTERXML', 'cat'=>'web', 'syntax'=>'FILTERXML(xml, xpath)', 'desc'=>'Memulangkan data tertentu daripada kandungan XML menggunakan ungkapan XPath; tidak tersedia dalam Excel untuk web dan Mac.', 'example'=>'=FILTERXML(A1,"//item/name")', 'version'=>'2013'],
    ['name'=>'WEBSERVICE', 'cat'=>'web', 'syntax'=>'WEBSERVICE(url)', 'desc'=>'Memulangkan data daripada perkhidmatan web pada URL yang diberikan; berfungsi dalam Excel untuk Windows sahaja.', 'example'=>'=WEBSERVICE("https://api.example.com/data")', 'version'=>'2013'],
];
