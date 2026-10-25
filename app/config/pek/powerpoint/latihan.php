<?php

declare(strict_types=1);

/**
 * Latihan PowerPoint, BM (suntingan seterusnya di Admin → Kandungan). Versi EN: en/latihan.php (id, mod, tahap, jawapan,
 * correct_index dan susunan pilihan SAMA). Pintasan disahkan dengan halaman Microsoft (23 Okt 2026).
 * Elak pintasan yang dirampas pelayar (Ctrl+N/T/W, Ctrl+1..8).
 */
['pintasan' => $p, 'pilihan' => $c] = require __DIR__ . '/../_soalan.php';

return [
    'pintasan' => [
        'name' => 'Pintasan utama',
        'icon' => 'keyboard',
        'tagline' => 'Pintasan untuk membina dan membentang slaid',
        'description' => 'Tekan kombinasi kekunci yang betul. Di telefon, taip kombinasinya dengan tanda +, contohnya Ctrl+huruf. Meliputi slaid, objek dan Slide Show.',
        'color' => 'purple',
        'questions' => [
            $p('p1', 'Slaid', '🗂️', 1, 'Tekan pintasan untuk <strong>memasukkan slaid baharu</strong>.', ['Ctrl+M'],
                'Kekunci yang sama dengan butang New Slide.', '<strong>Ctrl+M</strong> memasukkan slaid baharu selepas slaid semasa (Home → New Slide).'),
            $p('p2', 'Objek', '📐', 1, 'Tekan pintasan untuk <strong>mengumpulkan (group)</strong> objek yang dipilih.', ['Ctrl+G'],
                'G untuk Group.', '<strong>Ctrl+G</strong> ialah Group. <strong>Ctrl+Shift+G</strong> ialah Ungroup.'),
            $p('p3', 'Objek', '📐', 2, 'Tekan pintasan untuk <strong>meleraikan (ungroup)</strong> kumpulan objek.', ['Ctrl+Shift+G'],
                'Pintasan Group dengan satu kekunci pengubah tambahan.', '<strong>Ctrl+Shift+G</strong> meleraikan kumpulan supaya setiap objek boleh diubah secara berasingan.'),
            $p('p4', 'Objek', '📋', 2, 'Tekan pintasan untuk <strong>membuka Selection Pane</strong> dan memilih objek yang tertimbus.', ['Alt+F10'],
                'Alt dengan satu kekunci fungsi.', '<strong>Alt+F10</strong> membuka Selection Pane (Home → Arrange → Selection Pane).'),
            $p('p5', 'Membentang', '🎤', 1, 'Tekan pintasan untuk <strong>memulakan Slide Show dari slaid pertama</strong>.', ['F5'],
                'Satu kekunci fungsi.', '<strong>F5</strong> bermula dari awal. <strong>Shift+F5</strong> bermula dari slaid semasa.'),
            $p('p6', 'Membentang', '🎤', 2, 'Tekan pintasan untuk <strong>bermula dari slaid yang sedang dibuka</strong>, bukan dari awal.', ['Shift+F5'],
                'Kekunci untuk bermula dari awal, ditambah satu kekunci pengubah.', '<strong>Shift+F5</strong> berguna apabila menyambung pembentangan selepas sesi soal jawab.'),
            $p('p7', 'Membentang', '🖥️', 2, 'Tekan pintasan untuk <strong>bermula dalam Presenter View</strong>, dengan nota di skrin anda sahaja.', ['Alt+F5'],
                'Kali ini bukan Shift.', '<strong>Alt+F5</strong> memulakan Slide Show dalam Presenter View.'),
            $p('p8', 'Membentang', '⬛', 2, 'Semasa membentang, anda mahu audiens memberi tumpuan kepada anda. Tekan kekunci untuk <strong>skrin hitam</strong>.', ['B', '.'],
                'Huruf pertama perkataan "Black".', '<strong>B</strong> (atau titik) memaparkan skrin hitam. Tekan sekali lagi untuk kembali. <strong>W</strong> memaparkan skrin putih.'),
            $p('p9', 'Membentang', '🔴', 3, 'Semasa membentang, tekan pintasan untuk <strong>penunjuk laser</strong>.', ['Ctrl+L'],
                'L untuk Laser.', '<strong>Ctrl+L</strong> menukar penuding tetikus kepada laser. <strong>Ctrl+P</strong> ialah pen dan <strong>Ctrl+E</strong> ialah pemadam.'),
            $p('p10', 'Pautan', '🔗', 1, 'Tekan pintasan untuk <strong>memasukkan pautan</strong> ke slaid lain atau ke laman web.', ['Ctrl+K'],
                'Sama dalam Excel, Word dan Outlook.', '<strong>Ctrl+K</strong> membuka Insert Hyperlink, termasuk pautan ke slaid lain dalam fail yang sama (Place in This Document).'),
        ],
    ],
    'slaid' => [
        'name' => 'Slaid kemas',
        'icon' => 'layout-template',
        'tagline' => 'Slide Master, susunan objek, carta dan membentang',
        'description' => 'Pilih cara yang betul untuk pembentangan kerja yang seragam, kemas dan mudah dikemas kini.',
        'color' => 'amber',
        'questions' => [
            $c('s1', 'Slide Master', '🎨', 1, 'Logo syarikat perlu ada pada <strong>setiap slaid</strong>. Apakah cara yang paling sesuai?',
                ['Salin dan tampal logo ke setiap slaid', 'Letakkan logo pada Slide Master (View → Slide Master)', 'Jadikan logo gambar latar slaid pertama', 'Insert → Header & Footer'], 1,
                'Satu tempat untuk semua slaid.', 'Logo pada <strong>Slide Master</strong> muncul pada semua slaid dan tidak terusik semasa menyunting. Jika disalin ke setiap slaid, 30 slaid bermakna 30 logo yang perlu dikemas kini.'),
            $c('s2', 'Slide Master', '🎨', 2, 'Pengurus anda meminta fon semua tajuk dalam 40 slaid ditukar. Apakah cara yang paling cepat?',
                ['Pilih setiap tajuk satu demi satu', 'Ubah fon tajuk di Slide Master (atau Design → Variants → Fonts)', 'Find and Replace', 'Format Painter pada setiap slaid'], 1,
                'Ubah pada induknya.', 'Fon tajuk yang ditetapkan di <strong>Slide Master</strong> atau fon tema berubah serentak pada semua slaid yang menggunakan layout tersebut.'),
            $c('s3', 'Susunan', '📐', 1, 'Tiga kotak perlu mempunyai <strong>jarak yang sama</strong> antara satu sama lain.',
                ['Seret dan anggar dengan mata', 'Align → Distribute Horizontally', 'Group, kemudian Center', 'Tukar saiz kotak'], 1,
                'Ciri ini ada dalam menu Align.', '<strong>Distribute Horizontally</strong> menyamakan jarak antara objek yang dipilih.'),
            $c('s4', 'Susunan', '📋', 2, 'Satu bentuk tertimbus di bawah gambar dan tidak boleh diklik. Bagaimanakah cara memilihnya?',
                ['Padam gambar dahulu', 'Selection Pane (Alt+F10) → klik nama bentuk itu', 'Ctrl+A', 'Undo berulang kali'], 1,
                'Ada senarai yang memaparkan semua objek.', '<strong>Selection Pane</strong> menyenaraikan setiap objek pada slaid. Anda boleh memilih, menyorok atau menyusun lapisannya.'),
            $c('s5', 'Carta', '📊', 2, 'Carta jualan daripada Excel perlu <strong>sentiasa terkini</strong> apabila data Excel berubah.',
                ['Ambil tangkapan skrin carta', 'Paste → Use Destination Theme & Link Data', 'Paste → Picture', 'Taip semula angka'], 1,
                'Pilihan tampal yang mengandungi perkataan "Link".', '<strong>Link Data</strong> memautkan carta kepada fail Excel. Kemas kini dengan Chart Design → Refresh Data.'),
            $c('s6', 'Animasi', '✨', 2, 'Apakah perbezaan antara <strong>Transition</strong> dan <strong>Animation</strong>?',
                ['Kedua-duanya sama', 'Transition = antara slaid; Animation = objek di dalam slaid', 'Transition = objek; Animation = slaid', 'Transition hanya untuk video'], 1,
                'Kedua-duanya berada dalam tab yang berlainan.', '<strong>Transition</strong> berlaku ketika bertukar slaid. <strong>Animation</strong> menggerakkan objek di dalam satu slaid.'),
            $c('s7', 'Animasi', '✨', 3, 'Anda mahu sebuah kotak bergerak dengan lancar dari kiri slaid 3 ke kanan slaid 4. Ciri manakah yang paling sesuai?',
                ['Animation → Fly In', 'Gandakan slaid, alihkan kotak, kemudian Transitions → Morph', 'Motion Path pada setiap kotak', 'Hyperlink'], 1,
                'Satu jenis transition dalam Microsoft 365.', '<strong>Morph</strong> menganimasikan perbezaan antara dua slaid yang hampir sama. Anda tidak perlu melukis laluan animasi.'),
            $c('s8', 'Membentang', '🖥️', 1, 'Anda mahu melihat <strong>nota penceramah dan slaid seterusnya</strong>, sementara audiens hanya nampak slaid.',
                ['Reading View', 'Presenter View', 'Slide Sorter', 'Outline View'], 1,
                'Slide Show → Use …', '<strong>Presenter View</strong> (Alt+F5) memaparkan nota, pemasa dan slaid seterusnya di skrin anda sahaja.'),
            $c('s9', 'Reka bentuk', '🎨', 1, 'Slaid anda penuh dengan perenggan panjang. Apakah nasihat yang paling sesuai?',
                ['Kecilkan fon supaya muat', 'Satu idea bagi setiap slaid; perincian dalam Speaker Notes', 'Tambah animasi pada setiap baris', 'Gunakan dua lajur teks'], 1,
                'Audiens sepatutnya mendengar, bukan membaca.', 'Pastikan teks pada slaid ringkas. Letakkan perincian dalam <strong>Speaker Notes</strong> untuk rujukan anda.'),
            $c('s10', 'Kongsi', '💾', 2, 'Fail .pptx menggunakan fon khas jenama dan akan dibuka di komputer pelanggan. Apakah langkah yang betul?',
                ['Tiada langkah diperlukan kerana fon ikut fail', 'File → Options → Save → Embed fonts in the file (atau hantar PDF)', 'Tukar Slide Size kepada 4:3', 'Compress Pictures'], 1,
                'Fon perlu dibawa bersama fail.', 'Tanpa <strong>Embed fonts</strong>, komputer penerima menggantikan fon dan susun atur boleh berubah. PDF pula mengunci rupa slaid.'),
        ],
    ],
];
