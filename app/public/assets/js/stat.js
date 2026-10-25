// Statistik pengajar (tanpa identiti): satu suar bila halaman dibuka + carian terakhir selepas pelajar berhenti menaip.
// Bot pengimbas tidak menjalankan JS, jadi kiraan ini = pelawat sebenar. Penerima: /api/stat.php (sentiasa 204).
(function () {
    const subjek = document.documentElement.dataset.subjek || 'excel';
    function hantar(data) {
        const badan = JSON.stringify(Object.assign({ s: subjek }, data));
        try {
            if (navigator.sendBeacon && navigator.sendBeacon('/api/stat.php', new Blob([badan], { type: 'application/json' }))) return;
        } catch (e) { /* jatuh ke fetch */ }
        try { fetch('/api/stat.php', { method: 'POST', body: badan, headers: { 'Content-Type': 'application/json' }, keepalive: true }); } catch (e) {}
    }
    hantar({ j: 'halaman', p: location.pathname });

    // Dipanggil oleh skrip carian halaman formula selepas setiap tapisan; hanya carian yang "berhenti" 1.5 s direkod.
    let tunda = null, terakhir = '';
    window.statCari = function (q, hasil) {
        clearTimeout(tunda);
        q = (q || '').trim().toLowerCase();
        if (q.length < 2) return;
        tunda = setTimeout(() => {
            if (q === terakhir) return;
            terakhir = q;
            hantar({ j: 'cari', q: q, n: hasil });
        }, 1500);
    };
})();
