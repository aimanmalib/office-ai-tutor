// Latihan interaktif (revamp v2): pilih set → skrin mula → soalan (formula | result | choice | pintasan) → selesai.
// Kemajuan dalam localStorage '<subjek>_latihan_v2_<set>' (sama dengan versi lama — kemajuan pelajar dikekalkan; BM/EN
// berkongsi kemajuan kerana id soalan sama). Jawapan disemak di pelayan (/api/exercise-check.php); enjin formula pelayar
// hanya untuk hasil langsung + "formula setara". Teks UI = window.LAMAN.T (config/bahasa/<bahasa>.php, kunci js.*).
(function () {
    const L = window.LAMAN || {};
    const T = L.T || {};
    const t = (k, g) => { let s = T[k] || k; Object.entries(g || {}).forEach(([a, b]) => { s = s.split('{' + a + '}').join(b); }); return s; };
    const sets = (window.DATA || {}).sets || {};
    const ids = Object.keys(sets);
    if (!ids.length) return;
    const SUBJEK = L.subjek || 'excel';
    const PREFIX = SUBJEK + '_latihan_v2_';
    const $ = (id) => document.getElementById(id);
    const el = { pilih: $('set-picker'), mula: $('welcome-screen'), soalan: $('question-area'), selesai: $('completion-screen'), bar: $('progress-bar-wrap'),
        kad: $('question-card'), label: $('set-label'), kemajuan: $('progress-text'), seg: $('progress-seg'), skor: $('score-text'),
        mulaTajuk: $('welcome-title'), mulaTeks: $('welcome-desc'), mulaBil: $('welcome-count'), rawak: $('random-toggle'), btnMula: $('start-btn'),
        btnReset: $('reset-btn'), skorAkhir: $('final-score'), jumlahAkhir: $('final-total'), mesejAkhir: $('completion-msg'),
        lemah: $('weak-areas'), senaraiLemah: $('weak-list'), mulaJubin: $('welcome-jubin'), mulaKemajuan: $('welcome-kemajuan'), mulaSeg: $('welcome-seg') };
    const esc = (s) => String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const polos = (h) => { const d = document.createElement('div'); d.innerHTML = String(h || ''); return (d.textContent || '').trim(); };
    const ikon = (n, k) => (window.IKON && window.IKON[n] ? window.IKON[n].replace('class="', 'class="' + (k || 'w-4 h-4') + ' ') : '');

    let aktif = null;
    let st = baru();
    function baru() { return { order: [], currentIndex: 0, answers: {}, started: false, completed: false, randomOrder: false }; }
    function muat(id) {
        try {
            const s = JSON.parse(localStorage.getItem(PREFIX + id) || 'null');
            if (s && typeof s === 'object') {
                // soalan boleh ditambah/dibuang oleh pengajar selepas kemajuan disimpan: padankan ikut id
                const semua = sets[id].questions.map((q) => q.id), ada = new Set(semua);
                const lama = Array.isArray(s.order) ? s.order : [];
                const order = lama.filter((x) => ada.has(x));
                if (order.length) semua.forEach((x) => { if (!order.includes(x)) order.push(x); });
                const answers = {};
                Object.entries(s.answers || {}).forEach(([x, a]) => { if (ada.has(x)) answers[x] = a; });
                let i = order.indexOf(lama[s.currentIndex || 0]);
                if (i < 0) i = Math.max(0, order.findIndex((x) => !answers[x]));
                return { order, currentIndex: i, answers, started: !!s.started, randomOrder: !!s.randomOrder,
                    completed: !!s.completed && order.length > 0 && order.every((x) => answers[x]) };
            }
        } catch (e) { /* kosong */ }
        return baru();
    }
    function simpan() { if (aktif) try { localStorage.setItem(PREFIX + aktif, JSON.stringify(st)); } catch (e) {} }
    function kosongkan() { st = baru(); if (aktif) try { localStorage.removeItem(PREFIX + aktif); } catch (e) {} }
    const betul = () => Object.values(st.answers).filter((a) => a.correct).length;
    const dicuba = () => Object.keys(st.answers).length;
    const kocok = (a) => { a = a.slice(); for (let i = a.length - 1; i > 0; i--) { const j = Math.floor(Math.random() * (i + 1)); [a[i], a[j]] = [a[j], a[i]]; } return a; };
    const huruf = (i) => { let s = ''; i += 1; while (i > 0) { i -= 1; s = String.fromCharCode(65 + (i % 26)) + s; i = Math.floor(i / 26); } return s; };
    const sel = (c) => { const m = /^([A-Z]+)(\d+)$/.exec(c || ''); if (!m) return null; let k = 0; for (const ch of m[1]) k = k * 26 + ch.charCodeAt(0) - 64; return { col: k - 1, row: +m[2] - 1 }; };

    function tunjuk(nama) {
        ['pilih', 'mula', 'soalan', 'selesai'].forEach((n) => { el[n].hidden = n !== nama; });
        // selepas set dipilih, kepala halaman disorok: skrin mula/soalan/selesai + butang utama kekal di atas lipatan (telefon)
        const kepala = $('lat-kepala'); if (kepala) kepala.hidden = nama !== 'pilih';
        el.bar.hidden = nama !== 'soalan';
    }
    function keAtas() { try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch (e) { window.scrollTo(0, 0); } }

    // ---------- pilih set ----------
    function lukisKemajuanSet() {
        document.querySelectorAll('[data-set]').forEach((kad) => {
            const id = kad.dataset.set, n = sets[id].questions.length, s = muat(id);
            const c = Object.keys(s.answers).length, b = Object.values(s.answers).filter((a) => a.correct).length;
            const p = kad.querySelector('[data-kemajuan]'), bar = kad.querySelector('[data-kemajuan-bar]');
            const angka = kad.querySelector('[data-kemajuan-angka]'), cta = kad.querySelector('[data-cta]');
            if (p) p.textContent = !c ? t('js.lat.belum') : s.completed ? t('js.lat.siap', { b, n }) : t('js.lat.sambung', { i: s.currentIndex + 1, n });
            if (bar) bar.style.width = (c / n * 100) + '%';
            if (angka) angka.textContent = c + '/' + n;
            if (cta) { cta.dataset.asal = cta.dataset.asal || cta.textContent; cta.textContent = !c ? cta.dataset.asal : t(s.completed ? 'js.lat.cta_keputusan' : 'js.lat.cta_sambung'); }
            kad.dataset.keadaan = !c ? 'belum' : s.completed ? 'siap' : 'sambung';
        });
    }
    function keSenarai() {
        aktif = null;
        if (location.hash || location.search) try { history.replaceState(null, '', location.pathname); } catch (e) {}
        tunjuk('pilih');
        lukisKemajuanSet();
        keAtas();
    }
    function aktifkan(id) {
        if (!sets[id]) return keSenarai();
        aktif = id;
        st = muat(id);
        try { history.replaceState(null, '', location.pathname + '#' + id); } catch (e) {}
        if (dicuba() >= sets[id].questions.length) return selesai();
        skrinMula();
    }

    // ---------- skrin mula ----------
    function skrinMula() {
        const s = sets[aktif], n = s.questions.length;
        if (dicuba() >= n) return selesai();
        tunjuk('mula');
        el.mulaTajuk.textContent = s.name;
        el.mulaTeks.textContent = s.description || '';
        el.mulaBil.textContent = t('js.lat.bil_soalan', { n });
        const kadSet = document.querySelector(`[data-set="${CSS.escape(aktif)}"] [data-jubin]`);
        el.mulaJubin.innerHTML = kadSet ? kadSet.innerHTML.replace('w-5 h-5', 'w-6 h-6') : '';
        el.mulaKemajuan.hidden = dicuba() === 0;
        if (dicuba()) segmen(el.mulaSeg, -1);
        el.rawak.checked = !!st.randomOrder;
        el.btnReset.hidden = dicuba() === 0;
        el.btnMula.querySelector('span').textContent = st.started && dicuba() < n ? t('js.lat.sambung_btn', { i: st.currentIndex + 1 }) : t('js.lat.mula_btn');
        keAtas();
    }
    function mulaSesi() {
        const s = sets[aktif], semua = s.questions.map((q) => q.id);
        const tukarRawak = !!el.rawak.checked !== !!st.randomOrder;
        st.randomOrder = !!el.rawak.checked;
        // susunan dibina semula bila bilangan soalan berubah, atau pilihan rawak ditukar sebelum menjawab apa-apa
        if (!st.order || st.order.length !== semua.length || (tukarRawak && dicuba() === 0)) { st.order = st.randomOrder ? kocok(semua) : semua; st.currentIndex = 0; }
        st.started = true;
        simpan();
        tunjuk('soalan');
        el.label.textContent = s.name;
        lukis(st.currentIndex);
        kemas();
    }

    // ---------- soalan ----------
    const soalan = (i) => sets[aktif].questions.find((q) => q.id === st.order[i]) || null;
    function grid(g, sasaran) {
        if (!g || !g.length) return '';
        const k = Math.max(...g.map((r) => r.length)), p = sel(sasaran);
        let h = '<thead><tr><td></td>';
        for (let c = 0; c < k; c++) h += `<th scope="col">${huruf(c)}</th>`;
        h += '</tr></thead><tbody>';
        g.forEach((r, b) => {
            h += `<tr><th scope="row">${b + 1}</th>`;
            for (let c = 0; c < k; c++) {
                const v = r[c] == null ? '' : r[c];
                const kls = [p && p.row === b && p.col === c ? 'sasaran' : '', b === 0 ? 'tajuk' : '', /^-?[\d,]+(\.\d+)?%?$/.test(String(v)) ? 'nombor' : ''].join(' ');
                h += `<td class="${kls}">${esc(p && p.row === b && p.col === c && v === '?' ? '' : v)}</td>`;
            }
            h += '</tr>';
        });
        return `<div class="tatal-x mt-5 rounded-md border border-line" tabindex="0" role="region" aria-label="${esc(t('js.lat.data'))}"><table class="hamparan w-full">${h}</tbody></table></div>`;
    }
    const tahap = (n) => `<span class="inline-flex items-center gap-1" role="img" title="${esc(t('js.lat.tahap', { n }))}" aria-label="${esc(t('js.lat.tahap', { n }))}">${[1, 2, 3].map((i) => `<span class="w-1.5 h-1.5 rounded-full ${i <= n ? 'bg-atas-aksen' : 'bg-atas-aksen/35'}"></span>`).join('')}</span>`;

    function lukis(i) {
        const q = soalan(i);
        if (!q) return;
        let interaksi;
        if (q.mode === 'choice') {
            interaksi = `<fieldset class="mt-6"><legend class="sr-only">${esc(t('js.lat.pilih_satu'))}</legend><div class="grid gap-2" role="radiogroup">
                ${(q.options || []).map((o, j) => `<button type="button" role="radio" aria-checked="false" data-choice="${j}" class="pilihan">
                    <span class="pilihan-huruf">${String.fromCharCode(65 + j)}</span>
                    <span class="flex-1 text-[0.9375rem] teks">${o}</span></button>`).join('')}
            </div></fieldset>`;
        } else {
            const pint = q.mode === 'pintasan';
            interaksi = `<div class="mt-6">
                <label for="answer-input" class="block text-sm font-medium mb-2">${esc(t(pint ? 'js.lat.label_pintasan' : q.mode === 'result' ? 'js.lat.label_jawapan' : 'js.lat.label_formula'))}</label>
                <div class="medan-fx">
                    ${q.highlight && !pint ? `<span class="medan-fx-nama" aria-hidden="true">${esc(q.highlight)}</span>` : ''}
                    ${q.mode === 'formula' ? '<span class="medan-fx-fx" aria-hidden="true">fx</span>' : ''}
                    <input id="answer-input" ${pint ? 'data-pintasan' : ''} type="text" autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false"
                       placeholder="${pint ? 'Ctrl + …' : q.mode === 'result' ? '' : '=…'}" aria-describedby="ralat-input">
                </div>
                <p id="ralat-input" class="text-sm text-err mt-2" hidden></p>
                ${q.grid && q.mode === 'formula' && window.EnjinFormula ? '<p id="hasil-langsung" class="font-mono text-xs text-muted mt-2.5 min-h-[1rem]" aria-live="polite"></p>' : ''}
                ${pint ? `<p class="text-xs text-muted mt-2">${esc(t('js.lat.nota_pintasan'))}</p>` : ''}
            </div>`;
        }
        el.kad.innerHTML = `
            <div class="tingkap-bar">
                <span class="tingkap-titik hidden sm:flex" aria-hidden="true"><i></i><i></i><i></i></span>
                <span class="font-mono text-[0.75rem] font-semibold uppercase tracking-[0.12em] truncate">${esc(q.category || '')}</span>
                <span class="ml-auto">${tahap(q.level || 1)}</span>
            </div>
            <div class="p-5 sm:p-8">
            <div class="text-[1.0625rem] sm:text-xl leading-relaxed tracking-[-0.01em] teks">${q.prompt}</div>
            ${grid(q.grid, q.highlight)}
            ${interaksi}
            <div class="mt-6 flex flex-wrap items-center gap-2">
                <button id="check-btn" type="button" class="btn btn-utama">${ikon('check')}${esc(t('js.lat.semak'))}</button>
                <button id="hint-btn" type="button" class="btn btn-kedua" aria-expanded="false" aria-controls="hint-area">${ikon('lightbulb')}${esc(t('js.lat.petunjuk'))}</button>
                ${L.url_tutor ? `<button id="ask-ai-btn" type="button" class="btn btn-hantu">${ikon('message-square')}${esc(t('js.lat.tanya'))}</button>` : ''}
                <button id="skip-btn" type="button" class="btn btn-hantu ml-auto">${esc(t('js.lat.langkau'))}${ikon('arrow-right')}</button>
            </div>
            <div id="hint-area" hidden class="nota-aksen mt-4 teks">${ikon('lightbulb', 'w-4 h-4 mt-0.5 shrink-0 text-accent')}<div>${q.hint || esc(t('js.lat.tiada_petunjuk'))}</div></div>
            <div id="feedback-area" hidden class="mt-6"></div>
            </div>`;

        const btnSemak = $('check-btn'), btnPetunjuk = $('hint-btn'), btnLangkau = $('skip-btn'), btnTanya = $('ask-ai-btn');
        const kotak = $('answer-input'), petunjuk = $('hint-area'), maklum = $('feedback-area'), ralatInput = $('ralat-input');
        const pilihan = el.kad.querySelectorAll('.pilihan');
        let dipilih = null;
        pilihan.forEach((b) => b.addEventListener('click', () => {
            pilihan.forEach((x) => { x.setAttribute('aria-checked', 'false'); delete x.dataset.hasil; });
            b.setAttribute('aria-checked', 'true');
            dipilih = +b.dataset.choice;
        }));
        btnPetunjuk.addEventListener('click', () => { petunjuk.hidden = !petunjuk.hidden; btnPetunjuk.setAttribute('aria-expanded', String(!petunjuk.hidden)); });
        btnTanya && btnTanya.addEventListener('click', () => tanya(q));
        btnLangkau.addEventListener('click', () => seterusnya(true));
        btnSemak.addEventListener('click', hantar);

        if (kotak && q.mode === 'pintasan') {
            // rakam kombinasi bila Ctrl/⌘/Alt/kekunci fungsi ditekan; selain itu taip biasa (telefon). Kekunci FIZIKAL (e.code):
            // Shift tidak menukar "=" kepada "+" — sama dengan notasi Microsoft.
            kotak.addEventListener('keydown', (e) => {
                const fn = /^F([1-9]|1[0-2])$/.test(e.key);
                if (!(e.ctrlKey || e.metaKey || e.altKey || fn || (e.shiftKey && e.key === 'Enter'))) { if (e.key === 'Enter') { e.preventDefault(); hantar(); } return; }
                if (['Control', 'Meta', 'Alt', 'Shift'].includes(e.key)) return;
                e.preventDefault();
                const tanda = { Equal: '=', Minus: '-', BracketLeft: '[', BracketRight: ']', Semicolon: ';', Quote: "'", Comma: ',', Period: '.', Slash: '/', Backslash: '\\', Backquote: '`', NumpadAdd: '+', NumpadSubtract: '-' };
                const nama = { ' ': 'Space', Enter: 'Enter', Escape: 'Esc', ArrowUp: '↑', ArrowDown: '↓', ArrowLeft: '←', ArrowRight: '→' }[e.key] || tanda[e.code]
                    || (/^Digit\d$/.test(e.code) ? e.code.slice(5) : /^Key[A-Z]$/.test(e.code) ? e.code.slice(3) : e.key.length === 1 ? e.key.toUpperCase() : e.key);
                const b = [];
                if (e.ctrlKey || e.metaKey) b.push('Ctrl');
                if (e.altKey) b.push('Alt');
                if (e.shiftKey) b.push('Shift');
                b.push(nama);
                kotak.value = b.join('+');
                ralatInput.hidden = true;
            });
        } else if (kotak) {
            kotak.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); hantar(); } });
            kotak.addEventListener('input', () => { ralatInput.hidden = true; });
        }
        if (kotak && matchMedia('(pointer: fine)').matches) kotak.focus();

        // hasil langsung (formula atas grid soalan; sel sasaran dikosongkan — tiada rujukan bulat)
        const gridSoalan = () => { const g = (q.grid || []).map((r) => r.slice()), p = sel(q.highlight); if (p && g[p.row]) g[p.row][p.col] = null; return g; };
        const kira = (f) => (window.EnjinFormula ? EnjinFormula.nilai(f, gridSoalan()) : null);
        const hasilEl = $('hasil-langsung'), selSasaran = el.kad.querySelector('td.sasaran');
        const asalSasaran = selSasaran ? selSasaran.textContent : '';
        // sel sasaran memaparkan hasil sementara (condong, pudar) — pratonton sahaja, bukan tanda betul
        const pratonton = (teks) => { if (!selSasaran || (kotak && kotak.disabled)) return; selSasaran.textContent = teks == null ? asalSasaran : teks; selSasaran.classList.toggle('pratonton', teks != null); };
        if (hasilEl && kotak) {
            let tunda = null;
            kotak.addEventListener('input', () => {
                clearTimeout(tunda);
                tunda = setTimeout(() => {
                    const f = kotak.value.trim();
                    if (!f.startsWith('=') || f.length < 2) { hasilEl.textContent = ''; pratonton(null); return; }
                    const v = kira(f);
                    hasilEl.textContent = '→ ' + EnjinFormula.papar(v);
                    hasilEl.className = 'font-mono text-xs mt-2.5 min-h-[1rem] ' + (EnjinFormula.isRalat(v) ? 'text-err' : 'text-ink-2');   // hasil ≠ betul: warna neutral
                    pratonton(EnjinFormula.papar(v));
                }, 180);
            });
        }
        // betul: sel sasaran diisi hasil jawapan dan berkelip sekali (seperti hamparan hero)
        function isiSasaran(jawapan) {
            if (!selSasaran || !jawapan) return;
            let teks = null;
            if (q.mode === 'formula' && window.EnjinFormula && jawapan.startsWith('=')) { const v = kira(jawapan); if (!EnjinFormula.isRalat(v)) teks = EnjinFormula.papar(v); }
            else if (q.mode === 'result') teks = jawapan;
            if (teks == null) return;
            selSasaran.textContent = teks; selSasaran.classList.remove('pratonton', 'kelip'); void selSasaran.offsetWidth; selSasaran.classList.add('kelip');
        }
        // "Setara" = sel, pemalar dan fungsi SAMA dengan jawapan contoh, hanya ditulis lain (=SUM(B4,B3,B2) untuk =SUM(B2:B4))
        // + hasil sama. Hasil sama secara kebetulan BUKAN setara. Merujuk sel jawapan = ditolak.
        function setara(j, kanon) {
            if (!q.setara || !window.EnjinFormula || !j.startsWith('=') || !EnjinFormula.adaRujukan(j)) return null;
            const ta = EnjinFormula.tandatangan(j, q.grid), tb = EnjinFormula.tandatangan(kanon, q.grid), s = sel(q.highlight);
            if (!ta || !tb || (s && ta.sel.includes(s.row + ':' + s.col))) return null;
            const a = kira(j), b = kira(kanon);
            if (EnjinFormula.isRalat(a) || EnjinFormula.isRalat(b) || !EnjinFormula.sama(a, b)) return null;
            const fungsiSama = JSON.stringify(ta.fungsi) === JSON.stringify(tb.fungsi) || (q.setara_bebas && !ta.fungsi.length);
            const lainSama = JSON.stringify(ta.sel) === JSON.stringify(tb.sel) && JSON.stringify(ta.pemalar) === JSON.stringify(tb.pemalar);
            return { sama: fungsiSama && lainSama, nilai: EnjinFormula.papar(a), fungsiBeza: !fungsiSama && lainSama, fungsi: tb.fungsi };
        }

        async function hantar() {
            const badan = { id: q.id, set: aktif, subjek: SUBJEK, bahasa: L.bahasa || 'ms' };
            if (q.mode === 'choice') {
                if (dipilih === null) { pilihan[0] && pilihan[0].focus(); return tegur(t('js.lat.pilih_dahulu')); }
                badan.choice_index = dipilih;
            } else {
                const v = (kotak.value || '').trim();
                if (!v) { kotak.focus(); return tegur(t(q.mode === 'pintasan' ? 'js.lat.kosong_pintasan' : 'js.lat.kosong')); }
                badan.answer = v;
            }
            btnSemak.disabled = true;
            try {
                const pos = (b) => fetch('/api/exercise-check.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(b) }).then((r) => r.json());
                let d = await pos(badan);
                if (!d.success) throw new Error(d.error || 'ralat');
                let nota = '';
                if (!d.correct && badan.answer) {
                    const s = setara(badan.answer, d.canonical);
                    if (s && s.sama) {
                        const d2 = await pos(Object.assign({}, badan, { setara: true }));
                        if (d2.success && d2.correct) { d = d2; nota = t('js.lat.setara', { nilai: `<strong>${esc(s.nilai)}</strong>`, contoh: `<code>${esc(d.canonical)}</code>` }); }
                    } else if (s) {
                        nota = s.fungsiBeza
                            ? t('js.lat.kebetulan_fungsi', { nilai: `<strong>${esc(s.nilai)}</strong>`, fungsi: s.fungsi.length ? s.fungsi.map((f) => `<code>${esc(f)}</code>`).join(' + ') : t('js.lat.tanpa_fungsi') })
                            : t('js.lat.kebetulan', { nilai: `<strong>${esc(s.nilai)}</strong>` });
                    }
                }
                maklumBalas(d.correct, d.canonical, d.explanation, nota);
            } catch (e) {
                maklum.hidden = false;
                maklum.innerHTML = `<p class="flex gap-2.5 rounded-md bg-err-soft text-err px-4 py-3 text-sm" role="alert">${ikon('triangle-alert', 'w-4 h-4 mt-0.5 shrink-0')}${esc(t('js.lat.ralat_hantar'))}</p>`;
                btnSemak.disabled = false;
            }
        }
        function tegur(m) { if (!ralatInput) return; ralatInput.textContent = m; ralatInput.hidden = false; }

        // Betul = dijawab betul SEBELUM jawapan dilihat (cuba lagi dibenarkan). Salah: petunjuk + Cuba lagi; jawapan dan
        // penerangan hanya bila pelajar meminta "Tunjuk jawapan" (dan soalan itu tidak lagi dikira betul).
        function maklumBalas(ok, kanon, terang, nota) {
            const lama = st.answers[q.id] || { attempts: 0 };
            st.answers[q.id] = { correct: lama.correct === true || (ok && !lama.dilihat), attempts: (lama.attempts || 0) + 1,
                dilihat: !!lama.dilihat, category: q.category };
            simpan();
            kemas();
            maklum.hidden = false;
            if (!ok) {
                if (dipilih !== null && pilihan[dipilih]) pilihan[dipilih].dataset.hasil = 'salah';
                maklum.className = 'mt-6 masuk';
                maklum.innerHTML = `
                    <div class="maklum maklum-salah" role="status">
                        <span class="maklum-ikon" aria-hidden="true">${ikon('circle-x', 'w-5 h-5')}</span>
                        <div class="min-w-0 pt-1.5">
                        <p class="font-semibold text-err">${esc(t('js.lat.salah'))}</p>
                        ${nota ? `<p class="mt-2 text-sm leading-relaxed teks text-ink">${nota}</p>` : ''}
                        ${q.hint ? `<p class="mt-2 text-sm leading-relaxed teks text-ink-2"><span class="font-medium text-ink">${esc(t('js.lat.petunjuk'))}:</span> ${q.hint}</p>` : ''}
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                        <button id="show-btn" type="button" class="btn btn-hantu">${esc(t('js.lat.tunjuk'))}</button>
                        <button id="retry-btn" type="button" class="btn btn-utama">${ikon('rotate-ccw')}${esc(t('js.lat.cuba_lagi'))}</button>
                    </div>`;
                petunjuk.hidden = true;
                $('retry-btn').addEventListener('click', () => {
                    maklum.hidden = true; btnSemak.disabled = false;
                    pilihan.forEach((x) => { delete x.dataset.hasil; });
                    if (kotak) { kotak.select(); kotak.focus(); } else pilihan[0] && pilihan[0].focus();
                });
                $('show-btn').addEventListener('click', () => {
                    st.answers[q.id].dilihat = true; simpan();
                    tunjukJawapan(false, kanon, terang, '');
                });
                $('retry-btn').focus({ preventScroll: true });
                return skrolMaklum(true);
            }
            if (dipilih !== null && pilihan[dipilih]) pilihan[dipilih].dataset.hasil = 'betul';
            isiSasaran(kotak ? kotak.value.trim() : '');
            tunjukJawapan(true, kanon, terang, nota);
        }
        function tunjukJawapan(ok, kanon, terang, nota) {
            if (kotak) kotak.disabled = true;
            pilihan.forEach((b) => { b.disabled = true; });
            [btnSemak, btnPetunjuk, btnLangkau, btnTanya].forEach((b) => { if (b) b.hidden = true; });
            petunjuk.hidden = true;
            maklum.className = 'mt-6 masuk';
            maklum.innerHTML = `
                <div class="maklum ${ok ? 'maklum-betul' : 'maklum-neutral'}" role="status">
                    <span class="maklum-ikon" aria-hidden="true">${ikon(ok ? 'circle-check' : 'lightbulb', 'w-5 h-5')}</span>
                    <div class="min-w-0 pt-1.5">
                    <p class="font-semibold ${ok ? 'text-ok' : 'text-ink'}">${esc(t(ok ? 'js.lat.betul' : 'js.lat.jawapan_tajuk'))}</p>
                    ${nota ? `<p class="mt-2 text-sm leading-relaxed teks text-ink">${nota}</p>` : ''}
                    ${!ok && kanon ? `<p class="mt-2 text-sm text-ink-2">${esc(t('js.lat.jawapan'))} <code class="kod">${esc(polos(kanon))}</code></p>` : ''}
                    ${terang ? `<div class="mt-2 text-sm leading-relaxed teks text-ink-2">${terang}</div>` : ''}
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap justify-end gap-2">
                    <button id="next-btn" type="button" class="btn btn-utama">${esc(t('js.lat.seterusnya'))}${ikon('arrow-right')}</button>
                </div>`;
            $('next-btn').addEventListener('click', () => seterusnya(false));
            $('next-btn').focus({ preventScroll: true });
            skrolMaklum();
        }
        function skrolMaklum(dariKad) {
            // salah: bawa bahagian atas kad (soalan) ke bawah bar jika maklum balas muat dalam skrin; betul: maklum balas
            const tutup = 64 + el.bar.offsetHeight + 12;
            const kadAtas = el.kad.getBoundingClientRect().top + window.scrollY - tutup;
            if (dariKad && maklum.getBoundingClientRect().bottom + window.scrollY - kadAtas < window.innerHeight - tutup) {
                if (window.scrollY > kadAtas || maklum.getBoundingClientRect().bottom > window.innerHeight) try { window.scrollTo({ top: kadAtas, behavior: 'smooth' }); } catch (e) {}
                return;
            }
            const atas = maklum.getBoundingClientRect().top + window.scrollY - tutup;
            if (maklum.getBoundingClientRect().bottom > window.innerHeight - 24) try { window.scrollTo({ top: atas, behavior: 'smooth' }); } catch (e) {}
        }
    }

    function seterusnya(langkau) {
        const q = soalan(st.currentIndex);
        if (langkau && q && !st.answers[q.id]) { st.answers[q.id] = { correct: false, attempts: 0, category: q.category }; simpan(); }
        const n = sets[aktif].questions.length;
        if (st.currentIndex + 1 >= n) return selesai();
        st.currentIndex += 1;
        simpan();
        lukis(st.currentIndex);
        kemas();
        // kad soalan baharu mesti kelihatan di bawah header + bar melekat (bukan tersorok di bawahnya)
        const tutup = 64 + el.bar.offsetHeight + 12;
        const sasar = el.kad.getBoundingClientRect().top + window.scrollY - tutup;
        if (window.scrollY > sasar) try { window.scrollTo({ top: Math.max(0, sasar), behavior: 'smooth' }); } catch (e) { window.scrollTo(0, sasar); }
    }
    // satu segmen satu soalan ikut susunan sesi: betul / belum betul (atau dilangkau) / semasa
    function segmen(bekas, semasa) {
        const order = st.order && st.order.length ? st.order : sets[aktif].questions.map((q) => q.id);
        bekas.innerHTML = order.map((id, i) => { const a = st.answers[id];
            return `<i class="${a ? (a.correct ? 'betul' : 'salah') : i === semasa ? 'semasa' : ''}"></i>`; }).join('');
    }
    function kemas() {
        const n = sets[aktif].questions.length, i = st.currentIndex + 1;
        el.kemajuan.textContent = t('js.lat.soalan_n', { i, n });
        segmen(el.seg, st.currentIndex);
        el.skor.textContent = t('js.lat.skor', { b: betul() });
    }

    // ---------- selesai ----------
    function selesai() {
        st.completed = true;
        simpan();
        const s = sets[aktif], n = s.questions.length, b = betul(), pct = n ? Math.round(b / n * 100) : 0;
        tunjuk('selesai');
        el.skorAkhir.textContent = String(b);
        el.jumlahAkhir.textContent = '/ ' + n;
        $('final-pct').textContent = pct + '%';
        const cincin = $('final-cincin'); cincin.setAttribute('stroke-dasharray', '0 100');
        requestAnimationFrame(() => requestAnimationFrame(() => cincin.setAttribute('stroke-dasharray', pct + ' 100')));
        el.mesejAkhir.textContent = t(pct === 100 ? 'js.lat.akhir_100' : pct >= 80 ? 'js.lat.akhir_80' : pct >= 50 ? 'js.lat.akhir_50' : 'js.lat.akhir_rendah', { set: s.name });
        const kat = {};
        Object.values(st.answers).forEach((a) => { if (!a.category) return; kat[a.category] = kat[a.category] || { n: 0, b: 0 }; kat[a.category].n++; if (a.correct) kat[a.category].b++; });
        const lemah = Object.entries(kat).filter(([, v]) => v.b < v.n);
        el.lemah.hidden = !lemah.length;
        el.senaraiLemah.innerHTML = lemah.map(([c, v]) => `<li><span class="flex justify-between gap-4"><span class="min-w-0 truncate">${esc(c)}</span><span class="font-mono text-xs font-semibold tabular-nums text-ink-2">${v.b}/${v.n}</span></span>
            <span class="block mt-1.5 h-1.5 rounded-full bg-surface-3 overflow-hidden"><span class="block h-full rounded-full bg-accent" style="width:${Math.round(v.b / v.n * 100)}%"></span></span></li>`).join('');
        keAtas();
    }

    function tanya(q) {
        const panel = $('widget-panel'), input = $('widget-input');
        const mesej = t('js.lat.mesej_ai', { subjek: L.nama || '', kategori: q.category || '', soalan: polos(q.prompt) });
        if (!panel || !input) { location.href = L.url_tutor + '#q=' + encodeURIComponent(mesej); return; }
        window.bukaWidget && window.bukaWidget();
        input.value = mesej;
        input.focus();
        try { input.setSelectionRange(mesej.length, mesej.length); } catch (e) {}
    }

    // ---------- mula ----------
    document.querySelectorAll('[data-set]').forEach((k) => k.addEventListener('click', () => aktifkan(k.dataset.set)));
    el.btnMula.addEventListener('click', mulaSesi);
    el.btnReset.addEventListener('click', async () => {
        const ya = await (window.sahkan ? sahkan({ tajuk: t('js.lat.reset_tajuk'), teks: t('js.lat.reset_teks', { set: sets[aktif].name }), ya: t('js.lat.reset_ya'), bahaya: true }) : Promise.resolve(confirm(t('js.lat.reset_tajuk'))));
        if (ya) { kosongkan(); skrinMula(); }
    });
    $('restart-btn').addEventListener('click', () => { kosongkan(); skrinMula(); });
    $('quit-btn').addEventListener('click', skrinMula);
    ['back-to-sets', 'back-from-welcome', 'back-from-completion'].forEach((x) => $(x) && $(x).addEventListener('click', keSenarai));
    el.rawak.addEventListener('change', () => { st.randomOrder = el.rawak.checked; simpan(); });
    function ikutUrl() {
        const h = (location.hash || '').slice(1) || new URLSearchParams(location.search).get('set') || '';
        if (sets[h]) aktifkan(h); else keSenarai();
    }
    window.addEventListener('hashchange', ikutUrl);
    ikutUrl();
})();
