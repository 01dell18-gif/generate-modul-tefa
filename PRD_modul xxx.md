# Analisis Sistem, Basis Data SQL, & Panduan Rekayasa Balik: SINTESA VOKASI

Dokumen ini berisi arsitektur lengkap sistem aplikasi **SINTESA VOKASI** (`https://sintesa-vokasi.doantara.my.id/`), mencakup perancangan basis data relasional (SQL), kode backend PHP, modul generator fallback tanpa API, integrasi Google Gemini API, proteksi keamanan, panel analitik, serta generator ekspor dokumen Microsoft Word (.docx).

Sistem ini dirancang khusus untuk mendukung **Mata Pelajaran Umum SMK** maupun **Mata Pelajaran Kejuruan** lintas rumpun keahlian (TKJ, DKV, Manajemen Perkantoran, Bisnis Digital, Akuntansi, Layanan Perbankan, TSM, Perhotelan, dan Farmasi Klinis & Komunitas).

---

## 1. Arsitektur & Spesifikasi Basis Data (SQL)

Aplikasi SINTESA VOKASI dihosting pada server berbasis PHP (cPanel / Apache / LiteSpeed). Sistem basis data relasional yang digunakan adalah **MySQL** atau **MariaDB**.

### Mengapa Menggunakan SQL?
1. **Pencatatan Counter Cepat & Akurat**: Menghindari *race condition* saat ratusan guru menekan tombol generate secara bersamaan melalui transaksi atomik database.
2. **Penyimpanan Dokumen Panjang**: Dokumen RPP lengkap beserta 10 tabel lampirannya berukuran rata-rata $35\text{ KB} - 90\text{ KB}$ HTML. Tipe data SQL `LONGTEXT` atau `MEDIUMTEXT` (kapasitas hingga $16\text{ MB}$) dirancang khusus untuk menangani dokumen sebesar ini tanpa risiko data terpotong.
3. **Audit Trail & Analitik**: Sekolah atau pengembang dapat melihat statistik: Program keahlian apa yang paling sering dibuat, jenis produk TEFA yang populer, dan rasio keberhasilan engine AI vs Fallback.
4. **Normalisasi Referensi Jurusan & Mapel**: Mengakomodasi mata pelajaran umum (Bahasa Inggris, Matematika, Bahasa Indonesia, IPAS, dll.) yang diintegrasikan ke proyek TEFA riil di sekolah.

---

## 2. Skema Tabel Basis Data (DDL SQL)

Berikut adalah struktur skema SQL teroptimasi yang mencakup tabel riwayat RPP, tabel counter, tabel master referensi keahlian/mapel umum, dan rate limiter.

