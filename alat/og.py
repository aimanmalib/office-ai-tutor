#!/usr/bin/env python3
"""Jana kad OG 1200×630 (JPEG < 300 KB: kad lebih besar tiada gambar di WhatsApp) → app/public/og/<id>-<bahasa>.jpg.
id = utama | excel | word | powerpoint. Teks diambil daripada fail kandungan/bahasa (bukan ditaip di sini): tajuk laman utama
= config/bahasa/<b>.php utama.tajuk; subjek = config/pek/<s>/[en/]utama.php ringkas. Rupa = arah "buku kerja yang hidup"
(docs/REKA.md §1.11): subjek = latar aksen subjek + grid halus + jubin ikon besar; laman utama = latar gelap dengan cahaya
tiga warna + tiga jubin subjek. WARNA dibaca daripada token tailwind/tapak.css (:root + [data-tema="gelap"]) — tiada palet
kedua di sini; logo = src/papar/_logo.php. Perlu Playwright Chromium + imej Docker PHP 8.3 (php:8.3-cli, membaca PHP).
  python3 excel/alat/og.py
"""
import base64, html, json, pathlib, re, subprocess

from playwright.sync_api import sync_playwright

SINI = pathlib.Path(__file__).resolve().parent
APP = SINI.parent / "app"
KELUAR = APP / "public" / "og"
HAD_BAIT = 300 * 1024

kod = r'''
require "src/Kandungan.php"; require "src/Subjek.php"; require "src/Ikon.php"; $o = [];
$laman = require "config/laman.php";
ob_start(); include "src/papar/_logo.php"; $logo = ob_get_clean();
$semua = Subjek::semua();
foreach (["ms", "en"] as $b) {
    $t = require "config/bahasa/$b.php";
    $o["utama-$b"] = ["tajuk" => $t["utama.tajuk"], "kecil" => implode(" · ", array_column($semua, "nama")), "jenama" => $laman["jenama"],
                      "ikon" => array_map(fn($s) => Ikon::svg($s["ikon"], ""), $semua)];
    foreach ($semua as $id => $s) {
        $u = Kandungan::benih($id, "utama", $b);
        $o["$id-$b"] = ["tajuk" => $s["nama"], "kecil" => $u["ringkas"], "jenama" => $laman["jenama"], "ikon" => [$id => Ikon::svg($s["ikon"], "")]];
    }
}
echo json_encode(["logo" => $logo, "kad" => $o], JSON_UNESCAPED_UNICODE);'''


def token():
    """Blok :root dan [data-tema="gelap"] tapak.css → deklarasi CSS (gelap diberi awalan --g-)."""
    css = (SINI.parent / "tailwind" / "tapak.css").read_text()
    akar = re.search(r":root\s*\{(.*?)\n\s*\}", css, re.S).group(1)
    gelap = re.search(r'\[data-tema="gelap"\]\s*\{(.*?)\n\s*\}', css, re.S).group(1)
    pasang = lambda blok, awal: " ".join(f"--{awal}{k}: {v.strip()};" for k, v in re.findall(r"--([a-z0-9-]+):\s*([0-9]+ [0-9]+ [0-9]+)\s*;", blok))
    return pasang(akar, "") + " " + pasang(gelap, "g-")


