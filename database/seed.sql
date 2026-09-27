-- ==========================================================
-- SINTESA VOKASI - Master Data Seeding (9 Jurusan + Mapel Umum TEFA)
-- ==========================================================

USE `sintesa_vokasi`;

INSERT INTO `master_keahlian` 
(`kategori`, `program_keahlian`, `mapel_default`, `konsentrasi_list`, `produk_list`, `jasa_list`, `klien_list`) 
VALUES 
-- 1. TEKNIK KOMPUTER DAN JARINGAN (TKJ)
(
    'kejuruan',
    'Teknik Jaringan Komputer dan Telekomunikasi',
    '["Konsentrasi Keahlian TKJ", "Dasar-dasar Teknik Jaringan Komputer dan Telekomunikasi", "Perencanaan dan Pengalamatan Jaringan", "Pemasangan dan Konfigurasi Perangkat Jaringan", "Administrasi Server Jaringan", "Projek Kreatif dan Kewirausahaan (PKK)"]',
    '["Teknik Komputer dan Jaringan", "Teknik Transmisi Telekomunikasi", "Rekayasa Perangkat Keras dan Jaringan"]',
    '["Kabel Jaringan UTP/STP Siap Pakai (Crimping & Testing Terkalibrasi)", "Router Mikrotik / Access Point Pre-configured", "Unit Server Hotspot / Mini PC Router Rumahan"]',
    '["Jasa Instalasi & Maintenance Jaringan LAN/WiFi Kantor/Sekolah", "Jasa Pemasangan & Konfigurasi CCTV Online", "Jasa Perakitan, Maintenance & Servis PC/Laptop", "Jasa Setup Hotspot Berbasis Voucher UMKM", "Jasa Terminasi & Splicing Kabel Fiber Optic"]',
    '["Pihak Tata Usaha Sekolah & Lab Komputer", "Kantor Desa / Kelurahan", "Kafe dan Warung Kopi Sekitar Sekolah", "Toko Ritel & Rumah Tinggal Masyarakat Sekitar", "Pelaku Usaha Warnet / Agen Pulsa"]'
),

-- 2. DESAIN KOMUNIKASI VISUAL (DKV)
(
    'kejuruan',
    'Desain Komunikasi Visual',
    '["Konsentrasi Keahlian DKV", "Dasar-dasar Desain Komunikasi Visual", "Perangkat Lunak Desain Grafis", "Fotografi dan Videografi", "Desain Publikasi & Kemasan", "Projek Kreatif dan Kewirausahaan (PKK)"]',
    '["Desain Komunikasi Visual", "Animasi", "Teknik Grafika"]',
    '["Infografis dan Materi Presentasi Interaktif", "Desain Identitas Merek (Logo, Brand Guidelines, Brosur)", "Desain Kemasan Produk UMKM (Packaging Box & Label Botol)", "Kaos Sablon Custom & Totebag Merchandise", "Company Profile Cetak & Digital", "Spanduk, Banner, & Media Promosi Luar Ruang"]',
    '["Jasa Desain Konten Media Sosial (Instagram/TikTok Carousel)", "Jasa Fotografi Produk Katalog UMKM", "Jasa Pembuatan Video Iklan Pendek / Promosi Sekolah", "Jasa Percetakan Kartu Nama, Sertifikat, dan ID Card Acara"]',
    '["UMKM Kuliner & Kerajinan Binaan Sekolah", "Panitia Event / OSIS / Komite Sekolah", "Dinas Pemerintah Daerah & Organisasi Kemasyarakatan", "Pelaku Usaha Startup Lokal", "Pengelola Wisata Daerah"]'
),

