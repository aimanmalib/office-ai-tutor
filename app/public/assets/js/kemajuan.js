// Kemajuan set latihan di luar halaman latihan (halaman subjek): baca localStorage yang sama dengan latihan.js dan isi
// [data-kemajuan] + [data-kemajuan-angka] + [data-kemajuan-bar] setiap kad [data-kemajuan-kunci]. Dimuat oleh subjek.php sahaja.
(function () {
    const T = (window.LAMAN && window.LAMAN.T) || {};
    const isi = (k, o) => String(T[k] || '').replace(/\{(\w+)\}/g, (m, n) => (n in o ? o[n] : m));
    document.querySelectorAll('[data-kemajuan-kunci]').forEach((kad) => {
        let s = null;
        try { s = JSON.parse(localStorage.getItem(kad.dataset.kemajuanKunci) || 'null'); } catch (e) { /* kosong */ }
        const n = +kad.dataset.n || 0, j = s && s.answers ? Object.values(s.answers) : [];
        if (!n || !j.length) return;
        const c = Math.min(j.length, n), b = j.filter((a) => a && a.correct).length;
        const p = kad.querySelector('[data-kemajuan]'), bar = kad.querySelector('[data-kemajuan-bar]'), angka = kad.querySelector('[data-kemajuan-angka]');
        if (angka) angka.textContent = c + '/' + n;
        if (p) p.textContent = s.completed && c >= n ? isi('js.lat.siap', { b, n }) : isi('js.lat.sambung', { i: Math.min((s.currentIndex || 0) + 1, n), n });
        if (bar) bar.style.width = (c / n * 100) + '%';
    });
})();
