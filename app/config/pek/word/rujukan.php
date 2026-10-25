<?php

declare(strict_types=1);

/**
 * Rujukan ciri Word (/word/reference), BM. Versi EN: en/rujukan.php (nama, cat, pintasan SAMA; susunan sama).
 * Laluan ribbon = Word untuk Microsoft 365 (Windows). Medan: nama, cat, desc, langkah, pintasan (pilihan), tip (pilihan),
 * popular (pilihan). Kategori: kunci → [label, ikon Lucide]. Paparan semasa meletakkan bilangan ciri di hadapan `intro`.
 */
return [
    'tajuk' => 'Rujukan ciri Word',
    'intro' => 'ciri Word yang penting, dengan laluan ribbon, pintasan dan tip untuk dokumen kerja.',
    'kategori' => [
        'format' => ['Format', 'type'],
        'gaya' => ['Gaya dan struktur', 'heading'],
        'halaman' => ['Halaman', 'ruler'],
        'jadual' => ['Jadual dan gambar', 'table'],
        'laporan' => ['Laporan', 'book-open'],
        'semakan' => ['Semakan', 'pen-line'],
        'mel' => ['Mail Merge', 'mail'],
        'fail' => ['Simpan dan kongsi', 'save'],
    ],
    'ciri' => [
        // === FORMAT ===
        ['nama' => 'Format Painter', 'cat' => 'format', 'popular' => true, 'desc' => 'Salin format satu teks ke teks lain.', 'langkah' => 'Pilih teks sumber → Home → Format Painter → pilih teks sasaran.', 'pintasan' => 'Ctrl+Alt+C / Ctrl+Alt+V', 'tip' => 'Dwiklik Format Painter untuk menggunakannya berkali-kali, kemudian tekan Esc. Word 2019 dan lebih lama: Ctrl+Shift+C dan Ctrl+Shift+V.'],
        ['nama' => 'Clear Formatting', 'cat' => 'format', 'desc' => 'Buang format pada teks dan kembali kepada style asal.', 'langkah' => 'Pilih teks → Home → Clear All Formatting (ikon huruf A dengan pemadam).', 'pintasan' => 'Ctrl+Space', 'tip' => 'Butang Clear All Formatting membuang semua format. Ctrl+Space membuang format huruf manual sahaja. Teks yang ditampal daripada web selalunya membawa format asing, jadi bersihkan dahulu.'],
        ['nama' => 'Paste: Keep Text Only', 'cat' => 'format', 'popular' => true, 'desc' => 'Tampal teks tanpa format sumber.', 'langkah' => 'Home → Paste (anak panah bawah) → Keep Text Only.', 'pintasan' => 'Ctrl+Shift+V', 'tip' => 'Pintasan ini untuk Microsoft 365. Untuk menetapkan cara tampal lalai: File → Options → Advanced → Cut, copy, and paste.'],
        ['nama' => 'Line & Paragraph Spacing', 'cat' => 'format', 'desc' => 'Jarak antara baris dan antara perenggan.', 'langkah' => 'Home → Line and Paragraph Spacing (1.0, 1.15, 1.5, 2.0). Jarak antara perenggan: Layout → Spacing Before dan After.', 'pintasan' => 'Ctrl+1 / Ctrl+5 / Ctrl+2', 'tip' => 'Ctrl+1 = satu baris, Ctrl+5 = 1.5 baris, Ctrl+2 = dua baris. Gunakan Spacing After, bukan Enter kosong, untuk jarak antara perenggan.'],
        ['nama' => 'Change Case', 'cat' => 'format', 'desc' => 'Tukar kepada huruf kecil, HURUF BESAR atau Capitalize Each Word tanpa menaip semula.', 'langkah' => 'Pilih teks → Home → Change Case (Aa).', 'pintasan' => 'Shift+F3'],
        ['nama' => 'Bullets & Numbering', 'cat' => 'format', 'popular' => true, 'desc' => 'Senarai bertitik atau bernombor yang disusun semula secara automatik.', 'langkah' => 'Home → Bullets atau Numbering. Untuk bermula semula dari 1: klik kanan nombor → Restart at 1.', 'tip' => 'Di awal item senarai, Tab menurunkan satu aras dan Shift+Tab menaikkan satu aras.'],
        ['nama' => 'Find & Replace', 'cat' => 'format', 'popular' => true, 'desc' => 'Cari dan ganti teks, termasuk format dan aksara khas.', 'langkah' => 'Home → Replace. Klik More >> untuk pilihan Format dan Special (contohnya <code>^p</code> = tanda perenggan).', 'pintasan' => 'Ctrl+H', 'tip' => 'Untuk membuang perenggan kosong berganda: Find what <code>^p^p</code>, Replace with <code>^p</code>.'],

        // === GAYA DAN STRUKTUR ===
        ['nama' => 'Styles (Heading 1/2/3)', 'cat' => 'gaya', 'popular' => true, 'desc' => 'Gaya tajuk yang menjadi asas Table of Contents, Navigation Pane dan penomboran bab.', 'langkah' => 'Pilih tajuk → Home → Styles → Heading 1.', 'pintasan' => 'Ctrl+Alt+1 / 2 / 3', 'tip' => 'Untuk mengubah rupa semua tajuk serentak: klik kanan Heading 1 → Modify.'],
        ['nama' => 'Modify Style', 'cat' => 'gaya', 'desc' => 'Tukar fon, saiz, warna atau jarak sesuatu style. Seluruh dokumen mengikutnya.', 'langkah' => 'Home → Styles → klik kanan style → Modify → Format (Font atau Paragraph).'],
        ['nama' => 'Navigation Pane', 'cat' => 'gaya', 'popular' => true, 'desc' => 'Peta dokumen: lompat ke tajuk, atau seret tajuk untuk menyusun semula bab.', 'langkah' => 'View → Navigation Pane (tab Headings).', 'pintasan' => 'Ctrl+F', 'tip' => 'Jika tajuk tidak muncul, tajuk itu belum menggunakan style Heading.'],
        ['nama' => 'Multilevel List', 'cat' => 'gaya', 'desc' => 'Penomboran bab 1, 1.1, 1.1.1 yang dipautkan pada style Heading.', 'langkah' => 'Home → Multilevel List → pilih senarai yang memaparkan "Heading 1, Heading 2".'],
        ['nama' => 'Show/Hide ¶', 'cat' => 'gaya', 'desc' => 'Papar tanda perenggan, tab, ruang dan break yang tersembunyi.', 'langkah' => 'Home → ¶.', 'pintasan' => 'Ctrl+Shift+8', 'tip' => 'Langkah pertama apabila susun atur kelihatan pelik.'],

        // === HALAMAN ===
        ['nama' => 'Page Break', 'cat' => 'halaman', 'popular' => true, 'desc' => 'Mulakan halaman baharu di kedudukan kursor.', 'langkah' => 'Insert → Page Break, atau Layout → Breaks → Page.', 'pintasan' => 'Ctrl+Enter'],
        ['nama' => 'Section Break', 'cat' => 'halaman', 'popular' => true, 'desc' => 'Bahagikan dokumen supaya orientasi, margin, header atau nombor halaman boleh berbeza.', 'langkah' => 'Layout → Breaks → Next Page (atau Continuous).', 'tip' => 'Untuk satu halaman landscape di tengah dokumen: Section Break sebelum dan selepas halaman itu, kemudian Layout → Orientation → Landscape.'],
        ['nama' => 'Margins, Orientation, Size', 'cat' => 'halaman', 'desc' => 'Margin, arah dan saiz kertas.', 'langkah' => 'Layout → Margins, Orientation atau Size.'],
        ['nama' => 'Header & Footer', 'cat' => 'halaman', 'popular' => true, 'desc' => 'Teks yang berulang di bahagian atas atau bawah setiap halaman.', 'langkah' => 'Insert → Header atau Footer, atau dwiklik bahagian atas halaman.', 'tip' => 'Untuk header yang berbeza dalam satu section: matikan Link to Previous dalam tab Header & Footer.'],
        ['nama' => 'Page Number', 'cat' => 'halaman', 'popular' => true, 'desc' => 'Nombor halaman automatik, termasuk i, ii, iii untuk halaman awal.', 'langkah' => 'Insert → Page Number → pilih kedudukan. Format: Page Number → Format Page Numbers.', 'tip' => 'Untuk halaman kulit tanpa nombor: tab Header & Footer → Different First Page.'],
        ['nama' => 'Columns', 'cat' => 'halaman', 'desc' => 'Teks dalam dua atau tiga lajur, seperti surat berita.', 'langkah' => 'Layout → Columns. Untuk sebahagian teks sahaja, pilih teks itu dahulu.', 'pintasan' => 'Ctrl+Shift+Enter (Column Break)'],
        ['nama' => 'Watermark', 'cat' => 'halaman', 'desc' => 'Teks pudar di belakang halaman, contohnya DRAF atau SULIT.', 'langkah' => 'Design → Watermark → Custom Watermark.'],

        // === JADUAL DAN GAMBAR ===
        ['nama' => 'Insert Table', 'cat' => 'jadual', 'popular' => true, 'desc' => 'Jadual untuk data dan susun atur yang lurus.', 'langkah' => 'Insert → Table → pilih bilangan baris dan lajur.', 'tip' => 'Tekan Tab dalam sel terakhir untuk menambah baris baharu.'],
        ['nama' => 'Repeat Header Rows', 'cat' => 'jadual', 'desc' => 'Baris tajuk jadual diulang pada setiap halaman.', 'langkah' => 'Pilih baris tajuk → Table Layout → Repeat Header Rows.', 'tip' => 'Baris tajuk yang diulang hanya kelihatan dalam paparan Print Layout dan semasa mencetak.'],
        ['nama' => 'Convert Text to Table', 'cat' => 'jadual', 'desc' => 'Tukar teks yang dipisahkan dengan Tab atau koma menjadi jadual.', 'langkah' => 'Pilih teks → Insert → Table → Convert Text to Table.'],
        ['nama' => 'Wrap Text', 'cat' => 'jadual', 'popular' => true, 'desc' => 'Cara teks mengelilingi gambar.', 'langkah' => 'Klik gambar → Picture Format → Wrap Text (In Line with Text, Square, Tight dan lain-lain).', 'tip' => 'Jika gambar berubah kedudukan semasa anda menyunting, gunakan In Line with Text untuk laporan.'],
        ['nama' => 'Compress Pictures', 'cat' => 'jadual', 'desc' => 'Kecilkan saiz fail dokumen yang mengandungi banyak gambar.', 'langkah' => 'Klik gambar → Picture Format → Compress Pictures.'],

        // === LAPORAN ===
        ['nama' => 'Table of Contents', 'cat' => 'laporan', 'popular' => true, 'desc' => 'Isi kandungan (jadual kandungan) automatik dengan nombor halaman.', 'langkah' => 'References → Table of Contents → Automatic Table 1. Untuk mengemas kini: References → Update Table, atau klik dalam Table of Contents dan tekan F9.', 'pintasan' => 'F9', 'tip' => 'Tajuk mesti menggunakan style Heading 1, 2 atau 3.'],
        ['nama' => 'Insert Caption', 'cat' => 'laporan', 'desc' => 'Label bernombor untuk rajah dan jadual.', 'langkah' => 'Klik kanan gambar atau jadual → Insert Caption, atau References → Insert Caption.', 'tip' => 'Senarai rajah: References → Insert Table of Figures.'],
        ['nama' => 'Cross-reference', 'cat' => 'laporan', 'desc' => 'Rujukan seperti "lihat Rajah 3" yang dikemas kini secara automatik.', 'langkah' => 'References → Cross-reference → pilih jenis (Figure, Table, Heading).'],
        ['nama' => 'Footnote', 'cat' => 'laporan', 'desc' => 'Nota kaki bernombor di bawah halaman.', 'langkah' => 'References → Insert Footnote.', 'pintasan' => 'Ctrl+Alt+F'],
        ['nama' => 'Citation & Bibliography', 'cat' => 'laporan', 'desc' => 'Petikan sumber dan senarai bibliografi (APA dan gaya lain).', 'langkah' => 'References → Insert Citation → Add New Source. Senarai: References → Bibliography.'],

        // === SEMAKAN ===
        ['nama' => 'Track Changes', 'cat' => 'semakan', 'popular' => true, 'desc' => 'Rekod setiap suntingan supaya boleh diterima atau ditolak.', 'langkah' => 'Review → Track Changes. Untuk menerima: Review → Accept atau Accept All Changes.', 'pintasan' => 'Ctrl+Shift+E'],
        ['nama' => 'Comments', 'cat' => 'semakan', 'desc' => 'Komen di tepi dokumen tanpa mengubah teks.', 'langkah' => 'Pilih teks → Review → New Comment.', 'pintasan' => 'Ctrl+Alt+M'],
        ['nama' => 'Compare', 'cat' => 'semakan', 'desc' => 'Bandingkan dua versi dokumen dan lihat perbezaannya.', 'langkah' => 'Review → Compare → Compare → pilih dokumen asal dan dokumen yang disemak.'],
        ['nama' => 'Editor', 'cat' => 'semakan', 'desc' => 'Semak ejaan dan tatabahasa.', 'langkah' => 'Review → Editor (atau Spelling & Grammar). Bahasa: Review → Language → Set Proofing Language.', 'pintasan' => 'F7', 'tip' => 'Untuk dokumen BM, tetapkan bahasa kepada Malay supaya bukan setiap perkataan bergaris merah.'],
        ['nama' => 'Word Count', 'cat' => 'semakan', 'desc' => 'Bilangan perkataan, aksara dan perenggan.', 'langkah' => 'Review → Word Count, atau klik bilangan perkataan di bar status.', 'pintasan' => 'Ctrl+Shift+G'],

        // === MAIL MERGE ===
        ['nama' => 'Mail Merge: Letters', 'cat' => 'mel', 'popular' => true, 'desc' => 'Satu surat untuk ramai penerima. Nama dan alamat diambil daripada senarai Excel.', 'langkah' => 'Mailings → Start Mail Merge → Letters → Select Recipients → Use an Existing List (fail Excel) → Insert Merge Field → Preview Results → Finish & Merge.', 'tip' => 'Baris pertama dalam Excel mesti mengandungi tajuk lajur (Nama, Alamat dan seterusnya). Jangan tinggalkan baris kosong.'],
        ['nama' => 'Mail Merge: Labels', 'cat' => 'mel', 'desc' => 'Cetak label alamat atau tanda nama daripada senarai.', 'langkah' => 'Mailings → Start Mail Merge → Labels → pilih pengeluar dan saiz label → Select Recipients → Update Labels → Finish & Merge.'],

        // === SIMPAN DAN KONGSI ===
        ['nama' => 'Save as PDF', 'cat' => 'fail', 'popular' => true, 'desc' => 'Dokumen akhir yang susun aturnya tidak berubah pada komputer lain.', 'langkah' => 'File → Save As → pilih PDF, atau File → Export → Create PDF/XPS.', 'pintasan' => 'F12 (Save As)'],
        ['nama' => 'AutoSave & Version History', 'cat' => 'fail', 'desc' => 'Simpan secara automatik dan pulihkan versi lama.', 'langkah' => 'Simpan fail dalam OneDrive → suis AutoSave di bahagian atas kiri. Versi lama: File → Info → Version History.'],
        ['nama' => 'Inspect Document', 'cat' => 'fail', 'desc' => 'Buang komen, Track Changes dan maklumat peribadi yang tersembunyi sebelum menghantar dokumen.', 'langkah' => 'File → Info → Check for Issues → Inspect Document.'],
        ['nama' => 'Restrict Editing', 'cat' => 'fail', 'desc' => 'Hadkan suntingan, contohnya borang yang hanya boleh diisi.', 'langkah' => 'Review → Restrict Editing.'],
    ],
];