-- 3. MANAJEMEN PERKANTORAN DAN LAYANAN BISNIS (MPLB)
(
    'kejuruan',
    'Manajemen Perkantoran dan Layanan Bisnis',
    '["Konsentrasi Keahlian Manajemen Perkantoran", "Dasar-dasar Manajemen Perkantoran", "Pengelolaan Administrasi Umum", "Komunikasi dan Humas Kantor", "Kearsipan Digital", "Teknologi Perkantoran & Otomatisasi"]',
    '["Manajemen Perkantoran", "Otomatisasi dan Tata Kelola Perkantoran"]',
    '["Buku Pedoman Standar Operasional Prosedur (SOP) Kantor Digital", "Modul Digital Panduan Tata Naskah Dinas", "Template Berkas Formulir Administrasi Standar"]',
    '["Jasa Digitalisasi & E-Filing Kearsipan Fisik ke Cloud", "Jasa Event Organizer (EO) Seminar, Workshop, & Rapat Dinas", "Jasa Notulensi Rapat Profesional & Transkripsi Audio", "Jasa Layanan Front Desk / Resepsionis Acara", "Jasa Entri Data & Pengetikan Dokumen Hukum/Akademik"]',
    '["Kantor Notaris & PPAT Rekanan", "Kantor Urusan Agama (KUA) & Kantor Camat", "Puskesmas / Fasilitas Layanan Kesehatan Pratama", "Asosiasi Komite Sekolah & Dewan Pendidikan Daerah"]'
),

-- 4. PEMASARAN / BISNIS DIGITAL
(
    'kejuruan',
    'Pemasaran',
    '["Konsentrasi Keahlian Bisnis Digital", "Dasar-dasar Pemasaran", "Pemasaran Digital (Digital Marketing)", "Perencanaan Bisnis", "Komunikasi Bisnis", "Pengelolaan Toko Online / E-Commerce"]',
    '["Bisnis Digital", "Pengelolaan Toko Ritel", "Pemasaran Daring"]',
    '["Katalog Digital Produk Interaktif (Flipbook / PDF)", "Website Toko Online Sederhana (Linktree/Landing Page)", "Paket Konten Iklan Digital (Copywriting + Visual Iklan)"]',
    '["Jasa Pengelolaan Akun Media Sosial Bisnis (Social Media Management)", "Jasa Pembuatan dan Optimalisasi Profil Toko di Marketplace (Shopee/Tokopedia/TikTok Shop)", "Jasa Iklan Berbayar Meta Ads & TikTok Ads untuk UMKM", "Jasa Live Streaming Host Penjualan Produk"]',
    '["Pelaku UMKM Binaan Kadin / Pemda", "Unit Produksi TEFA Jurusan Lain di Sekolah", "Toko Pakaian & Ritel Lokal", "Distributor Bahan Pokok / Sembako"]'
),

-- 5. AKUNTANSI DAN KEUANGAN LEMBAGA (AKL)
(
    'kejuruan',
    'Akuntansi dan Keuangan Lembaga',
    '["Konsentrasi Keahlian Akuntansi", "Dasar-dasar Akuntansi dan Keuangan Lembaga", "Praktik Akuntansi Perusahaan Jasa, Dagang, dan Manufaktur", "Komputer Akuntansi (MYOB / Accurate / Spreadsheet)", "Administrasi Perpajakan"]',
    '["Akuntansi", "Akuntansi Sektor Publik"]',
    '["Buku Kas Keuangan Sederhana Cetak & Digital", "Aplikasi Kas Berbasis Spreadsheet (Excel/Google Sheets) Siap Pakai", "Laporan Keuangan Neraca & Laba Rugi Standar SAK EMKM", "Draf Formulir Rekonsiliasi Bank & Faktur Pajak"]',
    '["Jasa Pembukuan Transaksi Keuangan Harian UMKM", "Jasa Pendampingan Pengisian SPT Tahunan Wajib Pajak Orang Pribadi", "Jasa Stock Opname Fisik Persediaan Barang Dagang", "Jasa Audit Internal Kas Kecil Koperasi Sekolah"]',
    '["Koperasi Karyawan & Koperasi Siswa Sekolah", "Pedagang Pasar & Pemilik Toko Kelontong", "Unit Usaha Kantin Sekolah", "Yayasan Sosial & Organisasi Keagamaan"]'
),

