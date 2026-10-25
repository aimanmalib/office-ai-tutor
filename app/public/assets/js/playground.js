// Makmal formula Excel: grid boleh disunting yang mengira formula (formula-enjin.js). Data contoh = window.DATA.playground
// (config/pek/excel/[en/]playground.php). Isian pelawat = localStorage pelayar ini sahaja (kunci berasingan setiap bahasa).
(function () {
    const DATA = (window.DATA || {}).playground || {};
    const E = window.EnjinFormula;
    const KUNCI = (window.DATA || {}).kunci || 'excel_playground_v1';
    const T = (window.LAMAN || {}).T || {};
    const pilih = document.getElementById('pg-contoh'), bar = document.getElementById('pg-bar'), nama = document.getElementById('pg-nama');
    const hasil = document.getElementById('pg-hasil'), jadual = document.getElementById('pg-grid'), cadangan = document.getElementById('pg-cadangan');
    if (!E || !jadual) return;
    const LAJUR = 8, BARIS = 20;
    let set = Object.keys(DATA)[0], grid = [], aktif = { b: 0, k: 0 };

    const huruf = (k) => String.fromCharCode(65 + k);
    const esc = (t) => String(t).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    function kosongkan(g) {
        const out = [];
        for (let b = 0; b < Math.max(BARIS, g.length + 3) && b < BARIS; b++) {
            const r = [];
            for (let k = 0; k < LAJUR; k++) r.push(g[b] && g[b][k] !== undefined && g[b][k] !== null ? g[b][k] : '');
            out.push(r);
        }
        return out;
    }
    function muat(id, simpanan) {
        set = id;
        grid = kosongkan(simpanan || (DATA[id] ? DATA[id].grid : []));
        pilih.value = id;
        lukisCadangan();
        lukis();
        pilihSel(0, 0);
    }
    function simpan() { try { localStorage.setItem(KUNCI, JSON.stringify({ set, grid })); } catch (e) {} }

    function lukis() {
        const L = E.lembaran(grid);   // satu penilai + cache untuk semua sel (formula berantai tidak dikira berulang)
        let h = '<thead><tr><td></td>';
        for (let k = 0; k < LAJUR; k++) h += `<th scope="col"${k === aktif.k ? ' class="aktif"' : ''}>${huruf(k)}</th>`;
        h += '</tr></thead><tbody>';
        for (let b = 0; b < grid.length; b++) {
            h += `<tr><th scope="row"${b === aktif.b ? ' class="aktif"' : ''}>${b + 1}</th>`;
            for (let k = 0; k < LAJUR; k++) {
                const mentah = grid[b][k];
                const formula = typeof mentah === 'string' && mentah.trim().startsWith('=');
                const v = formula ? L.sel(b, k) : mentah;
                const ralat = formula && E.isRalat(v);
                const tajuk = b === 0 && !formula && mentah !== '';
                const kelas = [(b === aktif.b && k === aktif.k) ? 'pilih' : '', typeof v === 'number' ? 'nombor' : '', ralat ? 'ralat' : '', tajuk ? 'tajuk' : '', 'cursor-pointer min-w-[5.5rem]'].join(' ');
                const papar = formula ? E.papar(v) : (typeof mentah === 'number' ? E.papar(mentah) : mentah);
                h += `<td class="${kelas}" data-b="${b}" data-k="${k}" tabindex="-1">${esc(papar)}</td>`;
            }
            h += '</tr>';
        }
        jadual.innerHTML = h + '</tbody>';
    }
    function pilihSel(b, k) {
        aktif = { b, k };
        nama.textContent = huruf(k) + (b + 1);
        const mentah = grid[b][k];
        bar.value = mentah === null || mentah === undefined ? '' : String(mentah);
        tunjukHasil();
        lukis();
    }
    function tunjukHasil() {
        const f = bar.value.trim();
        if (!f.startsWith('=')) { hasil.textContent = ''; return; }
        const g = grid.map((r) => r.slice()); g[aktif.b][aktif.k] = '';
        const v = E.nilai(f, g);
        const sebab = { '#SENARAI': 'js.pg.senarai', '#BELUM': 'js.pg.belum', '#NAME?': 'js.pg.name', '#VALUE!': 'js.pg.value', '#DIV/0!': 'js.pg.div0', '#N/A': 'js.pg.na', '#ERROR!': 'js.pg.error', '#CIRC!': 'js.pg.circ' }[v && v.ralat];
        hasil.textContent = '→ ' + E.papar(v) + (E.isRalat(v) && sebab ? '  ' + (T[sebab] || '') : '');
        hasil.className = 'px-4 py-1.5 font-mono text-xs min-h-[1.75rem] border-t border-line bg-surface-2/40 ' + (E.isRalat(v) ? 'text-err' : 'text-ok');
    }
    function tulis() {
        const t = bar.value.trim();
        grid[aktif.b][aktif.k] = t !== '' && !t.startsWith('=') && /^-?\d+(\.\d+)?$/.test(t) ? parseFloat(t) : t;
        simpan();
        lukis();
    }
    function lukisCadangan() {
        cadangan.innerHTML = ((DATA[set] && DATA[set].cadangan) || []).map((f) =>
            `<button type="button" class="cip cip-aksen font-mono font-normal">${esc(f)}</button>`).join('');
    }

    // Seperti Excel: sedang menaip formula (bar difokus, bermula "=") → ketik sel = sisip rujukannya pada kursor.
    let sisip = false;
    jadual.addEventListener('pointerdown', (e) => {
        const td = e.target.closest('td[data-b]');
        sisip = !!td && document.activeElement === bar && bar.value.trim().startsWith('=') && /[=(,:+\-*/^&<>;]\s*$/.test(bar.value.slice(0, bar.selectionStart ?? bar.value.length))
            && !(+td.dataset.b === aktif.b && +td.dataset.k === aktif.k);
        if (sisip) e.preventDefault();   // kekalkan fokus + kursor dalam bar
    });
    jadual.addEventListener('click', (e) => {
        const td = e.target.closest('td[data-b]');
        if (!td) return;
        if (sisip) {
            const ruj = huruf(+td.dataset.k) + (+td.dataset.b + 1);
            const a = bar.selectionStart ?? bar.value.length, z = bar.selectionEnd ?? a;
            bar.value = bar.value.slice(0, a) + ruj + bar.value.slice(z);
            bar.setSelectionRange(a + ruj.length, a + ruj.length);
            sisip = false;
            tulis(); tunjukHasil(); bar.focus();
            return;
        }
        pilihSel(+td.dataset.b, +td.dataset.k); bar.focus();
    });
    bar.addEventListener('input', () => { tulis(); tunjukHasil(); });
    bar.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); if (aktif.b < grid.length - 1) pilihSel(aktif.b + 1, aktif.k); bar.focus(); }
    });
    pilih.addEventListener('change', () => { muat(pilih.value); simpan(); });
    document.getElementById('pg-reset').addEventListener('click', async () => {
        if (window.sahkan && !(await sahkan({ tajuk: T['js.pg.reset_tajuk'], teks: T['js.pg.reset_teks'], ya: T['js.pg.reset_ya'], bahaya: true }))) return;
        muat(set); simpan();
    });
    cadangan.addEventListener('click', (e) => {
        const b = e.target.closest('button');
        if (!b) return;
        // sel kosong pertama di bawah data, lajur pertama yang ada data di atasnya
        let sasaran = grid.findIndex((r, i) => i > 0 && r.every((c) => c === '' || c === null));
        if (sasaran < 0) sasaran = grid.length - 1;
        pilihSel(sasaran, 0);
        bar.value = b.textContent;
        tulis(); tunjukHasil();
        bar.focus();
    });

    let simpanan = null;
    try { simpanan = JSON.parse(localStorage.getItem(KUNCI) || 'null'); } catch (e) {}
    if (simpanan && DATA[simpanan.set] && Array.isArray(simpanan.grid)) muat(simpanan.set, simpanan.grid);
    else muat(set);
})();
