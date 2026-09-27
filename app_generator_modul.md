# PRODUCT REQUIREMENT DOCUMENT (PRD) & SPESIFIKASI SISTEM LENGKAP
## SISTEM GENERATOR MODUL AJAR & RPP TEFA VOKASI (GEMA FYJ / SINTESA VOKASI)
- **Kode Proyek**: `app-generator-modul`
- **Versi**: `1.3.0-PROD`
- **Status Rilis**: Produksi / Stabil (Sinkronisasi Basis Data & Pengesahan Penuh)
- **Kategori**: *Web-based Intelligent Document Generator, Vocational Curriculum Engine & Automated Administration*
- **Target Pengguna**: Pendidik SMK (Guru Konsentrasi Keahlian & Guru Mata Pelajaran Umum), Tim Pengembang Kurikulum Sekolah, Instruktur Teaching Factory (TEFA), Waka Kurikulum, dan Kepala Sekolah.
- **Rujukan Standar Regulasi**: 
  1. **Keputusan Kepala BSKAP No. 032/H/KR/2024** (Capaian Pembelajaran Seluruh Konsentrasi Keahlian SMK Kurikulum Merdeka).
  2. **Kepmendikbudristek No. 244/M/2024** (Spektrum Keahlian dan Struktur Kurikulum SMK/MAK).
  3. **Permendikbudristek No. 12 Tahun 2024** (Standar Kurikulum Nasional & Pembelajaran Berbasis Teaching Factory).

---

## 1. RINGKASAN EKSEKUTIF (EXECUTIVE SUMMARY)

### 1.1 Latar Belakang & Permasalahan Lapangan
Penyusunan Rencana Pelaksanaan Pembelajaran (RPP) / Modul Ajar Mendalam berbasis **Teaching Factory (TEFA)** pada jenjang SMK/MAK sering menjadi beban administratif berat bagi guru vokasi. Hal ini disebabkan oleh keharusan mengintegrasikan multi-komponen yang saling silang:
1. **Capaian Pembelajaran (CP) & Elemen**: Menghubungkan capaian resmi BSKAP dengan elemen kompetensi spesifik yang diajarkan pada semester berjalan.
2. **Konteks Alur Industri Nyata (Order Riil)**: Mensimulasikan atau mengeksekusi pesanan nyata dari konsumen/klien (analisis brief, estimasi kebutuhan bahan & alat, pembagian peran kerja tim, kepatuhan K3, pelaksanaan SOP, kendali mutu QC, hingga serah terima produk/jasa).
3. **Instrumen Operasional Lengkap**: Guru dituntut menyertakan sedikitnya 10 jenis instrumen lampiran operasional (LKPD, SOP K3, Jurnal Progres, Lembar QC, Berita Acara, Rubrik Asesmen, dll.).
4. **Portofolio Digital Otentik**: Dokumentasi digital karya siswa yang dapat diakses publik oleh industri pasangan (DUDI).
5. **Standar Legalitas & Pengesahan Dokumen**: Keabsahan administrasi modul ajar memerlukan lembar pengesahan formal dua kolom (Kepala Sekolah & Guru Pengampu Kejuruan/TEFA lengkap dengan NIP, serta Tempat dan Tanggal Pengesahan resmi) yang tersimpan secara terpusat agar tidak perlu diketik ulang berulang kali.

### 1.2 Solusi Produk: GEMA FYJ
`app-generator-modul` (GEMA FYJ) adalah platform *zero-friction intelligent document generator* yang memungkinkan guru menyusun dokumen modul ajar TEFA komprehensif siap cetak dan siap akreditasi dalam hitungan detik. Keunggulan utama sistem:
- **Dual-Engine Orchestration**: Memadukan penalaran kontekstual **Google Gemini AI** (dengan *live search grounding*) dengan mesin cadangan deterministik (**Smart Fallback Engine**) berbasis matriks kurikulum SMK terverifikasi, sehingga sistem 100% selalu berfungsi tanpa risiko downtime.
- **Elemen Pembelajaran Dinamis**: Pemilihan Program Keahlian dan Konsentrasi Keahlian langsung memicu pemuatan Elemen Pembelajaran resmi sesuai Keputusan Kepala BSKAP No. 032/H/KR/2024.
- **Asisten AI Konteks TEFA & Pedagogis**: Bantuan instan satu klik untuk menyelaraskan Produk, Materi Teknis, Unit TEFA, Klien Pemesan, Detail Brief, Sarana Bengkel, Mitra DUDI, Kesiapan Siswa, CP Baku, dan Tujuan Pembelajaran (TP) terukur.
- **Standar Pengesahan & Kredensial Permanen**: Penyimpanan database terintegrasi untuk Nama dan NIP Kepala Sekolah, Nama dan NIP Guru Pengampu, Tempat serta Tanggal Pengesahan. Tersimpan secara instansional di `app_settings` dan otomatis mengalir ke formulir input serta lembar pengesahan Bagian F di pratinjau maupun ekspor Word.
- **True WYSIWYG Continuous A4 Paper**: Simulasi kanvas kertas A4 bersambung warna putih bersih (`#ffffff`) dengan margin 1 inci standar kementerian, mode sunting langsung (*contenteditable*), dan penangkal cache otomatis (*cache-busting auto-version*).
- **True WYSIWYG Word Exporter**: Kemampuan mengunduh hasil modul langsung ke dalam berkas Microsoft Word (`.doc`) dengan format margin, tabel, dan tanda tangan yang presisi tanpa pergeseran layout.
- **Katalog Referensi Nasional 18 Program Keahlian**: Pustaka komprehensif berisi referensi konsentrasi, mata pelajaran, elemen, dan produk TEFA dari 18 program keahlian lintas bidang vokasi (TIK, Desain, Otomotif, Mesin, Listrik, Bisnis, Pariwisata, Kesehatan, hingga Mapel Umum TEFA).

---

## 2. ARSITEKTUR TEKNOLOGI & INFRASTRUKTUR SISTEM

### 2.1 Tech Stack
- **Frontend Layer**:
  - Semantic HTML5 (Modular Section Form, Simulated A4 Article, Interactive Modals).
  - Vanilla CSS3 (CSS Grid Split-Screen Layout, Standard Block Viewport, CSS Custom Properties Design System, Responsive Media Queries, Print Engine).
  - Modern JavaScript ES6+ (Native Fetch API, DOM Reactive Handlers, Dynamic Auto-Suggest, LocalStorage API Key Persistence, Multi-Format Modal Controller).
