<?php

declare(strict_types=1);

/**
 * Excel playground sample data, English (/en/excel/playground). Same set keys, grid shape, and numbers as ../playground.php.
 * Text literals inside formulas (grid and 'cadangan') must match the English grid text ("KL", "Apr", "Beverages" …).
 * Grid maximum 8 columns × 20 rows; row 1 = headers; a cell may hold a formula "=...".
 */
return [
    'jualan' => [
        'nama' => 'Monthly sales',
        'grid' => [
            ['Month', 'Sales', 'Cost', 'Profit', 'Region'],
            ['Jan', 12000, 7500, '=B2-C2', 'KL'],
            ['Feb', 15000, 8200, '=B3-C3', 'JB'],
            ['Mar', 9800, 6900, '=B4-C4', 'KL'],
            ['Apr', 17500, 9100, '=B5-C5', 'PG'],
            ['May', 14200, 8800, '=B6-C6', 'KL'],
            ['Jun', 16900, 9400, '=B7-C7', 'JB'],
            ['Total', '=SUM(B2:B7)', '=SUM(C2:C7)', '=SUM(D2:D7)', ''],
        ],
        'cadangan' => ['=AVERAGE(B2:B7)', '=MAX(D2:D7)', '=SUMIF(E2:E7,"KL",B2:B7)', '=COUNTIF(B2:B7,">15000")',
            '=XLOOKUP("Apr",A2:A7,D2:D7)', '=IF(D8>40000,"Target met","Not yet")', '=D8/B8', '=ROUND(AVERAGE(D2:D7),0)'],
    ],
    'produk' => [
        'nama' => 'Product list',
        'grid' => [
            ['Code', 'Product', 'Category', 'Price', 'Stock'],
            ['P001', 'Coffee', 'Beverages', 12.5, 40],
            ['P002', 'Tea', 'Beverages', 9.9, 0],
            ['P003', 'Biscuits', 'Food', 6.5, 120],
            ['P004', 'Bread', 'Food', 4.2, 15],
            ['P005', 'Juice', 'Beverages', 7.8, 60],
            ['', '', '', '', ''],
        ],
        'cadangan' => ['=VLOOKUP("P003",A2:E6,4,FALSE)', '=INDEX(B2:B6,MATCH("P004",A2:A6,0))', '=SUMIFS(E2:E6,C2:C6,"Beverages",E2:E6,">0")',
            '=COUNTIF(E2:E6,0)', '=AVERAGEIF(C2:C6,"Food",D2:D6)', '=IFERROR(VLOOKUP("P009",A2:E6,2,FALSE),"Not found")', '=SUMPRODUCT(D2:D6,E2:E6)'],
    ],
    'kosong' => [
        'nama' => 'Blank',
        'grid' => array_fill(0, 10, array_fill(0, 6, '')),
        'cadangan' => ['=SUM(1,2,3)', '=PMT(4%/12,60,-50000)', '=TODAY()', '="Today: "&TEXT(TODAY(),"mmm d, yyyy")'],
    ],
];