-- 6. LAYANAN PERBANKAN
(
    'kejuruan',
    'Layanan Perbankan',
    '["Konsentrasi Keahlian Layanan Perbankan", "Dasar-dasar Perbankan", "Pengelolaan Kas Bank (Teller & Customer Service)", "Layanan Kliring & Transaksi Valuta Asing", "Akuntansi Perbankan Syariah dan Konvensional"]',
    '["Layanan Perbankan", "Layanan Perbankan Syariah"]',
    '["Buku Tabungan Siswa / Mini Bank Sekolah", "Formulir Aplikasi Pembukaan Rekening & Slip Setoran/Tarikan", "Modul Panduan Literasi & Inklusi Keuangan Pelajar"]',
    '["Jasa Layanan Loket Bank Mini Sekolah (Setoran Tabungan, Pembayaran Iuran/SPP)", "Jasa Agen Pembayaran PPOB (Listrik, Pulsa, PDAM) di Bank Mini", "Jasa Konsultasi Edukasi Tabungan Rencana Siswa", "Jasa Penukaran Uang Pecahan Kecil untuk Acara Bazar Sekolah"]',
    '["Seluruh Siswa, Guru, dan Karyawan Sekolah", "Orang Tua / Wali Murid", "Warga Masyarakat Sekitar Lingkungan Sekolah", "Unit Bisnis & Kantin Sekolah"]'
),

-- 7. TEKNIK OTOMOTIF / TEKNIK SEPEDA MOTOR (TSM)
(
    'kejuruan',
    'Teknik Otomotif',
    '["Konsentrasi Keahlian Teknik Sepeda Motor", "Dasar-dasar Teknik Otomotif", "Pemeliharaan Mesin Sepeda Motor", "Pemeliharaan Sasis dan Suspensi Sepeda Motor", "Pemeliharaan Kelistrikan Sepeda Motor", "Projek Kreatif dan Kewirausahaan (PKK)"]',
    '["Teknik Sepeda Motor", "Teknik Kendaraan Ringan", "Teknik Bodi Kendaraan Ringan"]',
    '["Cairan Pembersih Throttle Body & Injector Cleaner", "Gantungan Kunci & Aksesoris Modifikasi Touring", "Kampas Rem & Busi Siap Pasang Standar Pabrikan OEM"]',
    '["Jasa Servis Ringan / Tune Up Sepeda Motor Matik & Manual", "Jasa Ganti Oli Mesin & Gardan Express", "Jasa Pembersihan Injektor & Kalibrasi ECU Sederhana", "Jasa Servis CVT, Ganti Roller, & V-Belt", "Jasa Cuci Motor Salju & Detailing Mengkilap"]',
    '["Sepeda Motor Guru, Karyawan, dan Siswa Sekolah", "Masyarakat Umum Pemilik Motor di Lingkungan Sekitar Sekolah", "Driver Ojek Online (Gojek / Grab / Maxim)", "Komunitas Pengendara Sepeda Motor Lokal"]'
),

-- 8. PERHOTELAN
(
    'kejuruan',
    'Perhotelan',
    '["Konsentrasi Keahlian Perhotelan", "Dasar-dasar Perhotelan", "Front Office (Kantor Depan)", "Housekeeping (Tata Graha)", "Laundry & Dry Cleaning", "Food and Beverage Service (Restoran & Bar)"]',
    '["Perhotelan", "Akomodasi Perhotelan"]',
    '["Paket Amenitas Kamar Ramah Lingkungan (Sabun Alami, Slippers, Dental Kit)", "Linen & Towel Standar Hotel Bersih Higienis", "Voucher Paket Menginap Edotel (Hotel Edukasi SMK)"]',
    '["Jasa Reservasi & Akomodasi Kamar Tamu (Edotel)", "Jasa Laundry Kiloan & Dry Cleaning Pakaian Resmi", "Jasa Layanan Table Setting & Banquet Acara Formal", "Jasa Pembersihan Ruangan / Office Cleaning Service Harian"]',
    '["Tamu Dinas / Narasumber Pelatihan di Lingkungan Sekolah", "Orang Tua Siswa Saat Acara Wisuda / Rapat Komite", "Instansi Pemerintah yang Menggelar Konsinyering", "Wisatawan Lokal yang Membutuhkan Penginapan Ekonomis"]'
),

