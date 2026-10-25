<?php

declare(strict_types=1);

/**
 * Data contoh Playground Excel (/playground.php). Satu set = nama, grid (baris 1 = tajuk; sel boleh mengandungi formula
 * "=..."), cadangan (formula yang pelajar boleh ketik untuk dicuba atas data itu). Grid maksimum 8 lajur × 20 baris.
 */
return [
    'jualan' => [
        'nama' => 'Jualan bulanan',
        'grid' => [
            ['Bulan', 'Jualan', 'Kos', 'Untung', 'Wilayah'],
            ['Jan', 12000, 7500, '=B2-C2', 'KL'],
            ['Feb', 15000, 8200, '=B3-C3', 'JB'],
            ['Mac', 9800, 6900, '=B4-C4', 'KL'],
            ['Apr', 17500, 9100, '=B5-C5', 'PG'],
            ['Mei', 14200, 8800, '=B6-C6', 'KL'],
            ['Jun', 16900, 9400, '=B7-C7', 'JB'],
            ['Jumlah', '=SUM(B2:B7)', '=SUM(C2:C7)', '=SUM(D2:D7)', ''],
        ],
        'cadangan' => ['=AVERAGE(B2:B7)', '=MAX(D2:D7)', '=SUMIF(E2:E7,"KL",B2:B7)', '=COUNTIF(B2:B7,">15000")',
            '=XLOOKUP("Apr",A2:A7,D2:D7)', '=IF(D8>40000,"Sasaran capai","Belum")', '=D8/B8', '=ROUND(AVERAGE(D2:D7),0)'],
    ],
    'produk' => [
        'nama' => 'Senarai produk',
        'grid' => [
            ['Kod', 'Produk', 'Kategori', 'Harga', 'Stok'],
            ['P001', 'Kopi', 'Minuman', 12.5, 40],
            ['P002', 'Teh', 'Minuman', 9.9, 0],
            ['P003', 'Biskut', 'Makanan', 6.5, 120],
            ['P004', 'Roti', 'Makanan', 4.2, 15],
            ['P005', 'Jus', 'Minuman', 7.8, 60],
            ['', '', '', '', ''],
        ],
        'cadangan' => ['=VLOOKUP("P003",A2:E6,4,FALSE)', '=INDEX(B2:B6,MATCH("P004",A2:A6,0))', '=SUMIFS(E2:E6,C2:C6,"Minuman",E2:E6,">0")',
            '=COUNTIF(E2:E6,0)', '=AVERAGEIF(C2:C6,"Makanan",D2:D6)', '=IFERROR(VLOOKUP("P009",A2:E6,2,FALSE),"Tiada")', '=SUMPRODUCT(D2:D6,E2:E6)'],
    ],
    'kosong' => [
        'nama' => 'Kosong',
        'grid' => array_fill(0, 10, array_fill(0, 6, '')),
        'cadangan' => ['=SUM(1,2,3)', '=PMT(4%/12,60,-50000)', '=TODAY()', '="Hari ini: "&TEXT(TODAY(),"dd/mm/yyyy")'],
    ],
];
