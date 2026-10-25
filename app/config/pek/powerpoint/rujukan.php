<?php

declare(strict_types=1);

/**
 * Rujukan ciri PowerPoint (/powerpoint/reference), BM. Versi EN: en/rujukan.php (nama, cat, pintasan SAMA).
 * Laluan ribbon = PowerPoint Microsoft 365 (Windows). Pintasan disahkan dengan halaman Microsoft
 * "Use keyboard shortcuts to create / deliver PowerPoint presentations" (23 Okt 2026).
 */
return [
    'tajuk' => 'Rujukan ciri PowerPoint',
    'intro' => 'Ciri utama Microsoft PowerPoint dengan laluan ribbon yang tepat, pintasan papan kekunci dan nota penggunaan untuk pembentangan kerja.',
    'kategori' => [
        'slaid' => ['Slaid', 'presentation'],
        'master' => ['Slide Master dan tema', 'layout-template'],
        'objek' => ['Objek dan susunan', 'layers'],
        'media' => ['Gambar, carta dan media', 'image'],
        'gerak' => ['Animasi dan peralihan', 'play'],
        'bentang' => ['Membentang', 'monitor'],
        'fail' => ['Simpan dan kongsi', 'share-2'],
    ],
    'ciri' => [
        // === SLAID ===
        ['nama' => 'New Slide & Layout', 'cat' => 'slaid', 'popular' => true, 'desc' => 'Masukkan slaid baharu dengan susun atur tertentu, seperti Title and Content, Two Content atau Comparison.', 'langkah' => 'Home → New Slide (anak panah) → pilih layout. Untuk menukar layout slaid sedia ada: Home → Layout.', 'pintasan' => 'Ctrl+M'],
        ['nama' => 'Duplicate Slide', 'cat' => 'slaid', 'desc' => 'Salin slaid untuk membina versi seterusnya. Ini juga asas peralihan Morph.', 'langkah' => 'Klik kanan lakaran kecil slaid → Duplicate Slide.', 'pintasan' => 'Ctrl+D'],
        ['nama' => 'Sections', 'cat' => 'slaid', 'desc' => 'Kumpulkan slaid mengikut bahagian supaya mudah disusun dan dipindahkan bersama.', 'langkah' => 'Klik di antara dua slaid dalam panel lakaran kecil → Home → Section → Add Section.'],
        ['nama' => 'Reuse Slides', 'cat' => 'slaid', 'desc' => 'Masukkan slaid daripada fail lain tanpa membuka fail tersebut.', 'langkah' => 'Home → New Slide (anak panah) → Reuse Slides.'],
        ['nama' => 'Hide Slide', 'cat' => 'slaid', 'desc' => 'Simpan slaid simpanan yang tidak dipaparkan semasa membentang.', 'langkah' => 'Klik kanan lakaran kecil slaid → Hide Slide, atau Slide Show → Hide Slide.'],
        ['nama' => 'Slide Number & Footer', 'cat' => 'slaid', 'desc' => 'Paparkan nombor slaid dan teks kaki pada setiap slaid.', 'langkah' => 'Insert → Header & Footer → tandakan Slide number dan Footer → Apply to All.'],

        // === SLIDE MASTER DAN TEMA ===
        ['nama' => 'Slide Master', 'cat' => 'master', 'popular' => true, 'desc' => 'Templat induk yang menetapkan logo, fon, warna dan kedudukan tajuk untuk semua slaid.', 'langkah' => 'View → Slide Master. Untuk keluar: Slide Master → Close Master View.', 'tip' => 'Logo pada induk yang paling atas muncul pada semua slaid dan tidak terusik semasa menyunting slaid biasa.'],
        ['nama' => 'Theme Colors & Fonts', 'cat' => 'master', 'desc' => 'Set warna dan fon jenama yang digunakan oleh carta, SmartArt dan bentuk.', 'langkah' => 'Design → Variants (anak panah More) → Colors atau Fonts → Customize Colors / Customize Fonts.'],
        ['nama' => 'Designer', 'cat' => 'master', 'desc' => 'Cadangan susun atur automatik untuk slaid semasa. Memerlukan Microsoft 365 dan sambungan internet.', 'langkah' => 'Design → Designer.'],
        ['nama' => 'Slide Size', 'cat' => 'master', 'desc' => 'Pilih nisbah 16:9 untuk skrin, 4:3, atau saiz tersuai seperti kertas A4.', 'langkah' => 'Design → Slide Size → Widescreen, Standard atau Custom Slide Size.', 'tip' => 'Tetapkan saiz sebelum mula. Menukarnya kemudian akan menganjak atau mengubah skala semua objek.'],

        // === OBJEK DAN SUSUNAN ===
        ['nama' => 'Align & Distribute', 'cat' => 'objek', 'popular' => true, 'desc' => 'Sejajarkan objek dan samakan jarak di antaranya.', 'langkah' => 'Pilih objek → Shape Format → Align → Align Middle atau Distribute Horizontally.'],
        ['nama' => 'Group', 'cat' => 'objek', 'desc' => 'Satukan beberapa objek supaya bergerak dan berubah saiz bersama.', 'langkah' => 'Pilih objek → Shape Format → Group → Group.', 'pintasan' => 'Ctrl+G / Ctrl+Shift+G (Ungroup)'],
        ['nama' => 'Selection Pane', 'cat' => 'objek', 'popular' => true, 'desc' => 'Senarai semua objek pada slaid. Gunakannya untuk memilih objek yang tertimbus, menamakan, menyorok dan menyusun lapisan.', 'langkah' => 'Home → Arrange → Selection Pane.', 'pintasan' => 'Alt+F10', 'tip' => 'Namakan objek sebelum menggunakan Morph. Objek yang sama nama pada dua slaid dipadankan dengan tepat.'],
        ['nama' => 'Format Painter', 'cat' => 'objek', 'desc' => 'Salin format satu objek atau teks ke objek lain.', 'langkah' => 'Pilih objek sumber → Home → Format Painter → klik objek sasaran. Klik dua kali Format Painter untuk menggunakannya berulang kali.', 'pintasan' => 'Ctrl+Shift+C / Ctrl+Shift+V'],
        ['nama' => 'Guides & Gridlines', 'cat' => 'objek', 'desc' => 'Garis panduan untuk margin dan lajur yang konsisten pada setiap slaid.', 'langkah' => 'View → tandakan Guides atau Gridlines. Untuk menambah garis panduan: klik kanan garis panduan → Add Vertical Guide / Add Horizontal Guide.'],
        ['nama' => 'Convert to SmartArt', 'cat' => 'objek', 'desc' => 'Tukar senarai bertitik menjadi rajah proses, kitaran atau hierarki.', 'langkah' => 'Pilih kotak teks → Home → Convert to SmartArt.'],

        // === GAMBAR, CARTA DAN MEDIA ===
        ['nama' => 'Crop to Shape', 'cat' => 'media', 'desc' => 'Potong gambar mengikut bentuk, contohnya bulatan untuk foto profil.', 'langkah' => 'Klik gambar → Picture Format → Crop (anak panah) → Crop to Shape.'],
        ['nama' => 'Linked Excel Chart', 'cat' => 'media', 'popular' => true, 'desc' => 'Carta yang mengikut perubahan data dalam fail Excel asal.', 'langkah' => 'Salin carta dalam Excel → dalam PowerPoint: Home → Paste (anak panah) → Use Destination Theme & Link Data. Untuk mengemas kini: Chart Design → Refresh Data.'],
        ['nama' => 'Icons', 'cat' => 'media', 'desc' => 'Ikon vektor yang boleh diwarnakan mengikut tema.', 'langkah' => 'Insert → Icons.'],
        ['nama' => 'Compress Pictures', 'cat' => 'media', 'desc' => 'Kecilkan saiz fail yang mengandungi banyak gambar.', 'langkah' => 'Klik gambar → Picture Format → Compress Pictures.'],
        ['nama' => 'Alt Text', 'cat' => 'media', 'desc' => 'Penerangan gambar untuk pengguna pembaca skrin.', 'langkah' => 'Klik kanan gambar → View Alt Text. Untuk menyemak semua slaid: Review → Check Accessibility.'],

        // === ANIMASI DAN PERALIHAN ===
        ['nama' => 'Transition', 'cat' => 'gerak', 'desc' => 'Kesan peralihan ketika bertukar dari satu slaid ke slaid seterusnya.', 'langkah' => 'Transitions → pilih kesan → Apply To All jika mahu semua slaid sama.'],
        ['nama' => 'Morph', 'cat' => 'gerak', 'popular' => true, 'desc' => 'Objek bergerak dengan lancar dari kedudukannya pada satu slaid ke kedudukan baharu pada slaid seterusnya.', 'langkah' => 'Gandakan slaid → ubah kedudukan atau saiz objek pada salinan → pilih slaid kedua → Transitions → Morph.'],
        ['nama' => 'Animation Pane', 'cat' => 'gerak', 'desc' => 'Urutan dan masa setiap animasi di dalam satu slaid.', 'langkah' => 'Pilih objek → Animations → pilih kesan. Untuk mengatur urutan: Animations → Animation Pane.', 'tip' => 'Gunakan animasi untuk menunjukkan urutan idea, bukan sebagai hiasan pada setiap objek.'],

        // === MEMBENTANG ===
        ['nama' => 'Presenter View', 'cat' => 'bentang', 'popular' => true, 'desc' => 'Nota, slaid seterusnya dan pemasa dipaparkan di skrin anda. Audiens hanya nampak slaid.', 'langkah' => 'Slide Show → tandakan Use Presenter View → From Beginning.', 'pintasan' => 'Alt+F5'],
        ['nama' => 'Speaker Notes', 'cat' => 'bentang', 'desc' => 'Nota penceramah di bawah slaid. Nota ini tidak dipaparkan kepada audiens.', 'langkah' => 'Klik Notes pada bar status di bawah, atau View → Notes Page.'],
        ['nama' => 'Rehearse Timings', 'cat' => 'bentang', 'desc' => 'Berlatih dan rekod masa yang diambil untuk setiap slaid.', 'langkah' => 'Slide Show → Rehearse Timings.'],
        ['nama' => 'Slide Show Controls', 'cat' => 'bentang', 'desc' => 'Kekunci untuk memulakan, melompat slaid, menggelapkan skrin dan menggunakan laser, pen atau pemadam.', 'langkah' => 'F5 mula dari awal · Shift+F5 mula dari slaid semasa · nombor slaid + Enter untuk melompat · B skrin hitam · W skrin putih · Esc tamat. Ctrl+L laser, Ctrl+P pen, Ctrl+E pemadam.', 'pintasan' => 'Ctrl+L · Ctrl+P · Ctrl+E'],

        // === SIMPAN DAN KONGSI ===
        ['nama' => 'Export PDF', 'cat' => 'fail', 'popular' => true, 'desc' => 'Edarkan slaid dalam bentuk yang susun aturnya tidak berubah.', 'langkah' => 'File → Export → Create PDF/XPS Document → Create PDF/XPS. Untuk handout: Options → Publish what → Handouts.'],
        ['nama' => 'Export Video', 'cat' => 'fail', 'desc' => 'Tukar pembentangan, termasuk masa dan narasi yang dirakam, kepada video MP4.', 'langkah' => 'File → Export → Create a Video.'],
        ['nama' => 'Embed Fonts', 'cat' => 'fail', 'desc' => 'Bawa fon khas bersama fail supaya tidak digantikan di komputer lain.', 'langkah' => 'File → Options → Save → tandakan Embed fonts in the file.'],
    ],
];