-- 9. LAYANAN PENUNJANG KEFARMASIAN KLINIS DAN KOMUNITAS (FARMASI)
(
    'kejuruan',
    'Layanan Penunjang Kefarmasian Klinis dan Komunitas',
    '["Konsentrasi Keahlian Farmasi Klinis dan Komunitas", "Dasar-dasar Farmasi", "Pelayanan Farmasi & Peracikan Obat", "Farmakologi & Terminologi Medis", "Kimia Farmasi", "Manajemen Pengelolaan Perbekalan Farmasi di Apotek"]',
    '["Farmasi Klinis dan Komunitas", "Farmasi Industri"]',
    '["Hand Sanitizer Herbal Beraroma Alami", "Minyak Aromaterapi / Roll-on Pelega Otot Herbal", "Minuman Herbal Siap Seduh (Ekstrak Jahe Merah, Kunyit Asam)", "Salep / Balsem Herbal Pelega Nafas", "Sabun Cuci Tangan Antiseptik Cair"]',
    '["Jasa Simulasi Pelayanan Resep & Informasi Obat (KIE Apotek Mini)", "Jasa Pengecekan Kesehatan Sederhana (Tensi Darah, Gula Darah Sewaktu, Asam Urat)", "Jasa Pengemasan & Pelabelan Ulang Bahan Baku Herbal Higienis", "Jasa Edukasi DAGUSIBU (Dapatkan, Gunakan, Simpan, Buang Obat) untuk Warga"]',
    '["Warga Sekolah (Siswa, Pendidik, Tenaga Kependidikan)", "Apotek Rekanan & Toko Obat Berizin", "Warga Lansia di Lingkungan Sekitar Sekolah", "Posyandu & Komunitas Senam Lansia Binaan Kelurahan"]'
),

-- 10. KATEGORI MATA PELAJARAN UMUM SMK (TERINTEGRASI TEFA)
(
    'umum',
    'Mata Pelajaran Umum SMK (Terintegrasi TEFA)',
    '["Bahasa Indonesia", "Bahasa Inggris", "Matematika (Kejuruan)", "Projek Ilmu Pengetahuan Alam dan Sosial (IPAS)", "Informatika", "Pendidikan Pancasila", "Sejarah", "Pendidikan Jasmani Olahraga dan Kesehatan (PJOK)", "Seni Budaya", "Projek Kreatif dan Kewirausahaan (PKK)"]',
    '["Integrasi TEFA Fase E (Kelas X)", "Integrasi TEFA Fase F (Kelas XI)", "Integrasi TEFA Fase F (Kelas XII)"]',
    '["Dokumen Surat Penawaran, Kontrak Kerja, & Portofolio Karya TEFA (B. Indonesia)", "User Manual, SOP Bilingual, & Presentasi Pitching Berbahasa Inggris (B. Inggris)", "Kalkulator RAB, Estimasi HPP, & Analisis BEP Produksi (Matematika)", "Laporan Analisis Dampak Lingkungan Limbah Produksi & Eco-Efficiency (IPAS)", "Katalog Web / Sistem Manajemen Database Sederhana Hasil Produksi (Informatika)", "Buku Kode Etik Budaya Kerja Industri & Integritas K3 (Pendidikan Pancasila)", "Rancangan Identitas Visual & Kemasan Produk Bernilai Kearifan Lokal (Seni Budaya)"]',
    '["Jasa Penyuntingan Naskah Laporan Mutu & Penulisan Copywriting Iklan TEFA (B. Indonesia)", "Jasa Layanan Pemandu Wisata & Pendamping Tamu Asing Edotel (B. Inggris)", "Jasa Perhitungan Akurasi Bahan Baku & Pengurangan Waste Pabrikasi (Matematika)", "Jasa Audit K3 & Pengujian Parameter Sanitasi Bengkel/Lab (IPAS)", "Jasa Edukasi Sikap Disiplin Kerja Industri 5R (Pancasila/PJOK)"]',
    '["Unit Bisnis & Teaching Factory Seluruh Jurusan di Sekolah", "Mitra DUDI Rekanan Sekolah", "Koperasi Sekolah & Pengelola Toko Komersial Sekolah", "Masyarakat Sasaran Program Pengabdian Sekolah"]'
)
ON DUPLICATE KEY UPDATE 
    `kategori` = VALUES(`kategori`),
    `mapel_default` = VALUES(`mapel_default`),
    `konsentrasi_list` = VALUES(`konsentrasi_list`),
    `produk_list` = VALUES(`produk_list`),
    `jasa_list` = VALUES(`jasa_list`),
    `klien_list` = VALUES(`klien_list`);