```sql
-- 1. Buat Database
CREATE DATABASE IF NOT EXISTS `sintesa_vokasi` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `sintesa_vokasi`;

-- 2. Tabel Utama: Riwayat RPP yang Dihasilkan
CREATE TABLE IF NOT EXISTS `rpp_generations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `satuan_pendidikan` VARCHAR(255) NULL,
    `nama_guru` VARCHAR(255) NULL,
    `mata_pelajaran` VARCHAR(255) NULL,
    `kategori_mapel` ENUM('kejuruan', 'umum') DEFAULT 'kejuruan',
    `program_keahlian` VARCHAR(150) NULL,
    `konsentrasi_keahlian` VARCHAR(150) NULL,
    `fase_kelas` VARCHAR(100) NULL,
    `semester` ENUM('Ganjil', 'Genap') NULL,
    `tahun_pelajaran` VARCHAR(50) NULL,
    `alokasi_waktu` VARCHAR(100) NULL,
    `produk_jasa` VARCHAR(255) NULL,
    `klien` VARCHAR(255) NULL,
    `mitra_industri` VARCHAR(255) NULL,
    `platform_portofolio` VARCHAR(255) DEFAULT 'Google Sites',
    `brief` TEXT NULL,
    `raw_input` JSON NULL COMMENT 'Menyimpan payload input lengkap dari form',
    `html_content` MEDIUMTEXT NOT NULL COMMENT 'Menyimpan hasil HTML RPP lengkap (A-G + Lampiran)',
    `sumber` ENUM('ai', 'smart') DEFAULT 'ai' COMMENT 'Engine pembuat RPP',
    `ip_address` VARCHAR(45) NULL COMMENT 'Mendukung IPv4 & IPv6 untuk tracking/rate limiting',
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_program` (`program_keahlian`),
    INDEX `idx_kategori` (`kategori_mapel`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_ip_time` (`ip_address`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel Master Data Referensi (Dropdown Dinamis Jurusan & Mapel Umum)
CREATE TABLE IF NOT EXISTS `master_keahlian` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kategori` ENUM('kejuruan', 'umum') DEFAULT 'kejuruan',
    `program_keahlian` VARCHAR(150) NOT NULL UNIQUE,
    `mapel_default` JSON NOT NULL COMMENT 'Array JSON rekomendasi mata pelajaran',
    `konsentrasi_list` JSON NOT NULL COMMENT 'Array JSON nama konsentrasi keahlian',
    `produk_list` JSON NOT NULL COMMENT 'Array JSON referensi produk TEFA',
    `jasa_list` JSON NOT NULL COMMENT 'Array JSON referensi jasa TEFA',
    `klien_list` JSON NOT NULL COMMENT 'Array JSON referensi klien sasaran',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel Pelacak Rate Limiting (Mencegah Spam / Abuse API)
CREATE TABLE IF NOT EXISTS `api_rate_limits` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) NOT NULL,
    `endpoint` VARCHAR(50) NOT NULL,
    `request_time` INT UNSIGNED NOT NULL,
    INDEX `idx_lookup` (`ip_address`, `endpoint`, `request_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 3. Master Data Seeding Komprehensif: 9 Jurusan SMK & Mapel Umum

Data berikut mencakup 9 konsentrasi keahlian pesanan khusus beserta kelompok **Mata Pelajaran Umum SMK** yang dikaitkan langsung dengan konteks TEFA.

```sql
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
```

---

## 4. Mekanisme Penerapan TEFA pada Mata Pelajaran Umum SMK

Salah satu keunikan Kurikulum Merdeka di SMK adalah pembelajaran mata pelajaran umum yang **harus berkontekstual dengan dunia kerja**. Guru mata pelajaran umum tidak mengajarkan teori secara terisolasi, melainkan mendukung pesanan nyata TEFA:

| Mata Pelajaran Umum | Relevansi dalam Pembelajaran Mendalam TEFA | Contoh Produk/Jasa Nyata | Bukti Portofolio Siswa |
| :--- | :--- | :--- | :--- |
| **Bahasa Indonesia** | Berkomunikasi formal, menyusun surat penawaran, perjanjian kerja sama, *Standard Operating Procedure* (SOP), dan teks negosiasi dengan klien TEFA. | Dokumen kontrak kerja sama TEFA & teks negosiasi klien | Lembar negosiasi, surat pesanan, draf SOP |
| **Bahasa Inggris** | Melayani klien internasional (di Edotel, bengkel internasional), membaca manual book perangkat impor, menyusun katalog dwibahasa. | *Bilingual Product Catalog* & rekaman video percakapan *handling customer request* | Brosur dwibahasa, rekaman audio/video serah terima produk |
| **Matematika** | Menghitung Harga Pokok Produksi (HPP), Break Even Point (BEP), persentase keuntungan, estimasi kebutuhan material, dan toleransi ukuran teknis. | Lembar kalkulasi biaya produksi, tabel diskon berjenjang, dan kalkulator HPP berbasis spreadsheet | Lembar hitung estimasi bahan, formula spreadsheet laba rugi |
| **Projek IPAS** | Mengidentifikasi sifat zat kimia bahan baku, mengelola limbah bengkel/studio (B3), efisiensi energi kelistrikan, dan keselamatan kerja (K3). | Laporan uji mutu material, sabun ramah lingkungan dari minyak jelantah, audit limbah lab | Form checklist penanganan limbah, laporan uji coba bahan |
| **Pendidikan Pancasila** | Penanaman integritas, kejujuran terhadap klien, menghormati hak cipta intelektual (HAKI), dan kolaborasi lintas keberagaman dalam tim kerja. | Piagam etika kerja unit produksi dan panduan kepatuhan anti-plagiasi karya | Jurnal refleksi etika profesi, berita acara kepatuhan mutu |
| **Informatika** | Manajemen file cloud, pembuatan tautan pembayaran QRIS, publikasi portofolio di web sekolah, dan perlindungan data pribadi pelanggan. | Halaman web landing page produk TEFA & database pesanan Google Sheets | Tautan portofolio Google Sites, tangkapan layar konfigurasi link order |

---

## 5. Implementasi Koneksi Database PHP (`config/db.php`)

```php
<?php
// config/db.php
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'sintesa_vokasi';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["ok" => false, "error" => "Koneksi database gagal: " . $e->getMessage()]);
    exit;
}
```

---

## 6. Endpoint Counter, Referensi, dan Penyimpanan Log

### A. Endpoint Menghitung Counter Global (`api/counter.php`)
```php
<?php
// api/counter.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM rpp_generations");
    $row = $stmt->fetch();
    echo json_encode(["ok" => true, "total" => (int)$row['total']]);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "total" => 0, "error" => $e->getMessage()]);
}
```

### B. Endpoint Referensi Dinamis Lengkap (`api/referensi.php`)
Endpoint ini menyediakan data dropdown hierarkis: Program Keahlian, Konsentrasi, Daftar Mapel, Produk TEFA, Jasa TEFA, dan Sasaran Klien:

```php
<?php
// api/referensi.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT kategori, program_keahlian, mapel_default, konsentrasi_list, produk_list, jasa_list, klien_list 
                         FROM master_keahlian 
                         ORDER BY kategori DESC, program_keahlian ASC");
    $rows = $stmt->fetchAll();

    $data = [];
    foreach ($rows as $row) {
        $data[$row['program_keahlian']] = [
            'kategori'    => $row['kategori'],
            'mapel'       => json_decode($row['mapel_default'], true) ?? [],
            'konsentrasi' => json_decode($row['konsentrasi_list'], true) ?? [],
            'produk'      => json_decode($row['produk_list'], true) ?? [],
            'jasa'        => json_decode($row['jasa_list'], true) ?? [],
            'klien'       => json_decode($row['klien_list'], true) ?? []
        ];
    }

    echo json_encode(["ok" => true, "data" => $data]);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
```

### C. Endpoint Menyimpan Dokumen RPP (`api/save.php`)
```php
<?php
// api/save.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || empty($data['html'])) {
    echo json_encode(["ok" => false, "error" => "Payload tidak lengkap"]);
    exit;
}

$input  = $data['input'] ?? [];
$html   = $data['html'];
$sumber = in_array($data['sumber'] ?? '', ['ai', 'smart']) ? $data['sumber'] : 'smart';
$ip     = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$ua     = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Deteksi apakah mapel tergolong mapel umum atau kejuruan
$prog = $input['program'] ?? '';
$kategoriMapel = (strpos($prog, 'Umum') !== false) ? 'umum' : 'kejuruan';

try {
    $sql = "INSERT INTO rpp_generations (
                satuan_pendidikan, nama_guru, mata_pelajaran, kategori_mapel,
                program_keahlian, konsentrasi_keahlian, fase_kelas, 
                semester, tahun_pelajaran, alokasi_waktu, 
                produk_jasa, klien, mitra_industri, platform_portofolio, 
                brief, raw_input, html_content, sumber, ip_address, user_agent
            ) VALUES (
                :satuan, :guru, :mapel, :kategori,
                :program, :konsentrasi, :fase, 
                :semester, :tahun, :alokasi, 
                :produk, :klien, :mitra, :portofolio, 
                :brief, :raw_input, :html, :sumber, :ip, :ua
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':satuan'      => $input['satuan'] ?? null,
        ':guru'        => $input['guru'] ?? null,
        ':mapel'       => $input['mapel'] ?? null,
        ':kategori'    => $kategoriMapel,
        ':program'     => $input['program'] ?? null,
        ':konsentrasi' => $input['konsentrasi'] ?? null,
        ':fase'        => $input['fase'] ?? null,
        ':semester'    => in_array($input['semester'] ?? '', ['Ganjil', 'Genap']) ? $input['semester'] : null,
        ':tahun'       => $input['tahun'] ?? null,
        ':alokasi'     => $input['alokasi'] ?? null,
        ':produk'      => $input['produk'] ?? null,
        ':klien'       => $input['klien'] ?? null,
        ':mitra'       => $input['mitra'] ?? null,
        ':portofolio'  => $input['portofolio'] ?? 'Google Sites',
        ':brief'       => $input['brief'] ?? null,
        ':raw_input'   => json_encode($input, JSON_UNESCAPED_UNICODE),
        ':html'        => $html,
        ':sumber'      => $sumber,
        ':ip'          => $ip,
        ':ua'          => substr($ua, 0, 500)
    ]);

    $countStmt = $pdo->query("SELECT COUNT(*) AS total FROM rpp_generations");
    $total = (int)$countStmt->fetchColumn();

    echo json_encode(["ok" => true, "total" => $total]);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => "Gagal menyimpan: " . $e->getMessage()]);
}
```

---

## 7. Frontend Logic Tanpa Mengubah Tata Letak Antarmuka (`assets/app.js`)

Kode JavaScript ini memanfaatkan tata letak antarmuka preview yang sudah ada (panel kiri split dan preview kertas A4 di panel kanan), dengan penyempurnaan pada *auto-suggest* mata pelajaran dan opsi dropdown keahlian:

```javascript
/* ============================================================
   SINTESA VOKASI — Logika Frontend (Updated with Full Jurusan)
   ============================================================ */
(function () {
    'use strict';

    const $ = (sel) => document.querySelector(sel);

    const form        = $('#rppForm');
    const preview     = $('#preview');
    const btnGenerate = $('#btnGenerate');
    const btnDownload = $('#btnDownload');
    const btnReset    = $('#btnReset');
    const btnEditable = $('#btnEditable');
    const sourceHint  = $('#sourceHint');
    const counterNum  = $('#counterNum');
    const toast       = $('#toast');

    let lastSource = 'smart';
    let hasContent = false;

    // Notifikasi toast
    let toastTimer = null;
    function showToast(msg, type) {
        if (!toast) return;
        toast.textContent = msg;
        toast.className = 'toast' + (type ? ' ' + type : '');
        toast.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => { toast.hidden = true; }, 4000);
    }

    // Ambil counter global
    function loadCounter() {
        fetch('api/counter.php')
            .then((r) => r.json())
            .then((d) => { if (d && typeof d.total !== 'undefined') animateCounter(d.total); })
            .catch(() => {});
    }

    function animateCounter(target) {
        target = parseInt(target, 10) || 0;
        let cur = parseInt(counterNum.textContent, 10) || 0;
        const step = Math.max(1, Math.ceil(Math.abs(target - cur) / 20));
        const tick = () => {
            if (cur < target) { cur = Math.min(target, cur + step); }
            else if (cur > target) { cur = Math.max(target, cur - step); }
            counterNum.textContent = cur;
            if (cur !== target) requestAnimationFrame(tick);
        };
        tick();
    }

    // Muat referensi dinamis jurusan dan mapel
    let REF = {};
    function loadReferensi() {
        fetch('api/referensi.php')
            .then((r) => r.json())
            .then((d) => {
                if (!d || !d.ok) return;
                REF = d.data || {};
                const selProgram = $('#selProgram');
                selProgram.innerHTML = '<option value="">-- pilih program keahlian / umum --</option>';

                Object.keys(REF).forEach((prog) => {
                    const opt = document.createElement('option');
                    opt.value = prog; 
                    opt.textContent = prog;
                    selProgram.appendChild(opt);
                });
            })
            .catch(() => {});
    }

    function onProgramChange() {
        const prog = $('#selProgram').value;
        const selKons = $('#selKonsentrasi');
        const dlProduk = $('#produkList');
        const dlKlien  = $('#klienList');
        const inpMapel = $('input[name="mapel"]');

        selKons.innerHTML = '<option value="">-- pilih konsentrasi --</option>';
        dlProduk.innerHTML = '';
        dlKlien.innerHTML = '';

        if (!REF[prog]) return;

        // Isi konsentrasi keahlian
        (REF[prog].konsentrasi || []).forEach((k) => {
            const o = document.createElement('option'); 
            o.value = k;
            o.textContent = k; 
            selKons.appendChild(o);
        });

        // Rekomendasi mata pelajaran otomatis di placeholder
        if (REF[prog].mapel && REF[prog].mapel.length > 0) {
            inpMapel.placeholder = "Rekomendasi: " + REF[prog].mapel[0];
        }

        // Gabungkan produk & jasa untuk datalist
        const produkItems = [].concat(REF[prog].produk || [], REF[prog].jasa || []);
        produkItems.forEach((p) => {
            const o = document.createElement('option'); 
            o.value = p; 
            dlProduk.appendChild(o);
        });

        // Isi target klien
        (REF[prog].klien || []).forEach((k) => {
            const o = document.createElement('option'); 
            o.value = k; 
            dlKlien.appendChild(o);
        });
    }

    function collectInput() {
        const data = {};
        new FormData(form).forEach((v, k) => { data[k] = (v || '').toString().trim(); });
        return data;
    }

    function generate(e) {
        e.preventDefault();
        const input = collectInput();

        btnGenerate.disabled = true;
        btnGenerate.querySelector('.btn-text').textContent = 'Membuat RPP...';
        preview.innerHTML = '<div class="placeholder-empty"><p>Sedang menyusun RPP Pembelajaran Mendalam TEFA, mohon tunggu...</p></div>';

        fetch('api/generate.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(input),
        })
            .then((r) => r.json())
            .then((d) => {
                if (!d || !d.ok || !d.html) {
                    throw new Error('Respons generator tidak valid.');
                }
                preview.innerHTML = d.html;
                lastSource = d.sumber || 'smart';
                hasContent = true;
                btnDownload.disabled = false;
                updateSourceHint(d);
                saveResult(input, d.html, lastSource);
                preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch((err) => {
                showToast('Gagal membuat RPP: ' + err.message, 'err');
                preview.innerHTML = '<div class="placeholder-empty"><p>Terjadi kesalahan. Coba ulangi kembali.</p></div>';
            })
            .finally(() => {
                btnGenerate.disabled = false;
                btnGenerate.querySelector('.btn-text').textContent = 'Generate RPP';
            });
    }

    function updateSourceHint(d) {
        if (d.sumber === 'ai') {
            sourceHint.innerHTML = '<span class="badge ai">Dibuat oleh AI</span> Anda bisa mengedit langsung teks di bawah.';
        } else {
            let msg = '<span class="badge smart">Smart Generator</span> ';
            msg += d.ai_error
                ? 'AI tidak tersedia, memakai template cadangan. Anda bisa mengedit langsung.'
                : 'Dibuat memakai template cadangan (Smart Generator). Anda bisa mengedit langsung teks.';
            sourceHint.innerHTML = msg;
        }
    }

    function saveResult(input, html, sumber) {
        fetch('api/save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ input: input, html: html, sumber: sumber }),
        })
            .then((r) => r.json())
            .then((d) => {
                if (d && d.ok && typeof d.total !== 'undefined') {
                    animateCounter(d.total);
                    showToast('RPP berhasil dibuat & tersimpan.', 'ok');
                }
            })
            .catch(() => {});
    }

    function download() {
        if (!hasContent) return;
        const html = preview.innerHTML;
        const input = collectInput();
        let judul = 'RPP-TEFA';
        if (input.mapel) judul += '-' + input.mapel;
        if (input.satuan) judul += '-' + input.satuan;

        $('#docxHtml').value = html;
        $('#docxJudul').value = judul.substring(0, 80);
        $('#docxForm').submit();
        showToast('Menyiapkan berkas Word (.doc)...', 'ok');
    }

    function toggleEditable() {
        const on = preview.getAttribute('contenteditable') === 'true';
        preview.setAttribute('contenteditable', on ? 'false' : 'true');
        btnEditable.textContent = on ? 'Edit: OFF' : 'Edit: ON';
        btnEditable.classList.toggle('off', on);
    }

    function resetAll() {
        form.reset();
        $('#selKonsentrasi').innerHTML = '<option value="">-- pilih program dulu --</option>';
        $('#produkList').innerHTML = '';
        $('#klienList').innerHTML = '';
        preview.innerHTML = '<div class="placeholder-empty"><p><strong>Belum ada RPP.</strong></p><p>Isi form lalu klik <em>Generate RPP</em>.</p></div>';
        hasContent = false;
        btnDownload.disabled = true;
        sourceHint.textContent = 'Hasil akan tampil di sini. Anda bisa mengedit langsung.';
    }

    function initModal() {
        const modal = $('#aboutModal');
        if (!modal) return;
        $('#btnAbout').addEventListener('click', () => { modal.hidden = false; });
        modal.querySelectorAll('[data-close]').forEach((el) => {
            el.addEventListener('click', () => { modal.hidden = true; });
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') modal.hidden = true;
        });
    }

    // Inisialisasi Event Listener
    form.addEventListener('submit', generate);
    btnDownload.addEventListener('click', download);
    btnReset.addEventListener('click', resetAll);
    btnEditable.addEventListener('click', toggleEditable);
    $('#selProgram').addEventListener('change', onProgramChange);
    initModal();
    loadCounter();
    loadReferensi();
})();
```

---

## 8. Smart Fallback Generator Komprehensif (`engine/SmartGenerator.php`)

Skrip generator lokal ini secara cerdas menyesuaikan konten RPP berdasarkan apakah mata pelajaran yang dipilih adalah mata pelajaran umum atau kejuruan (TKJ, DKV, TSM, Farmasi, Perhotelan, dll.), dan menyusun 10 lampiran kerja yang sama persis seperti dokumen cetak `RPP.docx`.

```php
<?php
// engine/SmartGenerator.php

class SmartGenerator {
    public static function generate(array $in): string {
        $val = function($key, $default = '') use ($in) {
            $v = trim($in[$key] ?? '');
            return $v !== '' ? htmlspecialchars($v, ENT_QUOTES, 'UTF-8') : $default;
        };

        $satuan       = $val('satuan', 'SMK [nama sekolah] (disesuaikan)');
        $guru         = $val('guru', '[nama guru pengampu] (disesuaikan)');
        $program      = $val('program', 'Keahlian Vokasi (disesuaikan)');
        $mapel        = $val('mapel', 'mata pelajaran kejuruan pada ' . $program . ' (disesuaikan)');
        $konsentrasi  = $val('konsentrasi', $program);
        $fase         = $val('fase', 'Fase F / Kelas XII');
        $semester     = $val('semester', 'Ganjil');
        $tahun        = $val('tahun', '2026/2027 (disesuaikan)');
        $alokasi      = $val('alokasi', '12 JP (3 pertemuan @ 4 JP) (disesuaikan)');
        $produk       = $val('produk', 'Produk / Jasa Unggulan Standar Industri');
        $materi       = $val('materi', 'materi terkait pembuatan ' . $produk . ' (disesuaikan)');
        $konteksTefa  = $val('konteks_tefa', 'unit produksi / teaching factory ' . $program . ' di sekolah (disesuaikan)');
        $klien        = $val('klien', 'UMKM sekitar sekolah, Panitia kegiatan sekolah, OSIS (disesuaikan)');
        $brief        = $val('brief', 'pesanan pembuatan ' . $produk . ' sesuai kebutuhan dan standar mutu klien (disesuaikan)');
        $sarana       = $val('sarana', 'Lab/Bengkel Praktik Kejuruan, Komputer & Perangkat Kerja Standar Industri (disesuaikan)');
        $mitra        = $val('mitra', 'belum ada / dapat ditambahkan bila tersedia (jangan mengarang mitra nyata) (disesuaikan)');
        $portofolio   = $val('portofolio', 'Google Sites / website sekolah (disesuaikan)');
        $cp           = $val('cp', 'tempel Capaian Pembelajaran (CP) resmi mata pelajaran ini (disesuaikan)');
        $tp           = $val('tp', 'peserta didik mampu merencanakan dan menghasilkan ' . $produk . ' sesuai standar mutu dan kebutuhan klien (disesuaikan)');

        ob_start();
        ?>
        <h1>RENCANA PELAKSANAAN PEMBELAJARAN (RPP)</h1>
        <h1>PERENCANAAN PEMBELAJARAN MENDALAM BERBASIS TEACHING FACTORY (TEFA)</h1>

        <h2>A. IDENTITAS DAN KONTEKS PEMBELAJARAN</h2>
        <table>
            <tbody>
                <tr><th style="width:32%;">Satuan Pendidikan</th><td><?= $satuan ?></td></tr>
                <tr><th>Nama Guru</th><td><?= $guru ?></td></tr>
                <tr><th>Mata Pelajaran</th><td><?= $mapel ?></td></tr>
                <tr><th>Program Keahlian</th><td><?= $program ?></td></tr>
                <tr><th>Konsentrasi Keahlian</th><td><?= $konsentrasi ?></td></tr>
                <tr><th>Fase / Kelas</th><td><?= $fase ?></td></tr>
                <tr><th>Semester</th><td><?= $semester ?></td></tr>
                <tr><th>Tahun Pelajaran</th><td><?= $tahun ?></td></tr>
                <tr><th>Alokasi Waktu</th><td><?= $alokasi ?></td></tr>
                <tr><th>Materi / Topik</th><td><?= $materi ?></td></tr>
                <tr><th>Konteks TEFA / Unit Produksi</th><td><?= $konteksTefa ?></td></tr>
                <tr><th>Produk atau Jasa</th><td><?= $produk ?></td></tr>
                <tr><th>Klien / Konsumen</th><td><?= $klien ?></td></tr>
                <tr><th>Brief / Pesanan</th><td><?= $brief ?></td></tr>
                <tr><th>Sarana dan Prasarana</th><td><?= $sarana ?></td></tr>
                <tr><th>Mitra Industri</th><td><?= $mitra ?></td></tr>
                <tr><th>Platform Portofolio Digital</th><td><?= $portofolio ?></td></tr>
            </tbody>
        </table>

        <h2>B. IDENTIFIKASI</h2>
        <h3>1. Kesiapan Peserta Didik</h3>
        <p>Peserta didik memiliki pengetahuan awal terkait <?= $produk ?> dan siap belajar melalui praktik nyata (disesuaikan). Uraikan pengetahuan awal, keterampilan prasyarat, minat/kebutuhan, kesiapan belajar, serta kebutuhan pendampingan atau pengayaan.</p>
        <p><strong>Asesmen Diagnostik:</strong></p>
        <ul>
            <li><strong>Tujuan:</strong> memetakan pengetahuan awal dan keterampilan prasyarat terkait <?= $materi ?>.</li>
            <li><strong>Teknik:</strong> tanya jawab lisan / kuis singkat / observasi awal.</li>
            <li><strong>Instrumen:</strong> daftar pertanyaan diagnostik dan lembar observasi.</li>
            <li><strong>Aspek yang diperiksa:</strong> konsep dasar, keterampilan prasyarat, minat, kesiapan kerja.</li>
            <li><strong>Tindak lanjut:</strong> pengelompokan sesuai kesiapan, pendampingan, atau pengayaan.</li>
        </ul>

        <h3>2. Karakteristik Materi</h3>
        <ul>
            <li><strong>Konsep utama:</strong> pemahaman konsep dasar dan prinsip kerja dalam pembuatan <?= $produk ?> (disesuaikan).</li>
            <li><strong>Keterampilan prosedural:</strong> langkah kerja menyiapkan, memproduksi, dan menyelesaikan <?= $produk ?> sesuai SOP (disesuaikan).</li>
            <li><strong>Tingkat kesulitan:</strong> sedang — memadukan teori dan praktik nyata sesuai standar industri (disesuaikan).</li>
            <li><strong>Penerapan di dunia kerja:</strong> terhubung langsung dengan pekerjaan menghasilkan <?= $produk ?>.</li>
            <li><strong>Potensi miskonsepsi:</strong> menganggap hasil selesai tanpa memenuhi standar/kebutuhan klien, atau melewati tahap pemeriksaan mutu (QC) (disesuaikan).</li>
            <li><strong>Etika, budaya kerja, dan keselamatan:</strong> penerapan SOP, K3, dan sikap profesional.</li>
        </ul>

        <h3>3. Dimensi Profil Lulusan (DPL)</h3>
        <p>Pilih dimensi yang relevan dengan tujuan pembelajaran (jangan centang semua otomatis). Untuk tiap dimensi terpilih, tuliskan alasan, indikator perilaku yang dapat diamati, aktivitas pengembangan, dan bukti asesmen.</p>
        <p>[ ] DPL 1 Keimanan dan Ketakwaan terhadap Tuhan Yang Maha Esa<br>
           [ ] DPL 2 Kewargaan<br>
           [✓] DPL 3 Penalaran Kritis — analisis brief dan pemecahan masalah produksi.<br>
           [✓] DPL 4 Kreativitas — pengembangan ide/solusi produk atau jasa.<br>
           [✓] DPL 5 Kolaborasi — kerja tim dalam proses produksi.<br>
           [✓] DPL 6 Kemandirian — tanggung jawab menyelesaikan pekerjaan.<br>
           [ ] DPL 7 Kesehatan<br>
           [✓] DPL 8 Komunikasi — presentasi/serah terima produk kepada klien.<br>
           <em>Catatan: sesuaikan centang di atas dengan tujuan pembelajaran nyata Anda.</em></p>

        <h2>C. DESAIN PEMBELAJARAN</h2>
        <h3>1. Capaian Pembelajaran (CP)</h3>
        <p><?= $cp ?></p>

        <h3>2. Tujuan Pembelajaran (TP)</h3>
        <p><?= $tp ?></p>

        <h3>3. Kriteria Ketercapaian Tujuan Pembelajaran (KKTP)</h3>
        <ol>
            <li>Peserta didik mampu menganalisis brief/pesanan dengan tepat.</li>
            <li>Peserta didik mampu menyusun rencana kerja produksi <?= $produk ?>.</li>
            <li>Peserta didik mampu melaksanakan produksi sesuai standar dan SOP.</li>
            <li>Peserta didik mampu melakukan quality control dan revisi produk.</li>
            <li>Peserta didik mampu menyusun portofolio digital dan merefleksi hasil kerja.</li>
        </ol>

        <h3>4. Pemahaman Bermakna</h3>
        <p>Pekerjaan menghasilkan <?= $produk ?> menghubungkan materi <?= $materi ?> dengan kebutuhan nyata dunia kerja, sehingga peserta didik memahami relevansi kompetensinya bagi kehidupan dan karier.</p>

        <h3>5. Pertanyaan Pemantik</h3>
        <ol>
            <li>Siapa target pengguna/klien dari produk ini dan apa kebutuhannya?</li>
            <li>Standar kualitas seperti apa yang membuat produk ini layak diterima klien?</li>
            <li>Bagaimana cara menyelesaikan masalah yang muncul selama produksi?</li>
            <li>Bagaimana kita menilai keberhasilan pekerjaan ini?</li>
        </ol>

        <h3>6. Praktik Pedagogis</h3>
        <p>Model Teaching Factory dengan strategi kontekstual dan kolaboratif; diferensiasi sesuai kesiapan; metode demonstrasi, diskusi, praktik langsung, coaching, umpan balik, dan refleksi.</p>

        <h3>7. Kemitraan Pembelajaran</h3>
        <p>Peran klien/konsumen: <?= $klien ?>. Mitra industri: <?= $mitra ?>. Libatkan unit produksi sekolah dan guru lintas mapel jika relevan. Jangan mengarang mitra nyata.</p>

        <h3>8. Lingkungan Pembelajaran</h3>
        <p>Ruang praktik/studio/bengkel/laboratorium dengan budaya kerja profesional, penerapan K3, pembagian area kerja, serta lingkungan digital pendukung.</p>

        <h3>9. Pemanfaatan Digital</h3>
        <p>Penggunaan aplikasi/platform untuk brief, komunikasi, produksi, dokumentasi, asesmen, penyimpanan, dan publikasi portofolio digital (<?= $portofolio ?>).</p>

        <h2>D. PENGALAMAN BELAJAR DAN SINTAKS TEFA</h2>
        <h3>1. Kegiatan Awal (Berkesadaran, Bermakna) — TEFA: Orientasi & Briefing</h3>
        <p><em>Tujuan tahap: membangun kesadaran bahwa pembelajaran adalah pengalaman kerja nyata, bukan sekadar tugas untuk nilai.</em></p>
        <table>
            <thead>
                <tr><th style="width:50%;">Aktivitas Guru</th><th>Aktivitas Peserta Didik</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Membuka pembelajaran dan membangun suasana; mengaitkan pekerjaan dengan dunia kerja; menyampaikan brief/pesanan; menjelaskan tujuan, standar kualitas, batas waktu, SOP, dan K3; membagi tugas; menyampaikan kriteria keberhasilan produk.</td>
                    <td>Menyimak briefing; mengidentifikasi tujuan dan kebutuhan pekerjaan; bertanya bila ada brief yang belum jelas; menghubungkan dengan pengetahuan sebelumnya; memahami peran; menyiapkan alat dan kebutuhan kerja.</td>
                </tr>
            </tbody>
        </table>
        <p><em>Bukti Portofolio: brief/pesanan, catatan tujuan pekerjaan, pembagian tugas, checklist persiapan.</em></p>

        <h3>2. Kegiatan Inti (Berkesadaran, Bermakna, Menggembirakan)</h3>
        <p><strong>a. Memahami (Berkesadaran, Bermakna) — TEFA: Analisis Brief</strong></p>
        <p><em>Tujuan tahap: memahami apa yang harus dibuat, untuk siapa, mengapa, dan standar seperti apa — sebelum produksi dimulai.</em></p>
        <table>
            <thead><tr><th>Aktivitas Guru</th><th>Aktivitas Peserta Didik</th></tr></thead>
            <tbody>
                <tr>
                    <td>Memberi contoh/referensi produk; membimbing analisis brief; mengajukan pertanyaan pemantik (target pengguna, kebutuhan pelanggan, standar produk); memberi arahan teknis; memastikan pemahaman sebelum produksi.</td>
                    <td>Membaca dan menganalisis brief; mengidentifikasi kebutuhan pelanggan; menentukan target pengguna; mencari referensi; mengidentifikasi alat/bahan/teknik; menentukan kriteria produk berhasil; mendiskusikan alternatif solusi.</td>
                </tr>
            </tbody>
        </table>
        <p><em>Bukti Portofolio: analisis brief, referensi, moodboard, sketsa awal, konsep, catatan kebutuhan produksi.</em></p>

        <p><strong>b. Mengaplikasi (Bermakna, Menggembirakan) — TEFA: Perencanaan & Produksi</strong></p>
        <p><em>Tujuan tahap: inti aktivitas TEFA — belajar melalui pengalaman mengerjakan pekerjaan seperti di dunia nyata.</em></p>
        <table>
            <thead><tr><th>Aktivitas Guru</th><th>Aktivitas Peserta Didik</th></tr></thead>
            <tbody>
                <tr>
                    <td>Membimbing penyusunan rencana kerja; memastikan penggunaan alat/bahan sesuai prosedur; mengamati proses; memberi coaching dan umpan balik; mendorong kolaborasi dan kreativitas; mengarahkan menemukan solusi (bukan memberi jawaban langsung).</td>
                    <td>Menyusun langkah kerja; membagi tugas; menyiapkan alat/bahan; melaksanakan produksi <?= $produk ?>; menerapkan teknik; berkolaborasi; mengatasi masalah; mendokumentasikan proses; mencatat keputusan yang dibuat.</td>
                </tr>
            </tbody>
        </table>
        <p><em>Bukti Portofolio: rencana kerja, sketsa, moodboard, dokumentasi proses, file kerja, catatan masalah & solusi, draft produk.</em></p>

        <p><strong>c. Merefleksi (Berkesadaran, Bermakna) — TEFA: Quality Control, Umpan Balik & Revisi</strong></p>
        <p><em>Tujuan tahap: produk tidak langsung dianggap selesai; peserta didik belajar memeriksa kualitas seperti di dunia kerja.</em></p>
        <table>
            <thead><tr><th>Aktivitas Guru</th><th>Aktivitas Peserta Didik</th></tr></thead>
            <tbody>
                <tr>
                    <td>Mengarahkan Quality Control; membandingkan hasil dengan brief & standar; memberi feedback; mengajak menemukan kekurangan; membimbing revisi; mengajukan pertanyaan reflektif.</td>
                    <td>Memeriksa hasil; membandingkan produk dengan brief; mengidentifikasi kekurangan; menerima & memberi umpan balik; melakukan revisi; membandingkan sebelum/sesudah revisi; menjelaskan alasan perubahan; menilai proses kerja diri/kelompok.</td>
                </tr>
            </tbody>
        </table>
        <p><em>Bukti Portofolio: checklist QC, feedback guru/teman/klien, draft sebelum & sesudah revisi, catatan revisi, produk final.</em></p>

        <h3>3. Kegiatan Penutup (Berkesadaran) — TEFA: Finishing, Delivery & Refleksi</h3>
        <p><em>Tujuan tahap: menutup pekerjaan secara profesional (bukan sekadar "dikumpulkan").</em></p>
        <table>
            <thead><tr><th>Aktivitas Guru</th><th>Aktivitas Peserta Didik</th></tr></thead>
            <tbody>
                <tr>
                    <td>Memfasilitasi presentasi/penyerahan produk; memberi apresiasi proses & hasil; menguatkan kompetensi; mengajak refleksi; menyampaikan tindak lanjut.</td>
                    <td>Finalisasi produk; presentasi/penyerahan; menjelaskan proses & keputusan; menyampaikan kendala & solusi; mengisi refleksi; menyusun portofolio akhir.</td>
                </tr>
            </tbody>
        </table>
        <p><em>Bukti Portofolio: produk final, dokumentasi produk, presentasi/penyerahan, refleksi siswa, portofolio lengkap.</em></p>

        <h2>E. INTEGRASI PORTOFOLIO DIGITAL</h2>
        <p><strong>Tujuan portofolio:</strong> bukti nyata proses & hasil belajar terjadi, bagian dari pembelajaran (bukan tugas tambahan).<br>
        <strong>Platform:</strong> <?= $portofolio ?><br>
        <strong>Jenis karya & bukti:</strong> analisis brief, rencana kerja, dokumentasi proses, draft & produk final, refleksi.<br>
        <strong>Dokumentasi:</strong> foto/video proses, file kerja, catatan revisi.<br>
        <strong>Struktur minimal portofolio:</strong> Profil pembuat, Judul proyek dan konteks klien, Hasil akhir, Deskripsi teknik dan peran siswa.</p>

        <h2>F. ASESMEN PEMBELAJARAN</h2>
        <h3>1. Asesmen Awal</h3>
        <p>Tujuan: memetakan kesiapan. Teknik: kuis/tanya jawab. Instrumen: daftar pertanyaan. Aspek: prasyarat & minat. Tindak lanjut: pengelompokan/pendampingan.</p>
        <h3>2. Asesmen Formatif</h3>
        <p>Dilakukan selama analisis brief, perencanaan, produksi, QC, dan penyusunan portofolio. Teknik: observasi & umpan balik. Instrumen: lembar observasi & checklist.</p>
        <h3>3. Asesmen Sumatif / Unjuk Kerja</h3>
        <p>Menilai kompetensi berdasarkan produk/jasa, proses kerja, pemenuhan brief, kualitas, penerapan SOP, komunikasi, dan refleksi.</p>

        <h3>4. Rubrik Penilaian Unjuk Kerja TEFA</h3>
        <table>
            <thead>
                <tr><th>Kriteria</th><th>Perlu Bimbingan (1)</th><th>Cukup (2)</th><th>Baik (3)</th><th>Sangat Baik (4)</th></tr>
            </thead>
            <tbody>
                <tr><td>Analisis Brief</td><td>Belum memahami brief</td><td>Memahami sebagian</td><td>Memahami dengan tepat</td><td>Memahami & mengembangkan solusi</td></tr>
                <tr><td>Proses Produksi</td><td>Belum sesuai SOP</td><td>Sebagian sesuai SOP</td><td>Sesuai SOP</td><td>Sesuai SOP & efisien</td></tr>
                <tr><td>Kualitas Produk</td><td>Belum sesuai brief</td><td>Sebagian sesuai</td><td>Sesuai brief</td><td>Melebihi harapan brief</td></tr>
                <tr><td>Kolaborasi & Komunikasi</td><td>Pasif</td><td>Kadang aktif</td><td>Aktif & kooperatif</td><td>Memimpin & komunikatif</td></tr>
                <tr><td>Refleksi</td><td>Belum reflektif</td><td>Reflektif terbatas</td><td>Reflektif jelas</td><td>Reflektif & ada rencana perbaikan</td></tr>
            </tbody>
        </table>

        <h3>5. Penilaian Portofolio Digital</h3>
        <table>
            <thead><tr><th style="width:35%;">Aspek</th><th>Deskripsi</th></tr></thead>
            <tbody>
                <tr><td>Kelengkapan dokumentasi</td><td>Semua tahap terdokumentasi rapi dari awal hingga akhir.</td></tr>
                <tr><td>Kesesuaian karya dengan brief</td><td>Produk memenuhi permintaan klien dan SOP yang ditentukan.</td></tr>
                <tr><td>Kejelasan deskripsi & identitas proyek</td><td>Judul, konteks, dan peran siswa dalam tim jelas.</td></tr>
                <tr><td>Dokumentasi proses & revisi</td><td>Terlihat perkembangan nyata sebelum dan sesudah revisi mutu.</td></tr>
                <tr><td>Kualitas penyajian digital</td><td>Rapi, mudah dibaca, tertata estetis di platform web.</td></tr>
                <tr><td>Refleksi kompetensi</td><td>Menyampaikan pembelajaran bermakna dan rencana tindak lanjut karier.</td></tr>
            </tbody>
        </table>

        <table class="ttd">
            <tr>
                <td>Mengetahui,<br>Kepala Sekolah<br><br><br><br><strong>( ……………………………… )</strong><br>NIP. ……………………………</td>
                <td>Dibuat di: ...................., Tanggal: ..............<br>Guru Mata Pelajaran<br><br><br><br><strong>( <?= $guru ?> )</strong><br>NIP. ……………………………</td>
            </tr>
        </table>

        <!-- ==================== G. LAMPIRAN ==================== -->
        <div class="page-break"></div>
        <h2>G. LAMPIRAN LENGKAP</h2>
        <p>Berikut lampiran lengkap yang dapat langsung digunakan dan diedit sesuai kebutuhan.</p>

        <h3>Lampiran 1. LKPD Analisis Brief</h3>
        <p><strong>Produk/Jasa:</strong> <?= $produk ?> &nbsp;&nbsp; <strong>Klien/Konsumen:</strong> <?= $klien ?><br>
        <strong>Nama/Kelompok:</strong> ........................................................... &nbsp;&nbsp; <strong>Kelas:</strong> <?= $fase ?></p>
        <table>
            <thead><tr><th style="width:5%;">No</th><th style="width:45%;">Pertanyaan Analisis</th><th>Jawaban Peserta Didik</th></tr></thead>
            <tbody>
                <tr><td>1</td><td>Apa produk/jasa yang dipesan?</td><td><?= $produk ?></td></tr>
                <tr><td>2</td><td>Siapa target pengguna/klien?</td><td><?= $klien ?></td></tr>
                <tr><td>3</td><td>Apa kebutuhan dan harapan klien?</td><td></td></tr>
                <tr><td>4</td><td>Apa standar/kriteria produk dianggap berhasil?</td><td></td></tr>
                <tr><td>5</td><td>Alat, bahan, dan teknik apa yang diperlukan?</td><td></td></tr>
                <tr><td>6</td><td>Referensi apa yang digunakan?</td><td></td></tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 2. Lembar Perencanaan Kebutuhan Produk</h3>
        <table>
            <thead><tr><th style="width:5%;">No</th><th>Kebutuhan</th><th>Spesifikasi</th><th>Jumlah</th><th>Ketersediaan</th></tr></thead>
            <tbody>
                <tr><td>1</td><td></td><td></td><td></td><td>Ada / Tidak</td></tr>
                <tr><td>2</td><td></td><td></td><td></td><td>Ada / Tidak</td></tr>
                <tr><td>3</td><td></td><td></td><td></td><td>Ada / Tidak</td></tr>
                <tr><td>4</td><td></td><td></td><td></td><td>Ada / Tidak</td></tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 3. Lembar Pembagian Peran dan Jadwal Produksi</h3>
        <table>
            <thead><tr><th style="width:5%;">No</th><th>Nama Anggota</th><th>Peran/Tugas</th><th>Target Waktu</th><th>Keterangan</th></tr></thead>
            <tbody>
                <tr><td>1</td><td></td><td>Koordinator Tim / Manajer Proyek</td><td></td><td></td></tr>
                <tr><td>2</td><td></td><td>Pelaksana Teknis / Produksi</td><td></td><td></td></tr>
                <tr><td>3</td><td></td><td>Quality Control & Finishing</td><td></td><td></td></tr>
                <tr><td>4</td><td></td><td>Dokumentasi & Portofolio Digital</td><td></td><td></td></tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 4. SOP / Checklist Keselamatan Kerja (K3)</h3>
        <table>
            <thead><tr><th style="width:5%;">No</th><th>Poin Keselamatan Kerja</th><th style="width:15%;">Sudah</th><th style="width:15%;">Belum</th></tr></thead>
            <tbody>
                <tr><td>1</td><td>Menggunakan alat pelindung diri (APD) sesuai kebutuhan</td><td>[ ]</td><td>[ ]</td></tr>
                <tr><td>2</td><td>Area kerja bersih, rapi, dan aman (menerapkan budaya kerja 5R)</td><td>[ ]</td><td>[ ]</td></tr>
                <tr><td>3</td><td>Alat digunakan sesuai prosedur/SOP pabrikan</td><td>[ ]</td><td>[ ]</td></tr>
                <tr><td>4</td><td>Bahan disimpan dan ditangani dengan benar sesuai MSDS/aturan</td><td>[ ]</td><td>[ ]</td></tr>
                <tr><td>5</td><td>Mematuhi tata tertib bengkel/studio/lab sekolah</td><td>[ ]</td><td>[ ]</td></tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 5. Jurnal Kerja</h3>
        <table>
            <thead><tr><th style="width:18%;">Pertemuan/Tanggal</th><th>Kegiatan yang Dilakukan</th><th>Kendala</th><th>Solusi/Tindak Lanjut</th><th style="width:12%;">Paraf Guru</th></tr></thead>
            <tbody>
                <tr><td>Pertemuan 1</td><td></td><td></td><td></td><td></td></tr>
                <tr><td>Pertemuan 2</td><td></td><td></td><td></td><td></td></tr>
                <tr><td>Pertemuan 3</td><td></td><td></td><td></td><td></td></tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 6. Checklist Quality Control (QC)</h3>
        <table>
            <thead><tr><th style="width:5%;">No</th><th>Kriteria Mutu Produk</th><th style="width:15%;">Sesuai</th><th style="width:15%;">Belum Sesuai</th><th>Catatan Perbaikan</th></tr></thead>
            <tbody>
                <tr><td>1</td><td>Kesesuaian dengan brief/pesanan klien</td><td>[ ]</td><td>[ ]</td><td></td></tr>
                <tr><td>2</td><td>Kualitas hasil/kerapian pengerjaan</td><td>[ ]</td><td>[ ]</td><td></td></tr>
                <tr><td>3</td><td>Kelengkapan fungsi dan bagian produk</td><td>[ ]</td><td>[ ]</td><td></td></tr>
                <tr><td>4</td><td>Ketepatan waktu penyelesaian</td><td>[ ]</td><td>[ ]</td><td></td></tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 7. Lembar Presentasi / Serah Terima Produk</h3>
        <table>
            <tbody>
                <tr><th style="width:30%;">Nama Produk/Jasa</th><td><?= $produk ?></td></tr>
                <tr><th>Diserahkan oleh</th><td>............................................................ (Perwakilan Siswa)</td></tr>
                <tr><th>Diterima oleh (klien)</th><td><?= $klien ?></td></tr>
                <tr><th>Tanggal serah terima</th><td>............................................................</td></tr>
                <tr><th>Catatan/tanggapan klien</th><td>...........................................................................................................</td></tr>
                <tr><th>Tanda tangan</th><td>Penyerah: ............................ &nbsp;&nbsp;&nbsp;&nbsp; Penerima: ............................</td></tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 8. Template Portofolio Digital</h3>
        <p><strong>Platform:</strong> <?= $portofolio ?></p>
        <table>
            <thead><tr><th style="width:30%;">Bagian</th><th>Isi yang Dilampirkan</th></tr></thead>
            <tbody>
                <tr><td>Profil/identitas peserta didik</td><td>Nama, NISN, foto profesional, serta peran dalam tim kerja TEFA.</td></tr>
                <tr><td>Judul proyek & konteks klien</td><td>Latar belakang order, profil klien, dan tujuan pembuatan karya.</td></tr>
                <tr><td>Dokumentasi proses (foto/video/file)</td><td>Foto *behind the scenes*, catatan revisi, dan log pengerjaan.</td></tr>
                <tr><td>Hasil akhir/produk</td><td>Foto produk studio resolusi tinggi atau demo video produk final.</td></tr>
                <tr><td>Deskripsi teknik & peran siswa</td><td>Uraian alat, software, formula, atau SOP yang diterapkan.</td></tr>
                <tr><td>Refleksi</td><td>Catatan kemandirian dan evaluasi perbaikan untuk pesanan berikutnya.</td></tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 9. Lembar Refleksi Peserta Didik</h3>
        <table>
            <tbody>
                <tr><th style="width:35%;">Saya sudah mampu</th><td>...........................................................................................................</td></tr>
                <tr><th>Hal yang paling saya pelajari</th><td>...........................................................................................................</td></tr>
                <tr><th>Kendala yang saya alami</th><td>...........................................................................................................</td></tr>
                <tr><th>Cara saya mengatasinya</th><td>...........................................................................................................</td></tr>
                <tr><th>Jika mengerjakan kembali, saya akan memperbaiki</th><td>...........................................................................................................</td></tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 10. Rubrik Penilaian</h3>
        <table>
            <thead><tr><th>Kriteria</th><th>Perlu Bimbingan (1)</th><th>Cukup (2)</th><th>Baik (3)</th><th>Sangat Baik (4)</th></tr></thead>
            <tbody>
                <tr><td>Analisis Brief</td><td>Belum memahami brief</td><td>Memahami sebagian</td><td>Memahami dengan tepat</td><td>Memahami & mengembangkan solusi</td></tr>
                <tr><td>Proses Produksi</td><td>Belum sesuai SOP</td><td>Sebagian sesuai SOP</td><td>Sesuai SOP</td><td>Sesuai SOP & efisien</td></tr>
                <tr><td>Kualitas Produk</td><td>Belum sesuai brief</td><td>Sebagian sesuai</td><td>Sesuai brief</td><td>Melebihi harapan brief</td></tr>
                <tr><td>Kolaborasi & Komunikasi</td><td>Pasif</td><td>Kadang aktif</td><td>Aktif & kooperatif</td><td>Memimpin & komunikatif</td></tr>
                <tr><td>Refleksi</td><td>Belum reflektif</td><td>Reflektif terbatas</td><td>Reflektif jelas</td><td>Reflektif & ada rencana perbaikan</td></tr>
            </tbody>
        </table>
        <p><em>Nilai akhir dapat dihitung sesuai kebijakan guru; skala di atas contoh yang dapat diedit, bukan ketentuan resmi.</em></p>
        <?php
        return ob_get_clean();
    }
}
```

---

## 9. Controller Orkestrasi AI & Prompt Engineering Kurikulum Merdeka (`api/generate.php`)

Prompt ini telah dikonfigurasi untuk secara otomatis membedakan apakah pengguna membuat RPP untuk mata pelajaran umum (misal Bahasa Inggris untuk resepsionis hotel) atau mata pelajaran kejuruan (misal servis CVT di TSM, peracikan salep di Farmasi, atau konfigurasi Mikrotik di TKJ):

```php
<?php
// api/generate.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/RateLimiter.php';
require_once __DIR__ . '/../engine/SmartGenerator.php';

// Rate Limiting: Maksimal 6 permintaan per 5 menit per IP
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (!RateLimiter::check($pdo, $ip, 'generate', 6, 300)) {
    http_response_code(429);
    echo json_encode(["ok" => false, "error" => "Terlalu banyak permintaan generate. Mohon tunggu 5 menit lagi."]);
    exit;
}

$apiKey = getenv('GEMINI_API_KEY') ?: 'YOUR_GEMINI_API_KEY_HERE';

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (!$input) {
    echo json_encode(["ok" => false, "error" => "Input formulir tidak valid"]);
    exit;
}

$useAi = !empty($apiKey) && $apiKey !== 'YOUR_GEMINI_API_KEY_HERE';
$generatedHtml = null;
$sumber = 'smart';
$aiError = null;

if ($useAi) {
    $prompt = "Anda adalah Konsultan Kurikulum Vokasi (SMK) Kemendikbudristek dan Fasilitator Nasional Teaching Factory (TEFA).\n"
            . "Buat dokumen RPP / Perencanaan Pembelajaran Mendalam Berbasis Teaching Factory (TEFA) LENGKAP dengan potongan kode HTML murni (tanpa tag <html>, <head>, <body>, atau blok pembungkus ```html).\n\n"
            . "PANDUAN KHUSUS KONTEN:\n"
            . "1. Jika mata pelajaran yang diminta adalah MATA PELAJARAN UMUM (seperti Bahasa Indonesia, Bahasa Inggris, Matematika, IPAS, Informatika, PPKn/Pancasila, Sejarah, PJOK, Seni Budaya, PKK), integrasikan konten kejuruan TEFA yang dipilih sebagai studi kasus/proyek nyata pembelajaran tersebut (misal Bahasa Inggris untuk Front Office Hotel, Matematika untuk HPP Produk TEFA, atau IPAS untuk pengolahan limbah/K3 bengkel).\n"
            . "2. Jika mata pelajaran yang diminta adalah MATA PELAJARAN KEJURUAN (TKJ, DKV, MPLB, Bisnis Digital, Akuntansi, Perbankan, TSM, Perhotelan, Farmasi), susun alur kerja riil industri mulai dari telaah brief pesanan, eksekusi teknis, quality control (QC), penyerahan ke klien, hingga portofolio digital.\n"
            . "3. Struktur Dokumen WAJIB mencakup:\n"
            . "   - Judul Utama: RENCANA PELAKSANAAN PEMBELAJARAN (RPP) & PERENCANAAN PEMBELAJARAN MENDALAM BERBASIS TEACHING FACTORY (TEFA)\n"
            . "   - Bagian A: IDENTITAS DAN KONTEKS PEMBELAJARAN (Tabel 2 kolom)\n"
            . "   - Bagian B: IDENTIFIKASI (1. Kesiapan Peserta Didik, 2. Karakteristik Materi, 3. Dimensi Profil Lulusan / DPL)\n"
            . "   - Bagian C: DESAIN PEMBELAJARAN (CP, TP, KKTP, Pemahaman Bermakna, Pertanyaan Pemantik, Praktik Pedagogis, Kemitraan, Lingkungan, Digital)\n"
            . "   - Bagian D: PENGALAMAN BELAJAR DAN SINTAKS TEFA (Kegiatan Awal, Kegiatan Inti [Memahami-Analisis Brief, Mengaplikasi-Produksi, Merefleksi-QC], Kegiatan Penutup)\n"
            . "   - Bagian E: INTEGRASI PORTOFOLIO DIGITAL\n"
            . "   - Bagian F: ASESMEN PEMBELAJARAN (Asesmen Awal, Formatif, Sumatif, Rubrik Unjuk Kerja TEFA, Rubrik Portofolio, Kolom Tanda Tangan)\n"
            . "   - Bagian G: LAMPIRAN (Wajib menyertakan 10 Lampiran lengkap: 1. LKPD Analisis Brief, 2. Lembar Perencanaan Bahan/Alat, 3. Pembagian Peran & Jadwal, 4. SOP K3, 5. Jurnal Kerja, 6. Checklist QC, 7. Berita Acara Serah Terima Klien, 8. Template Portofolio Digital, 9. Lembar Refleksi Diri, 10. Rubrik Penilaian Lengkap dengan tag <div class='page-break'></div> di setiap awal lampiran).\n\n"
            . "DATA MASUKAN DARI GURU:\n"
            . "- Satuan Pendidikan: " . ($input['satuan'] ?? '-') . "\n"
            . "- Nama Guru: " . ($input['guru'] ?? '-') . "\n"
            . "- Mata Pelajaran: " . ($input['mapel'] ?? '-') . "\n"
            . "- Program Keahlian: " . ($input['program'] ?? '-') . "\n"
            . "- Konsentrasi Keahlian: " . ($input['konsentrasi'] ?? '-') . "\n"
            . "- Fase / Kelas: " . ($input['fase'] ?? '-') . "\n"
            . "- Semester: " . ($input['semester'] ?? '-') . "\n"
            . "- Tahun Pelajaran: " . ($input['tahun'] ?? '-') . "\n"
            . "- Alokasi Waktu: " . ($input['alokasi'] ?? '-') . "\n"
            . "- Produk / Jasa TEFA: " . ($input['produk'] ?? '-') . "\n"
            . "- Klien Pemesan: " . ($input['klien'] ?? '-') . "\n"
            . "- Brief Pesanan: " . ($input['brief'] ?? '-') . "\n"
            . "- Sarana / Prasarana: " . ($input['sarana'] ?? '-') . "\n"
            . "- Platform Portofolio: " . ($input['portofolio'] ?? 'Google Sites');

    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

    $payload = [
        "contents" => [
            ["parts" => [["text" => $prompt]]]
        ],
        "generationConfig" => [
            "temperature" => 0.25,
            "maxOutputTokens" => 8192,
            "topP" => 0.8
        ]
    ];

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($response && $httpCode === 200) {
        $result = json_decode($response, true);
        $candidateText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        if (!empty($candidateText)) {
            $candidateText = preg_replace('/^```(?:html)?\s*/i', '', trim($candidateText));
            $candidateText = preg_replace('/\s*```$/i', '', $candidateText);
            
            $generatedHtml = $candidateText;
            $sumber = 'ai';
        } else {
            $aiError = "Respons AI kosong.";
        }
    } else {
        $aiError = "API Error (Code: {$httpCode}): " . ($curlErr ?: substr($response, 0, 150));
    }
}

