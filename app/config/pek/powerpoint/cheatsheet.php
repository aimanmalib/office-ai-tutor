<?php

declare(strict_types=1);

/**
 * Cheat sheet PowerPoint (/powerpoint/cheatsheet), BM. Versi EN: en/cheatsheet.php (struktur sama; skema docs/REVAMP.md).
 * Pintasan = PowerPoint Microsoft 365 untuk Windows.
 */
return [
    'lencana' => 'Cheat sheet PowerPoint',
    'tajuk' => 'Rujukan pantas PowerPoint',
    'intro' => 'Pintasan papan kekunci, Slide Master, susunan objek dan kawalan semasa membentang, dalam satu halaman.',
    'bahagian' => [
        [
            'id' => 'shortcut', 'ikon' => 'keyboard', 'tajuk' => 'Pintasan utama', 'jenis' => 'pintasan',
            'sub' => 'PowerPoint untuk Windows. Di Mac, kebanyakan pintasan Ctrl menggunakan Command, tetapi tidak semuanya.',
            'item' => [
                ['keys' => ['Ctrl', 'M'], 'name' => 'New Slide', 'desc' => 'Masukkan slaid baharu selepas slaid semasa.'],
                ['keys' => ['Ctrl', 'D'], 'name' => 'Duplicate', 'desc' => 'Gandakan objek atau slaid yang dipilih.'],
                ['keys' => ['Ctrl', 'G'], 'name' => 'Group', 'desc' => 'Satukan objek yang dipilih menjadi satu kumpulan.'],
                ['keys' => ['Ctrl', 'Shift', 'G'], 'name' => 'Ungroup', 'desc' => 'Leraikan kumpulan kepada objek asal.'],
                ['keys' => ['Ctrl', 'Shift', 'C'], 'name' => 'Copy Formatting', 'desc' => 'Salin format objek atau teks. Tampal format dengan Ctrl+Shift+V.'],
                ['keys' => ['Ctrl', 'K'], 'name' => 'Insert Link', 'desc' => 'Pautan ke laman web atau ke slaid lain dalam fail yang sama.'],
                ['keys' => ['Ctrl', 'E'], 'name' => 'Center', 'desc' => 'Tengahkan perenggan yang dipilih.'],
                ['keys' => ['Ctrl', 'Shift', '>'], 'name' => 'Increase Font Size', 'desc' => 'Besarkan saiz fon teks yang dipilih.'],
                ['keys' => ['Alt', 'F10'], 'name' => 'Selection Pane', 'desc' => 'Senarai semua objek pada slaid untuk memilih, menyorok dan menyusun lapisan.'],
                ['keys' => ['Shift', 'Drag'], 'name' => 'Constrain', 'desc' => 'Gerakkan objek lurus mendatar atau menegak, atau ubah saiz mengikut nisbah.'],
                ['keys' => ['Ctrl', 'Drag'], 'name' => 'Drag to copy', 'desc' => 'Seret objek sambil menekan Ctrl untuk menghasilkan salinan.'],
                ['keys' => ['Ctrl', '←↑→↓'], 'name' => 'Nudge', 'desc' => 'Gerakkan objek terpilih dalam langkah yang sangat kecil.'],
            ],
        ],
        [
            'id' => 'presenting', 'ikon' => 'presentation', 'tajuk' => 'Semasa membentang', 'jenis' => 'pintasan',
            'sub' => 'Kekunci yang digunakan dalam Slide Show, supaya anda tidak perlu mencari tetikus di hadapan audiens.',
            'item' => [
                ['keys' => ['F5'], 'name' => 'From Beginning', 'desc' => 'Mulakan Slide Show dari slaid pertama.'],
                ['keys' => ['Shift', 'F5'], 'name' => 'From Current Slide', 'desc' => 'Mulakan Slide Show dari slaid yang sedang dibuka.'],
                ['keys' => ['Alt', 'F5'], 'name' => 'Presenter View', 'desc' => 'Mulakan Slide Show dengan nota dan slaid seterusnya di skrin anda.'],
                ['keys' => ['B'], 'name' => 'Skrin hitam', 'desc' => 'Tekan sekali lagi untuk kembali. W memaparkan skrin putih.'],
                ['keys' => ['5', 'Enter'], 'name' => 'Pergi ke slaid', 'desc' => 'Taip nombor slaid, kemudian tekan Enter.'],
                ['keys' => ['Ctrl', 'L'], 'name' => 'Laser Pointer', 'desc' => 'Tukar penuding tetikus kepada penunjuk laser.'],
                ['keys' => ['Ctrl', 'P'], 'name' => 'Pen', 'desc' => 'Tulis atau lukis pada slaid semasa membentang.'],
                ['keys' => ['Esc'], 'name' => 'End Show', 'desc' => 'Keluar daripada Slide Show.'],
            ],
        ],
        [
            'id' => 'master', 'ikon' => 'layout-template', 'tajuk' => 'Slide Master untuk slaid yang seragam', 'jenis' => 'kad',
            'sub' => 'Satu perubahan pada Slide Master mengemas kini setiap slaid yang menggunakannya. Mengubah slaid satu demi satu menghasilkan slaid yang tidak seragam.',
            'item' => [
                ['Buka Slide Master', '<strong>View → Slide Master</strong>. Lakaran kecil yang paling atas ialah induk untuk semua layout. Layout di bawahnya mewakili jenis slaid tertentu.'],
                ['Logo pada setiap slaid', 'Letakkan logo pada induk yang paling atas. Logo muncul pada semua slaid dan tidak terusik semasa anda menyunting slaid biasa.'],
                ['Fon dan warna tema', '<strong>Slide Master → Colors / Fonts</strong>. Semua slaid mengikutnya, termasuk carta dan SmartArt.'],
                ['Layout sendiri', '<strong>Slide Master → Insert Layout</strong>, kemudian <strong>Insert Placeholder</strong>. Sesuai untuk jenis slaid yang berulang, contohnya ringkasan kewangan bulanan.'],
            ],
        ],
        [
            'id' => 'arrange', 'ikon' => 'align-left', 'tajuk' => 'Menyusun objek', 'jenis' => 'jadual',
            'sub' => 'Slaid yang kemas mempunyai objek yang sejajar dan jarak yang sama.',
            'kepala' => ['Tugas', 'Laluan', 'Catatan'],
            'baris' => [
                ['Sejajarkan objek', 'Shape Format → Align → Align Middle / Align Center', 'Pilih dua objek atau lebih dahulu. Untuk gambar, tab ialah Picture Format.'],
                ['Samakan jarak', 'Shape Format → Align → Distribute Horizontally', 'Jarak antara objek terpilih menjadi sama rata. Paling berguna untuk tiga objek atau lebih.'],
                ['Garis panduan', 'View → Guides / Gridlines', 'Smart Guides muncul sendiri apabila objek sejajar dengan objek lain.'],
                ['Pilih objek tertimbus', 'Home → Arrange → Selection Pane', 'Pintasan Alt+F10. Objek boleh dinamakan dan disorok di sini.'],
            ],
        ],
        [
            'id' => 'mistakes', 'ikon' => 'triangle-alert', 'tajuk' => 'Kesilapan biasa', 'jenis' => 'kesilapan',
            'sub' => 'Kesilapan yang paling kerap dalam pembentangan kerja, dan cara membetulkannya.',
            'item' => [
                ['Satu slaid penuh dengan teks', 'Audiens membaca slaid dan berhenti mendengar. <strong>Pembetulan:</strong> satu idea bagi setiap slaid; perincian dalam nota penceramah.'],
                ['Memformat setiap slaid secara manual', 'Slaid tidak seragam dan sukar diubah. <strong>Pembetulan:</strong> ubah format di <strong>View → Slide Master</strong>.'],
                ['Animasi pada setiap objek', 'Melambatkan pembentangan dan mengalih perhatian. <strong>Pembetulan:</strong> gunakan animasi hanya untuk menunjukkan urutan; gunakan Morph untuk peralihan antara slaid.'],
                ['Menampal carta Excel sebagai gambar', 'Angka menjadi lapuk apabila data berubah. <strong>Pembetulan:</strong> <strong>Paste → Use Destination Theme &amp; Link Data</strong>.'],
                ['Menghantar .pptx dengan fon khas', 'Komputer penerima menggantikan fon dan susun atur berubah. <strong>Pembetulan:</strong> <strong>File → Options → Save → Embed fonts in the file</strong>, atau eksport ke PDF.'],
            ],
            'amalan' => [
                'Gunakan kontras tinggi: teks gelap atas latar cerah, atau sebaliknya. Projektor memudarkan warna.',
                'Saiz fon sekurang-kurangnya 24 untuk isi dan 36 untuk tajuk.',
                'Berlatih dengan <strong>Slide Show → Rehearse Timings</strong> untuk menganggar masa.',
                'Jalankan <strong>Review → Check Accessibility</strong> dan tambah Alt Text pada setiap gambar.',
            ],
        ],
    ],
];
