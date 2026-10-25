// Panel admin (/admin.php) = reka bentuk v2 yang sama dengan laman awam (token tapak.css, fon Geist), TANPA menulis semula
// markup admin: kelas lama dipetakan ke token di sini — brand-purple/pink (gradien lama) = dakwat, purple-* = garis/permukaan,
// gray-* = skala dakwat, sudut besar dikecilkan. Kelas baharu dalam admin = bina semula (bina.sh).
const base = require('./tailwind.config.js');
const token = (n) => `rgb(var(--${n}) / <alpha-value>)`;

module.exports = {
  content: ['../app/public/admin.php', '../app/src/admin/*.php', '../app/public/assets/js/modal.js', '../app/src/Ikon.php'],
  darkMode: base.darkMode,
  theme: {
    container: base.theme.container,
    extend: {
      ...base.theme.extend,
      colors: {
        ...base.theme.extend.colors,
        brand: { purple: token('ink'), pink: token('ink'), amber: token('warn'), emerald: token('ok') },
        purple: { 50: token('surface-2'), 100: token('line'), 200: token('line-2'), 300: token('line-2'), 700: token('ink'), 800: token('ink'), 900: token('ink') },
        pink: { 50: token('surface-2'), 500: token('ink') },
        gray: { 50: token('surface-2'), 100: token('surface-3'), 200: token('line-2'), 300: token('line-2'), 400: token('muted'), 500: token('muted'),
                600: token('ink-2'), 700: token('ink-2'), 800: token('ink'), 900: token('ink') },
      },
      borderRadius: { ...base.theme.extend.borderRadius, '2xl': '0.75rem', '3xl': '0.875rem' },
    },
  },
};
