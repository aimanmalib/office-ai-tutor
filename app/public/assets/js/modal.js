(function () {
    function build({ title, message, confirmText, cancelText, danger, icon, kind }) {
        const backdrop = document.createElement('div');
        backdrop.className = 'fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/60 px-5 modal-fade-in';
        backdrop.setAttribute('role', 'dialog');
        backdrop.setAttribute('aria-modal', 'true');

        const card = document.createElement('div');
        card.className = 'bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 sm:p-7 modal-pop-in relative';

        const iconColor = danger ? 'from-err to-err' :
                          kind === 'success' ? 'from-ok to-ok' :
                          'from-brand-purple to-brand-pink';

        const iconChar = icon || (danger ? '!' : kind === 'success' ? '✓' : '?');

        const confirmCls = danger
            ? 'bg-err hover:bg-err/90'
            : 'bg-gradient-to-r from-brand-purple to-brand-pink hover:opacity-90';

        const cancelHtml = cancelText
            ? `<button data-act="cancel" class="flex-1 px-4 py-3 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm transition">${cancelText}</button>`
            : '';

        card.innerHTML = `
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br ${iconColor} flex items-center justify-center text-white text-2xl font-extrabold mb-4 shadow-lg">
                ${iconChar}
            </div>
            <h3 class="text-lg font-extrabold text-gray-800 text-center mb-1.5">${title}</h3>
            ${message ? `<p class="text-sm text-gray-600 text-center mb-5 leading-relaxed">${message}</p>` : '<div class="mb-3"></div>'}
            <div class="flex gap-2">
                ${cancelHtml}
                <button data-act="ok" class="flex-1 px-4 py-3 rounded-2xl text-white font-bold text-sm transition shadow-md ${confirmCls}">${confirmText}</button>
            </div>
        `;
        backdrop.appendChild(card);
        return { backdrop, card };
    }

    function openModal(opts, resolve) {
        const o = {
            title: opts.title || 'Konfirmasi',
            message: opts.message || '',
            confirmText: opts.confirmText || 'OK',
            cancelText: 'cancelText' in opts ? opts.cancelText : 'Batal',
            danger: !!opts.danger,
            kind: opts.kind || null,
            icon: opts.icon || null,
        };
        const { backdrop, card } = build(o);
        document.body.appendChild(backdrop);
        document.body.style.overflow = 'hidden';

        function close(result) {
            backdrop.classList.add('modal-fade-out');
            card.classList.add('modal-pop-out');
            setTimeout(() => {
                backdrop.remove();
                document.body.style.overflow = '';
                document.removeEventListener('keydown', onKey);
                resolve(result);
            }, 150);
        }

        function onKey(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                close(false);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                close(true);
            }
        }
        document.addEventListener('keydown', onKey);

        card.querySelector('[data-act="cancel"]')?.addEventListener('click', () => close(false));
        card.querySelector('[data-act="ok"]')?.addEventListener('click', () => close(true));
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) close(false);
        });

        setTimeout(() => {
            const btn = card.querySelector('[data-act="ok"]');
            btn && btn.focus();
        }, 50);
    }

    window.appConfirm = function (opts) {
        const o = typeof opts === 'string' ? { message: opts } : (opts || {});
        return new Promise((resolve) => openModal(o, resolve));
    };

    window.appAlert = function (opts) {
        const o = typeof opts === 'string' ? { message: opts } : (opts || {});
        return new Promise((resolve) => openModal({ ...o, cancelText: null, kind: o.kind || 'success' }, resolve));
    };

    // Intercept any <form data-confirm="..."> to use modal instead of native confirm
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        const msg = form.dataset.confirm;
        if (!msg || form.dataset.confirmed === '1') return;

        e.preventDefault();
        appConfirm({
            title: form.dataset.confirmTitle || 'Sahkan tindakan',
            message: msg,
            confirmText: form.dataset.confirmOk || 'Ya, teruskan',
            cancelText: 'Batal',
            danger: form.dataset.confirmDanger !== '0',
        }).then((ok) => {
            if (ok) {
                form.dataset.confirmed = '1';
                form.submit();
            }
        });
    }, true);
})();
