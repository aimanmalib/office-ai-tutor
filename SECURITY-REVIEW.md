# Security review before going public (23 Oct 2026)

This code ran in production for a real class before it was published. Before publishing, it went through five independent
read-only reviews (separate reviewer sessions that did not write the code), each followed by fixes **with a regression test
per finding** (`tests/` in the private repository runs ~180 checks against the production container image).

| Area | Findings fixed (summary) |
|---|---|
| AI answers | Model output rendered as HTML could run scripts (stored in `localStorage`, re-rendered on every page) → sanitised with DOMPurify; reproduced against the old code first. |
| Admin | Login rate limit (atomic, 10 / 15 min per IP, IPv6 per /64), password hash, CSRF token on every form and on logout, session files outside the shared `/tmp`, `Secure` cookie, `no-store`. |
| Content editor | Allowed formatting tags kept **without attributes** (`<strong onclick>` was possible); text after `<` was silently truncated by `strip_tags` → escape-then-restore. Read-modify-write under one lock (two devices saving at once lost edits). |
| Rate limiting | Counted per Cloudflare edge IP (whole class shared one limit); spoofable `CF-Connecting-IP` from non-Cloudflare peers is ignored (`src/ClientIp.php`); concurrent requests can no longer exceed the limit; failed AI calls are refunded. |
| AI key pool | 400 errors no longer burn every key; 401/403 and 429 keys cool down across requests; 55 s total budget (proxy timeout is 100 s). |
| Drafts | Unpublished subjects return 404 to the public on every route (pages, exercise API, chat API, sitemap); admin preview only. |
| Statistics | No IP, no student cookies, bots excluded (JS beacon); AI question text kept 60 days. |

Secret scanning: `gitleaks` over the full private history before export found no leaks. Secrets (`app/config/.env`,
`app/config/groq-keys.json`) and data (`app/data/`) are never exported; the public history starts fresh.
