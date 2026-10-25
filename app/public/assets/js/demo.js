// Demo wira laman utama: grid data Playground Excel + satu formula yang pelawat boleh ubah. Sel yang dirujuk formula
// diserlahkan; hasil duduk dalam sel sasaran (lajur selepas data, baris 2) yang berkelip sekali setiap kali dinilai.
// Semasa muat, formula "ditaip" sekali (nilai input sahaja, tiada animasi CSS); fokus/ketikan pelawat menamatkannya serta-merta.
// prefers-reduced-motion = keadaan akhir terus. Enjin = formula-enjin.js (sama dengan latihan dan playground).
(function () {
    const D = (window.DATA || {}).demo, E = window.EnjinFormula;
    const jadual = document.getElementById('demo-grid'), input = document.getElementById('demo-formula');
    const hasil = document.getElementById('demo-hasil'), namaSel = document.getElementById('demo-sel');
    if (!D || !E || !jadual || !input) return;
    const grid = D.grid, lajur = Math.max(...grid.map((r) => r.length));
    const huruf = (k) => String.fromCharCode(65 + k);
    const esc = (t) => String(t).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const tenang = matchMedia('(prefers-reduced-motion: reduce)').matches;
    namaSel.textContent = huruf(lajur) + '2';

    function dirujuk(f) {
        const set = new Set();
        const re = /\$?([A-Z]{1,2})\$?(\d+)(?::\$?([A-Z]{1,2})\$?(\d+))?/g;
        let m;
        while ((m = re.exec(f.toUpperCase()))) {
            const k1 = m[1].charCodeAt(0) - 65, b1 = +m[2] - 1;
            const k2 = m[3] ? m[3].charCodeAt(0) - 65 : k1, b2 = m[4] ? +m[4] - 1 : b1;
            for (let b = Math.min(b1, b2); b <= Math.max(b1, b2); b++) for (let k = Math.min(k1, k2); k <= Math.max(k1, k2); k++) set.add(b + ',' + k);
        }
        return set;
    }
    function lukis(kelip) {
        const f = input.value.trim(), L = E.lembaran(grid), formula = f.startsWith('='), ruj = formula ? dirujuk(f) : new Set();
        const v = formula ? E.nilai(f, grid) : null, teks = formula ? E.papar(v) : '—', ralat = formula && E.isRalat(v);
        let h = '<thead><tr><td></td>';
        for (let k = 0; k <= lajur; k++) h += `<th scope="col">${huruf(k)}</th>`;
        h += '</tr></thead><tbody>';
        grid.forEach((r, b) => {
            h += `<tr><th scope="row">${b + 1}</th>`;
            for (let k = 0; k < lajur; k++) {
                const m = r[k] === undefined ? '' : r[k];
                const form = typeof m === 'string' && m.startsWith('=');
                const nv = form ? L.sel(b, k) : m;
                const kls = [typeof nv === 'number' ? 'nombor' : '', b === 0 ? 'tajuk' : '', ruj.has(b + ',' + k) ? 'pilih' : '', form && E.isRalat(nv) ? 'ralat' : ''].join(' ');
                h += `<td class="${kls}">${esc(form || typeof m === 'number' ? E.papar(nv) : m)}</td>`;
            }
            h += b === 1 ? `<td class="sasaran${typeof v === 'number' ? ' nombor' : ''}${ralat ? ' ralat' : ''}" id="demo-sasaran">${formula ? esc(teks) : ''}</td>` : '<td></td>';
            h += '</tr>';
        });
        jadual.innerHTML = h + '</tbody>';
        hasil.textContent = teks;
        hasil.classList.toggle('text-err', ralat);
        hasil.classList.toggle('text-accent', !ralat);
        if (kelip && formula && !tenang) document.getElementById('demo-sasaran').classList.add('kelip');
    }

    // taip sekali semasa muat
    const penuh = input.value;
    let pemasa = null, tunda = null;
    function tamatTaip() {
        if (pemasa === null) return;
        clearTimeout(pemasa); pemasa = null;
        input.value = penuh;
    }
    input.addEventListener('focus', tamatTaip);
    input.addEventListener('pointerdown', tamatTaip);
    input.addEventListener('input', () => {
        tamatTaip();
        lukis(false);
        clearTimeout(tunda);
        tunda = setTimeout(() => lukis(true), 500);   // berkelip bila pelawat berhenti menaip
    });
    // skrin sempit: grid ditatal mendatar — bawa sel hasil ke dalam pandangan sekali selepas dinilai (tatal bekas sahaja, bukan halaman)
    function tunjukSasaran() {
        const bekas = jadual.parentElement, s = document.getElementById('demo-sasaran');
        if (!s || bekas.scrollWidth <= bekas.clientWidth) return;
        const lebih = s.getBoundingClientRect().right - bekas.getBoundingClientRect().right;
        if (lebih > 0) bekas.scrollTo({ left: bekas.scrollLeft + lebih + 8, behavior: tenang ? 'auto' : 'smooth' });
    }
    if (tenang) { lukis(false); tunjukSasaran(); return; }
    input.value = '';
    lukis(false);
    let i = 0;
    const langkah = () => {
        i++;
        input.value = penuh.slice(0, i);
        if (i < penuh.length) { pemasa = setTimeout(langkah, 55); return; }
        pemasa = null;
        lukis(true);
        tunjukSasaran();
    };
    pemasa = setTimeout(langkah, 700);
})();
