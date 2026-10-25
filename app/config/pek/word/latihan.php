<?php

declare(strict_types=1);

/**
 * Latihan Word, BM — benih asal (diterbitkan 23 Okt 2026; suntingan seterusnya di Admin → Kandungan). Versi EN: en/latihan.php
 * (id, mode, level, jawapan, correct_index dan susunan pilihan SAMA). Bentuk sama dengan config/pek/excel/latihan.php.
 * Mod 'pintasan': pelajar menekan kombinasi kekunci (atau menaipnya di telefon, cth "Ctrl+B"); 'jawapan' = senarai yang
 * diterima, Ctrl ≡ ⌘ (Mac). Elak pintasan yang pelayar rampas (Ctrl+N/T/W, Ctrl+Shift+N, Ctrl+1..8, F12).
 * `icon` set = nama Lucide; `icon` soalan diabaikan oleh reka bentuk baharu.
 */
['pintasan' => $p, 'pilihan' => $c] = require __DIR__ . '/../_soalan.php';

return [
    'pintasan' => [
        'name' => 'Pintasan harian',
        'icon' => 'keyboard',
        'tagline' => 'Pintasan yang digunakan setiap hari dalam Word',
        'description' => 'Tekan kombinasi kekunci yang betul. Di telefon, taip jawapan dengan tanda +, contohnya Ctrl+huruf. Meliputi format, heading, page break dan semakan.',
        'color' => 'purple',
        'questions' => [
            $p('p1', 'Format', '🔤', 1, 'Tekan pintasan untuk <strong>menebalkan (bold)</strong> teks yang dipilih.', ['Ctrl+B'],
                'B untuk Bold.', '<strong>Ctrl+B</strong> menogol bold. Tekan sekali lagi untuk membuangnya. Mac: ⌘+B.'),
            $p('p2', 'Format', '🔤', 1, 'Tekan pintasan untuk <strong>menengahkan (center)</strong> perenggan.', ['Ctrl+E'],
                'Bukan C, kerana C digunakan untuk Copy. Huruf kedua dalam "cEnter".', '<strong>Ctrl+E</strong> = Center. Kiri: Ctrl+L, kanan: Ctrl+R, justify: Ctrl+J.'),
            $p('p3', 'Halaman', '📐', 1, 'Tekan pintasan untuk <strong>memulakan halaman baharu</strong> (page break) tanpa menekan Enter berkali-kali.', ['Ctrl+Enter'],
                'Ctrl bersama kekunci yang biasa anda tekan untuk baris baharu.', '<strong>Ctrl+Enter</strong> memasukkan Page Break. Teks di atas boleh bertambah tanpa merosakkan susunan halaman seterusnya.'),
            $p('p4', 'Format', '🔤', 2, 'Tekan pintasan untuk <strong>baris baharu dalam perenggan yang sama</strong> (line break), contohnya alamat dalam surat.', ['Shift+Enter'],
                'Kali ini bukan Ctrl.', '<strong>Shift+Enter</strong> = Line Break: baris baharu tanpa jarak perenggan. Sesuai untuk alamat dan blok tandatangan.'),
            $p('p5', 'Styles', '🎨', 2, 'Tekan pintasan untuk menjadikan tajuk ini <strong>Heading 1</strong>.', ['Ctrl+Alt+1'],
                'Dua kekunci pengubah dan nombor aras tajuk.', '<strong>Ctrl+Alt+1</strong> = Heading 1 (Ctrl+Alt+2 untuk Heading 2). Tajuk yang menggunakan style Heading muncul dalam Table of Contents dan Navigation Pane.'),
            $p('p6', 'Format', '🔤', 2, 'Teks tertaip dalam HURUF BESAR secara tidak sengaja. Tekan pintasan untuk <strong>menukar huruf (change case)</strong> tanpa menaip semula.', ['Shift+F3'],
                'Kekunci fungsi di baris atas papan kekunci.', '<strong>Shift+F3</strong> bertukar antara huruf kecil, HURUF BESAR dan Capitalize Each Word.'),
            $p('p7', 'Format', '🖌️', 2, 'Tekan pintasan untuk <strong>menyalin format</strong> teks (bukan teksnya).', ['Ctrl+Alt+C', 'Ctrl+Shift+C'],
                'Copy biasa dengan satu kekunci pengubah lagi.', 'Microsoft 365: <strong>Ctrl+Alt+C</strong> menyalin format dan <strong>Ctrl+Alt+V</strong> menampalnya (sama seperti Format Painter). Word 2019 dan lebih lama: Ctrl+Shift+C dan Ctrl+Shift+V.'),
            $p('p8', 'Format', '🧽', 2, 'Tekan pintasan untuk <strong>membuang semua format huruf manual</strong> pada teks yang dipilih.', ['Ctrl+Space'],
                'Kekunci paling panjang di papan kekunci. (Di Mac, ⌘+Space membuka Spotlight, jadi taip jawapan.)', '<strong>Ctrl+Space</strong> membuang format huruf manual (bold, warna, saiz) dan kembali kepada format style perenggan.'),
            $p('p9', 'Semakan', '✍️', 2, 'Tekan pintasan untuk <strong>menghidupkan Track Changes</strong>.', ['Ctrl+Shift+E'],
                'E untuk "Edits".', '<strong>Ctrl+Shift+E</strong> menogol Track Changes (Review → Track Changes).'),
            $p('p10', 'Semakan', '💬', 3, 'Tekan pintasan untuk <strong>menambah komen</strong> pada teks yang dipilih.', ['Ctrl+Alt+M'],
                'M untuk "Memo", dengan dua kekunci pengubah. (Di Mac, sistem menggunakan kombinasi ini, jadi taip jawapan.)', '<strong>Ctrl+Alt+M</strong> = New Comment (Review → New Comment).'),
            $p('p11', 'Pautan', '🔗', 1, 'Tekan pintasan untuk <strong>memasukkan hyperlink</strong>.', ['Ctrl+K'],
                'Huruf yang sama digunakan dalam Excel dan PowerPoint.', '<strong>Ctrl+K</strong> membuka dialog Insert Link.'),
            $p('p12', 'Semakan', '🔎', 1, 'Tekan pintasan untuk <strong>menyemak ejaan dan tatabahasa</strong> (Editor).', ['F7'],
                'Satu kekunci fungsi sahaja.', '<strong>F7</strong> membuka Editor (Spelling & Grammar).'),
        ],
    ],
    'dokumen' => [
        'name' => 'Dokumen kemas',
        'icon' => 'file-text',
        'tagline' => 'Styles, halaman, laporan dan Mail Merge',
        'description' => 'Pilih cara yang betul untuk dokumen kerja sebenar: laporan, surat rasmi dan dokumen yang dikongsi.',
        'color' => 'amber',
        'questions' => [
            $c('d1', 'Styles', '🎨', 1, 'Anda mahukan <strong>Table of Contents</strong> automatik. Apakah yang perlu ada pada setiap tajuk bab?',
                ['Bold dan saiz fon 16', 'Style Heading 1 atau Heading 2', 'Nombor di hadapan tajuk', 'Garis bawah'], 1,
                'Table of Contents membaca style, bukan rupa teks.', 'Table of Contents dibina daripada style <strong>Heading 1, 2 dan 3</strong>. Tajuk yang hanya ditebalkan tidak dikenali.'),
            $c('d2', 'Laporan', '📑', 1, 'Anda menambah 3 halaman ke dalam laporan. Nombor halaman dalam <strong>Table of Contents</strong> kini salah. Apakah yang perlu dilakukan?',
                ['Padam Table of Contents dan masukkan semula', 'Taip nombor baharu secara manual', 'Klik Table of Contents → Update Table (atau F9)', 'Simpan dan buka semula fail'], 2,
                'Table of Contents ialah field.', 'Klik dalam Table of Contents → <strong>Update Table</strong> → Update entire table. Tajuk dan nombor halaman dikemas kini serentak.'),
            $c('d3', 'Halaman', '📐', 2, 'Laporan berorientasi potret, tetapi <strong>halaman 5 sahaja</strong> perlu landscape untuk jadual yang lebar. Apakah cara yang betul?',
                ['Putar jadual 90°', 'Section Break (Next Page) sebelum dan selepas halaman 5, kemudian Orientation → Landscape', 'Page Break sebelum halaman 5', 'Simpan halaman 5 dalam fail berasingan'], 1,
                'Orientasi yang berbeza memerlukan section yang berbeza.', '<strong>Section Break</strong> membolehkan setiap section mempunyai orientasi, margin dan header sendiri. Page Break tidak boleh.'),
            $c('d4', 'Halaman', '🔢', 2, 'Halaman kulit laporan <strong>tidak perlu nombor halaman</strong>, tetapi halaman lain perlu. Apakah pilihan yang perlu ditanda?',
                ['Different First Page', 'Different Odd & Even Pages', 'Link to Previous', 'Show Document Text'], 0,
                'Terdapat dalam tab Header & Footer.', '<strong>Different First Page</strong> memberi halaman pertama header dan footer sendiri, yang boleh dibiarkan kosong.'),
            $c('d5', 'Format', '⏎', 1, 'Cara yang paling kemas untuk memberi jarak antara perenggan ialah…',
                ['Tekan Enter dua kali', 'Spacing After pada perenggan (atau dalam style)', 'Tambah baris kosong dengan Space', 'Tukar kepada fon yang lebih besar'], 1,
                'Jarak yang kekal walaupun teks berubah.', '<strong>Spacing After</strong> (Layout → Spacing) memberi jarak tetap tanpa perenggan kosong yang mudah tercicir.'),
            $c('d6', 'Jadual', '🖼️', 2, 'Sebuah jadual panjang merentas 3 halaman. Anda mahu <strong>baris tajuk jadual muncul pada setiap halaman</strong>.',
                ['Salin baris tajuk ke setiap halaman', 'Table Layout → Repeat Header Rows', 'Insert → Header', 'Pecahkan kepada 3 jadual'], 1,
                'Pilih baris tajuk dahulu.', '<strong>Repeat Header Rows</strong> mengulang baris tajuk secara automatik, dan ia kekal betul apabila jadual berubah.'),
            $c('d7', 'Gambar', '🖼️', 2, 'Gambar dalam laporan sering berubah kedudukan apabila teks disunting. Pilihan Wrap Text yang paling stabil untuk laporan ialah…',
                ['Square', 'Behind Text', 'In Line with Text', 'Tight'], 2,
                'Gambar dilayan seperti satu aksara.', '<strong>In Line with Text</strong> menjadikan gambar sebahagian daripada baris teks, jadi ia bergerak bersama perenggannya.'),
            $c('d8', 'Semakan', '✍️', 2, 'Sebelum menghantar versi akhir kepada klien, dokumen masih mengandungi Track Changes dan komen. Apakah langkah yang betul?',
                ['Matikan Track Changes sahaja', 'Accept All Changes, padam komen, kemudian Inspect Document', 'Tukar kepada paparan "No Markup"', 'Simpan sebagai fail .docx baharu'], 1,
                '"No Markup" hanya menyorokkan paparan.', 'Paparan <strong>No Markup</strong> masih menyimpan semua suntingan dalam fail. Accept All Changes, padam komen, kemudian <strong>Inspect Document</strong> untuk membuangnya sepenuhnya.'),
            $c('d9', 'Mail Merge', '✉️', 2, 'Anda perlu menghantar surat yang sama kepada 200 pelanggan. Nama dan alamat mereka dalam Excel. Apakah ciri yang sesuai?',
                ['Salin dan tampal 200 kali', 'Mailings → Start Mail Merge → Letters', 'Insert → Object', 'References → Citation'], 1,
                'Tab Mailings.', '<strong>Mail Merge</strong> mengambil setiap baris dalam Excel dan menjana satu surat untuk setiap pelanggan.'),
            $c('d10', 'Mail Merge', '✉️', 3, 'Dalam Mail Merge, fail Excel penerima mesti…',
                ['Mempunyai baris pertama sebagai tajuk lajur (Nama, Alamat…) tanpa baris kosong', 'Disimpan sebagai PDF', 'Mempunyai satu lajur sahaja', 'Dikunci dengan kata laluan'], 0,
                'Word membaca nama lajur sebagai nama medan.', 'Baris pertama menjadi <strong>Merge Field</strong> (contohnya «Nama»). Baris kosong menghasilkan surat kosong.'),
            $c('d11', 'Simpan', '💾', 1, 'Dokumen akhir perlu dihantar dan <strong>susun aturnya tidak boleh berubah</strong> pada komputer penerima. Apakah format yang paling sesuai?',
                ['.docx', '.txt', 'PDF', '.rtf'], 2,
                'Format yang berfungsi seperti cetakan digital.', '<strong>PDF</strong> mengekalkan fon dan susun atur. Hantar .docx hanya apabila penerima perlu menyunting.'),
            $c('d12', 'Format', '🔤', 1, 'Teks yang ditampal daripada laman web membawa fon dan warna yang tidak sepadan. Apakah cara yang paling cepat?',
                ['Tukar fon satu demi satu', 'Paste → Keep Text Only (atau Ctrl+Space selepas menampal)', 'Taip semula', 'Simpan sebagai PDF'], 1,
                'Tampal tanpa format sumber.', '<strong>Keep Text Only</strong> menampal teks sahaja, jadi format dokumen anda kekal. Dalam Microsoft 365, pintasannya ialah Ctrl+Shift+V.'),
        ],
    ],
];
