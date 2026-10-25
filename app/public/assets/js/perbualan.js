// Perbualan tutor AI — SATU modul untuk halaman tutor (#tutor) dan butang terapung (#chat-widget).
// Sejarah dalam localStorage '<subjek>_chat_history_v2' (kunci sama dengan versi lama; dikongsi halaman + widget + tab lain).
// Jawapan AI = markdown → DOMPurify (teks luar tidak dipercayai). Gambar: dikecilkan ≤1280 px JPEG dalam pelayar.
(function () {
    const L = window.LAMAN || {};
    const T = L.T || {};
    const t = (k, g) => { let s = T[k] || k; Object.entries(g || {}).forEach(([a, b]) => { s = s.split('{' + a + '}').join(b); }); return s; };
    const C = L.chat || {};
    const KUNCI = (L.subjek || 'excel') + '_chat_history_v2';
    const esc = (s) => String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const ikon = (n, k) => (window.IKON && window.IKON[n] ? window.IKON[n].replace('class="', 'class="' + (k || 'w-4 h-4') + ' ') : '');
    const markdown = (s) => (window.marked && window.DOMPurify ? DOMPurify.sanitize(marked.parse(String(s || ''))) : esc(s));

    function baca() { try { const h = JSON.parse(localStorage.getItem(KUNCI) || '[]'); return Array.isArray(h) ? h : []; } catch (e) { return []; } }
    function tulis(h) {
        try { localStorage.setItem(KUNCI, JSON.stringify(h)); } catch (e) {
            try { localStorage.setItem(KUNCI, JSON.stringify(h.map((m) => ({ role: m.role, content: m.content })))); } catch (e2) {}  // kuota penuh: teks sahaja
        }
    }
    async function jsonAtauRalat(res) {
        const s = await res.text();
        try { return JSON.parse(s); } catch (e) { return { success: false, error: res.status === 413 ? t('js.ai.terlalu_besar') : t('js.ai.pelayan', { kod: res.status }) }; }
    }
    function kecilkan(fail) {
        return new Promise((ok, gagal) => {
            const r = new FileReader();
            r.onload = () => {
                const img = new Image();
                img.onload = () => {
                    let { width: w, height: h } = img;
                    const m = 1280;
                    if (w > m || h > m) { const k = Math.min(m / w, m / h); w = Math.round(w * k); h = Math.round(h * k); }
                    const c = document.createElement('canvas'); c.width = w; c.height = h;
                    c.getContext('2d').drawImage(img, 0, 0, w, h);
                    ok(c.toDataURL('image/jpeg', 0.85));
                };
                img.onerror = () => gagal(new Error('imej'));
                img.src = r.result;
            };
            r.onerror = () => gagal(new Error('baca'));
            r.readAsDataURL(fail);
        });
    }

    /** Ikat satu permukaan perbualan. kecil = widget (cadangan lebih sedikit, gelembung lebih padat). */
    function ikat(akar, kecil) {
        const q = (s) => akar.querySelector(s);
        const mesej = q('[data-c-mesej]'), borang = q('[data-c-borang]'), input = q('[data-c-input]'), hantarBtn = q('[data-c-hantar]');
        const baru = q('[data-c-baru]'), gInput = q('[data-c-gambar]'), gPratonton = q('[data-c-pratonton]');
        if (!mesej || !borang || !input) return null;
        let sejarah = baca(), gambar = null, sibuk = false;

        function kosong() {
            const cad = (C.cadangan || []).slice(0, kecil ? 2 : 4);
            mesej.innerHTML = `<div data-c-alu class="relative ${kecil ? 'py-4 px-1' : 'py-5 sm:py-14'}">
                ${kecil ? '' : '<div class="hiasan grid-aksen -top-6" aria-hidden="true"></div>'}
                <div class="relative">
                <span class="jubin ${kecil ? '' : 'sm:jubin-lg'}" aria-hidden="true">${ikon('sparkle', kecil ? 'w-5 h-5' : 'w-5 h-5 sm:w-6 sm:h-6')}</span>
                <p class="${kecil ? 'mt-3 text-[0.9375rem] font-semibold tracking-tight' : 'mt-4 sm:mt-5 text-h2'}">${esc(C.salam || '')}</p>
                <p class="mt-2 text-sm text-ink-2 max-w-[52ch]">${esc(C.intro || '')}</p>
                ${!kecil && C.lampiran && gInput ? `<p class="mt-2 text-sm text-ink-2 flex items-center gap-1.5">${ikon('paperclip')}${esc(C.lampiran)}</p>` : ''}
                <p class="${kecil ? 'mt-5' : 'mt-6 sm:mt-8'} eyebrow-aksen">${esc(t('js.ai.cuba'))}</p>
                <div class="mt-3 grid gap-2.5 ${kecil ? '' : 'sm:grid-cols-2'}">${cad.map((c, i) => `<button type="button" class="kad-timbul boleh-tekan cadangan ${i === 3 ? 'hidden sm:flex' : ''}"><span class="jubin jubin-sm" aria-hidden="true">${ikon('message-square')}</span><span data-teks class="flex-1 min-w-0">${esc(c)}</span>${ikon('arrow-right')}</button>`).join('')}</div>
                </div>
            </div>`;
            mesej.querySelectorAll('.cadangan').forEach((b) => b.addEventListener('click', () => { input.value = b.querySelector('[data-teks]').textContent; hantar(); }));
        }
        function gelembung(peranan, isi, gmb) {
            const w = document.createElement('div');
            if (peranan === 'user') {
                w.className = 'flex justify-end';
                w.innerHTML = `<div class="gelembung-saya ${kecil ? 'text-sm' : 'text-[0.9375rem]'}">
                    ${gmb ? `<img src="${esc(gmb)}" alt="" class="rounded-lg mb-2 max-h-56 object-contain">` : ''}<div class="whitespace-pre-wrap break-words"></div></div>`;
                w.querySelector('.whitespace-pre-wrap').textContent = isi || '';
            } else {
                w.className = kecil ? 'flex gap-2.5' : 'flex gap-3';
                const ralat = peranan === 'ralat';
                w.innerHTML = `<span class="avatar-ai ${ralat ? 'bg-err-soft text-err' : ''}" aria-hidden="true">${ikon(ralat ? 'triangle-alert' : 'sparkle', 'w-4 h-4')}</span>
                    <div class="gelembung-ai ${ralat ? 'ralat' : ''} ${kecil ? 'px-3.5 py-2.5' : ''}"><div class="prosa ${kecil ? 'text-sm' : ''} ${ralat ? 'text-err' : ''}"></div></div>`;
                const isiEl = w.querySelector('.prosa');
                if (ralat) isiEl.textContent = isi; else isiEl.innerHTML = markdown(isi);
            }
            mesej.appendChild(w);
            return w;
        }
        function lukisSemua() {
            mesej.innerHTML = '';
            if (!sejarah.length) return kosong();
            sejarah.forEach((m) => gelembung(m.role, m.content, m.imageThumb));
            mesej.scrollTop = mesej.scrollHeight;
        }
        function menaip() {
            const w = document.createElement('div');
            w.className = 'flex gap-3'; w.dataset.cMenaip = '';
            w.innerHTML = `<span class="avatar-ai" aria-hidden="true">${ikon('sparkle')}</span><span class="menaip" role="status" aria-label="${esc(t('js.ai.menaip'))}"><i></i><i></i><i></i></span>`;
            mesej.appendChild(w);
            mesej.scrollTop = mesej.scrollHeight;
            return w;
        }
        function besarkan() { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, kecil ? 120 : 200) + 'px'; }

        async function hantar() {
            const teks = input.value.trim();
            if (sibuk || (!teks && !gambar)) return;
            sibuk = true;
            mesej.querySelector('[data-c-alu]')?.remove();
            const gmb = gambar ? gambar.dataUrl : null;
            gelembung('user', teks, gmb);
            const entri = { role: 'user', content: teks };
            if (gmb) entri.imageThumb = gmb;
            sejarah.push(entri); tulis(sejarah);
            input.value = ''; besarkan(); buangGambar();
            mesej.scrollTop = mesej.scrollHeight;
            const tunggu = menaip();
            hantarBtn && (hantarBtn.disabled = true);
            try {
                const badan = { subjek: L.subjek || 'excel', message: teks, history: sejarah.slice(0, -1).map((m) => ({ role: m.role, content: m.content })) };
                if (gmb) badan.image = gmb;
                const d = await jsonAtauRalat(await fetch('/api/chat.php?bahasa=' + encodeURIComponent(L.bahasa || 'ms'), { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(badan) }));
                tunggu.remove();
                if (d.success) {
                    const w = gelembung('assistant', d.reply);
                    sejarah.push({ role: 'assistant', content: d.reply }); tulis(sejarah);
                    mesej.scrollTop = Math.max(0, w.offsetTop - 12);   // baca jawapan dari atas
                } else {
                    gelembung('ralat', d.error || t('js.ai.gagal'));
                    mesej.scrollTop = mesej.scrollHeight;
                }
            } catch (e) {
                tunggu.remove();
                gelembung('ralat', t('js.ai.rangkaian'));
            } finally {
                sibuk = false;
                hantarBtn && (hantarBtn.disabled = false);
                if (matchMedia('(pointer: fine)').matches) input.focus();
            }
        }
        function buangGambar() { gambar = null; if (gInput) gInput.value = ''; if (gPratonton) { gPratonton.hidden = true; gPratonton.querySelector('img').src = ''; } }

        borang.addEventListener('submit', (e) => { e.preventDefault(); hantar(); });
        input.addEventListener('keydown', (e) => { if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); hantar(); } });
        input.addEventListener('input', besarkan);
        gInput && gInput.addEventListener('change', async () => {
            const f = gInput.files && gInput.files[0];
            if (!f) return;
            if (!/^image\/(jpeg|jpg|png|webp)$/.test(f.type)) return gelembung('ralat', t('js.ai.format'));
            try {
                const dataUrl = await kecilkan(f);
                if ((dataUrl.length - 23) * 0.75 > 4 * 1024 * 1024) return gelembung('ralat', t('js.ai.terlalu_besar'));
                gambar = { dataUrl, nama: f.name || 'image.jpg' };
                gPratonton.querySelector('img').src = dataUrl;
                gPratonton.querySelector('[data-c-nama]').textContent = gambar.nama;
                gPratonton.hidden = false;
            } catch (e) { gelembung('ralat', t('js.ai.gambar_gagal')); }
        });
        akar.querySelector('[data-c-buang-gambar]')?.addEventListener('click', buangGambar);
        baru && baru.addEventListener('click', async () => {
            if (sejarah.length && window.sahkan && !(await sahkan({ tajuk: t('js.ai.baru_tajuk'), teks: t('js.ai.baru_teks'), ya: t('js.ai.baru_ya'), bahaya: true }))) return;
            sejarah = []; try { localStorage.removeItem(KUNCI); } catch (e) {}
            buangGambar(); lukisSemua(); input.focus();
        });
        window.addEventListener('storage', (e) => { if (e.key === KUNCI && !sibuk) { sejarah = baca(); lukisSemua(); } });
        return { lukis: () => { sejarah = baca(); lukisSemua(); }, input };
    }

    // Halaman tutor
    const tutor = document.getElementById('tutor');
    if (tutor) {
        const p = ikat(tutor, false);
        if (p) {
            p.lukis();
            const m = /^#q=(.+)$/.exec(location.hash);
            if (m) { try { p.input.value = decodeURIComponent(m[1]); } catch (e) {} history.replaceState(null, '', location.pathname); }
            if (matchMedia('(pointer: fine)').matches) p.input.focus();
        }
    }

    // Butang terapung
    const w = document.getElementById('chat-widget');
    if (w) {
        const togol = document.getElementById('widget-toggle'), panel = document.getElementById('widget-panel');
        let p = null;
        const buka = (b) => {
            panel.hidden = !b;
            togol.setAttribute('aria-expanded', String(b));
            if (b) { if (!p) p = ikat(panel, true); p && p.lukis(); if (matchMedia('(pointer: fine)').matches) p && p.input.focus(); }
        };
        window.bukaWidget = () => buka(true);
        // butang terapung tidak menutup kandungan: sorok bila menatal ke bawah, tunjuk bila menatal ke atas; sorok bila kad
        // "Tanya tutor" di hujung halaman kelihatan (pautan yang sama)
        let akhirY = window.scrollY, ctaNampak = false;
        const tetapNampak = () => {
            const sorok = panel.hidden && (ctaNampak || (window.scrollY > akhirY + 4 && window.scrollY > 200));
            togol.classList.toggle('translate-y-24', sorok);
            togol.tabIndex = sorok ? -1 : 0;
        };
        window.addEventListener('scroll', () => { const y = window.scrollY; if (Math.abs(y - akhirY) > 6) { tetapNampak(); akhirY = y; } }, { passive: true });
        const cta = document.querySelector('[data-cta-tutor]');
        if (cta && 'IntersectionObserver' in window) new IntersectionObserver((e) => { ctaNampak = e[0].isIntersecting; tetapNampak(); }).observe(cta);
        togol.addEventListener('click', () => buka(panel.hidden));
        document.getElementById('widget-close')?.addEventListener('click', () => { buka(false); togol.focus(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !panel.hidden) { buka(false); togol.focus(); } });
    }
})();