if (empty($generatedHtml)) {
    $generatedHtml = SmartGenerator::generate($input);
    $sumber = 'smart';
}

echo json_encode([
    "ok"       => true,
    "html"     => $generatedHtml,
    "sumber"   => $sumber,
    "ai_error" => $aiError
]);
```

---

## 10. Endpoint Ekspor Dokumen Microsoft Word (`api/export-docx.php`)

Skrip ini menerima seluruh isi HTML dari elemen preview browser yang telah diedit oleh guru, lalu membungkusnya ke dalam format XML Word dengan ukuran kertas standar A4 portrait dan batas margin 1 inci:

```php
<?php
// api/export-docx.php

$htmlContent = $_POST['html'] ?? '';
$judul       = $_POST['judul'] ?? 'RPP-SINTESA-VOKASI';

if (empty($htmlContent)) {
    die("Konten dokumen tidak ditemukan.");
}

$filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $judul) . '.doc';

$wordHeader = '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" 
      xmlns:w="urn:schemas-microsoft-com:office:word" 
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="utf-8">
<title>' . htmlspecialchars($judul) . '</title>
<style>
@page {
    size: 21.0cm 29.7cm; /* Ukuran Kertas A4 */
    margin: 2.54cm 2.54cm 2.54cm 2.54cm; /* Margin 1 inci */
    mso-page-orientation: portrait;
}
body {
    font-family: "Times New Roman", Times, serif;
    font-size: 11pt;
    line-height: 1.35;
    color: #000000;
}
h1 { font-size: 13pt; text-align: center; margin: 2pt 0; text-transform: uppercase; font-weight: bold; }
h2 { font-size: 12pt; margin: 14pt 0 4pt; border-bottom: 1.5pt solid #000; padding-bottom: 2pt; font-weight: bold; }
h3 { font-size: 11pt; margin: 10pt 0 3pt; font-weight: bold; }
p, li { font-size: 11pt; text-align: justify; margin: 0 0 5pt; }
table {
    width: 100%;
    border-collapse: collapse;
    margin: 6pt 0 10pt;
    font-size: 10.5pt;
}
th, td {
    border: 1px solid #000000;
    padding: 4.5pt 6pt;
    vertical-align: top;
}
th {
    background-color: #f2f2f2;
    font-weight: bold;
}
table.ttd {
    margin-top: 20pt;
    border: none;
}
table.ttd td {
    border: none;
    text-align: center;
    width: 50%;
}
.page-break {
    page-break-before: always;
    mso-special-character: line-break;
}
</style>
</head>
<body>';

$wordFooter = '</body></html>';

header('Content-Type: application/vnd.ms-word; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Pragma: public');

echo $wordHeader . $htmlContent . $wordFooter;
exit;
```

---

## 11. Panduan Eksekusi Database SQL

Untuk menerapkan data seluruh jurusan dan mata pelajaran umum ke dalam database lokal maupun hosting:

1. Buka **phpMyAdmin** pada cPanel atau XAMPP (`http://localhost/phpmyadmin`).
2. Pilih basis data `sintesa_vokasi` yang telah dibuat.
3. Buka tab **SQL**, lalu salin dan tempelkan seluruh kode DDL pada **Bagian 2** dan perintah Seeding pada **Bagian 3**.
4. Klik tombol **Go / Kirim**.
5. Database kini telah terisi dengan 10 rumpun keahlian lengkap, siap menyajikan referensi dinamis tanpa perlu mengubah baris kode antarmuka preview Anda.