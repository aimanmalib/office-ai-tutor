// Laman awam (revamp v2, docs/REVAMP.md). Warna = token CSS dalam tapak.css (tema cerah + gelap), bukan nilai tetap:
// kelas `bg-surface`, `text-ink`, `border-line`, `text-accent` … mengikut tema dan subjek semasa.
// Bina: ./bina.sh → public/assets/css/tw.css. Kelas baharu dalam PHP/JS = bina semula sebelum deploy (deploy.sh menyemak).
const token = (n) => `rgb(var(--${n}) / <alpha-value>)`;

module.exports = {
  content: ['../app/src/papar/**/*.php', '../app/src/Ikon.php', '../app/public/laman.php', '../app/public/*.html',
    '../app/public/assets/js/*.js', '../app/config/bahasa/*.php'],
  darkMode: ['selector', '[data-tema="gelap"]'],
  theme: {
    container: { center: true, padding: { DEFAULT: '1.25rem', sm: '1.5rem', lg: '2rem' } },
    extend: {
      fontFamily: {
        sans: ['Geist', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
        mono: ['"Geist Mono"', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Consolas', 'monospace'],
      },
      colors: {
        bg: token('bg'), surface: token('surface'), 'surface-2': token('surface-2'), 'surface-3': token('surface-3'),
        ink: token('ink'), 'ink-2': token('ink-2'), muted: token('muted'),
        line: token('line'), 'line-2': token('line-2'),
        accent: token('accent'), 'accent-soft': token('accent-soft'), 'accent-ink': token('accent-ink'), 'atas-aksen': token('atas-aksen'),
        tirai: token('tirai'),
        ok: token('ok'), 'ok-soft': token('ok-soft'), err: token('err'), 'err-soft': token('err-soft'),
        warn: token('warn'), 'warn-soft': token('warn-soft'),
        excel: token('excel'), word: token('word'), powerpoint: token('powerpoint'),
      },
      fontSize: {
        'display': ['clamp(2.5rem, 6vw, 4.5rem)', { lineHeight: '1', letterSpacing: '-0.045em', fontWeight: '750' }],
        'h1': ['clamp(2rem, 3.6vw, 2.75rem)', { lineHeight: '1.08', letterSpacing: '-0.03em', fontWeight: '600' }],
        'h2': ['clamp(1.5rem, 2.4vw, 1.875rem)', { lineHeight: '1.15', letterSpacing: '-0.02em', fontWeight: '600' }],
        'h3': ['1.125rem', { lineHeight: '1.35', letterSpacing: '-0.01em', fontWeight: '600' }],
      },
      maxWidth: { prose: '68ch', tapak: '76rem' },
      borderRadius: { DEFAULT: '0.5rem', lg: '0.75rem', xl: '1rem' },
      boxShadow: {
        // nilai = token tapak.css (cerah/gelap), bukan warna di sini
        kad: 'var(--shadow-kad)',
        timbul: 'var(--shadow-tingkap)',
        tingkap: 'var(--shadow-tingkap)',
      },
    },
  },
};
