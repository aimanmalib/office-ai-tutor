// Sauh sisi halaman rujukan pantas: pautan #toc bagi bahagian yang sedang dibaca dapat aria-current="true" (garis aksen, CSS
// .sauh). Bahagian aktif = yang terakhir kepalanya sudah melepasi garis 30 % skrin; dikira semula semasa tatal (rAF).
(function () {
    const toc = document.getElementById('toc');
    if (!toc) return;
    const pautan = new Map([...toc.querySelectorAll('a[href^="#"]')].map((a) => [decodeURIComponent(a.hash.slice(1)), a]));
    const bahagian = [...document.querySelectorAll('[data-sauh-bahagian]')].filter((s) => pautan.has(s.id));
    if (!bahagian.length) return;
    let semasa = null, tunggu = 0;
    function kira() {
        tunggu = 0;
        const garis = innerHeight * 0.3;
        let aktif = bahagian[0];
        for (const s of bahagian) { if (s.getBoundingClientRect().top - garis <= 0) aktif = s; else break; }
        if (innerHeight + scrollY >= document.documentElement.scrollHeight - 2) aktif = bahagian[bahagian.length - 1];
        if (aktif === semasa) return;
        if (semasa) pautan.get(semasa.id).removeAttribute('aria-current');
        pautan.get(aktif.id).setAttribute('aria-current', 'true');
        semasa = aktif;
    }
    addEventListener('scroll', () => { if (!tunggu) tunggu = requestAnimationFrame(kira); }, { passive: true });
    addEventListener('resize', kira);
    kira();
})();