- **Backend / API Service**:
  - PHP 8.1+ Native RESTful Architecture.
  - cURL Multi-Session Client (Mendukung integrasi Google Gemini API v1beta dengan Search Tools).
  - Prepared Statements PDO (PHP Data Objects) dengan proteksi transaksi ACID.
- **Database Engine**:
  - MySQL 8.0+ / MariaDB 10.4+ (InnoDB Engine, Charset `utf8mb4_unicode_ci`).
- **AI Engine**:
  - Google Gemini 1.5 Flash / 2.0 Flash / 1.5 Pro via REST API Endpoint dengan dukungan *Google Search Grounding* untuk pencarian DUDI nyata Indonesia.

### 2.2 Diagram Alur Sistem Terintegrasi (Dual-Engine Data Pipeline)

```
┌────────────────────────────────────────────────────────────────────────┐
│                        USER INTERFACE (PANEL KIRI)                     │
│  [Program Keahlian] ──► [Konsentrasi] ──► [Elemen Pembelajaran BSKAP]  │
│  [Konteks TEFA: Produk, Materi, Klien, Brief, Sarana, Mitra DUDI]      │
│  [Opsi Lanjutan: Asesmen Diagnostik Kesiapan, CP BSKAP, TP TEFA]       │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
           ┌────────────────────────┴────────────────────────┐
           ▼                                                 ▼
[Tombol: ✨ Saran AI / Hubungkan TEFA]             [Tombol: ⚡ Generate RPP TEFA]
           │                                                 │
           ▼                                                 ▼
   [api/suggest.php]                                 [api/generate.php]
           │                                                 │
           ├────────────► Cek Google Gemini API Key ◄────────┤
           │                      │                          │
    (Ada API Key)           (Ada API Key)              (Tanpa API Key)
           │                      │                          │
           ▼                      ▼                          ▼
[Gemini AI Assistant]   [Gemini AI RPP Generator]  [Smart Fallback Generator]
(Live Search Grounding) (10 Lampiran Operasional)  (Matriks Kurikulum BSKAP)
           │                      │                          │
           ▼                      ▼                          ▼
[Isi Field Otomatis]     [Return JSON HTML] ◄────────────────┘
                                  │
                                  ▼
                 [Render ke #preview (.paper-container)]
                 (Kanvas Kertas Putih Bersambung A4)
                                  │
                  ┌───────────────┴───────────────┐
                  ▼                               ▼
       [Simpan ke Basis Data]          [Unduh Microsoft Word]
          (api/save.php)                (api/export-docx.php)
```

---

## 3. DOKUMENTASI REKAYASA PROMPT AI (PROMPT ENGINEERING SPECIFICATION)

Aplikasi mengimplementasikan 3 tingkatan prompt rekayasa instruksional (*Instructional System Prompts*) yang dirancang khusus untuk menghasilkan keluaran berkualitas tinggi sesuai kurikulum vokasi Indonesia:

### 3.1 Prompt 1: Asisten Rekomendasi Terpadu Konteks TEFA (`api/suggest.php` - `all_tefa`)

Digunakan saat pengguna menekan tombol **[✨ Hubungkan Seluruh Konteks TEFA via AI]** atau tombol saran pada bidang Konteks TEFA:

```
[SYSTEM PROMPT]
Anda adalah Pakar Asisten AI Kurikulum SMK TEFA (Teaching Factory) & Kurikulum Merdeka Kemendikbudristek.
Gunakan bantuan pencarian internet jika diperlukan untuk memastikan akurasi data regulasi BSKAP dan data DUDI (Dunia Usaha / Dunia Industri) nyata di Indonesia.

DATA INPUT SAAT INI:
- Program Keahlian: {program}
- Konsentrasi Keahlian: {konsentrasi}
- Elemen Pembelajaran: {elemen}
- Fase/Kelas: {fase}
- Produk/Jasa Eksisting: {produk}
- Materi Teknis Eksisting: {materi}
- Unit TEFA Eksisting: {konteks_tefa}
- Klien Eksisting: {klien}
- Brief Order Eksisting: {brief}
- Sarana Eksisting: {sarana}
- Mitra DUDI Eksisting: {mitra}

TUGAS:
Rumuskan dan hubungkan seluruh parameter pesanan TEFA di atas agar saling selaras, realistis, dan mencerminkan alur kerja industri profesional nyata di Indonesia.
Mitra industri HARUS merupakan perusahaan DUDI nyata atau asosiasi profesi resmi di Indonesia (misal: Telkom, Astra, ADGI, APJII, dsb.).
Kembalikan HANYA format JSON valid berikut tanpa markdown pembungkus:
{
  "produk": "Nama produk fisik atau layanan jasa TEFA spesifik",
  "materi": "Topik teknis operasional yang mendasari pembuatan produk tersebut",
  "konteks_tefa": "Nama unit produksi / bengkel / studio TEFA di sekolah",
  "klien": "Profil pemesan riil (UMKM, industri mitra, instansi, atau komunitas)",
  "brief": "Rincian spesifikasi teknis pesanan, batas toleransi, volume order, dan tenggat waktu",
  "sarana": "Peralatan utama, mesin kerja, instrumen uji, dan software standar industri",
  "mitra": "Nama perusahaan industri rekanan (DUDI) atau asosiasi industri resmi di Indonesia"
}
```

### 3.2 Prompt 2: Asisten Rekomendasi Pedagogis CP, TP & Kesiapan (`api/suggest.php` - `all_cp_tp`)

Digunakan saat pengguna menekan tombol **[✨ Rumuskan CP, TP & Kesiapan via AI]**:

```
[SYSTEM PROMPT]
Anda adalah Konsultan Kurikulum Vokasi Direktorat SMK Kemendikbudristek.
DATA INPUT:
- Program Keahlian: {program}
- Konsentrasi Keahlian: {konsentrasi}
- Elemen Pembelajaran: {elemen}
- Produk/Jasa TEFA: {produk}
- Klien: {klien}

TUGAS:
1. Rumuskan kesiapan awal peserta didik dan asesmen diagnostik prasyarat.
2. Rumuskan rumusan resmi Capaian Pembelajaran (CP) elemen tersebut merujuk pada Keputusan Kepala BSKAP No. 032/H/KR/2024.
3. Rumuskan Tujuan Pembelajaran (TP) berbasis Taksonomi Bloom (C4-C6 / P3-P5) yang mengintegrasikan pengerjaan order TEFA secara profesional.

Kembalikan HANYA format JSON valid:
{
  "kesiapan": "Penjelasan penguasaan prasyarat awal dan pemetaan peran kerja siswa",
  "cp": "Rumusan Capaian Pembelajaran (CP) elemen sesuai regulasi resmi BSKAP",
  "tp": "Tujuan Pembelajaran kontekstual yang mengintegrasikan model TEFA dan kepuasan klien"
}
```

