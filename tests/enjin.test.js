// Ujian enjin formula berbanding jawapan Excel sebenar (kes dari semakan bebas 23 Okt 2026). Guna: node enjin.test.js <app>
// Keluar 1 bila mana-mana kes berbeza. Dijalankan oleh uji.py.
const vm = require('vm'), fs = require('fs'), path = require('path');
const app = process.argv[2];
const ctx = {}; ctx.window = ctx; vm.createContext(ctx);
vm.runInContext(fs.readFileSync(path.join(app, 'public/assets/vendor/formula.min.js'), 'utf8'), ctx);
vm.runInContext(fs.readFileSync(path.join(app, 'public/assets/js/formula-enjin.js'), 'utf8'), ctx);
const E = ctx.EnjinFormula;
// A..E, baris 1 = tajuk
const g = [
    ['Kod', 'Produk', 'Kategori', 'Tarikh', 'Harga'],
    ['P001', 'Kopi', 'Minuman', '15/03/2024', 40],
    ['P002', 'Teh', 'minuman', '01/02/2024', 10],
    ['P003', 'Biskut', 'Makanan', '10/01/2024', 25],
    ['P004', 'Roti', 'Makanan', '', 15],
    ['P005', 'Jus', 'Minuman', '', 'x'],
    ['', '', '', '', '=1/0'],
];
const kes = [
    // operator + keutamaan
    ['=-2^2', '4'], ['=2^-1', '0.5'], ['=2^3^2', '64'], ['=2-3^2', '-7'], ['=10%', '0.1'], ['="a"&1', 'a1'],
    ['=0.1+0.2', '0.3'], ['=0.1+0.2=0.3', 'TRUE'], ['=0.1+0.2&""', '0.3'], ['=1E+20&""', '1E+20'],
    // tarikh
    ['=D2+1', '16/03/2024'], ['=DATE(2024,3,15)-DATE(2024,3,1)', '14'], ['=MAX(D2:D4)', '15/03/2024'], ['=YEAR(D2)', '2024'],
    ['=COUNTIF(D2:D4,">"&DATE(2024,2,1))', '1'], ['="x"&D2', 'x45366'], ['=INT(D2)', '45366'],
    ['=TEXT(DATE(2024,3,15),"dd/mm/yyyy")', '15/03/2024'], ['=TEXT(D2,"mmm yyyy")', 'Mar 2024'], ['=TEXT(D2,"dddd")', 'Friday'],
    ['=TEXT(0.256,"0%")', '26%'], ['=TEXT(1234.5,"#,##0.00")', '1,234.50'], ['=DATEDIF(D4,D2,"M")', '2'],
    // kriteria
    ['=SUMIF(C2:C6,"minuman",E2:E6)', '50'], ['=SUMIF(C2:C6,"M*",E2:E4)', '90'], ['=COUNTIF(C2:C6,"M*")', '5'],
    ['=COUNTIF(B2:B6,"*i*")', '3'], ['=COUNTIF(B2:B6,"?eh")', '1'], ['=COUNTIF(D2:D6,"")', '2'], ['=COUNTIF(E2:E5,">20")', '2'],
    ['=SUMIF(B2:B5,"<>Kopi",E2:E5)', '50'], ['=SUMIFS(E2:E5,C2:C5,"Makanan",E2:E5,">20")', '25'], ['=AVERAGEIF(C2:C5,"makanan",E2:E5)', '20'],
    ['=COUNTIFS(C2:C6,"Minuman",E2:E6,">5")', '2'],
    // IS* + malas
    ['=ISERROR(1/0)', 'TRUE'], ['=ISNA(VLOOKUP("Z",A2:B5,2,FALSE))', 'TRUE'], ['=IF(ISNA(VLOOKUP("Z",A2:B5,2,FALSE)),"Tiada","Ada")', 'Tiada'],
    ['=CHOOSE(1,"a",1/0)', 'a'], ['=IFERROR(1/0,"x")', 'x'], ['=IFNA(1/0,"x")', '#DIV/0!'], ['=ERROR.TYPE(1/0)', '2'],
    // tatasusunan
    ['=SUMPRODUCT((C2:C5="Minuman")*E2:E5)', '50'], ['=SUMPRODUCT(--(C2:C6="Minuman"))', '3'], ['=SUM(IF(C2:C6="Minuman",1,0))', '3'], ['=SUM(E2:E3*2)', '100'],
    // ralat dalam julat + agregat
    ['=SUM(E2:E7)', '#DIV/0!'], ['=SUM(E2:E5)', '90'], ['=SUM(1,"a")', '#VALUE!'], ['=SUM(1,"2")', '3'], ['=COUNT(1,"2","a")', '2'],
    ['=COUNT(E2:E6)', '4'], ['=COUNTA(B2:B6)', '5'], ['=AVERAGE(E2:E6)', '22.5'], ['=IF(E7,1,2)', '#DIV/0!'],
    // carian
    ['=VLOOKUP("P003",A2:E6,5,FALSE)', '25'], ['=VLOOKUP("p00*",A2:B6,2,FALSE)', 'Kopi'], ['=VLOOKUP("P003",A2:B6,0,FALSE)', '#VALUE!'],
    ['=VLOOKUP("P003",A2:B6,3,FALSE)', '#REF!'], ['=INDEX(B2:B6,MATCH("P004",A2:A6,0))', 'Roti'], ['=INDEX(A1:E1,3)', 'Kategori'],
    ['=MATCH(25,E2:E4,0)', '3'], ['=XLOOKUP("P002",A2:A6,B2:B6)', 'Teh'], ['=XLOOKUP("Z",A2:A6,B2:B6,"Tiada")', 'Tiada'],
    ['=XLOOKUP(12,E2:E5,B2:B5,,-1)', 'Teh'], ['=XLOOKUP(12,E2:E5,B2:B5,,1)', 'Roti'], ['=XLOOKUP("R*",B2:B6,A2:A6,,2)', 'P004'],
    ['=XLOOKUP("P001",A2:A6,B2:B6,,5)', '#VALUE!'], ['=HLOOKUP("Harga",A1:E3,2,FALSE)', '40'],
    // teks
    ['=TEXTJOIN(",",TRUE,B2:B4)', 'Kopi,Teh,Biskut'], ['=TEXTJOIN("-",TRUE,D5:D6,"a")', 'a'], ['=UPPER(B2)&LEN(B3)', 'KOPI3'],
    // lain
    ['=ROW(B5)', '5'], ['=COLUMN(C3)', '3'], ['=RANK(25,E2:E4)', '2'], ['=STDEV(1,2,3,4)', '1.29099444873581'], ['=ROUND(2.345,2)', '2.35'],
    ['=E10', '0'], ['=E10=FALSE', 'TRUE'], ['=ZZZZ1', '#NAME?'], ['=A0', '#NAME?'], ['=FOO(1)', '#NAME?'], ['=SUM(B2:B4', '#ERROR!'],
    ['=SUM(A1:A1048576)', '0'], ['=PMT(4%/12,60,-50000)', String(+((50000 * (0.04 / 12)) / (1 - Math.pow(1 + 0.04 / 12, -60))).toPrecision(15))],
];
let gagal = 0;
for (const [f, j] of kes) {
    const v = E.papar(E.nilai(f, g));
    const ok = v === j;
    if (!ok) { gagal++; console.log(`BEZA  ${f}  →  ${v}   (Excel: ${j})`); }
}
// setara: tandatangan
const t = (a, b) => JSON.stringify(E.tandatangan(a, g)) === JSON.stringify(E.tandatangan(b, g));
const setara = [['=SUM(E4,E3,E2)', '=SUM(E2:E4)', true], ['=MAX(E3,E4)', '=MAX(E2:E5)', false], ['=IF(E2>0,"a","b")', '=IF(E2>=50,"a","b")', false],
    ['=VLOOKUP("P003",A2:B5,2,TRUE)', '=VLOOKUP("P003",A2:B5,2,FALSE)', false], ['=E2*0+40', '=E2', false], ['=SUM(E2:E4)+0', '=SUM(E2:E4)', false]];
for (const [a, b, j] of setara) if (t(a, b) !== j) { gagal++; console.log(`SETARA ${a} vs ${b} → ${!j}  (jangka ${j})`); }
console.log(JSON.stringify({ kes: kes.length + setara.length, gagal }));
process.exit(gagal ? 1 : 0);
