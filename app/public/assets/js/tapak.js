// Kelakuan dikongsi setiap halaman: togol tema, menu telefon, tutup <details> menu bila klik di luar, muncul semasa tatal,
// butang salin. Kelakuan satu halaman sahaja = fail sendiri melalui $skrip (kemajuan.js, sauh.js, tapis.js).
(function () {
    const akar = document.documentElement;
    const T = (window.LAMAN && window.LAMAN.T) || {};
    function lukisTema() {
        const gelap = akar.getAttribute('data-tema') === 'gelap';
        document.querySelectorAll('.tema-cerah').forEach((e) => { e.classList.toggle('hidden', gelap); });
        document.querySelectorAll('.tema-gelap').forEach((e) => { e.classList.toggle('hidden', !gelap); e.classList.toggle('inline-flex', gelap); });
    }
    document.querySelectorAll('[data-togol-tema]').forEach((b) => b.addEventListener('click', () => {
        const baru = akar.getAttribute('data-tema') === 'gelap' ? 'cerah' : 'gelap';
        akar.setAttribute('data-tema', baru);
        try { localStorage.setItem('tema', baru); } catch (e) {}
        lukisTema();
    }));
    lukisTema();

    // menu telefon: dialog skrin penuh; kunci tatal pada <html> (bukan body — body jadi bekas tatal sendiri bila html overflow-x)
    const menu = document.getElementById('menu-telefon');
    const buka = document.querySelector('[data-buka-menu]');
    function tetapMenu(b) {
        if (!menu) return;
        menu.classList.toggle('hidden', !b); menu.classList.toggle('flex', b);
        akar.style.overflow = b ? 'hidden' : '';
        if (buka) buka.setAttribute('aria-expanded', String(b));
        if (b) menu.querySelector('[data-tutup-menu]')?.focus(); else buka?.focus();
    }
    buka?.addEventListener('click', () => tetapMenu(true));
    menu?.querySelector('[data-tutup-menu]')?.addEventListener('click', () => tetapMenu(false));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && menu && !menu.classList.contains('hidden')) tetapMenu(false); });

    // <details> menu (pilih subjek): tutup bila klik di luar / Esc
    document.addEventListener('click', (e) => {
        document.querySelectorAll('details[open]').forEach((d) => { if (d.id === 'pilih-subjek' && !d.contains(e.target)) d.open = false; });
    });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') document.querySelectorAll('details#pilih-subjek[open]').forEach((d) => { d.open = false; }); });

    // muncul semasa tatal: [data-muncul] dapat .muncul sekali bila masuk skrin (CSS dalam tapak.css; html.gerak dari <head>).
    // IO hanya melapor bila keadaan bersilang BERUBAH: lompat terus ke bawah (End, pautan sauh, tatal laju) melangkau bahagian
    // yang tidak pernah bersilang — jadi setiap laporan + setiap tatal juga memaparkan semua yang sudah di atas garis skrin.
    window.__gerak = 1;
    if (akar.classList.contains('gerak')) {
        let baki = [...document.querySelectorAll('[data-muncul]')], tunggu = 0;
        // selepas gerak tamat (450 ms + lengah 120 ms): "siap" = peraturan gerak berhenti, peralihan hover kad kembali (tapak.css)
        const tunjuk = (el) => { el.classList.add('muncul'); setTimeout(() => { el.dataset.muncul = 'siap'; }, 700); };
        const sapu = () => {
            tunggu = 0;
            const had = innerHeight * 0.92;
            baki = baki.filter((el) => (el.getBoundingClientRect().top < had ? (tunjuk(el), false) : true));
            if (!baki.length) { io.disconnect(); removeEventListener('scroll', jadual); }
        };
        const jadual = () => { if (!tunggu) tunggu = requestAnimationFrame(sapu); };
        const io = new IntersectionObserver(jadual, { rootMargin: '0px 0px -8% 0px' });
        baki.forEach((el) => io.observe(el));
        addEventListener('scroll', jadual, { passive: true });
    }

    // butang salin: data-salin="teks" → papan keratan; label bertukar sebentar
    document.addEventListener('click', async (e) => {
        const b = e.target.closest('[data-salin]');
        if (!b) return;
        try { await navigator.clipboard.writeText(b.getAttribute('data-salin')); } catch (err) { return; }
        const asal = b.getAttribute('aria-label') || '';
        b.setAttribute('aria-label', T['js.disalin'] || 'Copied');
        b.classList.add('disalin');   // tapak.css: ikon → tanda semak
        setTimeout(() => { b.setAttribute('aria-label', asal); b.classList.remove('disalin'); }, 1400);
    });
})();

// Dialog pengesahan dalam halaman (ganti confirm() pelayar): sahkan({tajuk, teks, ya, tidak, bahaya}) → Promise<boolean>.
// Fokus awal pada Batal; Esc/klik tirai = Batal.
window.sahkan = function (o) {
    const T = (window.LAMAN && window.LAMAN.T) || {};
    return new Promise((selesai) => {
        const d = document.createElement('dialog');
        d.className = 'kad shadow-tingkap p-0 w-[min(26rem,calc(100vw-2rem))] backdrop:bg-tirai/40';
        const esc = (s) => String(s || '').replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
        d.innerHTML = `<form method="dialog" class="p-6"><h2 class="text-h3">${esc(o.tajuk)}</h2><p class="mt-2 text-sm text-ink-2">${esc(o.teks)}</p>
            <div class="mt-6 flex justify-end gap-2"><button value="0" class="btn btn-kedua" autofocus>${esc(o.tidak || T['js.batal'] || 'Cancel')}</button>
            <button value="1" class="btn ${o.bahaya ? 'btn-bahaya' : 'btn-utama'}">${esc(o.ya || 'OK')}</button></div></form>`;
        document.body.appendChild(d);
        d.addEventListener('click', (e) => { if (e.target === d) d.close('0'); });
        d.addEventListener('close', () => { selesai(d.returnValue === '1'); d.remove(); });
        d.showModal();
    });
};
