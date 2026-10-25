// Enjin formula Excel untuk playground + latihan (docs/PELAN.md Fasa 4, D53). Penghurai + penilai sendiri; fungsi yang
// semantiknya PENTING untuk kelas ditulis di sini supaya sama dengan Excel (kriteria SUMIF/COUNTIF dengan wildcard + tanpa beza
// huruf, carian VLOOKUP/HLOOKUP/MATCH/XLOOKUP/INDEX, agregat SUM/AVERAGE/COUNT…, IS*, CHOOSE, TEXT, TEXTJOIN, ROW/COLUMN, tarikh);
// selebihnya @formulajs/formulajs (MIT, assets/vendor/formula.min.js). Tarikh = nombor siri Excel (1 = 1/1/1900).
// API: nilai(formula, grid) · lembaran(grid).sel(b, k) (penilai berkongsi cache) · papar(v) · sama(a, b) · isRalat(v)
//      fungsi(f) · adaRujukan(f) · tandatangan(f, grid) (fungsi + sel dirujuk + pemalar — untuk jawapan "setara")
(function (global) {
    'use strict';
    const F = () => global.formulajs || {};
    const Ralat = (k) => ({ ralat: k });
    const isRalat = (v) => v !== null && typeof v === 'object' && !Array.isArray(v) && 'ralat' in v;
    const MAKS_LAJUR = 16384, MAKS_BARIS = 1048576;

    // ---------- tarikh (siri Excel, sistem 1900) ----------
    const EPOK = Date.UTC(1899, 11, 30);
    const keSiri = (y, m, d) => Math.round((Date.UTC(y, m, d) - EPOK) / 864e5);
    const dariTarikh = (dt) => keSiri(dt.getFullYear(), dt.getMonth(), dt.getDate()) + (dt.getHours() * 3600 + dt.getMinutes() * 60 + dt.getSeconds()) / 86400;
    const keTarikh = (s) => { const t = new Date(EPOK + Math.floor(s) * 864e5); return new Date(t.getUTCFullYear(), t.getUTCMonth(), t.getUTCDate()); };
    const FN_TARIKH = new Set(['DATE', 'TODAY', 'NOW', 'EOMONTH', 'EDATE', 'WORKDAY', 'WORKDAY.INTL', 'DATEVALUE']);

    // ---------- token ----------
    function tokens(s) {
        const out = [];
        let i = 0;
        const re = {
            ruang: /^\s+/,
            nombor: /^(\d+\.?\d*|\.\d+)(e[+-]?\d+)?/i,
            rujukan: /^\$?[A-Z]{1,3}\$?\d+(:\$?[A-Z]{1,3}\$?\d+)?(?![\w(])/i,
            lajur: /^\$?[A-Z]{1,3}:\$?[A-Z]{1,3}(?![\w(])/i,
            fungsi: /^[A-Z][A-Z0-9.]*(?=\s*\()/i,
            boolean: /^(TRUE|FALSE)(?![\w(.])/i,
            op: /^(<>|<=|>=|[-+*/^&=<>%(),;])/,
        };
        while (i < s.length) {
            const r = s.slice(i);
            let m;
            if ((m = re.ruang.exec(r))) { i += m[0].length; continue; }
            if (r[0] === '"') {
                let j = 1, t = '';
                for (; j < r.length; j++) {
                    if (r[j] === '"') { if (r[j + 1] === '"') { t += '"'; j++; continue; } break; }
                    t += r[j];
                }
                if (j >= r.length) throw Ralat('#ERROR!');
                out.push({ j: 'teks', v: t }); i += j + 1; continue;
            }
            if ((m = re.lajur.exec(r))) { out.push({ j: 'ruj', v: m[0].toUpperCase().replace(/\$/g, '') }); i += m[0].length; continue; }
            if ((m = re.fungsi.exec(r))) { out.push({ j: 'fungsi', v: m[0].toUpperCase() }); i += m[0].length; continue; }
            if ((m = re.boolean.exec(r))) { out.push({ j: 'bool', v: m[0].toUpperCase() === 'TRUE' }); i += m[0].length; continue; }
            if ((m = re.rujukan.exec(r))) { out.push({ j: 'ruj', v: m[0].toUpperCase().replace(/\$/g, '') }); i += m[0].length; continue; }
            if ((m = re.nombor.exec(r))) { out.push({ j: 'nombor', v: parseFloat(m[0]) }); i += m[0].length; continue; }
            if ((m = re.op.exec(r))) { out.push({ j: 'op', v: m[0] === ';' ? ',' : m[0] }); i += m[0].length; continue; }
            if (r[0] === '{') throw Ralat('#BELUM');        // pemalar tatasusunan {1,2} — belum disokong
            if (r[0] === '!' || /^[A-Z_]/i.test(r)) throw Ralat('#NAME?');
            throw Ralat('#ERROR!');
        }
        return out;
    }

    // ---------- penghurai (keutamaan Excel: perbandingan < & < +- < */ < ^ < unari < %) ----------
    function hurai(toks) {
        let p = 0;
        const lihat = () => toks[p];
        const ambil = (v) => { const t = toks[p]; if (v !== undefined && (!t || t.v !== v)) throw Ralat('#ERROR!'); p++; return t; };
        const ops = [['=', '<>', '<', '>', '<=', '>='], ['&'], ['+', '-'], ['*', '/'], ['^']];
        function binari(aras) {
            if (aras === ops.length) return unari();
            let kiri = binari(aras + 1);
            while (lihat() && lihat().j === 'op' && ops[aras].includes(lihat().v)) {
                const op = ambil().v;
                kiri = { j: 'bin', op, kiri, kanan: binari(aras + 1) };
            }
            return kiri;
        }
        function unari() {
            if (lihat() && lihat().j === 'op' && (lihat().v === '-' || lihat().v === '+')) {
                const op = ambil().v;
                return { j: 'unari', op, isi: unari() };
            }
            let n = asas();
            while (lihat() && lihat().j === 'op' && lihat().v === '%') { ambil(); n = { j: 'peratus', isi: n }; }
            return n;
        }
        function asas() {
            const t = ambil();
            if (!t) throw Ralat('#ERROR!');
            if (t.j === 'nombor' || t.j === 'teks' || t.j === 'bool') return { j: 'nilai', v: t.v };
            if (t.j === 'ruj') return { j: 'ruj', v: t.v };
            if (t.j === 'fungsi') {
                ambil('(');
                const hujah = [];
                if (lihat() && !(lihat().j === 'op' && lihat().v === ')')) {
                    for (;;) {
                        // semak JENIS token: teks "," bukan pemisah hujah
                        const pemisah = (t) => t && t.j === 'op' && (t.v === ',' || t.v === ')');
                        hujah.push(pemisah(lihat()) ? { j: 'kosong' } : binari(0));
                        if (lihat() && lihat().j === 'op' && lihat().v === ',') { ambil(); continue; }
                        break;
                    }
                }
                ambil(')');
                return { j: 'fungsi', nama: t.v, hujah };
            }
            if (t.v === '(') { const n = binari(0); ambil(')'); return n; }
            throw Ralat('#ERROR!');
        }
        const pokok = binari(0);
        if (p !== toks.length) throw Ralat('#ERROR!');
        return pokok;
    }

    // ---------- nilai sel paparan ----------
    function huruf(c) { let n = 0; for (const h of c) n = n * 26 + (h.charCodeAt(0) - 64); return n - 1; }
    function sel(ref) {
        const m = /^([A-Z]+)(\d+)$/.exec(ref);
        const s = { k: huruf(m[1]), b: parseInt(m[2], 10) - 1 };
        if (s.k >= MAKS_LAJUR || s.b < 0 || s.b >= MAKS_BARIS) throw Ralat('#NAME?');
        return s;
    }
    function nilaiPaparan(v) {
        if (v === null || v === undefined) return null;
        if (typeof v === 'number' || typeof v === 'boolean') return v;
        const s = String(v).trim();
        if (s === '' || s === '?') return null;
        if (/^-?(\d{1,3}(,\d{3})+|\d+)(\.\d+)?$/.test(s) || /^-?\.\d+$/.test(s)) return parseFloat(s.replace(/,/g, ''));
        if (/^-?\d+(\.\d+)?%$/.test(s)) return parseFloat(s) / 100;
        if (/^(TRUE|FALSE)$/i.test(s)) return /^TRUE$/i.test(s);
        let m;
        if ((m = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/.exec(s))) return keSiri(+m[3], +m[2] - 1, +m[1]);   // dd/mm/yyyy (Malaysia)
        if ((m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(s))) return keSiri(+m[1], +m[2] - 1, +m[3]);
        return s;
    }
    const nampakTarikh = (v) => typeof v === 'string' && /^(\d{1,2}\/\d{1,2}\/\d{4}|\d{4}-\d{2}-\d{2})$/.test(v.trim());
    const bulat15 = (n) => (typeof n === 'number' && isFinite(n) ? +n.toPrecision(15) : n);

    // ---------- penilai satu lembaran (cache dikongsi antara sel) ----------
    function lembaran(grid) {
        grid = grid || [];
        const tinggi = grid.length, lebar = grid.reduce((m, r) => Math.max(m, (r || []).length), 0);
        const sedang = new Set(), cache = new Map();
        const mentah = (b, k) => (grid[b] ? grid[b][k] : null);

        function selNilai(b, k) {
            const kunci = b + ':' + k;
            if (cache.has(kunci)) return cache.get(kunci);
            const m = mentah(b, k);
            let v;
            if (typeof m === 'string' && m.trim().startsWith('=')) {
                if (sedang.has(kunci)) return Ralat('#CIRC!');
                sedang.add(kunci);
                try { v = nilaiFormula(m.trim().slice(1)).v; } finally { sedang.delete(kunci); }
            } else {
                v = nilaiPaparan(m);
            }
            cache.set(kunci, v);
            return v;
        }
        function sudut(ref) {
            let [a, z] = ref.split(':');
            if (/^[A-Z]+$/.test(a)) { a += '1'; z += String(MAKS_BARIS); }
            const p = sel(a), q = sel(z || a);
            return { b1: Math.min(p.b, q.b), k1: Math.min(p.k, q.k), b2: Math.max(p.b, q.b), k2: Math.max(p.k, q.k), tunggal: !z };
        }
        function julat(ref) {
            const s = sudut(ref);
            if (s.tunggal) return selNilai(s.b1, s.k1);
            // di luar grid = kosong; dihadkan kepada saiz grid (A1:A1048576 tidak melukis sejuta sel)
            const b2 = Math.min(s.b2, Math.max(s.b1, tinggi - 1)), k2 = Math.min(s.k2, Math.max(s.k1, lebar - 1));
            const out = [];
            for (let b = s.b1; b <= b2; b++) {
                const baris = [];
                for (let k = s.k1; k <= k2; k++) baris.push(selNilai(b, k));
                out.push(baris);
            }
            return out;
        }

        // --- paksaan ---
        const nom = (v) => {
            if (isRalat(v)) throw v;
            if (v === null) return 0;
            if (typeof v === 'boolean') return v ? 1 : 0;
            if (typeof v === 'number') return v;
            if (v instanceof Date) return dariTarikh(v);
            const n = nilaiPaparan(v);
            if (typeof n === 'number') return n;
            throw Ralat('#VALUE!');
        };
        const teks = (v) => {
            if (isRalat(v)) throw v;
            if (v === null) return '';
            if (typeof v === 'boolean') return v ? 'TRUE' : 'FALSE';
            if (typeof v === 'number') return nomKeTeks(v);
            if (v instanceof Date) return nomKeTeks(dariTarikh(v));
            return String(v);
        };
        function banding(a, b, op) {
            if (isRalat(a)) throw a; if (isRalat(b)) throw b;
            if (a === null) a = typeof b === 'string' ? '' : typeof b === 'boolean' ? false : 0;
            if (b === null) b = typeof a === 'string' ? '' : typeof a === 'boolean' ? false : 0;
            let c;
            if (typeof a === 'string' && typeof b === 'string') { const x = a.toLowerCase(), y = b.toLowerCase(); c = x < y ? -1 : x > y ? 1 : 0; }
            else if (typeof a === 'number' && typeof b === 'number') { const x = bulat15(a), y = bulat15(b); c = x < y ? -1 : x > y ? 1 : 0; }
            else if (typeof a === typeof b) c = a === b ? 0 : (a < b ? -1 : 1);
            else { const p = (v) => (typeof v === 'number' ? 0 : typeof v === 'string' ? 1 : 2); c = p(a) - p(b); }   // nombor < teks < boolean
            return { '=': c === 0, '<>': c !== 0, '<': c < 0, '>': c > 0, '<=': c <= 0, '>=': c >= 0 }[op];
        }

        // --- tatasusunan (julat dalam operator = unsur demi unsur, macam Excel 365) ---
        const isArr = Array.isArray;
        function peta2(a, b, fn) {
            if (!isArr(a) && !isArr(b)) return fn(a, b);
            const A = isArr(a) ? a : [[a]], B = isArr(b) ? b : [[b]];
            const R = Math.max(A.length, B.length), K = Math.max(A[0].length, B[0].length);
            const ambilU = (M, r, k) => (M.length === 1 ? (M[0].length === 1 ? M[0][0] : M[0][k]) : M[0].length === 1 ? (M[r] || [])[0] : (M[r] || [])[k]);
            const out = [];
            for (let r = 0; r < R; r++) {
                const baris = [];
                for (let k = 0; k < K; k++) {
                    const x = ambilU(A, r, k), y = ambilU(B, r, k);
                    if (x === undefined || y === undefined) { baris.push(Ralat('#N/A')); continue; }
                    try { baris.push(fn(x, y)); } catch (e) { if (isRalat(e)) baris.push(e); else throw e; }
                }
                out.push(baris);
            }
            return out;
        }
        const peta1 = (a, fn) => peta2(a, null, (x) => fn(x));
        const rata = (v) => (isArr(v) ? v.flat() : [v]);

        function nilaiNod(n) {
            switch (n.j) {
                case 'nilai': return n.v;
                case 'kosong': return null;
                case 'ruj': return julat(n.v);
                case 'unari': { const v = nilaiNod(n.isi); return peta1(v, (x) => (n.op === '-' ? -nom(x) : nom(x))); }
                case 'peratus': return peta1(nilaiNod(n.isi), (x) => nom(x) / 100);
                case 'bin': {
                    const a = nilaiNod(n.kiri), b = nilaiNod(n.kanan);
                    return peta2(a, b, (x, y) => {
                        switch (n.op) {
                            case '+': return nom(x) + nom(y);
                            case '-': return nom(x) - nom(y);
                            case '*': return nom(x) * nom(y);
                            case '/': { const t = nom(x), d = nom(y); if (d === 0) throw Ralat('#DIV/0!'); return t / d; }
                            case '^': { const r = Math.pow(nom(x), nom(y)); if (!isFinite(r) || isNaN(r)) throw Ralat('#NUM!'); return r; }
                            case '&': return teks(x) + teks(y);
                            default: return banding(x, y, n.op);
                        }
                    });
                }
                case 'fungsi': return panggil(n.nama, n.hujah);
            }
            throw Ralat('#ERROR!');
        }
        function skalar(v) {
            if (isArr(v)) v = v[0] && v[0].length ? v[0][0] : null;
            if (isRalat(v)) throw v;
            return v;
        }
        const nilaiSkalar = (n) => skalar(nilaiNod(n));
        const tanpaRalat = (v) => { for (const x of rata(v)) if (isRalat(x)) throw x; return v; };

        // --- kriteria SUMIF/COUNTIF: tanpa beza huruf, * ? ~, operator, "" = kosong ---
        function kriteria(c) {
            if (isRalat(c)) throw c;
            if (typeof c === 'number' || typeof c === 'boolean') return (v) => typeof v === typeof c && banding(v, c, '=');
            if (c === null) c = '';
            const s = String(c);
            const m = /^(<=|>=|<>|<|>|=)?([\s\S]*)$/.exec(s);
            const op = m[1] || '=', isi = m[2];
            const sasar = nilaiPaparan(isi);
            if (isi === '') return op === '<>' ? (v) => v !== null && v !== '' : (v) => v === null || v === '';
            if (typeof sasar === 'number' || typeof sasar === 'boolean') {
                return (v) => {
                    if (op === '<>') return !(typeof v === typeof sasar && banding(v, sasar, '='));
                    return typeof v === typeof sasar && banding(v, sasar, op);
                };
            }
            if ((op === '=' || op === '<>') && /[*?]/.test(isi)) {
                const rx = new RegExp('^' + isi.replace(/~([*?~])|([*?])|([.+^${}()|[\]\\/])/g, (x, esc, wild, lain) =>
                    esc ? '\\' + esc : wild ? (wild === '*' ? '[\\s\\S]*' : '[\\s\\S]') : '\\' + lain) + '$', 'i');
                return op === '=' ? (v) => typeof v === 'string' && rx.test(v) : (v) => !(typeof v === 'string' && rx.test(v));
            }
            const t = isi.replace(/~([*?~])/g, '$1');
            if (op === '=') return (v) => typeof v === 'string' && v.toLowerCase() === t.toLowerCase();
            if (op === '<>') return (v) => !(typeof v === 'string' && v.toLowerCase() === t.toLowerCase());
            return (v) => typeof v === 'string' && banding(v, t, op);
        }
        const bentuk = (v) => (isArr(v) ? v : [[v]]);
        function pasangan(julatJulat, kriteriaKriteria) {   // indeks yang lulus SEMUA kriteria
            const base = bentuk(julatJulat[0]);
            const lulus = [];
            for (let r = 0; r < base.length; r++) for (let k = 0; k < base[0].length; k++) {
                let ok = true;
                for (let i = 0; i < julatJulat.length && ok; i++) {
                    const M = bentuk(julatJulat[i]);
                    if (M.length !== base.length || M[0].length !== base[0].length) throw Ralat('#VALUE!');
                    ok = kriteriaKriteria[i](M[r][k]);
                }
                if (ok) lulus.push([r, k]);
            }
            return lulus;
        }
        const nombor2 = (M, idx) => idx.map(([r, k]) => (bentuk(M)[r] || [])[k]).filter((v) => { if (isRalat(v)) throw v; return typeof v === 'number'; });

        // --- carian tepat dengan wildcard (teks) ---
        function padan(nilai, cari, wildcard) {
            if (typeof cari === 'string' && wildcard && /[*?]/.test(cari)) return kriteria('=' + cari)(nilai);
            return nilai !== null && banding(nilai, cari, '=');
        }
        function cariIndeks(senarai, cari, jenis, wildcard) {   // jenis: 0 tepat, 1 terbesar ≤ (tersusun naik), -1 terkecil ≥ (tersusun turun)
            if (jenis === 0) return senarai.findIndex((v) => padan(v, cari, wildcard));
            let terbaik = -1;
            for (let i = 0; i < senarai.length; i++) {
                const v = senarai[i];
                if (v === null || typeof v !== typeof cari) continue;
                if (jenis > 0 ? banding(v, cari, '<=') : banding(v, cari, '>=')) terbaik = i; else break;
            }
            return terbaik;
        }
        function xcari(senarai, cari, mod, arah) {
            const urutan = senarai.map((_, i) => i);
            if (arah < 0) urutan.reverse();
            for (const i of urutan) if (padan(senarai[i], cari, mod === 2)) return i;
            if (mod === 0 || mod === 2) return -1;
            let terbaik = -1;
            for (const i of urutan) {
                const v = senarai[i];
                if (v === null || typeof v !== typeof cari) continue;
                if (mod === -1 ? banding(v, cari, '<') : banding(v, cari, '>')) {
                    if (terbaik < 0 || (mod === -1 ? banding(v, senarai[terbaik], '>') : banding(v, senarai[terbaik], '<'))) terbaik = i;
                }
            }
            return terbaik;
        }

        const AGREGAT = {
            SUM: (n) => n.reduce((a, b) => a + b, 0),
            AVERAGE: (n) => { if (!n.length) throw Ralat('#DIV/0!'); return n.reduce((a, b) => a + b, 0) / n.length; },
            MIN: (n) => (n.length ? Math.min(...n) : 0),
            MAX: (n) => (n.length ? Math.max(...n) : 0),
            PRODUCT: (n) => n.reduce((a, b) => a * b, 1),
        };
        // Excel: dalam JULAT hanya nombor dikira; hujah TERUS: nombor, boolean, teks-nombor (teks lain = #VALUE!)
        function nomborAgregat(hujah) {
            const out = [];
            for (const h of hujah) {
                const v = nilaiNod(h);
                if (h.j === 'ruj' || isArr(v)) { for (const x of rata(v)) { if (isRalat(x)) throw x; if (typeof x === 'number') out.push(x); } }
                else if (v !== null) out.push(nom(v));
            }
            return out;
        }
        const LAMA = { RANK: 'RANK.EQ', STDEV: 'STDEV.S', VAR: 'VAR.S', MODE: 'MODE.SNGL', PERCENTILE: 'PERCENTILE.INC', QUARTILE: 'QUARTILE.INC',
            'CEILING.MATH': 'CEILINGMATH', 'FLOOR.MATH': 'FLOORMATH' };
        function cariFormulajs(nama) {
            nama = LAMA[nama] || nama;
            const f = F();
            if (typeof f[nama] === 'function') return f[nama];
            let o = f;
            for (const b of nama.split('.')) o = o ? o[b] : undefined;
            if (typeof o === 'function') return o;
            if (typeof f[nama.replace(/\./g, '')] === 'function') return f[nama.replace(/\./g, '')];
            return null;
        }
        function formatTeks(v, fmt) {
            if (typeof v !== 'number') { const n = nilaiPaparan(v); if (typeof n !== 'number') return teks(v); v = n; }
            const f = String(fmt);
            if (/[dmy]/i.test(f) && !/^[#0.,%]+$/.test(f)) {
                const t = keTarikh(v), BL = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                const HR = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                return f.replace(/yyyy|yy|mmmm|mmm|mm|m|dddd|ddd|dd|d/gi, (x) => {
                    switch (x.toLowerCase()) {
                        case 'yyyy': return String(t.getFullYear());
                        case 'yy': return String(t.getFullYear()).slice(-2);
                        case 'mmmm': return BL[t.getMonth()];
                        case 'mmm': return BL[t.getMonth()].slice(0, 3);
                        case 'mm': return String(t.getMonth() + 1).padStart(2, '0');
                        case 'm': return String(t.getMonth() + 1);
                        case 'dddd': return HR[t.getDay()];
                        case 'ddd': return HR[t.getDay()].slice(0, 3);
                        case 'dd': return String(t.getDate()).padStart(2, '0');
                        default: return String(t.getDate());
                    }
                });
            }
            const peratus = f.endsWith('%');
            const x = peratus ? v * 100 : v;
            const p = (f.split('.')[1] || '').replace(/[^0#]/g, '').length;
            let s = Math.abs(x).toFixed(p);
            if (f.includes(',')) { const [i, d] = s.split('.'); s = i.replace(/\B(?=(\d{3})+(?!\d))/g, ',') + (d ? '.' + d : ''); }
            return (x < 0 ? '-' : '') + s + (peratus ? '%' : '');
        }

        function panggil(nama, hujah) {
            const ada = (i) => hujah[i] && hujah[i].j !== 'kosong';
            const H = (i) => (ada(i) ? nilaiNod(hujah[i]) : undefined);
            const HS = (i) => (ada(i) ? nilaiSkalar(hujah[i]) : undefined);
            switch (nama) {
                // --- malas / ralat sebagai nilai ---
                case 'IFERROR': case 'IFNA': {
                    const ganti = () => (hujah[1] ? nilaiNod(hujah[1]) : 0);
                    let v;
                    try { v = nilaiNod(hujah[0]); if (!isArr(v) && isRalat(v)) throw v; }
                    catch (e) {
                        if (!isRalat(e) || (nama === 'IFNA' && e.ralat !== '#N/A')) throw e;
                        return ganti();
                    }
                    return isArr(v) ? peta1(v, (x) => (isRalat(x) && (nama === 'IFERROR' || x.ralat === '#N/A') ? skalar(ganti()) : x)) : v;
                }
                case 'IF': {
                    const s = nilaiNod(hujah[0]);
                    const pilih = (x) => {
                        if (isRalat(x)) throw x;
                        if (typeof x === 'string') throw Ralat('#VALUE!');
                        if (x) return ada(1) ? nilaiNod(hujah[1]) : (hujah[1] ? 0 : true);
                        return ada(2) ? nilaiNod(hujah[2]) : (hujah[2] ? 0 : false);
                    };
                    return isArr(s) ? peta1(s, (x) => skalar(pilih(x))) : pilih(s);
                }
                case 'IFS': {
                    for (let i = 0; i + 1 < hujah.length; i += 2) if (nilaiSkalar(hujah[i])) return nilaiNod(hujah[i + 1]);
                    throw Ralat('#N/A');
                }
                case 'CHOOSE': {
                    const i = Math.floor(nom(HS(0)));
                    if (i < 1 || i >= hujah.length) throw Ralat('#VALUE!');
                    return nilaiNod(hujah[i]);
                }
                case 'ISERROR': case 'ISERR': case 'ISNA': case 'ERROR.TYPE': case 'ISBLANK': case 'ISNUMBER': case 'ISTEXT': case 'ISLOGICAL': {
                    let v;
                    try { v = nilaiNod(hujah[0]); } catch (e) { if (!isRalat(e)) throw e; v = e; }
                    const uji = (x) => {
                        switch (nama) {
                            case 'ISERROR': return isRalat(x);
                            case 'ISERR': return isRalat(x) && x.ralat !== '#N/A';
                            case 'ISNA': return isRalat(x) && x.ralat === '#N/A';
                            case 'ISBLANK': return x === null;
                            case 'ISNUMBER': return typeof x === 'number';
                            case 'ISTEXT': return typeof x === 'string';
                            case 'ISLOGICAL': return typeof x === 'boolean';
                            default: {
                                const kod = { '#NULL!': 1, '#DIV/0!': 2, '#VALUE!': 3, '#REF!': 4, '#NAME?': 5, '#NUM!': 6, '#N/A': 7 };
                                if (!isRalat(x)) throw Ralat('#N/A');
                                return kod[x.ralat] || 8;
                            }
                        }
                    };
                    return isArr(v) ? peta1(v, uji) : uji(v);
                }
                // --- agregat ---
                case 'SUM': case 'AVERAGE': case 'MIN': case 'MAX': case 'PRODUCT': return AGREGAT[nama](nomborAgregat(hujah));
                case 'COUNT': {
                    let n = 0;
                    for (const h of hujah) {
                        let v;
                        try { v = nilaiNod(h); } catch (e) { if (isRalat(e)) continue; throw e; }
                        if (h.j === 'ruj' || isArr(v)) n += rata(v).filter((x) => typeof x === 'number').length;
                        else if (typeof v === 'number' || typeof v === 'boolean' || typeof nilaiPaparan(v) === 'number') n++;
                    }
                    return n;
                }
                case 'COUNTA': return hujah.reduce((n, h) => n + rata(h.j === 'kosong' ? null : nilaiNod(h)).filter((x) => x !== null).length, 0);
                case 'COUNTBLANK': return rata(nilaiNod(hujah[0])).filter((x) => x === null || x === '').length;
                case 'SUMPRODUCT': {
                    const M = hujah.map((h) => bentuk(tanpaRalat(nilaiNod(h))));
                    if (M.some((m) => m.length !== M[0].length || m[0].length !== M[0][0].length)) throw Ralat('#VALUE!');
                    let t = 0;
                    for (let r = 0; r < M[0].length; r++) for (let k = 0; k < M[0][0].length; k++) {
                        t += M.reduce((p, m) => p * (typeof m[r][k] === 'number' ? m[r][k] : 0), 1);
                    }
                    return t;
                }
                // --- kriteria ---
                case 'SUMIF': case 'AVERAGEIF': {
                    // Excel: sum_range diambil dari sel kiri-atasnya dengan SAIZ julat kriteria (SUMIF(C2:C6,…,E2:E4) = E2:E6)
                    const j = H(0), c = kriteria(HS(1));
                    let sr = j;
                    if (ada(2)) {
                        if (hujah[2].j === 'ruj' && hujah[0].j === 'ruj') {
                            const a = sudut(hujah[0].v), b = sudut(hujah[2].v), kol = (k) => { let t = ''; k++; while (k > 0) { k--; t = String.fromCharCode(65 + (k % 26)) + t; k = Math.floor(k / 26); } return t; };
                            sr = julat(kol(b.k1) + (b.b1 + 1) + ':' + kol(b.k1 + a.k2 - a.k1) + (b.b1 + 1 + a.b2 - a.b1));
                        } else sr = H(2);
                    }
                    const n = nombor2(sr, pasangan([j], [c]));
                    return nama === 'SUMIF' ? AGREGAT.SUM(n) : AGREGAT.AVERAGE(n);
                }
                case 'COUNTIF': return pasangan([H(0)], [kriteria(HS(1))]).length;
                case 'SUMIFS': case 'AVERAGEIFS': case 'MAXIFS': case 'MINIFS': case 'COUNTIFS': {
                    const mula = nama === 'COUNTIFS' ? 0 : 1;
                    const jj = [], cc = [];
                    for (let i = mula; i + 1 < hujah.length; i += 2) { jj.push(H(i)); cc.push(kriteria(HS(i + 1))); }
                    if (!jj.length) throw Ralat('#VALUE!');
                    const idx = pasangan(jj, cc);
                    if (nama === 'COUNTIFS') return idx.length;
                    const n = nombor2(H(0), idx);
                    return nama === 'SUMIFS' ? AGREGAT.SUM(n) : nama === 'AVERAGEIFS' ? AGREGAT.AVERAGE(n) : AGREGAT[nama.slice(0, 3)](n);
                }
                // --- carian ---
                case 'VLOOKUP': case 'HLOOKUP': {
                    const cari = HS(0), M = bentuk(H(1)), n = Math.floor(nom(HS(2)));
                    const anggar = ada(3) ? !!nom(HS(3)) : true;
                    const kunci = nama === 'VLOOKUP' ? M.map((r) => r[0]) : M[0];
                    const had = nama === 'VLOOKUP' ? M[0].length : M.length;
                    if (n < 1) throw Ralat('#VALUE!');
                    if (n > had) throw Ralat('#REF!');
                    const i = cariIndeks(kunci, cari, anggar ? 1 : 0, true);
                    if (i < 0) throw Ralat('#N/A');
                    return nama === 'VLOOKUP' ? M[i][n - 1] : M[n - 1][i];
                }
                case 'MATCH': case 'XMATCH': {
                    const cari = HS(0), senarai = rata(H(1));
                    const jenis = ada(2) ? Math.sign(nom(HS(2))) : (nama === 'MATCH' ? 1 : 0);
                    const i = nama === 'MATCH' ? cariIndeks(senarai, cari, jenis, true) : xcari(senarai, cari, jenis, 1);
                    if (i < 0) throw Ralat('#N/A');
                    return i + 1;
                }
                case 'XLOOKUP': {
                    const cari = HS(0), dalam = bentuk(H(1)), pulang = bentuk(H(2));
                    const mod = ada(4) ? nom(HS(4)) : 0, arah = ada(5) ? nom(HS(5)) : 1;
                    if (![0, -1, 1, 2].includes(mod) || ![1, -1].includes(arah)) throw Ralat('#VALUE!');
                    const i = xcari(rata(dalam), cari, mod, arah);
                    if (i < 0) { if (ada(3)) return nilaiNod(hujah[3]); throw Ralat('#N/A'); }
                    if (dalam[0].length === 1 && pulang.length === dalam.length) return pulang[i].length === 1 ? pulang[i][0] : [pulang[i]];
                    if (dalam.length === 1 && pulang[0].length === dalam[0].length) return pulang.length === 1 ? pulang[0][i] : pulang.map((r) => [r[i]]);
                    throw Ralat('#VALUE!');
                }
                case 'INDEX': {
                    const M = bentuk(H(0));
                    let r = Math.floor(nom(ada(1) ? HS(1) : 0)), k = ada(2) ? Math.floor(nom(HS(2))) : undefined;
                    if (k === undefined) { if (M.length === 1) { k = r; r = 1; } else k = M[0].length === 1 ? 1 : 0; }
                    if (r < 0 || k < 0 || r > M.length || k > M[0].length) throw Ralat('#REF!');
                    if (r === 0) return M.map((x) => [x[k - 1]]);
                    if (k === 0) return [M[r - 1]];
                    return M[r - 1][k - 1];
                }
                case 'ROW': case 'COLUMN': case 'ROWS': case 'COLUMNS': {
                    if (!hujah[0] || hujah[0].j !== 'ruj') {
                        if (nama === 'ROWS' || nama === 'COLUMNS') { const M = bentuk(H(0)); return nama === 'ROWS' ? M.length : M[0].length; }
                        throw Ralat('#N/A');
                    }
                    const s = sudut(hujah[0].v);
                    return { ROW: s.b1 + 1, COLUMN: s.k1 + 1, ROWS: s.b2 - s.b1 + 1, COLUMNS: s.k2 - s.k1 + 1 }[nama];
                }
                // --- teks ---
                case 'TEXT': return formatTeks(HS(0), teks(HS(1)));
                case 'TEXTJOIN': {
                    const pemisah = teks(HS(0)), abai = !!HS(1);
                    const bah = [];
                    for (let i = 2; i < hujah.length; i++) for (const x of rata(tanpaRalat(H(i)))) { const t = teks(x); if (!(abai && t === '')) bah.push(t); }
                    return bah.join(pemisah);
                }
                case 'CONCAT': case 'CONCATENATE': {
                    const bah = [];
                    for (const h of hujah) for (const x of rata(tanpaRalat(nilaiNod(h)))) bah.push(teks(x));
                    return bah.join('');
                }
                // --- tarikh (siri Excel) ---
                case 'TODAY': { const t = new Date(); return keSiri(t.getFullYear(), t.getMonth(), t.getDate()); }
                case 'DATE': {
                    let y = Math.floor(nom(HS(0)));
                    if (y < 1900) y += 1900;
                    return keSiri(y, Math.floor(nom(HS(1))) - 1, Math.floor(nom(HS(2))));
                }
                case 'YEAR': case 'MONTH': case 'DAY': case 'WEEKDAY': {
                    const t = keTarikh(nom(HS(0)));
                    return { YEAR: t.getFullYear(), MONTH: t.getMonth() + 1, DAY: t.getDate(), WEEKDAY: t.getDay() + 1 }[nama];
                }
                case 'EOMONTH': case 'EDATE': {
                    const t = keTarikh(nom(HS(0))), n = Math.floor(nom(HS(1)));
                    return nama === 'EOMONTH' ? keSiri(t.getFullYear(), t.getMonth() + n + 1, 0) : keSiri(t.getFullYear(), t.getMonth() + n, t.getDate());
                }
                case 'DATEDIF': {
                    const a = keTarikh(nom(HS(0))), b = keTarikh(nom(HS(1))), u = teks(HS(2)).toUpperCase();
                    if (b < a) throw Ralat('#NUM!');
                    const bulan = (b.getFullYear() - a.getFullYear()) * 12 + b.getMonth() - a.getMonth() - (b.getDate() < a.getDate() ? 1 : 0);
                    if (u === 'Y') return Math.floor(bulan / 12);
                    if (u === 'M') return bulan;
                    if (u === 'D') return Math.round((b - a) / 864e5);
                    throw Ralat('#NUM!');
                }
            }
            // --- selebihnya: formulajs (hasil Date ditukar ke siri) ---
            const fn = cariFormulajs(nama);
            if (!fn) throw Ralat('#NAME?');
            const args = hujah.map((h) => (h.j === 'kosong' ? undefined : tanpaRalat(nilaiNod(h))));
            let v;
            try { v = fn.apply(null, args); } catch (e) { if (isRalat(e)) throw e; throw Ralat('#VALUE!'); }
            if (v instanceof Error) throw Ralat(v.message && v.message.startsWith('#') ? v.message : '#VALUE!');
            if (v instanceof Date) return dariTarikh(v);
            if (typeof v === 'number' && !isFinite(v)) throw Ralat('#NUM!');
            return v;
        }
        // Tarikh dalam hasil: fungsi tarikh, atau sel tarikh ± nombor (Excel memformat sel sebegitu sebagai tarikh)
        function jenisTarikh(n) {
            if (!n) return false;
            if (n.j === 'fungsi') return FN_TARIKH.has(n.nama) || (['IF', 'IFERROR', 'MAX', 'MIN', 'LARGE', 'SMALL', 'INDEX', 'VLOOKUP', 'XLOOKUP'].includes(n.nama) && n.hujah.some(jenisTarikh));
            if (n.j === 'ruj') {   // julat: format sel kiri-atas (Excel mewarisi format sel yang dirujuk)
                const s = sudut(n.v), m = mentah(s.b1, s.k1);
                return nampakTarikh(m) || (typeof m === 'string' && m.trim().startsWith('=') && jenisTarikhFormula(m));
            }
            if (n.j === 'bin' && (n.op === '+' || n.op === '-')) {
                const a = jenisTarikh(n.kiri), b = jenisTarikh(n.kanan);
                return n.op === '+' ? a !== b : a && !b;   // tarikh − tarikh = bilangan hari (nombor)
            }
            return false;
        }
        function jenisTarikhFormula(f) { try { return jenisTarikh(hurai(tokens(f.trim().replace(/^=/, '')))); } catch (e) { return false; } }
        function nilaiFormula(f) {
            try {
                const pokok = hurai(tokens(f));
                let v = nilaiNod(pokok);
                if (isArr(v)) v = v[0] && v[0].length ? v[0][0] : null;   // hasil tatasusunan dalam satu sel = unsur pertama (tiada tumpahan)
                return { v, tarikh: typeof v === 'number' && jenisTarikh(pokok) };
            } catch (e) {
                if (isRalat(e)) return { v: e };
                return { v: Ralat('#ERROR!') };
            }
        }
        return {
            formula(f) { const r = nilaiFormula(String(f).trim().replace(/^=/, '')); return r.tarikh ? { tarikh: r.v } : r.v; },
            sel(b, k) {
                const m = mentah(b, k);
                if (typeof m === 'string' && m.trim().startsWith('=')) {
                    const v = selNilai(b, k);
                    if (v === null) return 0;
                    return typeof v === 'number' && jenisTarikhFormula(m) ? { tarikh: v } : v;
                }
                return m;
            },
            sudut,
        };
    }
    function nomKeTeks(n) {
        if (Number.isInteger(n) && Math.abs(n) < 1e15) return String(n);
        const p = +n.toPrecision(15);
        if (Math.abs(p) >= 1e15 || (Math.abs(p) < 1e-9 && p !== 0)) return p.toExponential().toUpperCase().replace(/E\+?/, 'E+').replace('E+-', 'E-');
        return String(p);
    }
    const terimaTarikh = (v) => (v && typeof v === 'object' && 'tarikh' in v ? v.tarikh : v);

    const EnjinFormula = {
        nilai(formula, grid) {
            const f = String(formula || '').trim().replace(/[“”„″]/g, '"');
            if (!f.startsWith('=')) return nilaiPaparan(f);
            // fungsi yang MEMULANGKAN senarai (tumpah ke banyak sel) belum disokong: lebih baik ralat jelas daripada hasil
            // sel pertama yang kelihatan betul. Di dalam fungsi lain (=COUNTA(UNIQUE(…))) ia tetap dikira.
            if (/^=\s*(UNIQUE|SORT|SORTBY|FILTER|SEQUENCE|RANDARRAY|TRANSPOSE|TOCOL|TOROW|WRAPROWS|WRAPCOLS|VSTACK|HSTACK|CHOOSECOLS|CHOOSEROWS|TAKE|DROP|EXPAND|TEXTSPLIT|GROUPBY|PIVOTBY)\s*\(/i.test(f)) return Ralat('#SENARAI');
            const v = lembaran(grid).formula(f);
            return v === null ? 0 : v;   // =B4 (kosong) dipapar 0 macam Excel
        },
        lembaran,
        fungsi(formula) {
            try { return tokens(String(formula).replace(/^=/, '')).filter((t) => t.j === 'fungsi').map((t) => t.v).sort(); } catch (e) { return null; }
        },
        adaRujukan(formula) {
            try { return tokens(String(formula).replace(/^=/, '')).some((t) => t.j === 'ruj'); } catch (e) { return false; }
        },
        /**
         * Tandatangan untuk jawapan "setara": fungsi (diisih), SEL yang dirujuk (julat dikembangkan, dalam had grid), pemalar (diisih).
         * =SUM(B4,B3,B2) ≡ =SUM(B2:B4); =MAX(B4,B5) ≢ =MAX(B2:B6); =IF(B2>0,…) ≢ =IF(B2>=50,…).
         */
        tandatangan(formula, grid) {
            let t;
            try { t = tokens(String(formula).replace(/^=/, '')); } catch (e) { return null; }
            const L = lembaran(grid || []), sel = new Set(), pemalar = [], fungsi = [];
            const tinggi = (grid || []).length, lebar = (grid || []).reduce((m, r) => Math.max(m, (r || []).length), 0);
            for (const x of t) {
                if (x.j === 'fungsi') fungsi.push(x.v);
                else if (x.j === 'nombor' || x.j === 'teks' || x.j === 'bool') pemalar.push(x.j + ':' + String(x.v).toLowerCase());
                else if (x.j === 'ruj') {
                    let s;
                    try { s = L.sudut(x.v); } catch (e) { return null; }
                    for (let b = s.b1; b <= Math.min(s.b2, tinggi - 1); b++) for (let k = s.k1; k <= Math.min(s.k2, lebar - 1); k++) sel.add(b + ':' + k);
                }
            }
            return { fungsi: fungsi.sort(), sel: [...sel].sort(), pemalar: pemalar.sort() };
        },
        papar(v) {
            if (v && typeof v === 'object' && 'tarikh' in v) {
                const t = keTarikh(v.tarikh);
                return [String(t.getDate()).padStart(2, '0'), String(t.getMonth() + 1).padStart(2, '0'), t.getFullYear()].join('/');
            }
            if (isRalat(v)) return v.ralat === '#BELUM' ? '#N/A' : v.ralat === '#SENARAI' ? '#SPILL!' : v.ralat;   // sebab: UI (js.pg.*)
            if (v === null || v === undefined) return '';
            if (typeof v === 'boolean') return v ? 'TRUE' : 'FALSE';
            if (typeof v === 'number') return nomKeTeks(v);   // format "General" Excel: 15 digit bererti, tiada pemisah ribu
            return String(v);
        },
        sama(a, b) {
            a = terimaTarikh(a); b = terimaTarikh(b);
            if (isRalat(a) || isRalat(b)) return false;
            if (typeof a === 'number' && typeof b === 'number') return bulat15(a) === bulat15(b);
            if (typeof a === 'string' && typeof b === 'string') return a.toLowerCase() === b.toLowerCase();
            return a === b;
        },
        isRalat: (v) => isRalat(terimaTarikh(v)),
    };
    global.EnjinFormula = EnjinFormula;
})(typeof window !== 'undefined' ? window : globalThis);