TEMPLAT = """<!doctype html><html><head><meta charset="utf-8"><style>
@font-face {{ font-family: G; src: url(data:font/woff2;base64,{fon}) format('woff2'); font-weight: 100 900; }}
@font-face {{ font-family: M; src: url(data:font/woff2;base64,{mono}) format('woff2'); font-weight: 100 900; }}
:root {{ {token} }}
* {{ margin: 0; box-sizing: border-box; }}
body {{ width: 1200px; height: 630px; overflow: hidden; font-family: G, sans-serif; position: relative; padding: 64px 80px 74px;
  display: flex; flex-direction: column; font-feature-settings: "ss01", "cv11"; }}
body.subjek {{ --a: var(--{id}); --atas: var(--atas-aksen); background: rgb(var(--a)); color: rgb(var(--atas)); }}
body.utama {{ --atas: var(--g-ink); background: rgb(var(--g-bg)); color: rgb(var(--atas)); }}
.cahaya {{ position: absolute; inset: 0; }}
.subjek .cahaya {{ background: radial-gradient(circle at 82% 30%, rgb(var(--atas) / .2), transparent 48%),
                                 radial-gradient(circle at 0% 100%, rgb(var(--g-bg) / .28), transparent 55%); }}
.utama .cahaya {{ background: radial-gradient(circle at 78% 18%, rgb(var(--excel) / .55), transparent 38%),
                                radial-gradient(circle at 98% 70%, rgb(var(--word) / .5), transparent 36%),
                                radial-gradient(circle at 62% 105%, rgb(var(--powerpoint) / .45), transparent 34%); }}
.grid {{ position: absolute; inset: 0; background-image: linear-gradient(rgb(var(--atas) / .1) 1px, transparent 1px),
  linear-gradient(90deg, rgb(var(--atas) / .1) 1px, transparent 1px); background-size: 48px 48px;
  -webkit-mask-image: radial-gradient(ellipse 75% 85% at 85% 30%, #000 0%, transparent 70%); }}
.atas {{ position: relative; display: flex; align-items: center; gap: 16px; font-size: 28px; font-weight: 600; letter-spacing: -0.01em; }}
.atas svg {{ width: 48px; height: 48px; }}
.utama .atas {{ --ink: var(--g-ink); --bg: var(--g-bg); }}
.tengah {{ position: relative; margin-top: auto; max-width: 760px; }}
.eyebrow {{ font-family: M; font-size: 20px; letter-spacing: .14em; text-transform: uppercase; opacity: .8; display: flex; align-items: center; gap: 14px; }}
.eyebrow::before {{ content: ''; width: 36px; height: 2px; background: currentColor; }}
h1 {{ margin-top: 18px; font-size: {saiz}px; line-height: 1; letter-spacing: -0.045em; font-weight: 750; }}
p {{ margin-top: 24px; font-size: 30px; line-height: 1.35; letter-spacing: -0.01em; color: rgb(var(--atas) / .86); }}
.jubin {{ position: absolute; display: grid; place-items: center; }}
.jubin svg {{ fill: none; stroke: currentColor; stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round; }}
.subjek .jubin {{ right: 96px; top: 120px; width: 260px; height: 260px; border-radius: 60px; background: rgb(var(--atas) / .12);
  box-shadow: inset 0 0 0 2px rgb(var(--atas) / .28), 0 30px 80px rgb(var(--g-bg) / .3); }}
.subjek .jubin svg {{ width: 140px; height: 140px; }}
.utama .jubin {{ width: 150px; height: 150px; border-radius: 36px; color: rgb(var(--atas-aksen));
  box-shadow: inset 0 0 0 2px rgb(var(--atas-aksen) / .2), 0 30px 70px rgb(var(--g-bg) / .6); }}
.utama .jubin svg {{ width: 80px; height: 80px; }}
.j0 {{ right: 300px; top: 92px; background: rgb(var(--excel)); }} .j1 {{ right: 110px; top: 178px; background: rgb(var(--word)); }}
.j2 {{ right: 250px; top: 300px; background: rgb(var(--powerpoint)); }}
.jalur {{ position: absolute; left: 0; right: 0; bottom: 0; height: 12px; display: flex; }} .jalur i {{ flex: 1; }}
</style></head><body class="{kelas}"><div class="cahaya"></div><div class="grid"></div>
<div class="atas">{logo}{jenama}</div>{jubin}
<div class="tengah">{eyebrow}<h1>{tajuk}</h1>{kecil}</div>
<div class="jalur"><i style="background:rgb(var(--excel))"></i><i style="background:rgb(var(--word))"></i><i style="background:rgb(var(--powerpoint))"></i></div>
</body></html>"""


def main():
    data = json.loads(subprocess.run(["docker", "run", "--rm", "-v", f"{APP}:/app:ro", "-w", "/app", "php:8.3-cli", "php", "-r", kod],
                                     capture_output=True, text=True, check=True).stdout)
    fon = base64.b64encode((APP / "public/assets/fonts/geist.woff2").read_bytes()).decode()
    mono = base64.b64encode((APP / "public/assets/fonts/geist-mono.woff2").read_bytes()).decode()
    tok = token()
    KELUAR.mkdir(exist_ok=True)
    with sync_playwright() as p:
        b = p.chromium.launch()
        hal = b.new_page(viewport={"width": 1200, "height": 630})
        for nama, t in data["kad"].items():
            id_ = nama.rsplit("-", 1)[0]
            utama = id_ == "utama"
            ikon = list(t["ikon"].values())
            jubin = "".join(f'<div class="jubin j{i}">{s}</div>' for i, s in enumerate(ikon))
            hal.set_content(TEMPLAT.format(
                fon=fon, mono=mono, token=tok, id=id_, kelas="utama" if utama else "subjek", saiz=76 if utama else (150 if len(t["tajuk"]) <= 6 else 118),
                logo=data["logo"], jubin=jubin, jenama=html.escape(t["jenama"]),
                eyebrow=f'<div class="eyebrow">{html.escape(t["kecil"])}</div>' if utama else "",
                tajuk=html.escape(t["tajuk"]), kecil="" if utama else f'<p>{html.escape(t["kecil"])}</p>'), wait_until="load")
            hal.evaluate("document.fonts.ready")
            out = KELUAR / f"{nama}.jpg"
            hal.screenshot(path=str(out), type="jpeg", quality=86)
            saiz = out.stat().st_size
            print(out.name, saiz // 1024, "KB")
            if saiz >= HAD_BAIT:
                raise SystemExit(f"{out.name} {saiz} B ≥ {HAD_BAIT} (WhatsApp tidak memapar gambar)")
        b.close()


if __name__ == "__main__":
    main()