### 3.3 Prompt 3: Orkestrator RPP TEFA Lengkap + 10 Lampiran (`engine/GeminiClient.php`)

Digunakan pada tombol utama **[⚡ Generate RPP TEFA]**:

```
[SYSTEM PROMPT]
Anda adalah Perancang Pembelajaran Vokasi Senior (Master Teacher TEFA SMK Kurikulum Merdeka).
Tugas Anda adalah menyusun dokumen lengkap: RENCANA PELAKSANAAN PEMBELAJARAN (RPP) / PERENCANAAN PEMBELAJARAN MENDALAM BERBASIS TEACHING FACTORY (TEFA) dalam format HTML MURNI (hanya tag-tag konten seperti <h1>, <h2>, <h3>, <p>, <ul>, <ol>, <li>, <table>, <thead>, <tbody>, <tr>, <th>, <td>, <div class="page-break"></div>, dll. JANGAN menyertakan tag <html>, <head>, atau <body>, dan JANGAN membungkus dengan blok markdown ```html atau ```).

STRUKTUR DOKUMEN WAJIB:
- JUDUL UTAMA DOKUMEN (H1)
- BAGIAN A: IDENTITAS DAN KONTEKS PEMBELAJARAN (Tabel 17 baris mencakup Satuan, Guru, Mapel, Program, Konsentrasi, Elemen Pembelajaran, Fase, Semester, Tahun, Alokasi, Materi, Konteks TEFA, Produk, Klien, Brief, Sarana, Mitra DUDI, Platform Portofolio)
- BAGIAN B: IDENTIFIKASI (1. Kesiapan Siswa & Asesmen Diagnostik; 2. Karakteristik Materi & K3; 3. Centang DPL 1 s.d. 8)
- BAGIAN C: DESAIN PEMBELAJARAN (CP, TP, KKTP 5 poin, Pemahaman Bermakna, 4 Pertanyaan Pemantik, Praktik Pedagogis TEFA 6 Sintaks, Kemitraan DUDI, Lingkungan 5R, Pemanfaatan Digital)
- BAGIAN D: PENGALAMAN BELAJAR DAN SINTAKS TEFA (Tabel Sintaks 6 Langkah TEFA dengan Kolom Aktivitas Guru dan Peserta Didik)
- BAGIAN E: INTEGRASI PORTOFOLIO DIGITAL
- BAGIAN F: ASESMEN PEMBELAJARAN & PENGESAHAN:
  - Asesmen Diagnostik, Formatif, Sumatif
  - Rubrik Unjuk Kerja TEFA skala 4 & Rubrik Portofolio
  - **Tabel Tanda Tangan & Pengesahan Dokumen Resmi** (<table class="ttd">) dua kolom:
    - Kolom Kiri: Mengetahui, Kepala [Satuan Pendidikan], ruang tanda tangan, ( [Nama Kepala Sekolah] ), NIP. [NIP Kepala Sekolah].
    - Kolom Kanan: Disahkan di: [Tempat], Tanggal: [Tanggal Pengesahan], Guru Pengampu Kejuruan / TEFA, ruang tanda tangan, ( [Nama Guru Pengampu] ), NIP. [NIP Guru Pengampu].
- BAGIAN G: 10 LAMPIRAN OPERASIONAL LENGKAP (Pisahkan tiap lampiran dengan <div class="page-break"></div>):
  1. Lampiran 1: LKPD Analisis Brief Pesanan Klien
  2. Lampiran 2: Lembar Perencanaan Kebutuhan Bahan, Alat, dan Estimasi Biaya (HPP)
  3. Lampiran 3: Lembar Pembagian Peran Tim & Matriks Tanggung Jawab
  4. Lampiran 4: SOP Keselamatan Kerja & Checklist K3 Bengkel/Studio
  5. Lampiran 5: Jurnal Kerja Harian Siswa & Logbook Produksi
  6. Lampiran 6: Checklist Kendali Mutu (Quality Control) Standar Industri
  7. Lampiran 7: Berita Acara Serah Terima & Presentasi Produk ke Klien
  8. Lampiran 8: Template Dokumentasi Portofolio Digital
  9. Lampiran 9: Lembar Refleksi Diri & Umpan Balik Peserta Didik
  10. Lampiran 10: Rubrik Penilaian Portofolio Digital & Konversi Nilai
```

---

## 4. SKEMA BASIS DATA TERKINI (DATABASE DDL)

Database: `sintesa_vokasi` (MySQL 8.0+ / MariaDB)

