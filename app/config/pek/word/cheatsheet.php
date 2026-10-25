<?php

declare(strict_types=1);

/**
 * Cheat sheet Word (/word/cheatsheet), BM. Versi EN: en/cheatsheet.php (struktur sama). Skema: docs/REVAMP.md.
 * Jenis bahagian: 'pintasan' (grid kekunci), 'kad' (tajuk + teks), 'jadual' (kepala + baris), 'kesilapan' (masalah + pembetulan, 'amalan').
 * Pintasan = Word untuk Microsoft 365 (Windows).
 */
return [
    'lencana' => 'Cheat sheet Word',
    'tajuk' => 'Rujukan pantas Word',
    'intro' => 'Pintasan, Styles, susun atur halaman, laporan panjang dan kesilapan yang biasa dibuat dalam dokumen kerja.',
    'bahagian' => [
        [
            'id' => 'shortcuts', 'ikon' => 'keyboard', 'tajuk' => 'Pintasan harian', 'jenis' => 'pintasan',
            'sub' => 'Word untuk Microsoft 365 di Windows. Pintasan Mac berbeza bagi sesetengah item.',
            'item' => [
                ['keys' => ['Ctrl', 'B'], 'name' => 'Bold', 'desc' => 'Tebalkan teks yang dipilih.'],
                ['keys' => ['Ctrl', 'I'], 'name' => 'Italic', 'desc' => 'Condongkan teks.'],
                ['keys' => ['Ctrl', 'U'], 'name' => 'Underline', 'desc' => 'Gariskan bawah teks.'],
                ['keys' => ['Ctrl', 'E'], 'name' => 'Center', 'desc' => 'Tengahkan perenggan.'],
                ['keys' => ['Ctrl', 'L'], 'name' => 'Align Left', 'desc' => 'Jajarkan perenggan ke kiri.'],
                ['keys' => ['Ctrl', 'J'], 'name' => 'Justify', 'desc' => 'Jajarkan ke kiri dan kanan, lazim untuk surat rasmi.'],
                ['keys' => ['Ctrl', 'Enter'], 'name' => 'Page Break', 'desc' => 'Mulakan halaman baharu. Jangan tekan Enter berkali-kali.'],
                ['keys' => ['Shift', 'Enter'], 'name' => 'Line Break', 'desc' => 'Baris baharu dalam perenggan yang sama.'],
                ['keys' => ['Ctrl', 'Alt', '1'], 'name' => 'Heading 1', 'desc' => 'Gaya tajuk utama. Table of Contents dibina daripadanya.'],
                ['keys' => ['Ctrl', 'Alt', '2'], 'name' => 'Heading 2', 'desc' => 'Gaya subtajuk.'],
                ['keys' => ['Ctrl', 'Shift', 'N'], 'name' => 'Normal', 'desc' => 'Kembalikan perenggan kepada gaya Normal.'],
                ['keys' => ['Ctrl', 'Alt', 'C'], 'name' => 'Copy Formatting', 'desc' => 'Salin format. Tampal format dengan <strong>Ctrl+Alt+V</strong>. Word 2019 dan lebih lama: Ctrl+Shift+C dan Ctrl+Shift+V.'],
                ['keys' => ['Ctrl', 'Shift', 'V'], 'name' => 'Paste Text Only', 'desc' => 'Tampal teks sahaja tanpa format sumber (Microsoft 365).'],
                ['keys' => ['Ctrl', 'Space'], 'name' => 'Clear Character Formatting', 'desc' => 'Buang format huruf manual seperti bold, warna dan saiz.'],
                ['keys' => ['Shift', 'F3'], 'name' => 'Change Case', 'desc' => 'Tukar antara huruf kecil, HURUF BESAR dan Capitalize Each Word.'],
                ['keys' => ['Ctrl', 'K'], 'name' => 'Hyperlink', 'desc' => 'Masukkan pautan.'],
                ['keys' => ['Ctrl', 'H'], 'name' => 'Replace', 'desc' => 'Cari dan ganti teks, format atau aksara khas.'],
                ['keys' => ['Ctrl', 'Shift', '8'], 'name' => 'Show/Hide ¶', 'desc' => 'Papar atau sorok tanda perenggan, tab dan ruang.'],
                ['keys' => ['F7'], 'name' => 'Editor', 'desc' => 'Semak ejaan dan tatabahasa.'],
                ['keys' => ['Ctrl', 'Alt', 'M'], 'name' => 'New Comment', 'desc' => 'Tambah komen pada teks yang dipilih.'],
                ['keys' => ['Ctrl', 'Shift', 'E'], 'name' => 'Track Changes', 'desc' => 'Hidupkan atau matikan Track Changes.'],
            ],
        ],
        [
            'id' => 'styles', 'ikon' => 'heading', 'tajuk' => 'Styles untuk dokumen yang kemas', 'jenis' => 'kad',
            'sub' => 'Ubah satu style, dan setiap teks yang menggunakannya berubah. Format manual perlu diubah satu demi satu.',
            'item' => [
                ['Heading 1, 2, 3', 'Gunakan untuk tajuk bab dan subtajuk. Navigation Pane (Ctrl+F) dan Table of Contents dibina daripadanya.'],
                ['Normal', 'Gaya teks biasa. Untuk menukar fon atau saiz seluruh dokumen: Home → Styles → klik kanan Normal → Modify.'],
                ['Modify Style', 'Klik kanan nama style di Home → Styles → Modify. Jangan tanda <em>Automatically update</em>, kerana style akan berubah setiap kali anda memformat teks secara manual.'],
                ['Update to Match Selection', 'Formatkan satu tajuk dahulu, kemudian klik kanan Heading 1 → Update Heading 1 to Match Selection.'],
            ],
        ],
        [
            'id' => 'breaks', 'ikon' => 'between-horizontal-start', 'tajuk' => 'Jenis break', 'jenis' => 'jadual',
            'sub' => 'Pilih break mengikut apa yang perlu berubah selepasnya.',
            'kepala' => ['Break', 'Cara', 'Kegunaan'],
            'baris' => [
                ['Line Break', '<strong>Shift+Enter</strong>', 'Baris baharu dalam perenggan yang sama, contohnya alamat dalam surat.'],
                ['Page Break', '<strong>Ctrl+Enter</strong> atau Insert → Page Break', 'Mulakan halaman baharu. Tetapan halaman kekal sama.'],
                ['Column Break', '<strong>Ctrl+Shift+Enter</strong> atau Layout → Breaks → Column', 'Pindahkan teks ke lajur seterusnya dalam susun atur berlajur.'],
                ['Section Break (Next Page)', 'Layout → Breaks → Next Page', 'Halaman baharu dengan orientasi, margin, header atau nombor halaman yang boleh berbeza.'],
                ['Section Break (Continuous)', 'Layout → Breaks → Continuous', 'Bahagian baharu pada halaman yang sama, contohnya dua lajur untuk sebahagian halaman sahaja.'],
            ],
        ],
        [
            'id' => 'page-layout', 'ikon' => 'ruler', 'tajuk' => 'Susun atur halaman', 'jenis' => 'kad',
            'sub' => 'Tab Layout mengawal saiz kertas, margin, orientasi dan break.',
            'item' => [
                ['Halaman landscape di tengah dokumen', 'Masukkan Section Break (Next Page) sebelum dan selepas halaman itu. Kemudian klik dalam halaman itu dan pilih Layout → Orientation → Landscape.'],
                ['Margin dan saiz kertas', 'Layout → Margins dan Layout → Size. Dokumen rasmi di Malaysia biasanya menggunakan A4.'],
                ['Nombor halaman', 'Insert → Page Number. Untuk bermula pada nombor lain: Page Number → Format Page Numbers → Start at.'],
                ['Header dan footer', 'Insert → Header atau Footer, atau dwiklik bahagian atas halaman. Matikan <em>Link to Previous</em> untuk header yang berbeza dalam setiap section.'],
            ],
        ],
        [
            'id' => 'long-documents', 'ikon' => 'book-open', 'tajuk' => 'Laporan panjang', 'jenis' => 'kad',
            'sub' => 'Tab References. Semua item ini dikemas kini oleh Word, jadi jangan menaipnya sendiri.',
            'item' => [
                ['Table of Contents', 'References → Table of Contents. Tajuk mesti menggunakan Heading 1, 2 atau 3. Selepas menyunting: klik dalam Table of Contents → Update Table, atau tekan F9.'],
                ['Caption', 'Klik kanan gambar atau jadual → Insert Caption. Nombor Rajah 1, 2, 3 dijana secara automatik.'],
                ['Cross-reference', 'References → Cross-reference. Rujukan seperti "lihat Rajah 3" dikemas kini apabila rajah dialihkan.'],
                ['Footnote', 'References → Insert Footnote (Ctrl+Alt+F). Nombor nota kaki disusun semula secara automatik.'],
            ],
        ],
        [
            'id' => 'mistakes', 'ikon' => 'triangle-alert', 'tajuk' => 'Kesilapan biasa', 'jenis' => 'kesilapan',
            'sub' => 'Elakkan perkara ini supaya dokumen kekal kemas dan mudah disunting.',
            'item' => [
                ['Menekan Enter berkali-kali untuk ke halaman baharu', 'Apabila teks di atas bertambah, semua kandungan beralih dan susunan rosak. <strong>Pembetulan:</strong> Ctrl+Enter (Page Break).'],
                ['Menekan Space atau Tab berkali-kali untuk menjajarkan teks', 'Teks kelihatan lurus pada skrin anda tetapi senget pada komputer lain. <strong>Pembetulan:</strong> gunakan jajaran perenggan, tab stop pada ruler, atau jadual tanpa border.'],
                ['Memformat tajuk secara manual (bold dan saiz besar)', 'Table of Contents dan Navigation Pane tidak mengenalinya. <strong>Pembetulan:</strong> gunakan style Heading 1, 2 atau 3.'],
                ['Menaip nombor halaman atau Table of Contents sendiri', 'Nombor menjadi salah sebaik sahaja dokumen berubah. <strong>Pembetulan:</strong> Insert → Page Number dan References → Table of Contents.'],
                ['Menghantar dokumen yang masih mengandungi Track Changes', 'Penerima dapat melihat semua suntingan dan komen. <strong>Pembetulan:</strong> Review → Accept All Changes, padam komen, kemudian File → Info → Check for Issues → Inspect Document.'],
            ],
            'amalan' => [
                'Hidupkan Show/Hide ¶ (Ctrl+Shift+8) apabila susun atur kelihatan pelik. Enter, Tab dan break yang tersembunyi akan kelihatan.',
                'Simpan sebagai PDF (File → Save As → PDF) untuk dokumen akhir supaya susun atur tidak berubah pada komputer lain.',
                'Simpan fail dalam OneDrive untuk mendapatkan AutoSave dan Version History.',
                'Gunakan templat (File → New) untuk surat dan laporan yang berulang.',
            ],
        ],
    ],
];
