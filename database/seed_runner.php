<?php
// database/seed_runner.php

function seedMasterData(PDO $pdo, string $driver = 'mysql') {
    $data = [
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Teknik Jaringan Komputer dan Telekomunikasi',
            'mapel_default' => ["Konsentrasi Keahlian TKJ", "Dasar-dasar Teknik Jaringan Komputer dan Telekomunikasi", "Perencanaan dan Pengalamatan Jaringan", "Pemasangan dan Konfigurasi Perangkat Jaringan", "Administrasi Server Jaringan", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Teknik Komputer dan Jaringan", "Teknik Transmisi Telekomunikasi", "Rekayasa Perangkat Keras dan Jaringan"],
            'elemen_list' => [
                "Teknik Komputer dan Jaringan" => [
                    "Perencanaan dan Pengalamatan Jaringan",
                    "Pemasangan dan Konfigurasi Perangkat Jaringan",
                    "Administrasi Server Jaringan",
                    "Sistem Keamanan Jaringan",
                    "Konfigurasi Perangkat Telekomunikasi"
                ],
                "Teknik Transmisi Telekomunikasi" => [
                    "Sistem Transmisi Radio dan Optik",
                    "Penyambungan dan Pengukuran Fiber Optik",
                    "Sistem Antena dan Propagasi Gelombang",
                    "Sistem Komunikasi Seluler"
                ],
                "Rekayasa Perangkat Keras dan Jaringan" => [
                    "Perakitan dan Pemeliharaan Hardware Komputer",
                    "Troubleshooting Perangkat Keras dan Firmware",
                    "Integrasi Sistem Jaringan dan I/O Periferal"
                ]
            ],
            'produk_list' => ["Kabel Jaringan UTP/STP Siap Pakai (Crimping & Testing Terkalibrasi)", "Router Mikrotik / Access Point Pre-configured", "Unit Server Hotspot / Mini PC Router Rumahan"],
            'jasa_list' => ["Jasa Instalasi & Maintenance Jaringan LAN/WiFi Kantor/Sekolah", "Jasa Pemasangan & Konfigurasi CCTV Online", "Jasa Perakitan, Maintenance & Servis PC/Laptop", "Jasa Setup Hotspot Berbasis Voucher UMKM", "Jasa Terminasi & Splicing Kabel Fiber Optic"],
            'klien_list' => ["Pihak Tata Usaha Sekolah & Lab Komputer", "Kantor Desa / Kelurahan", "Kafe dan Warung Kopi Sekitar Sekolah", "Toko Ritel & Rumah Tinggal Masyarakat Sekitar", "Pelaku Usaha Warnet / Agen Pulsa"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Desain Komunikasi Visual',
            'mapel_default' => ["Konsentrasi Keahlian DKV", "Dasar-dasar Desain Komunikasi Visual", "Perangkat Lunak Desain Grafis", "Fotografi dan Videografi", "Desain Publikasi & Kemasan", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Desain Komunikasi Visual", "Animasi", "Teknik Grafika"],
            'elemen_list' => [
                "Desain Komunikasi Visual" => [
                    "Prinsip Dasar Desain dan Komunikasi",
                    "Perangkat Lunak Desain Grafis",
                    "Menerapkan Design Brief",
                    "Karya Desain Berbasis Vektor dan Bitmap",
                    "Proses Produksi Desain & Pre-press"
                ],
                "Animasi" => [
                    "Prinsip Dasar Animasi (12 Principles)",
                    "Perancangan Karakter & Storyboard",
                    "Produksi Animasi 2D dan 3D",
                    "Audio, Compositing, & Pascaproduksi"
                ],
                "Teknik Grafika" => [
                    "Persiapan Acuan Cetak Digital & Konvensional",
                    "Operasional Mesin Cetak Offset & Digital",
                    "Penyelesaian Grafika / Post-press & Finishing"
                ]
            ],
            'produk_list' => ["Infografis dan Materi Presentasi Interaktif", "Desain Identitas Merek (Logo, Brand Guidelines, Brosur)", "Desain Kemasan Produk UMKM (Packaging Box & Label Botol)", "Kaos Sablon Custom & Totebag Merchandise", "Company Profile Cetak & Digital", "Spanduk, Banner, & Media Promosi Luar Ruang"],
            'jasa_list' => ["Jasa Desain Konten Media Sosial (Instagram/TikTok Carousel)", "Jasa Fotografi Produk Katalog UMKM", "Jasa Pembuatan Video Iklan Pendek / Promosi Sekolah", "Jasa Percetakan Kartu Nama, Sertifikat, dan ID Card Acara"],
            'klien_list' => ["UMKM Kuliner & Kerajinan Binaan Sekolah", "Panitia Event / OSIS / Komite Sekolah", "Dinas Pemerintah Daerah & Organisasi Kemasyarakatan", "Pelaku Usaha Startup Lokal", "Pengelola Wisata Daerah"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Manajemen Perkantoran dan Layanan Bisnis',
            'mapel_default' => ["Konsentrasi Keahlian Manajemen Perkantoran", "Dasar-dasar Manajemen Perkantoran", "Pengelolaan Administrasi Umum", "Komunikasi dan Humas Kantor", "Kearsipan Digital", "Teknologi Perkantoran & Otomatisasi"],
            'konsentrasi_list' => ["Manajemen Perkantoran", "Otomatisasi dan Tata Kelola Perkantoran"],
            'elemen_list' => [
                "Manajemen Perkantoran" => [
                    "Pengelolaan Administrasi Umum & Dokumen Niaga",
                    "Komunikasi Tempat Kerja & Hubungan Masyarakat",
                    "Pengelolaan Kearsipan Digital (E-Filing Cloud)",
                    "Teknologi Perkantoran & Otomatisasi Tata Kelola",
                    "Pengelolaan Rapat / Pertemuan Bisnis"
                ],
                "Otomatisasi dan Tata Kelola Perkantoran" => [
                    "Otomatisasi Administrasi Kepegawaian & Sarpras",
                    "Tata Kelola Keuangan Kas Kecil Perkantoran",
                    "Protokoler dan Layanan Pelanggan (Front Desk)"
                ]
            ],
            'produk_list' => ["Buku Pedoman Standar Operasional Prosedur (SOP) Kantor Digital", "Modul Digital Panduan Tata Naskah Dinas", "Template Berkas Formulir Administrasi Standar"],
            'jasa_list' => ["Jasa Digitalisasi & E-Filing Kearsipan Fisik ke Cloud", "Jasa Event Organizer (EO) Seminar, Workshop, & Rapat Dinas", "Jasa Notulensi Rapat Profesional & Transkripsi Audio", "Jasa Layanan Front Desk / Resepsionis Acara", "Jasa Entri Data & Pengetikan Dokumen Hukum/Akademik"],
            'klien_list' => ["Kantor Notaris & PPAT Rekanan", "Kantor Urusan Agama (KUA) & Kantor Camat", "Puskesmas / Fasilitas Layanan Kesehatan Pratama", "Asosiasi Komite Sekolah & Dewan Pendidikan Daerah"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Pemasaran',
            'mapel_default' => ["Konsentrasi Keahlian Bisnis Digital", "Dasar-dasar Pemasaran", "Pemasaran Digital (Digital Marketing)", "Perencanaan Bisnis", "Komunikasi Bisnis", "Pengelolaan Toko Online / E-Commerce"],
            'konsentrasi_list' => ["Bisnis Digital", "Pengelolaan Toko Ritel", "Pemasaran Daring"],
            'elemen_list' => [
                "Bisnis Digital" => [
                    "Pemasaran Digital (Digital Marketing & Copywriting)",
                    "Perencanaan Bisnis & Analisis Tren Pasar",
                    "Komunikasi Bisnis & Negosiasi Klien",
                    "Pengelolaan Toko Daring (Marketplace & E-Commerce)",
                    "Live Streaming Commerce & Social Media Ads"
                ],
                "Pengelolaan Toko Ritel" => [
                    "Penataan Produk (Visual Merchandising)",
                    "Operasional Toko & Point of Sales (POS)",
                    "Pengendalian Persediaan Barang Dagangan (Stock Opname)"
                ],
                "Pemasaran Daring" => [
                    "Optimasi Mesin Pencari (SEO) & Riset Kata Kunci",
                    "Pengelolaan Konten Media Sosial Interaktif",
                    "Pemasaran Berbasis Komunitas Online"
                ]
            ],
            'produk_list' => ["Katalog Digital Produk Interaktif (Flipbook / PDF)", "Website Toko Online Sederhana (Linktree/Landing Page)", "Paket Konten Iklan Digital (Copywriting + Visual Iklan)"],
            'jasa_list' => ["Jasa Pengelolaan Akun Media Sosial Bisnis (Social Media Management)", "Jasa Pembuatan dan Optimalisasi Profil Toko di Marketplace (Shopee/Tokopedia/TikTok Shop)", "Jasa Iklan Berbayar Meta Ads & TikTok Ads untuk UMKM", "Jasa Live Streaming Host Penjualan Produk"],
            'klien_list' => ["Pelaku UMKM Binaan Kadin / Pemda", "Unit Produksi TEFA Jurusan Lain di Sekolah", "Toko Pakaian & Ritel Lokal", "Distributor Bahan Pokok / Sembako"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Akuntansi dan Keuangan Lembaga',
            'mapel_default' => ["Konsentrasi Keahlian Akuntansi", "Dasar-dasar Akuntansi dan Keuangan Lembaga", "Praktik Akuntansi Perusahaan Jasa, Dagang, dan Manufaktur", "Komputer Akuntansi (MYOB / Accurate / Spreadsheet)", "Administrasi Perpajakan"],
            'konsentrasi_list' => ["Akuntansi", "Akuntansi Sektor Publik"],
            'elemen_list' => [
                "Akuntansi" => [
                    "Pengelolaan Kas, Bank, dan Piutang Dagang",
                    "Praktik Akuntansi Perusahaan Jasa, Dagang, dan Manufaktur",
                    "Komputer Akuntansi (Spreadsheet & Software Akuntansi)",
                    "Administrasi Pajak PPh dan PPN",
                    "Penyusunan Laporan Keuangan Standar SAK EMKM"
                ],
                "Akuntansi Sektor Publik" => [
                    "Akuntansi Keuangan Desa & Pemerintah Daerah",
                    "Pengelolaan Anggaran Belanja Satuan Kerja (RKA/DPA)",
                    "Audit Internal dan Pertanggungjawaban Keuangan Publik"
                ]
            ],
            'produk_list' => ["Buku Kas Keuangan Sederhana Cetak & Digital", "Aplikasi Kas Berbasis Spreadsheet (Excel/Google Sheets) Siap Pakai", "Laporan Keuangan Neraca & Laba Rugi Standar SAK EMKM", "Draf Formulir Rekonsiliasi Bank & Faktur Pajak"],
            'jasa_list' => ["Jasa Pembukuan Transaksi Keuangan Harian UMKM", "Jasa Pendampingan Pengisian SPT Tahunan Wajib Pajak Orang Pribadi", "Jasa Stock Opname Fisik Persediaan Barang Dagang", "Jasa Audit Internal Kas Kecil Koperasi Sekolah"],
            'klien_list' => ["Koperasi Karyawan & Koperasi Siswa Sekolah", "Pedagang Pasar & Pemilik Toko Kelontong", "Unit Usaha Kantin Sekolah", "Yayasan Sosial & Organisasi Keagamaan"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Layanan Perbankan',
            'mapel_default' => ["Konsentrasi Keahlian Layanan Perbankan", "Dasar-dasar Perbankan", "Pengelolaan Kas Bank (Teller & Customer Service)", "Layanan Kliring & Transaksi Valuta Asing", "Akuntansi Perbankan Syariah dan Konvensional"],
            'konsentrasi_list' => ["Layanan Perbankan", "Layanan Perbankan Syariah"],
            'elemen_list' => [
                "Layanan Perbankan" => [
                    "Layanan Front Office Bank (Teller & Customer Service)",
                    "Pengelolaan Transaksi Kas, Kliring, dan Transfer",
                    "Pemasaran Produk Dana dan Jasa Bank Mini",
                    "Akuntansi Transaksi Perbankan"
                ],
                "Layanan Perbankan Syariah" => [
                    "Akad-akad Transaksi Syariah (Mudharabah, Musyarakah, Murabahah)",
                    "Operasional Penghimpunan & Penyaluran Dana Syariah",
                    "Layanan Customer Service & Kasir Bank Syariah"
                ]
            ],
            'produk_list' => ["Buku Tabungan Siswa / Mini Bank Sekolah", "Formulir Aplikasi Pembukaan Rekening & Slip Setoran/Tarikan", "Modul Panduan Literasi & Inklusi Keuangan Pelajar"],
            'jasa_list' => ["Jasa Layanan Loket Bank Mini Sekolah (Setoran Tabungan, Pembayaran Iuran/SPP)", "Jasa Agen Pembayaran PPOB (Listrik, Pulsa, PDAM) di Bank Mini", "Jasa Konsultasi Edukasi Tabungan Rencana Siswa", "Jasa Penukaran Uang Pecahan Kecil untuk Acara Bazar Sekolah"],
            'klien_list' => ["Seluruh Siswa, Guru, dan Karyawan Sekolah", "Orang Tua / Wali Murid", "Warga Masyarakat Sekitar Lingkungan Sekolah", "Unit Bisnis & Kantin Sekolah"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Teknik Otomotif',
            'mapel_default' => ["Konsentrasi Keahlian Teknik Sepeda Motor", "Dasar-dasar Teknik Otomotif", "Pemeliharaan Mesin Sepeda Motor", "Pemeliharaan Sasis dan Suspensi Sepeda Motor", "Pemeliharaan Kelistrikan Sepeda Motor", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Teknik Sepeda Motor", "Teknik Kendaraan Ringan", "Teknik Bodi Kendaraan Ringan"],
            'elemen_list' => [
                "Teknik Sepeda Motor" => [
                    "Proses Pelayanan dan Manajemen Bengkel Sepeda Motor",
                    "Perawatan dan Perbaikan Mesin Sepeda Motor (Injeksi & Karburator)",
                    "Perawatan dan Perbaikan Sasis dan Suspensi Sepeda Motor",
                    "Perawatan dan Perbaikan Sistem Pemindah Tenaga (CVT / Rantai)",
                    "Perawatan dan Perbaikan Sistem Kelistrikan Sepeda Motor"
                ],
                "Teknik Kendaraan Ringan" => [
                    "Perawatan dan Perbaikan Mesin Kendaraan Ringan (Engine Tune-up)",
                    "Perawatan dan Perbaikan Sasis dan Sistem Kemudi",
                    "Kelistrikan Bodi dan Sistem EFI Kendaraan Ringan"
                ],
                "Teknik Bodi Kendaraan Ringan" => [
                    "Perbaikan Panel Bodi & Ketok Las",
                    "Pengecatan Bodi, Color Matching, & Detailing",
                    "Pemasangan Kaca dan Aksesori Bodi"
                ]
            ],
            'produk_list' => ["Cairan Pembersih Throttle Body & Injector Cleaner", "Gantungan Kunci & Aksesoris Modifikasi Touring", "Kampas Rem & Busi Siap Pasang Standar Pabrikan OEM"],
            'jasa_list' => ["Jasa Servis Ringan / Tune Up Sepeda Motor Matik & Manual", "Jasa Ganti Oli Mesin & Gardan Express", "Jasa Pembersihan Injektor & Kalibrasi ECU Sederhana", "Jasa Servis CVT, Ganti Roller, & V-Belt", "Jasa Cuci Motor Salju & Detailing Mengkilap"],
            'klien_list' => ["Sepeda Motor Guru, Karyawan, dan Siswa Sekolah", "Masyarakat Umum Pemilik Motor di Lingkungan Sekitar Sekolah", "Driver Ojek Online (Gojek / Grab / Maxim)", "Komunitas Pengendara Sepeda Motor Lokal"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Perhotelan',
            'mapel_default' => ["Konsentrasi Keahlian Perhotelan", "Dasar-dasar Perhotelan", "Front Office (Kantor Depan)", "Housekeeping (Tata Graha)", "Laundry & Dry Cleaning", "Food and Beverage Service (Restoran & Bar)"],
            'konsentrasi_list' => ["Perhotelan", "Akomodasi Perhotelan"],
            'elemen_list' => [
                "Perhotelan" => [
                    "Front Office (Penerimaan Tamu, Reservasi, & Check-in/out)",
                    "Housekeeping (Pembersihan Kamar, Public Area, & Linen)",
                    "Laundry dan Dry Cleaning Pakaian Tamu & Hotel",
                    "Food and Beverage Service (Restoran, Room Service, & Banquet)",
                    "Higiene, Sanitasi, dan Keselamatan Kerja di Hotel"
                ],
                "Akomodasi Perhotelan" => [
                    "Operasional Reservasi & Telepon Kantor Depan",
                    "Penyiapan Kamar Tamu Standar Industri Bintang",
                    "Pelayanan Butler dan Tamu VIP"
                ]
            ],
            'produk_list' => ["Paket Amenitas Kamar Ramah Lingkungan (Sabun Alami, Slippers, Dental Kit)", "Linen & Towel Standar Hotel Bersih Higienis", "Voucher Paket Menginap Edotel (Hotel Edukasi SMK)"],
            'jasa_list' => ["Jasa Reservasi & Akomodasi Kamar Tamu (Edotel)", "Jasa Laundry Kiloan & Dry Cleaning Pakaian Resmi", "Jasa Layanan Table Setting & Banquet Acara Formal", "Jasa Pembersihan Ruangan / Office Cleaning Service Harian"],
            'klien_list' => ["Tamu Dinas / Narasumber Pelatihan di Lingkungan Sekolah", "Orang Tua Siswa Saat Acara Wisuda / Rapat Komite", "Instansi Pemerintah yang Menggelar Konsinyering", "Wisatawan Lokal yang Membutuhkan Penginapan Ekonomis"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Layanan Penunjang Kefarmasian Klinis dan Komunitas',
            'mapel_default' => ["Konsentrasi Keahlian Farmasi Klinis dan Komunitas", "Dasar-dasar Farmasi", "Pelayanan Farmasi & Peracikan Obat", "Farmakologi & Terminologi Medis", "Kimia Farmasi", "Manajemen Pengelolaan Perbekalan Farmasi di Apotek"],
            'konsentrasi_list' => ["Farmasi Klinis dan Komunitas", "Farmasi Industri"],
            'elemen_list' => [
                "Farmasi Klinis dan Komunitas" => [
                    "Pelayanan Farmasi, Pembacaan Resep, & Peracikan Obat",
                    "Farmakologi Dasar dan Pengenalan Spesialite Obat",
                    "Manajemen Pengelolaan Perbekalan Farmasi di Apotek",
                    "Komunikasi, Informasi, dan Edukasi (KIE) Obat kepada Pasien",
                    "Peracikan Sediaan Obat Tradisional dan Herbal"
                ],
                "Farmasi Industri" => [
                    "Formulasi Sediaan Padat, Semi Padat, dan Cair",
                    "Pengujian Mutu Laboratorium Farmasi (Quality Assurance)",
                    "Pengemasan Primer dan Sekunder Sediaan Farmasi"
                ]
            ],
            'produk_list' => ["Hand Sanitizer Herbal Beraroma Alami", "Minyak Aromaterapi / Roll-on Pelega Otot Herbal", "Minuman Herbal Siap Seduh (Ekstrak Jahe Merah, Kunyit Asam)", "Salep / Balsem Herbal Pelega Nafas", "Sabun Cuci Tangan Antiseptik Cair"],
            'jasa_list' => ["Jasa Simulasi Pelayanan Resep & Informasi Obat (KIE Apotek Mini)", "Jasa Pengecekan Kesehatan Sederhana (Tensi Darah, Gula Darah Sewaktu, Asam Urat)", "Jasa Pengemasan & Pelabelan Ulang Bahan Baku Herbal Higienis", "Jasa Edukasi DAGUSIBU (Dapatkan, Gunakan, Simpan, Buang Obat) untuk Warga"],
            'klien_list' => ["Warga Sekolah (Siswa, Pendidik, Tenaga Kependidikan)", "Apotek Rekanan & Toko Obat Berizin", "Warga Lansia di Lingkungan Sekitar Sekolah", "Posyandu & Komunitas Senam Lansia Binaan Kelurahan"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Pengembangan Perangkat Lunak dan Gim',
            'mapel_default' => ["Konsentrasi Keahlian Rekayasa Perangkat Lunak", "Dasar-dasar Pengembangan Perangkat Lunak dan Gim", "Pemrograman Web", "Pemrograman Perangkat Bergerak (Mobile)", "Pemrograman Berorientasi Objek", "Basis Data", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Rekayasa Perangkat Lunak", "Pengembangan Gim", "Sistem Informasi Jaringan dan Aplikasi"],
            'elemen_list' => [
                "Rekayasa Perangkat Lunak" => [
                    "Pemrograman Web (Frontend & Backend)",
                    "Pemrograman Perangkat Bergerak (Android/iOS)",
                    "Pemrograman Berorientasi Objek (PBO)",
                    "Pengelolaan Basis Data Relasional & NoSQL",
                    "Software Quality Assurance (SQA) & Testing"
                ],
                "Pengembangan Gim" => [
                    "Game Design Concept & Storyboarding",
                    "Game Mechanics & Level Design",
                    "Game Engine Programming (Unity/Godot)",
                    "Audio, Asset 2D/3D, dan UI/UX Gim"
                ],
                "Sistem Informasi Jaringan dan Aplikasi" => [
                    "Cloud Computing & Backend Deployment",
                    "Integrasi API & Microservices",
                    "Keamanan Sistem Informasi & Enkripsi"
                ]
            ],
            'produk_list' => ["Website Profil Bisnis & Landing Page Interaktif", "Aplikasi Kasir (Point of Sales) Berbasis Web/Mobile", "Sistem Informasi Manajemen Sekolah / Presensi Siswa", "Game Edukasi 2D Pembelajaran Anak", "Aplikasi Katalog E-Commerce Terintegrasi WhatsApp API"],
            'jasa_list' => ["Jasa Pembuatan dan Redesain Website Responsif", "Jasa Perawatan, Pembaruan & Debugging Aplikasi", "Jasa Integrasi Payment Gateway & Webhook", "Jasa UI/UX Design Prototyping (Figma)"],
            'klien_list' => ["Pelaku UMKM Lokal yang Membutuhkan Digitalisasi Usaha", "Sekolah dan Lembaga Kursus Pelatihan", "Startup Digital dan Agensi Kreatif", "Koperasi dan Unit Toko Ritel Sekolah"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Broadcasting dan Perfilman',
            'mapel_default' => ["Konsentrasi Keahlian Produksi dan Siaran Program Televisi", "Dasar-dasar Broadcasting dan Perfilman", "Tata Kamera dan Tata Cahaya", "Tata Suara dan Rekaman Audio", "Penyuntingan Video (Video Editing)", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Produksi dan Siaran Program Televisi", "Produksi Film"],
            'elemen_list' => [
                "Produksi dan Siaran Program Televisi" => [
                    "Manajemen Produksi Program Siaran Televisi",
                    "Tata Kamera, Pencahayaan, dan Studio Live",
                    "Tata Suara & Mixing Audio Lapangan",
                    "Penyuntingan Video & Visual Effects (VFX)",
                    "Penyiaran Langsung (Live Streaming Multi-Camera)"
                ],
                "Produksi Film" => [
                    "Penulisan Naskah dan Skenario Film",
                    "Tata Artistik dan Kostum Produksi Film",
                    "Penyutradaraan dan Pengarahan Aktor",
                    "Sinematografi dan Color Grading"
                ]
            ],
            'produk_list' => ["Video Profil Perusahaan (Company Profile HD)", "Video Iklan Komersial Produk UMKM (TVC/Reels)", "Film Pendek Edukasi & Dokumenter Komunitas", "Program Talkshow Podcast Video Siap Siar", "Liputan Acara Resmi & Highlight Video"],
            'jasa_list' => ["Jasa Operator Live Streaming Multi-Kamera Acara/Seminar", "Jasa Video Dokumentasi Wisuda & Resepsi", "Jasa Color Grading & Editing Video Profesional", "Jasa Voice Over & Sound Mixing Podcast"],
            'klien_list' => ["Instansi Dinas Pemerintah Daerah", "Pihak Humas Sekolah & Perguruan Tinggi", "Event Organizer (EO) & Panitia Acara Pernikahan/Formal", "Pelaku Usaha Wisata & UMKM Lokal"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Teknik Mesin',
            'mapel_default' => ["Konsentrasi Keahlian Teknik Pemesinan", "Dasar-dasar Teknik Mesin", "Gambar Teknik Manufaktur (CAD 2D/3D)", "Teknik Pemesinan Bubut", "Teknik Pemesinan Frais", "Teknik Pemesinan NC/CNC dan CAM", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Teknik Pemesinan", "Desain Gambar Mesin", "Teknik Mekanik Industri"],
            'elemen_list' => [
                "Teknik Pemesinan" => [
                    "Gambar Teknik Manufaktur Berbantuan CAD",
                    "Pemesinan Bubut Konvensional (Turning)",
                    "Pemesinan Frais Konvensional (Milling)",
                    "Pemesinan NC/CNC (Bubut & Milling CNC) serta CAM",
                    "Pengukuran Presisi & Metrologi Industri"
                ],
                "Desain Gambar Mesin" => [
                    "Pemodelan 3D Solid & Assembly CAD",
                    "Perancangan Alat Bantu Produksi (Jig & Fixture)",
                    "Penyusunan Bill of Materials & Gambar Kerja Toleransi"
                ],
                "Teknik Mekanik Industri" => [
                    "Pemeliharaan Mesin Produksi & Sistem Hidrolik/Pneumatik",
                    "Perakitan Komponen Mekanikal Presisi",
                    "Troubleshooting Mesin Industri Pabrik"
                ]
            ],
            'produk_list' => ["Komponen Baut/Mur Khusus & Bushing Poros Presisi", "Peralatan Tepat Guna Pertanian/Perkebunan Sederhana", "Sparepart Logam Pengganti Mesin Industri", "Bracket, Flens, dan Adaptor Logam Mesin", "Modul Meja Kerja Praktik Fabrikasi"],
            'jasa_list' => ["Jasa Bubut Logam & Pembuatan Poros Bertingkat/Ulir", "Jasa Milling / Frais Roda Gigi & Alur Pasak", "Jasa Pemrograman & Eksekusi Milling CNC Presisi", "Jasa Pengasahan Pisau Potong Industri & Reamer"],
            'klien_list' => ["Bengkel Rekayasa Mesin & Bubut Swasta", "Pabrik Industri Pengolahan Makanan & Kemasan", "Bengkel Otomotif Rekanan Modifikasi", "Kelompok Tani Pengguna Mesin Perontok/Penggiling"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Teknik Pengelasan dan Fabrikasi Logam',
            'mapel_default' => ["Konsentrasi Keahlian Teknik Pengelasan", "Dasar-dasar Teknik Pengelasan", "Teknik Pengelasan SMAW", "Teknik Pengelasan GMAW/MIG", "Teknik Pengelasan GTAW/TIG", "Fabrikasi Logam", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Teknik Pengelasan", "Teknik Pengelasan Kapal"],
            'elemen_list' => [
                "Teknik Pengelasan" => [
                    "Teknik Pengelasan Busur Manual (SMAW 1G-3G/4G)",
                    "Teknik Pengelasan Gas Metal / MIG (GMAW)",
                    "Teknik Pengelasan Gas Tungsten / Argon (GTAW/TIG)",
                    "Fabrikasi Konstruksi Logam & Perakitan Rangka",
                    "Pemeriksaan Hasil Las Visual & Non-Destructive Testing (NDT)"
                ],
                "Teknik Pengelasan Kapal" => [
                    "Pengelasan Konstruksi Lambung & Sekat Kapal",
                    "Pemotongan Termal Plat Tebal (Oxy-Fuel & Plasma Cutting)",
                    "Standardisasi Mutu Pengelasan Marine BKI"
                ]
            ],
            'produk_list' => ["Pagar Minimalis, Pintu Gerbang, & Kanopi Baja Ringan/Hollow", "Rangka Meja/Kursi Belajar Sekolah Bahan Besi", "Teralis Jendela & Railing Tangga Custom", "Troli Pengangkut Barang Serbaguna Bengkel", "Rak Besi Heavy Duty untuk Gudang"],
            'jasa_list' => ["Jasa Pengelasan Sambungan Pipa & Konstruksi Besi", "Jasa Fabrikasi Rangka Kanopi & Baja Ringan", "Jasa Pemotongan Plat Presisi Plasma Cutting", "Jasa Reparasi Patah Las Alat Berat & Truk"],
            'klien_list' => ["Warga Perumahan & Pemilik Rumah Tinggal Sekitar", "Pengembang Properti / Kontraktor Bangunan", "Sekolah Rekanan Pemesan Meja/Kursi Siswa", "Bengkel Karoseri & Truk Angkutan"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Teknik Ketenagalistrikan',
            'mapel_default' => ["Konsentrasi Keahlian Teknik Instalasi Tenaga Listrik", "Dasar-dasar Teknik Ketenagalistrikan", "Instalasi Penerangan Listrik", "Instalasi Tenaga Listrik", "Instalasi Motor Listrik & Kendali PLC", "Perbaikan Peralatan Rumah Tangga", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Teknik Instalasi Tenaga Listrik", "Teknik Pendingin dan Tata Udara"],
            'elemen_list' => [
                "Teknik Instalasi Tenaga Listrik" => [
                    "Standar PUIL & Keselamatan Ketenagalistrikan",
                    "Perancangan & Pemasangan Instalasi Penerangan Bangunan",
                    "Pemasangan & Pengujian Instalasi Tenaga 3 Fasa",
                    "Pengoperasian & Pemrograman Pengendali Motor Listrik (PLC & Smart Relay)",
                    "Perakitan Panel Distribusi Daya Listrik (LVMDP)"
                ],
                "Teknik Pendingin dan Tata Udara" => [
                    "Prinsip Termodinamika & Siklus Refrigerasi",
                    "Instalasi & Pemipaan AC Split Domestik dan Komersial",
                    "Perawatan, Pembersihan (*Cuci AC*), & Pengisian Refrigeran",
                    "Troubleshooting Kelistrikan Sistem Pendingin"
                ]
            ],
            'produk_list' => ["Panel Kontrol Pompa Air Otomatis Berbasis Sensor", "Panel Distribusi Daya Listrik 1 Fasa dan 3 Fasa Rumah/Toko", "Modul Trainer Kelistrikan & PLC Praktik Siswa", "Kabel Ekstensi Roll Heavy Duty dengan Pengaman ELCB"],
            'jasa_list' => ["Jasa Pemasangan & Peremajaan Instalasi Listrik Rumah Tinggal", "Jasa Perawatan Berkala & Cuci AC Split Rumah/Kantor", "Jasa Pengecekan Kebocoran Arus & Grounding Listrik", "Jasa Pemasangan Lampu Penerangan Taman & Jalan Tenaga Surya"],
            'klien_list' => ["Warga Masyarakat Pemilik Rumah Tinggal", "Pengelola Gedung Perkantoran & Sekolah", "Toko Ritel & Rumah Ibadah Sekitar", "Klinik Medis & Usaha Kos-kosan"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Teknik Elektronika',
            'mapel_default' => ["Konsentrasi Keahlian Teknik Audio Video", "Dasar-dasar Teknik Elektronika", "Penerapan Rangkaian Elektronika", "Sistem Audio Analog & Digital", "Sistem Televisi & Display", "Mikrokontroler & IoT", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Teknik Audio Video", "Teknik Elektronika Industri", "Teknik Mekatronika"],
            'elemen_list' => [
                "Teknik Audio Video" => [
                    "Pemrograman Mikrokontroler & Sistem Tertanam (IoT)",
                    "Perawatan dan Perbaikan Perangkat Audio Hi-Fi & Sound System",
                    "Perawatan dan Perbaikan Display Televisi LED/LCD",
                    "Instalasi Sistem CCTV & Keamanan Elektronik"
                ],
                "Teknik Elektronika Industri" => [
                    "Sensor, Tranduser, & Instrumentasi Industri",
                    "Sistem Kontrol Otomasi Berbasis PLC & SCADA",
                    "Pneumatik & Hidrolik Elektronik Industri"
                ],
                "Teknik Mekatronika" => [
                    "Mekanika Presisi & Aktuator Elektrik",
                    "Integrasi Robotika Industri & Lengan Robot",
                    "Pemrograman Kontrol Logika Mekatronika"
                ]
            ],
            'produk_list' => ["Modul Running Text LED Display Informasi Sekolah/Masjid", "Power Amplifier Audio Stereo Custom Rakitan", "Sistem Smart Home Saklar Lampu IoT Terkoneksi Smartphone", "Bel Sekolah Otomatis Berbasis Nada MP3 & Jadwal Digital"],
            'jasa_list' => ["Jasa Pemasangan dan Setting Sound System Lapangan/Auditorium", "Jasa Servis TV LED, Speaker Aktif, & Audio Mobil", "Jasa Pemasangan Instalasi Kamera CCTV Pantau Jarak Jauh", "Jasa Pembuatan PCB Custom dan Perakitan Komponen SMD"],
            'klien_list' => ["Pengurus Masjid & Rumah Ibadah Sekitar", "Masyarakat Umum Pemilik Perangkat Elektronik Rusak", "Sekolah dan Lembaga Instansi Terdekat", "Komunitas Sound System Acara Lokal"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Kuliner',
            'mapel_default' => ["Konsentrasi Keahlian Kuliner", "Dasar-dasar Kuliner", "Pengolahan Makanan dan Minuman Nusantara", "Pengolahan Makanan dan Minuman Kontinental", "Pastry dan Bakery", "Pelayanan Makan dan Minum (Food & Beverage Service)", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Kuliner", "Pastry dan Bakery"],
            'elemen_list' => [
                "Kuliner" => [
                    "Higiene, Sanitasi, dan Keamanan Pangan (HACCP Mini)",
                    "Pengolahan Makanan dan Minuman Tradisional Nusantara",
                    "Pengolahan Makanan dan Minuman Kontinental (Western)",
                    "Pelayanan Makanan dan Minuman (*Food and Beverage Service*)",
                    "Pengelolaan Usaha Jasa Boga (Catering & Banqueting)"
                ],
                "Pastry dan Bakery" => [
                    "Pembuatan Produk Roti Manis & Roti Tawar (*Yeast Products*)",
                    "Pembuatan Produk Kue Tradisional Indonesia",
                    "Pembuatan Cake, Tart, Pastry, & Cookies Modern",
                    "Dekorasi Kue & Seni Cokelat (*Cake Decoration & Chocolate Art*)"
                ]
            ],
            'produk_list' => ["Paket Bento Box Nasi Kuning / Liwet Komplit untuk Rapat", "Aneka Roti Manis Fresh Baked Aneka Topping", "Kue Kering Lebaran / Natal Kemasan Toples Premium", "Kue Ulang Tahun Tart Karakter & Custom Desain", "Minuman Herbal Sehat & Kopi Susu Kekinian Botolan"],
            'jasa_list' => ["Jasa Katering Prasmanan Seminar, Workshop, & Resepsi", "Jasa Penyediaan Snack Box Acara Kantor & Pengajian", "Jasa Coffee Break & Table Setup Acara Formal", "Jasa Kursus Singkat Baking / Memasak untuk Umum"],
            'klien_list' => ["Panitia Acara / Satuan Kerja Instansi Pemerintah Daerah", "Pihak Guru, Karyawan, dan Komite Sekolah", "Warga Perumahan Pemesan Katering Syukuran/Ulang Tahun", "Organisasi Kemasyarakatan & Dharma Wanita"]
        ],
        [
            'kategori' => 'kejuruan',
            'program_keahlian' => 'Busana',
            'mapel_default' => ["Konsentrasi Keahlian Desain dan Produksi Busana", "Dasar-dasar Busana", "Desain Busana (Fashion Illustration)", "Pembuatan Pola (Pattern Making)", "Pembuatan Busana Custom Made", "Pembuatan Busana Industri (Garmen/Konveksi)", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Desain dan Produksi Busana"],
            'elemen_list' => [
                "Desain dan Produksi Busana" => [
                    "Gambar Mode & Desain Busana (Fashion Sketching)",
                    "Pembuatan Pola Datar (Flat Pattern) dan Draping",
                    "Teknik Penjahitan Busana (*Sewing & Assembling*)",
                    "Penyelesaian Akhir (*Finishing & Quality Control*)",
                    "Pengelolaan Produksi Busana Massal (Konveksi/Garmen)"
                ]
            ],
            'produk_list' => ["Baju Seragam Kerja / Batik Dinas Kantor", "Wearpack Praktik Bengkel Siswa SMK", "Busana Pesta / Gamis / Kebaya Modern Custom Made", "Tote Bag & Pouch Kanvas Sablon Estetis", "Mukena dan Sajadah Travel Praktis"],
            'jasa_list' => ["Jasa Jahit Pakaian Pria & Wanita Custom Ukuran Pas", "Jasa Permak Rapi Pakaian (Potong Panjang, Kecilkan Ukuran)", "Jasa Pembuatan Pola Busana Siap Pakai Aneka Ukuran (S, M, L, XL)", "Jasa Bordir Komputer Nama & Logo Seragam"],
            'klien_list' => ["Pihak Sekolah Pemesan Seragam Siswa & Guru", "Instansi Kantor Swasta & Pemerintahan", "Masyarakat Umum Pemesan Pakaian Pesta & Hari Raya", "Komunitas Organisasi Kepemudaan & Karang Taruna"]
        ],
        [
            'kategori' => 'umum',
            'program_keahlian' => 'Mata Pelajaran Umum SMK (Terintegrasi TEFA)',
            'mapel_default' => ["Bahasa Indonesia", "Bahasa Inggris", "Matematika (Kejuruan)", "Projek Ilmu Pengetahuan Alam dan Sosial (IPAS)", "Informatika", "Pendidikan Pancasila", "Sejarah", "Pendidikan Jasmani Olahraga dan Kesehatan (PJOK)", "Seni Budaya", "Projek Kreatif dan Kewirausahaan (PKK)"],
            'konsentrasi_list' => ["Integrasi TEFA Fase E (Kelas X)", "Integrasi TEFA Fase F (Kelas XI)", "Integrasi TEFA Fase F (Kelas XII)"],
            'elemen_list' => [
                "Bahasa Indonesia" => [
                    "Menyimak (Briefing & Instruksi Klien TEFA)",
                    "Membaca dan Memirsa (Manual Book & Draf Kontrak)",
                    "Berbicara dan Mempresentasikan (Pitching Produk & Negosiasi)",
                    "Menulis (Penyusunan SOP TEFA & Berita Acara Serah Terima)"
                ],
                "Bahasa Inggris" => [
                    "Listening & Speaking (Handling Guest & Customer Inquiries)",
                    "Reading & Viewing (Technical Manuals & Import Specifications)",
                    "Writing & Presenting (Bilingual Product Catalog & Quotation Email)"
                ],
                "Matematika (Kejuruan)" => [
                    "Bilangan & Aljabar (Kalkulasi HPP, RAB, dan BEP Produksi)",
                    "Geometri & Pengukuran (Toleransi Ukuran & Efisiensi Bahan)",
                    "Analisis Data dan Peluang (Statistik Kendali Mutu / QC Chart)"
                ],
                "Projek Ilmu Pengetahuan Alam dan Sosial (IPAS)" => [
                    "Menjelaskan Fenomena secara Ilmiah (Karakteristik Zat & Bahan)",
                    "Mendesain & Mengevaluasi Penyelidikan (Uji Sampel & Uji Mutu)",
                    "Menerjemahkan Data & Bukti Ilmiah (Audit Limbah K3 & Eco-Efficiency)"
                ],
                "Informatika" => [
                    "Teknologi Informasi dan Komunikasi (Manajemen Dokumen Cloud TEFA)",
                    "Sistem Komputer & Jaringan (Infrastruktur POS dan Web Order)",
                    "Analisis Data (Dashboard Penjualan & Database Pesanan Klien)",
                    "Dampak Sosial Informatika (Perlindungan Data Pribadi Pelanggan)"
                ],
                "Pendidikan Pancasila" => [
                    "Pancasila (Integritas, Kejujuran Mutu, & Anti-Kecurangan)",
                    "UUD 1945 & Norma (Kepatuhan Regulasi K3 & Standar Industri)",
                    "Bhinneka Tunggal Ika (Kolaborasi Lintas Rumpun di Unit TEFA)"
                ],
                "Projek Kreatif dan Kewirausahaan (PKK)" => [
                    "Peluang Usaha & Desain Produk TEFA",
                    "Proses Produksi Massal & Standardisasi Mutu",
                    "Strategi Pemasaran & Laporan Keuangan Wirausaha"
                ]
            ],
            'produk_list' => ["Dokumen Surat Penawaran, Kontrak Kerja, & Portofolio Karya TEFA (B. Indonesia)", "User Manual, SOP Bilingual, & Presentasi Pitching Berbahasa Inggris (B. Inggris)", "Kalkulator RAB, Estimasi HPP, & Analisis BEP Produksi (Matematika)", "Laporan Analisis Dampak Lingkungan Limbah Produksi & Eco-Efficiency (IPAS)", "Katalog Web / Sistem Manajemen Database Sederhana Hasil Produksi (Informatika)", "Buku Kode Etik Budaya Kerja Industri & Integritas K3 (Pendidikan Pancasila)", "Rancangan Identitas Visual & Kemasan Produk Bernilai Kearifan Lokal (Seni Budaya)"],
            'jasa_list' => ["Jasa Penyuntingan Naskah Laporan Mutu & Penulisan Copywriting Iklan TEFA (B. Indonesia)", "Jasa Layanan Pemandu Wisata & Pendamping Tamu Asing Edotel (B. Inggris)", "Jasa Perhitungan Akurasi Bahan Baku & Pengurangan Waste Pabrikasi (Matematika)", "Jasa Audit K3 & Pengujian Parameter Sanitasi Bengkel/Lab (IPAS)", "Jasa Edukasi Sikap Disiplin Kerja Industri 5R (Pancasila/PJOK)"],
            'klien_list' => ["Unit Bisnis & Teaching Factory Seluruh Jurusan di Sekolah", "Mitra DUDI Rekanan Sekolah", "Koperasi Sekolah & Pengelola Toko Komersial Sekolah", "Masyarakat Sasaran Program Pengabdian Sekolah"]
        ]
    ];

    $sql = ($driver === 'mysql') 
        ? "INSERT INTO master_keahlian (kategori, program_keahlian, mapel_default, konsentrasi_list, elemen_list, produk_list, jasa_list, klien_list) 
           VALUES (:kategori, :prog, :mapel, :kons, :elemen, :prod, :jasa, :klien)
           ON DUPLICATE KEY UPDATE 
               kategori = VALUES(kategori),
               mapel_default = VALUES(mapel_default),
               konsentrasi_list = VALUES(konsentrasi_list),
               elemen_list = VALUES(elemen_list),
               produk_list = VALUES(produk_list),
               jasa_list = VALUES(jasa_list),
               klien_list = VALUES(klien_list)"
        : "INSERT OR REPLACE INTO master_keahlian (kategori, program_keahlian, mapel_default, konsentrasi_list, elemen_list, produk_list, jasa_list, klien_list) 
           VALUES (:kategori, :prog, :mapel, :kons, :elemen, :prod, :jasa, :klien)";

    $stmt = $pdo->prepare($sql);
    foreach ($data as $item) {
        $stmt->execute([
            ':kategori' => $item['kategori'],
            ':prog'     => $item['program_keahlian'],
            ':mapel'    => json_encode($item['mapel_default'], JSON_UNESCAPED_UNICODE),
            ':kons'     => json_encode($item['konsentrasi_list'], JSON_UNESCAPED_UNICODE),
            ':elemen'   => json_encode($item['elemen_list'], JSON_UNESCAPED_UNICODE),
            ':prod'     => json_encode($item['produk_list'], JSON_UNESCAPED_UNICODE),
            ':jasa'     => json_encode($item['jasa_list'], JSON_UNESCAPED_UNICODE),
            ':klien'    => json_encode($item['klien_list'], JSON_UNESCAPED_UNICODE),
        ]);
    }

    // Seed default settings if not exists
    $settings = [
        ['ai_model', 'gemini-1.5-flash', 'Model AI Google Gemini yang digunakan'],
        ['ai_temperature', '0.25', 'Tingkat kreativitas respons AI (0.0 - 1.0)'],
        ['rate_limit_max', '6', 'Maksimal request generate per jendela waktu'],
        ['rate_limit_window', '300', 'Durasi jendela waktu pembatasan dalam detik (5 menit)'],
        ['gemini_api_key', '', 'Google Gemini API Key']
    ];

    $setSql = ($driver === 'mysql')
        ? "INSERT INTO app_settings (key_name, key_value, description) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE description = VALUES(description)"
        : "INSERT OR IGNORE INTO app_settings (key_name, key_value, description) VALUES (?, ?, ?)";
    $setStmt = $pdo->prepare($setSql);
    foreach ($settings as $s) {
        $setStmt->execute($s);
    }
}
