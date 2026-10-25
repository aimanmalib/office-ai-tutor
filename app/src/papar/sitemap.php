<?php
/** /sitemap.xml — setiap halaman setiap subjek yang DITERBITKAN, dalam dua bahasa (xhtml:link hreflang). */
header('Content-Type: application/xml; charset=utf-8');
$asas = (require __DIR__ . '/../../config/laman.php')['url'];
$url = [['/', '1.0']];
foreach (Subjek::semua() as $id => $s) {
    if (!Subjek::hidup($id)) {
        continue;
    }
    $url[] = ["/$id/", '0.9'];
    foreach ($s['halaman'] as $hal) {
        $url[] = ["/$id/$hal", in_array($hal, ['reference', 'practice'], true) ? '0.9' : '0.8'];
    }
}
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n",
    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">', "\n";
foreach ($url as [$u, $p]) {
    foreach (['ms' => '', 'en' => '/en'] as $b => $aw) {
        $loc = htmlspecialchars($asas . $aw . $u);
        echo "  <url>\n    <loc>$loc</loc>\n    <priority>$p</priority>\n";
        foreach (['ms' => '', 'en' => '/en'] as $b2 => $aw2) {
            echo '    <xhtml:link rel="alternate" hreflang="' . $b2 . '" href="' . htmlspecialchars($asas . $aw2 . $u) . "\"/>\n";
        }
        echo "  </url>\n";
    }
}
echo "</urlset>\n";