```sql
CREATE DATABASE IF NOT EXISTS `sintesa_vokasi` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `sintesa_vokasi`;

-- ==========================================================
-- 1. TABEL RIWAYAT DOKUMEN RPP (rpp_generations)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `rpp_generations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `satuan_pendidikan` VARCHAR(255) NULL,
    `nama_guru` VARCHAR(255) NULL,
    `nip_guru` VARCHAR(50) NULL COMMENT 'NIP Guru Pengampu 18 digit',
    `nama_kepsek` VARCHAR(150) NULL COMMENT 'Nama Lengkap & Gelar Kepala Sekolah',
    `nip_kepsek` VARCHAR(50) NULL COMMENT 'NIP Kepala Sekolah 18 digit',
    `tempat_pengesahan` VARCHAR(100) NULL COMMENT 'Kota/Kabupaten pengesahan dokumen',
    `tanggal_pengesahan` VARCHAR(100) NULL COMMENT 'Tanggal pengesahan dokumen resmi',
    `mata_pelajaran` VARCHAR(255) NULL,
    `kategori_mapel` ENUM('kejuruan', 'umum') DEFAULT 'kejuruan',
    `program_keahlian` VARCHAR(150) NULL,
    `konsentrasi_keahlian` VARCHAR(150) NULL,
    `elemen_pembelajaran` VARCHAR(255) NULL,
    `fase_kelas` VARCHAR(100) NULL,
    `semester` ENUM('Ganjil', 'Genap') NULL,
    `tahun_pelajaran` VARCHAR(50) NULL,
    `alokasi_waktu` VARCHAR(100) NULL,
    `produk_jasa` VARCHAR(255) NULL,
    `klien` VARCHAR(255) NULL,
    `mitra_industri` VARCHAR(255) NULL,
    `platform_portofolio` VARCHAR(255) DEFAULT 'Google Sites',
    `brief` TEXT NULL,
    `raw_input` JSON NULL COMMENT 'Snapshot payload formulir asli pengguna',
    `html_content` MEDIUMTEXT NOT NULL COMMENT 'Payload dokumen HTML lengkap (A-G + 10 Lampiran)',
    `sumber` ENUM('ai', 'smart') DEFAULT 'ai' COMMENT 'Engine yang memproduksi hasil',
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_program` (`program_keahlian`),
    INDEX `idx_kategori` (`kategori_mapel`),
    INDEX `idx_sumber` (`sumber`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- 2. TABEL MASTER KEAHLIAN, ELEMEN & TEFA (master_keahlian)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `master_keahlian` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kategori` ENUM('kejuruan', 'umum') DEFAULT 'kejuruan',
    `program_keahlian` VARCHAR(150) NOT NULL UNIQUE,
    `mapel_default` JSON NOT NULL COMMENT 'Array referensi mata pelajaran',
    `konsentrasi_list` JSON NOT NULL COMMENT 'Array opsi konsentrasi',
    `elemen_list` JSON NULL COMMENT 'Object map: konsentrasi -> array elemen pembelajaran resmi BSKAP',
    `produk_list` JSON NOT NULL COMMENT 'Array referensi produk fisik TEFA',
    `jasa_list` JSON NOT NULL COMMENT 'Array referensi layanan jasa TEFA',
    `klien_list` JSON NOT NULL COMMENT 'Array profil klien sasaran',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- 3. TABEL RATE LIMITING (api_rate_limits)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `api_rate_limits` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) NOT NULL,
    `endpoint` VARCHAR(50) NOT NULL,
    `request_time` INT UNSIGNED NOT NULL,
    INDEX `idx_rate_lookup` (`ip_address`, `endpoint`, `request_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- 4. TABEL KONFIGURASI SISTEM & KREDENSIAL PENGESAHAN (app_settings)
-- ==========================================================
CREATE TABLE IF NOT EXISTS `app_settings` (
    `key_name` VARCHAR(100) PRIMARY KEY,
    `key_value` TEXT NULL,
    `description` VARCHAR(255) NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `app_settings` (`key_name`, `key_value`, `description`) VALUES
('gemini_api_key', '', 'Kunci Google Gemini API resmi (disimpan aman)'),
('ai_model', 'gemini-1.5-flash', 'Model AI Google Gemini aktif (1.5 Flash, 2.0 Flash, 1.5 Pro)'),
('ai_temperature', '0.25', 'Tingkat kreativitas respons AI (0.0 - 1.0)'),
('rate_limit_max', '6', 'Maksimal permintaan generate per jendela waktu per IP'),
('rate_limit_window', '300', 'Durasi jendela waktu pembatasan dalam detik (5 menit)'),
('default_satuan', 'SMK Negeri 1 Surabaya', 'Satuan Pendidikan / Nama Sekolah Default'),
('default_tempat_pengesahan', 'Surabaya', 'Kota / Tempat Pengesahan Dokumen'),
('default_nama_kepsek', 'Drs. H. M. Zainal Arifin, M.Pd.', 'Nama Lengkap & Gelar Kepala Sekolah Default'),
('default_nip_kepsek', '19680512 199403 1 007', 'NIP Kepala Sekolah Default (18 digit)'),
('default_nama_guru', 'Pendidik Pengampu, S.Pd.', 'Nama Lengkap & Gelar Guru Pengampu Mapel Default'),
('default_nip_guru', '19850720 201001 2 015', 'NIP Guru Pengampu Mapel Default (18 digit)'),
('default_tanggal_pengesahan', '26 September 2026', 'Tanggal Pengesahan Dokumen Modul Ajar Default');
```

---

## 5. SPESIFIKASI OPERASI CRUD & KONTRAK REST API LENGKAP

Berikut adalah seluruh kontrak rute endpoint RESTful yang aktif dan disinkronkan di dalam sistem:

### 5.1 RPP & Generator Endpoints

#### 1. Generate Dokumen RPP Modul Ajar
- **Endpoint**: `POST /api/generate.php`
- **Headers**: 
  - `Content-Type: application/json`
  - `X-Gemini-Key: [API_KEY_OPSIONAL]`
- **Request Body**:
  ```json
  {
    "satuan": "SMK Negeri 4 Yogyakarta",
    "tempat_pengesahan": "Yogyakarta",
    "nama_kepsek": "Drs. H. Sukamto, M.Pd.",
    "nip_kepsek": "19690412 199412 1 002",
    "guru": "Rina Anggraini, S.Sn., M.Ds.",
    "nip_guru": "19870824 201101 2 014",
    "tanggal_pengesahan": "18 Juli 2026",
    "program": "Desain Komunikasi Visual",
    "konsentrasi": "Desain Komunikasi Visual",
    "elemen": "Karya Desain Berbasis Vektor dan Bitmap",
    "mapel": "Desain Publikasi & Kemasan",
    "fase": "Fase F / Kelas XI",
    "semester": "Ganjil",
    "tahun": "2026/2027",
    "alokasi": "12 JP (3 pertemuan @ 4 JP)",
    "produk": "Desain Identitas Merek & Kemasan Box UMKM",
    "materi": "Prinsip Desain Kemasan, Tipografi Kemasan, dan Die-cut Dieline",
    "konteks_tefa": "Studio Kreatif TEFA Visual Arts SMKN 4",
    "klien": "UMKM Binaan Keripik Tempe Mbok Nem",
    "brief": "Pembuatan packaging box ramah lingkungan dengan toleransi cetak presisi",
    "sarana": "Studio Komputer Grafis, Pen Tablet Wacom, Software Adobe Illustrator, Printer Proof Digital",
    "mitra": "PT. Kreasi Grafika Nusantara",
    "portofolio": "Behance / Google Sites Portofolio Siswa DKV",
    "kesiapan": "Peserta didik memahami operasi software grafis vektor",
    "cp": "",
    "tp": "",
    "api_key": ""
  }
  ```
- **Response Success (HTTP 200)**:
  ```json
  {
    "ok": true,
    "html": "<h1>RENCANA PELAKSANAAN PEMBELAJARAN...</h1>...<table class=\"ttd\">...</table>...",
    "sumber": "ai",
    "ai_error": null
  }
  ```

#### 2. Asisten Rekomendasi Instan AI & Grounding
- **Endpoint**: `POST /api/suggest.php`
- **Request Body**:
  - `field`: `"all_tefa"` | `"all_cp_tp"` | `"produk"` | `"materi"` | `"konteks_tefa"` | `"klien"` | `"brief"` | `"sarana"` | `"mitra"` | `"kesiapan"` | `"cp"` | `"tp"`
  - `program`, `konsentrasi`, `elemen`, `fase`, dll.
- **Response Success (HTTP 200)**:
  ```json
  {
    "ok": true,
    "field": "all_tefa",
    "suggestions": {
      "produk": "Desain Identitas Merek & Kemasan Produk UMKM (Packaging Box & Label Stiker)",
      "materi": "Perancangan Layout Kemasan, Dieline Die-Cut, Color Management CMYK, dan Mockup 3D",
      "konteks_tefa": "Unit Produksi DKV Creative Studio & Printing Hub SMK",
      "klien": "UMKM Makanan Ringan & Minuman Herbal Sekitar Sekolah",
      "brief": "Redesain kemasan box lipat food-grade 300gsm dan stiker label tahan air siap cetak dalam 5 hari kerja",
      "sarana": "PC Studio Grafis, Drawing Tablet, Software Desain Vektor, Printer Color Proof, Cutting Mat",
      "mitra": "PT. Kreasi Grafika Nusantara / Asosiasi Desainer Grafis Indonesia (ADGI)"
    },
    "sumber": "ai"
  }
  ```

#### 3. Simpan Dokumen RPP ke Riwayat Basis Data
- **Endpoint**: `POST /api/save.php`
- **Request Body**: `{"input": {...}, "html": "...", "sumber": "ai"}`
- **Fungsi**: Mengekstraksi `nama_kepsek`, `nip_kepsek`, `nip_guru`, `tempat_pengesahan`, `tanggal_pengesahan` dari objek `input` dan menyimpannya ke kolom basis data tersendiri di tabel `rpp_generations` serta dalam bentuk snapshot `raw_input` (JSON).
- **Response Success (HTTP 200)**: `{"ok": true, "message": "Dokumen berhasil disimpan.", "id": 15, "total": 13}`

#### 4. Ambil Riwayat Dokumen RPP (Pagination & Filter)
- **Endpoint**: `GET /api/rpp.php?page=1&limit=10&search=DKV`
- **Response Success (HTTP 200)**:
  ```json
  {
    "ok": true,
    "data": [
      {
        "id": 15,
        "satuan_pendidikan": "SMK Negeri 4 Yogyakarta",
        "nama_guru": "Rina Anggraini, S.Sn., M.Ds.",
        "nip_guru": "19870824 201101 2 014",
        "nama_kepsek": "Drs. H. Sukamto, M.Pd.",
        "nip_kepsek": "19690412 199412 1 002",
        "tempat_pengesahan": "Yogyakarta",
        "tanggal_pengesahan": "18 Juli 2026",
        "mata_pelajaran": "Desain Publikasi & Kemasan",
        "program_keahlian": "Desain Komunikasi Visual",
        "sumber": "ai",
        "created_at": "2026-09-27 00:45:00"
      }
    ],
    "pagination": { "current_page": 1, "per_page": 10, "total_records": 13, "total_pages": 2 }
  }
  ```

#### 5. Ambil Detail Dokumen RPP by ID
- **Endpoint**: `GET /api/rpp.php?id=15`
- **Response Success (HTTP 200)**: 
  ```json
  { 
    "ok": true, 
    "data": { 
      "id": 15, 
      "satuan_pendidikan": "SMK Negeri 4 Yogyakarta",
      "nama_guru": "Rina Anggraini, S.Sn., M.Ds.",
      "nip_guru": "19870824 201101 2 014",
      "nama_kepsek": "Drs. H. Sukamto, M.Pd.",
      "nip_kepsek": "19690412 199412 1 002",
      "tempat_pengesahan": "Yogyakarta",
      "tanggal_pengesahan": "18 Juli 2026",
      "raw_input": {...}, 
      "html_content": "...", 
      "sumber": "ai" 
    } 
  }
  ```

#### 6. Update Suntingan Dokumen RPP (In-Place Save)
- **Endpoint**: `PUT /api/rpp.php?id=15` (atau `POST` dengan override header)
- **Request Body**: `{"html_content": "<h1>RPP Diperbarui...</h1>"}`
- **Response Success (HTTP 200)**: `{"ok": true, "message": "Dokumen RPP berhasil diperbarui."}`

#### 7. Hapus Dokumen RPP
- **Endpoint**: `DELETE /api/rpp.php?id=15`
- **Response Success (HTTP 200)**: `{"ok": true, "message": "Dokumen berhasil dihapus."}`

#### 8. Ekspor ke Dokumen Microsoft Word (.doc)
- **Endpoint**: `POST /api/export-docx.php`
- **Request Form Body**: `html` (String HTML), `judul` (Nama berkas)
- **Response Header**: `Content-Type: application/vnd.ms-word; charset=utf-8`

---

### 5.2 Master Data & Referensi Keahlian Endpoints

#### 1. Ambil Referensi Dinamis Lengkap (Dropdown Populator)
- **Endpoint**: `GET /api/referensi.php`
- **Fungsi**: Memuat seluruh 18 program keahlian, daftar konsentrasi, elemen resmi BSKAP, dan rekomendasi TEFA ke form antarmuka.

#### 2. Kelola Master Keahlian (`/api/admin/keahlian.php`)
- **GET**: Menampilkan seluruh data master program keahlian untuk tabel modal admin.
- **POST**: Menambah program keahlian baru ke tabel `master_keahlian`.
- **PUT**: Memperbarui program keahlian yang ada.
- **DELETE**: Menghapus program keahlian dari database.

---

### 5.3 Metrik & Pengaturan Sistem Endpoints

#### 1. Counter Global Dokumen
- **Endpoint**: `GET /api/counter.php`
- **Response**: `{"ok": true, "total": 13}`

#### 2. Analitik Produksi Modul
- **Endpoint**: `GET /api/admin/analytics.php`
- **Response**: `{"ok": true, "metrics": {"total_generated": 13, "source_ai_percentage": 75.0, "source_smart_fallback_percentage": 25.0, "popular_majors": [...], "recent_generations": [...]}}`

#### 3. Konfigurasi Google Gemini & Standar Pengesahan Dokumen
- **Endpoint**: `GET /api/settings.php`
- **Response**: 
  ```json
  {
    "ok": true, 
    "settings": {
      "gemini_api_key": { "value": "AIzaSy...4aX9" }, 
      "ai_model": { "value": "gemini-1.5-flash" },
      "ai_temperature": { "value": "0.25" },
      "rate_limit_max": { "value": "6" },
      "default_satuan": { "value": "SMK Negeri 1 Surabaya" },
      "default_tempat_pengesahan": { "value": "Surabaya" },
      "default_nama_kepsek": { "value": "Drs. H. M. Zainal Arifin, M.Pd." },
      "default_nip_kepsek": { "value": "19680512 199403 1 007" },
      "default_nama_guru": { "value": "Pendidik Pengampu, S.Pd." },
      "default_nip_guru": { "value": "19850720 201001 2 015" },
      "default_tanggal_pengesahan": { "value": "26 September 2026" }
    }, 
    "has_gemini_key": true
  }
  ```
- **Endpoint**: `POST /api/settings.php`
- **Request Body**: 
  ```json
  {
    "gemini_api_key": "AIzaSy...", 
    "ai_model": "gemini-1.5-flash", 
    "ai_temperature": 0.25, 
    "rate_limit_max": 6,
    "default_satuan": "SMK Negeri 4 Yogyakarta",
    "default_tempat_pengesahan": "Yogyakarta",
    "default_nama_kepsek": "Drs. H. Sukamto, M.Pd.",
    "default_nip_kepsek": "19690412 199412 1 002",
    "default_nama_guru": "Rina Anggraini, S.Sn., M.Ds.",
    "default_nip_guru": "19870824 201101 2 014",
    "default_tanggal_pengesahan": "18 Juli 2026"
  }
  ```

---

## 6. ARSITEKTUR TAMPILAN WYSIWYG & SOLUSI KANVAS PUTIH KERTAS A4

### 6.1 Diagnosis Masalah Pemotongan Layar Sebelumnya
Pada rilis awal, terdapat keluhan di mana teks modul ajar terpotong tepat di batas halaman pertama (setelah Bagian B: Identifikasi Kesiapan Siswa), dan teks lanjutan jatuh ke latar belakang gelap `#1a202c` sehingga teks hitam menjadi sulit terbaca.

**Akar Masalah Teknis**:
1. Elemen wadah kertas `.paper-container` berada di dalam kontainer flexbox baris (`display: flex; justify-content: center;`) dengan properti `min-height: 297mm`.
2. Pada spesifikasi Flexbox dengan kontainer *scrolling* (`overflow-y: auto`), perataan *cross-axis* (`align-items: stretch`) mengunci tinggi fisik elemen `.paper-container` tepat pada tinggi satu halaman A4 (297mm = 1122.5px).
3. Konten dokumen RPP TEFA yang sangat panjang (10+ halaman) meluap (*overflow*) keluar dari batas wadah kertas putih.
4. Di sisi klien, peramban menyimpan berkas CSS lama di *cache* sehingga pembaruan tidak langsung terlihat.

### 6.2 Solusi Desain Bersih (*Bulletproof White Canvas Architecture*)
1. **Pemisahan dari Flex Cross-Axis Clamping**:
   - Kontainer `.paper-viewport` diubah menjadi *block formatting context* murni (`display: block !important; text-align: center;`).
   - Wadah kertas `#preview` diatur sebagai elemen blok tengah (`display: block !important; margin: 0 auto 60px auto !important; width: 210mm !important; height: auto !important; min-height: 297mm !important;`).
   - Sifat blok standar menjamin tinggi elemen kertas selalu bertambah secara otomatis sesuai tinggi seluruh anak elemennya tanpa batas.
2. **Prioritas Gaya Inline pada `#preview`**:
   - Diterapkan langsung pada tag `<article id="preview" ...>` di `index.php` untuk memastikan gaya kanvas putih memiliki spesifisitas tertinggi dan aktif seketika:
     ```html
     style="background:#ffffff !important; background-color:#ffffff !important; width:210mm !important; min-height:297mm !important; height:auto !important; display:block !important; margin:0 auto 60px auto !important; box-sizing:border-box !important;"
     ```
3. **Pemberantasan Cache Peramban (*Automatic Cache-Busting*)**:
   - Penambahan header HTTP anti-cache pada `index.php`:
     ```php
     header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
     header("Pragma: no-cache");
     header("Expires: 0");
     ```
   - Penyematan query timestamp dinamis berbasis waktu modifikasi berkas fisik (`filemtime`):
     ```html
     <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(...) ?>">
     <script src="assets/js/app.js?v=<?= filemtime(...) ?>"></script>
     ```
4. **Penegakan Dinamis Berkelanjutan (*Runtime DOM Enforcement*)**:
   - Pada `assets/js/app.js`, setiap kali fungsi `generateRpp()` dan `loadRppIntoEditor()` mengubah `preview.innerHTML`, skrip secara terprogram memastikan atribut kanvas putih tetap membungkus seluruh dokumen hingga ke baris terbawah Lampiran 10.

### 6.3 Sub-sistem Pengesahan Dokumen & Pengaturan Instansional (v1.3.0-PROD)
Untuk memastikan keabsahan formal modul ajar saat diajukan ke dinas pendidikan, pengawas sekolah, maupun asesor akreditasi, sistem mengimplementasikan sub-sistem pengesahan terpadu:
1. **Bidang Formulir Input (Section 1: Identitas Satuan, Pengesahan & Guru)**:
   - `satuan` (`#inpSatuan`): Nama resmi satuan pendidikan / SMK.
   - `tempat_pengesahan` (`#inpTempatPengesahan`): Kota/kabupaten pengesahan dokumen (misal: "Surabaya", "Yogyakarta").
   - `nama_kepsek` (`#inpNamaKepsek`): Nama lengkap beserta gelar akademik Kepala Sekolah.
   - `nip_kepsek` (`#inpNipKepsek`): NIP Kepala Sekolah 18 digit resmi (atau NIP/NIPPPK).
   - `guru` (`#inpGuru`): Nama lengkap beserta gelar akademik Guru Pengampu Mapel/Kejuruan.
   - `nip_guru` (`#inpNipGuru`): NIP Guru Pengampu 18 digit resmi.
   - `tanggal_pengesahan` (`#inpTanggalPengesahan`): Tanggal pengesahan dokumen formal (misal: "18 Juli 2026").
2. **Standar Instansional di Modal Pengaturan (Modal 4 - `app_settings`)**:
   - Administrator atau guru dapat menetapkan nilai *default* sekolah dan kredensial pengesahan yang disimpan secara permanen di database tabel `app_settings`.
   - Nilai default dimuat saat aplikasi pertama kali dibuka (`loadSettings()`) dan secara cerdas mengisi formulir jika field masih kosong.
   - Saat pengaturan disimpan, formulir aktif yang sedang terbuka seketika disinkronisasikan secara otomatis (*live auto-sync*).
   - Saat pengguna menekan tombol **Reset Formulir**, sistem secara otomatis mengembalikan nilai default sekolah dan tanda tangan tanpa mengharuskan pengguna mengetik ulang.
3. **Mekanisme Fallback Manual Penandatanganan**:
   - Jika field Kepala Sekolah atau NIP dikosongkan oleh pengguna, generator secara elegan merender garis titik-titik tanda tangan resmi (`( ……………………………………………… )` dan `NIP. ……………………………………………`), sehingga dokumen siap cetak tetap dapat ditandatangani manual menggunakan pena basah dan stempel basah sekolah.
4. **Sinkronisasi Presets Vokasi**:
   - Seluruh preset bawaan (DKV, TKJ, TSM, Farmasi, RPL, Mesin, Kuliner, Bahasa Inggris, Matematika) telah dilengkapi dengan kredensial penandatangan dan tempat pengesahan yang realistis. Khususnya preset DKV merefleksikan profil SMK Negeri 4 Yogyakarta dengan Guru Pengampu Rina Anggraini, S.Sn., M.Ds. dan Kepala Sekolah Drs. H. Sukamto, M.Pd.

---

## 7. MATRIKS RUJUKAN NASIONAL 18 PROGRAM KEAHLIAN (BSKAP 032/H/KR/2024)

Pustaka referensi sistem memuat 18 Program Keahlian SMK lengkap dengan Konsentrasi Keahlian, Elemen Pembelajaran resmi, dan contoh pesanan TEFA:

| No | Bidang & Program Keahlian | Konsentrasi Keahlian | Elemen Pembelajaran Terverifikasi BSKAP | Contoh Produk / Jasa TEFA |
| :---: | :--- | :--- | :--- | :--- |
| 1 | **Teknik Jaringan Komputer & Telekomunikasi** | Teknik Komputer dan Jaringan (TKJ) | Perencanaan dan Pengalamatan Jaringan; Pemasangan Perangkat Jaringan; Administrasi Server Jaringan; Sistem Keamanan Jaringan | Jasa Instalasi WiFi/LAN Kantor, Router Pre-Configured, Server Hotspot Voucher |
| 2 | **Pengembangan Perangkat Lunak & Gim (PPLG)** | Rekayasa Perangkat Lunak (RPL) | Pemrograman Berorientasi Objek; Pemodelan Basis Data; Pemrograman Web & Perangkat Bergerak; Quality Assurance | Aplikasi POS Kasir Toko, Web Company Profile, Jasa Pembuatan Landing Page |
| 3 | **Desain Komunikasi Visual (DKV)** | Desain Grafis | Prinsip Desain dan Tipografi; Desain Publikasi dan Kemasan Produk; Fotografi & Digital Imaging; Branding Identitas Visual | Desain Kemasan Box UMKM, Logo & Brand Guidelines, Jasa Foto Katalog Produk |
| 4 | **Animasi** | Animasi 2D / 3D | Konsep Cerita & Storyboarding; Digital 2D Animation; 3D Modeling & Texturing; Compositing & Rendering | Video Iklan Animasi Pendek UMKM, Stiker WhatsApp Kustom, Asset 3D Game |
| 5 | **Teknik Mesin** | Teknik Pemesinan | Gambar Teknik Manufaktur CAD; Pemesinan Bubut Konvensional; Pemesinan Frais; Pemesinan CNC & CAM | Poros Bertingkat, Roda Gigi Mesin Industri, Bracket Logam Presisi |
| 6 | **Teknik Pengelasan & Fabrikasi Logam** | Teknik Pengelasan | Teknik Pengelasan SMAW (1G-3G); Pengelasan GMAW/MIG; Pengelasan GTAW/TIG; Fabrikasi Konstruksi Logam | Pagar & Kanopi Minimalis, Rangka Meja Belajar Besi, Teralis Jendela Custom |
| 7 | **Teknik Ketenagalistrikan** | Teknik Instalasi Tenaga Listrik (TITL) | Standar PUIL & K3 Listrik; Instalasi Penerangan Bangunan; Instalasi Tenaga 3 Fasa; Pengendali Motor Listrik (PLC) | Panel Kontrol Pompa Otomatis, Panel Distribusi Listrik Toko, Jasa Cuci AC |
| 8 | **Teknik Otomotif** | Teknik Sepeda Motor (TSM) | Perawatan Mesin Motor; Perawatan Sasis & Suspensi; Sistem Pemindah Tenaga (CVT); Kelistrikan Motor | Jasa Servis CVT & Tune Up, Ganti Oli Express, Throttle Body Cleaner |
| 9 | **Teknik Elektronika** | Teknik Audio Video (TAV) | Penerapan Rangkaian Elektronika; Perencanaan Sistem Audio; Perbaikan Sistem Televisi; Sistem Kendali Mikroprosesor | Modul Penguat Audio, Running Text LED Display, Jasa Servis Alat Elektronik |
| 10 | **Manajemen Perkantoran & Layanan Bisnis** | Manajemen Perkantoran (MPLB) | Pengelolaan Kearsipan Digital; Komunikasi di Tempat Kerja; Pengelolaan Rapat & Notulensi; Layanan Pelanggan | Digitalisasi Dokumen Arsip Cloud, Notulensi Rapat Profesional, SOP Kantor |
| 11 | **Akuntansi & Keuangan Lembaga** | Akuntansi | Komputer Akuntansi (Spreadsheet/MYOB); Akuntansi Keuangan SAK EMKM; Administrasi Perpajakan; Akuntansi Perbankan | Buku Kas Digital Excel, Jasa Pembukuan Keuangan UMKM, Pelaporan SPT Tahunan |
| 12 | **Pemasaran** | Bisnis Digital | Digital Marketing & Copywriting; Pengelolaan Marketplace; Customer Relationship Management; Live Streaming Commerce | Katalog Produk Flipbook, Pengelolaan Konten Medsos Toko, Live Streaming Host |
| 13 | **Perhotelan** | Perhotelan | Layanan Front Office; Housekeeping & Pengelolaan Kamar; Laundry & Dry Cleaning; Food and Beverage Service | Voucher Reservasi Kamar Edotel, Jasa Laundry Kiloan Pakaian Dinas, Table Setting |
| 14 | **Kuliner** | Kuliner | Pengolahan Makanan Kontinental & Oriental; Pengolahan Pastry & Bakery; Pengelolaan Usaha Jasa Boga; Higiene Sanitasi | Paket Snack Box Rapat Kantor, Frozen Food Sehat, Kue Kering & Pastry |
| 15 | **Desain & Produksi Busana** | Tata Busana | Pembuatan Pola Manual & CAD; Teknik Menjahit Busana Wanita/Pria; Pembuatan Busana Kustom; Fashion Merchandising | Seragam Kerja Dinas Batik, Busana Pesta Kustom, Tote Bag Kanvas Sablon |
| 16 | **Kesehatan & Pekerjaan Sosial** | Farmasi Klinis & Komunitas | Pelayanan Farmasi & Peracikan Obat; Farmakologi; KIE Pasien; Manajemen Pengelolaan Obat di Apotek | Minuman Herbal Jahe Instan, Minyak Aromaterapi Roll-On, Hand Sanitizer Alami |
| 17 | **Agribisnis Tanaman** | Agribisnis Tanaman Pangan & Hortikultura | Penyiapan Media Tanam; Budidaya Tanaman Sayur & Buah; Pengendalian Hama Terpadu; Pascapanen & Pengemasan | Sayuran Hidroponik Segar, Bibit Buah Okulasi Unggul, Pupuk Kompos Organik |
| 18 | **Mata Pelajaran Umum SMK** | Integrasi TEFA Fase E & F | Analisis Teks Prosedur & Dokumen Industri; Komunikasi Bahasa Kerja Bilingual; Kalkulasi Biaya Produksi (HPP/BEP) | Dokumen Kontrak TEFA, Manual SOP Bilingual, Lembar Perhitungan HPP Otomatis |

---

## 8. RIWAYAT PERUBAHAN SISTEM (SYSTEM CHANGELOG & AUDIT TRAIL)

| Versi | Tanggal / Waktu | Modul Terdampak | Deskripsi Perubahan & Rasional Desain |
| :---: | :---: | :---: | :--- |
| **v1.0.0** | 2026-09-26 | Core App | Peluncuran rilis inisial aplikasi web generator modul TEFA dengan antarmuka split-screen, SmartGenerator 10 lampiran, dan ekspor Word. |
| **v1.1.0** | 2026-09-26 | Database & UI | Penambahan Elemen Pembelajaran di bawah Konsentrasi Keahlian, penyelarasan regulasi BSKAP 032/H/KR/2024, pembuatan Modal Katalog Referensi 18 Program Keahlian. |
| **v1.1.5** | 2026-09-26 | AI & Engine | Integrasi penuh Google Gemini API Client dengan Google Search Grounding pada endpoint `api/suggest.php` (Konteks TEFA terpadu dan CP/TP terpadu). |
| **v1.1.8** | 2026-09-27 | Settings & Security | Pembuatan modal konfigurasi Google Gemini API Key di database dan localStorage dengan masking keamanan, kontrol suhu kreativitas, dan rate limiting. |
| **v1.2.0** | 2026-09-27 | CSS, JS & Preview | **Perbaikan Kanvas Kertas A4 Bersambung**: Mengubah viewport menjadi layout blok independen (`margin: 0 auto; width: 210mm; height: auto !important; background: #ffffff !important;`) untuk mengeliminasi pemotongan tinggi dokumen (flexbox clamping). Penambahan *cache-busting auto-version* (`?v=<?= filemtime(...) ?>`) dan header `no-cache` pada `index.php` dan `app.js`. Penulisan dokumentasi master lengkap di `app_generator_modul.md`. |
| **v1.3.0-PROD** | 2026-09-27 | Database, API, Engine & UI | **Sub-sistem Pengesahan & Penyimpanan Database Kredensial**: Menambahkan 5 kolom basis data baru pada `rpp_generations` (`nama_kepsek`, `nip_kepsek`, `nip_guru`, `tempat_pengesahan`, `tanggal_pengesahan`) dan 7 kunci pengaturan instansional pada `app_settings`. Menambahkan input formulir Section 1, input default pada Modal Pengaturan, orkestrasi Dual-Engine (SmartGenerator & GeminiClient) dengan tabel tanda tangan dua kolom resmi, auto-fill dan sinkronisasi live saat simpan pengaturan, penanganan fallback dotted lines untuk tanda tangan manual, dan pengayaan seluruh preset kejuruan/umum. |

---

## 9. ATURAN PENERAPAN & KONSISTENSI PENGEMBANGAN (DEVELOPER GUIDELINES)

1. **Prinsip Fail-Safe Mutlak (Dual-Engine Policy)**:
   Setiap penambahan field atau logika baru di frontend WAJIB memiliki penanganan kembar:
   - Sisi AI: Terpetakan pada skema prompt di `engine/GeminiClient.php` dan `api/suggest.php`.
   - Sisi Fallback: Terpetakan pada fungsi substitusi nilai deterministik di `engine/SmartGenerator.php`.
2. **Kepatuhan Format Standar Cetak**:
   Struktur HTML yang dihasilkan di panel pratinjau `#preview` tidak boleh menggunakan elemen selain tag dokumen standar (`h1`, `h2`, `h3`, `p`, `table`, `ul`, `ol`, `li`, `div.page-break`) guna menjamin konversi yang sempurna saat diekspor ke Microsoft Word (.doc) atau dicetak ke PDF.
3. **Pencatatan Berkelanjutan**:
   Setiap kali ada penyesuaian fungsionalitas, endpoint, skema basis data, atau perilaku antarmuka, pengembang **wajib memperbarui dokumen `app_generator_modul.md` ini** secara komprehensif.