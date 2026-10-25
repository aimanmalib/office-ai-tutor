// Tapisan senarai rujukan: kotak carian [data-cari-input] + butang kategori [data-kat] menapis [data-item]
// (data-cat; teks carian = data-cari atau teks elemen; data-nama = nama untuk kedudukan). Setiap perkataan carian mesti ada
// (bukan frasa tepat). Hasil disusun: nama tepat → nama bermula → nama mengandungi → teks lain; kosongkan carian = susunan asal.
// Bilangan → [data-bil] (+ bilangan setiap kategori [data-kat-bil]); kosong → [data-kosong]. Suar statistik carian (stat.js).
(function () {
    const input = document.querySelector('[data-cari-input]');
    const item = Array.from(document.querySelectorAll('[data-item]'));
    if (!input || !item.length) return;
    const butang = Array.from(document.querySelectorAll('[data-kat]'));
    const bil = document.querySelector('[data-bil]'), kosong = document.querySelector('[data-kosong]');
    const kumpulan = Array.from(document.querySelectorAll('[data-kumpulan]'));
    const T = (window.LAMAN && window.LAMAN.T) || {};
    const asal = new Map(item.map((el, i) => [el, i]));
    const asalKumpulan = new Map(kumpulan.map((g, i) => [g, i]));
    const cache = new Map();
    const normal = (s) => s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    const teks = (el) => { if (!cache.has(el)) cache.set(el, normal(el.dataset.cari || el.textContent)); return cache.get(el); };
    const nama = (el) => normal(el.dataset.nama || '');
    let kat = 'all';

    function markah(el, q, kata) {
        if (!q) return 0;
        const n = nama(el);
        if (n && n === q) return 4;
        if (n && n.startsWith(q)) return 3;
        if (n && kata.every((k) => n.includes(k))) return 2;
        return 1;
    }
    function susun(bekas, anak, kunci) {
        anak.sort((a, b) => kunci(a) - kunci(b)).forEach((el) => bekas.appendChild(el));
    }
    function tapis() {
        const q = normal(input.value.trim()), kata = q.split(/\s+/).filter(Boolean);
        const padan = (el) => !kata.length || kata.every((k) => teks(el).includes(k));
        const ikutKat = {};
        let n = 0;
        const m = new Map();
        item.forEach((el) => {
            const ok = padan(el);
            if (ok) ikutKat[el.dataset.cat] = (ikutKat[el.dataset.cat] || 0) + 1;
            const nampak = ok && (kat === 'all' || el.dataset.cat === kat);
            el.hidden = !nampak;
            n += nampak ? 1 : 0;
            m.set(el, markah(el, q, kata));
        });
        // susunan: ikut markah bila mencari, asal bila kosong
        const kunci = (el) => (q ? -m.get(el) * 1e5 : 0) + asal.get(el);
        new Set(item.map((el) => el.parentElement)).forEach((p) => susun(p, Array.from(p.children).filter((c) => asal.has(c)), kunci));
        if (kumpulan.length) {
            const terbaik = (g) => Math.max(0, ...Array.from(g.querySelectorAll('[data-item]:not([hidden])')).map((el) => m.get(el)));
            const induk = kumpulan[0].parentElement;
            susun(induk, kumpulan.slice(), (g) => (q ? -terbaik(g) * 1e3 : 0) + asalKumpulan.get(g));
            kumpulan.forEach((g) => { g.hidden = !g.querySelector('[data-item]:not([hidden])'); });
        }
        document.querySelectorAll('[data-kat-bil]').forEach((s) => {
            const b = s.closest('[data-kat]'), k = b.dataset.kat;
            const nilai = k === 'all' ? Object.values(ikutKat).reduce((a, c) => a + c, 0) : (ikutKat[k] || 0);
            s.textContent = nilai;
            b.hidden = k !== 'all' && nilai === 0 && k !== kat;   // kategori tanpa padanan disorok semasa mencari
        });
        if (bil) bil.textContent = (n === 1 ? (T['js.hasil_1'] || '1') : (T['js.hasil_n'] || '{n}')).replace('{n}', n);
        if (kosong) kosong.hidden = n > 0;
        window.statCari && window.statCari(input.value.trim().toLowerCase(), n);
    }
    let tunda;
    input.addEventListener('input', () => { clearTimeout(tunda); tunda = setTimeout(tapis, 80); });
    butang.forEach((b) => b.addEventListener('click', () => {
        kat = b.dataset.kat;
        butang.forEach((x) => x.setAttribute('aria-pressed', String(x === b)));
        tapis();
    }));
    document.addEventListener('keydown', (e) => {
        if (e.key === '/' && document.activeElement !== input && !/INPUT|TEXTAREA/.test(document.activeElement.tagName)) { e.preventDefault(); input.focus(); }
    });
    // sauh #fn-xxx: buka butiran item itu (muat halaman DAN pautan dalam halaman yang sama)
    function bukaSauh() {
        const sauh = location.hash && document.getElementById(decodeURIComponent(location.hash.slice(1)));
        if (sauh && sauh.tagName === 'DETAILS') { sauh.hidden = false; sauh.open = true; }
    }
    window.addEventListener('hashchange', bukaSauh);
    tapis();
    bukaSauh();
})();
