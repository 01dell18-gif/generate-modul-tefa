# ⚡ GEMA FYJ - Generate Modul Pembelajaran Aktif
### *Sistem Generator Modul Ajar & RPP SMK Berbasis Teaching Factory (TEFA)*

[![Versi](https://img.shields.io/badge/Versi-1.3.0--PROD-blue.svg)](file:///d:/Coba/App%20Generate%20Modul/app_generator_modul.md)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777bb4.svg)](https://www.php.net/)
[![Database](https://img.shields.io/badge/Database-MySQL%20%7C%20SQLite%20(Dual--Driver)-orange.svg)](file:///d:/Coba/App%20Generate%20Modul/config/db.php)
[![AI Engine](https://img.shields.io/badge/AI-Google%20Gemini%20%2B%20Smart%20Engine-brightgreen.svg)](file:///d:/Coba/App%20Generate%20Modul/engine/)
[![Kurikulum](https://img.shields.io/badge/Kurikulum-Merdeka%20SMK%20(BSKAP%20032%2FH%2FKR%2F2024)-purple.svg)](https://kurikulum.kemdikbud.go.id/)

---

## 📖 Daftar Isi
1. [Tentang Aplikasi](#-tentang-aplikasi)
2. [Rujukan Regulasi Resmi](#-rujukan-regulasi-resmi)
3. [Fitur Unggulan](#-fitur-unggulan)
4. [Struktur Direktori Proyek](#-struktur-direktori-proyek)
5. [Persyaratan Sistem](#-persyaratan-sistem)
6. [Panduan Instalasi di PC Baru / PC Lain](#-panduan-instalasi-di-pc-baru--pc-lain)
   - [Metode 1: Menggunakan XAMPP di Windows (Rekomendasi)](#metode-1-menggunakan-xampp-di-windows-rekomendasi)
   - [Metode 2: Menggunakan PHP CLI & SQLite (Portabel / Ringan)](#metode-2-menggunakan-php-cli--sqlite-portabel--ringan)
7. [Panduan Penggunaan Aplikasi (Step-by-Step)](#-panduan-penggunaan-aplikasi-step-by-step)
8. [Panduan Upload & Sinkronisasi ke GitHub](#-panduan-upload--sinkronisasi-ke-github)
   - [Upload Pertama Kali (First Push)](#1-upload-pertama-kali-ke-github)
   - [Alur Update Rutin Setiap Ada Pembaruan](#2-alur-update-rutin-setiap-kali-ada-perubahan-kode)
9. [Troubleshooting & Solusi Kendala](#-troubleshooting--solusi-kendala)
10. [Catatan Pemeliharaan & Riwayat Pembaruan (Changelog)](#-catatan-pemeliharaan--riwayat-pembaruan)

---

## 📌 Tentang Aplikasi

**GEMA FYJ** (*Generate Modul Pembelajaran Aktif*) adalah aplikasi web cerdas yang dirancang khusus untuk mempermudah guru SMK dalam menyusun **Modul Ajar dan Rencana Pelaksanaan Pembelajaran (RPP) Mendalam berbasis Teaching Factory (TEFA)** sesuai standar Kurikulum Merdeka.

Aplikasi ini mengatasi kendala administratif guru dengan menghadirkan:
- Penyusunan modul lengkap siap cetak dan siap akreditasi hanya dalam beberapa klik.
- Pengintegrasian alur industri riil (pesanan/order nyata konsumen, analisis brief, HPP, pembagian peran kerja tim, K3, kendali mutu QC, hingga serah terima produk/jasa).
- **10 instrumen lampiran operasional lengkap** (LKPD, SOP K3, Jurnal Kerja, Lembar QC, Berita Acara, Rubrik Asesmen, Portofolio Digital, dll.).
- Lembar pengesahan formal dua kolom (Kepala Sekolah & Guru Pengampu lengkap dengan NIP dan tanggal pengesahan) yang tersimpan permanen.

---

## 🏛️ Rujukan Regulasi Resmi

Sistem dirancang mengacu pada standar resmi pemerintah Republik Indonesia:
1. **Keputusan Kepala BSKAP No. 032/H/KR/2024**: Capaian Pembelajaran (CP) seluruh Konsentrasi Keahlian SMK Kurikulum Merdeka.
2. **Kepmendikbudristek No. 244/M/2024**: Spektrum Keahlian dan Struktur Kurikulum SMK/MAK.
3. **Permendikbudristek No. 12 Tahun 2024**: Standar Kurikulum Nasional & Pembelajaran Berbasis Teaching Factory.

---

## ✨ Fitur Unggulan

- **Dual-Engine Orchestration (AI + Smart Fallback)**:
  - Menggunakan **Google Gemini AI** (1.5 Flash / 2.0 Flash / Pro) dengan fitur *live web search grounding* untuk mencari DUDI nyata Indonesia.
  - Dilengkapi **Smart Fallback Engine** berbasis matriks kurikulum BSKAP terverifikasi, sehingga aplikasi **tetap 100% berfungsi** meskipun tanpa kuota internet atau tanpa API key.
- **Katalog Terpadu 18 Program Keahlian SMK Nasional**:
  - Pilihan Program Keahlian, Konsentrasi Keahlian, dan Elemen Pembelajaran otomatis sinkron.
  - Mendukung bidang TIK, Bisnis, Otomotif, Mesin, Listrik, Elektronika, Pariwisata, Kuliner, Busana, Bangunan, Farmasi, hingga Mata Pelajaran Umum berbasis TEFA.
- **Asisten AI 1-Klik (Sparkle Assist)**:
  - Tombol *[✨ Hubungkan Seluruh Konteks TEFA via AI]* untuk mensinkronkan Produk, Materi, Unit TEFA, Klien, Brief, Sarana, dan Mitra Industri nyata.
  - Tombol *[✨ Rumuskan CP, TP & Kesiapan via AI]* untuk menghasilkan rumusan pedagogis baku.
- **True WYSIWYG A4 Continuous Paper Canvas**:
  - Tampilan pratinjau lembar kertas putih bersih A4 bersambung (`#ffffff`) dengan margin 1 inci standar dokumen resmi kementerian.
  - Mode sunting langsung (*inline contenteditable*) dengan toolbar pemformatan teks (Tebal, Miring, Garis Bawah, Heading, List Bullet/Angka).
- **True Word Exporter (.doc)**:
  - Unduh hasil modul ajar ke dokumen Microsoft Word siap cetak dengan tata letak, margin, tabel, dan tanda tangan presisi tanpa geser.
- **Dual-Driver Database (MySQL & SQLite)**:
  - Terhubung otomatis ke MySQL XAMPP jika tersedia.
  - Jika MySQL mati/belum disiapkan, sistem otomatis beralih ke SQLite (`data/sintesa_vokasi.sqlite`) tanpa konfigurasi manual!
- **Sistem Pengesahan Permanen**:
  - Identitas Satuan Pendidikan, Nama & NIP Kepala Sekolah, Nama & NIP Guru, serta Kota Pengesahan tersimpan di basis data dan otomatis mengisi modul baru berikutnya.
- **Manajemen Riwayat & Analitik**:
  - Simpan, buka kembali, salin kode HTML, dan lacak statistik dokumen yang pernah dibuat.

---

## 📁 Struktur Direktori Proyek

```text
App Generate Modul/
│
├── api/                        # REST API Endpoint Backend
│   ├── admin/                  # Endpoint Administrasi & Analitik
│   │   ├── analytics.php       # Data statistik penggunaan modul
│   │   └── keahlian.php        # Pengelolaan data master program keahlian
│   ├── counter.php             # Counter total RPP yang berhasil diproduksi
│   ├── export-docx.php         # Engine ekspor HTML ke Microsoft Word (.doc)
│   ├── generate.php            # Endpoint utama produksi RPP (Dual Engine)
│   ├── referensi.php           # Data referensi spektrum keahlian SMK
│   ├── rpp.php                 # CRUD dokumen RPP tersimpan (Buka/Hapus/Daftar)
│   ├── save.php                # Simpan & perbarui hasil suntingan ke database
│   ├── settings.php            # Pengaturan kunci API Gemini & konfigurasi
│   └── suggest.php             # Engine rekomendasi konteks TEFA & CP/TP via AI
│
├── assets/                     # Aset Antarmuka Pengguna (UI)
│   ├── css/
│   │   └── style.css           # Desain CSS modern, responsif, dan layout A4
│   └── js/
│       └── app.js              # Logika frontend, reactive DOM, dan AJAX
│
├── config/                     # Konfigurasi Inti
│   ├── app.php                 # Konstanta aplikasi & resolusi API Key
│   └── db.php                  # Dual-driver connector (MySQL auto-fallback SQLite)
│
├── database/                   # Skrip & Migrasi Database
│   ├── init.php                # Skrip CLI untuk inisialisasi basis data
│   ├── schema.sql              # Struktur tabel MySQL lengkap
│   ├── seed.sql                # Data seeder referensi keahlian format SQL
│   └── seed_runner.php         # Seeder otomatis PHP untuk 18 program keahlian
│
├── engine/                     # Mesin Produksi Modul (Core Generator)
│   ├── GeminiClient.php        # Integrasi cURL ke Google Gemini API v1beta
│   └── SmartGenerator.php      # Mesin cadangan deterministik tanpa internet/API key
│
├── middleware/
│   └── RateLimiter.php         # Proteksi keamanan pembatasan laju permintaan API
│
├── .gitignore                  # Berkas pengecualian Git (file lokal/sensitif)
├── .htaccess                   # Pengaturan konfigurasi web server Apache
├── index.php                   # Halaman antarmuka utama (Split Screen)
├── start_app.bat               # Skrip peluncur otomatis 1-klik di Windows
├── app_generator_modul.md      # Spesifikasi PRD dan dokumentasi teknis sistem
├── README.md                   # Panduan dokumentasi standar GitHub
└── REDME.md                    # Salinan panduan dokumentasi
```

---

## 🖥️ Persyaratan Sistem

Sebelum memasang aplikasi di PC baru, pastikan spesifikasi berikut terpenuhi:
1. **Sistem Operasi**: Windows 10/11, macOS, atau Linux.
2. **PHP**: Versi **8.1 atau lebih baru** (ekstensi aktif: `php-pdo`, `php-pdo_mysql`, `php-pdo_sqlite`, `php-curl`, `php-json`, `php-mbstring`).
3. **Database**: MySQL 8.0+ / MariaDB 10.4+ (Opsional jika ingin pakai MySQL; jika tidak ada, otomatis menggunakan SQLite lokal).
4. **Browser**: Google Chrome, Microsoft Edge, Mozilla Firefox, atau Safari (versi modern).
5. **Git**: Untuk keperluan clone dan update repositori dari GitHub.

---

## 🚀 Panduan Instalasi di PC Baru / PC Lain

Pilih salah satu metode instalasi di bawah ini sesuai kenyamanan Anda:

### Metode 1: Menggunakan XAMPP di Windows (Rekomendasi)

Metode ini sangat cocok bagi pengguna Windows karena sudah menyediakan PHP dan MySQL dalam satu paket.

#### Langkah 1: Pasang XAMPP
1. Download installer XAMPP dengan PHP 8.1+ dari situs resmi: [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP ke lokasi default: `C:\xampp`.

#### Langkah 2: Dapatkan Source Code Aplikasi
Buka Terminal / Command Prompt (CMD) di folder yang Anda inginkan (misalnya `D:\` atau `C:\xampp\htdocs`), lalu clone repositori:
```bash
git clone https://github.com/01dell18-gif/generate-modul-tefa.git
```
*(Atau salin seluruh folder proyek ini langsung menggunakan Flashdisk/LAN).*

#### Langkah 3: Jalankan Aplikasi dengan `start_app.bat` (1-Klik)
1. Buka folder proyek hasil clone/salinan.
2. Klik dua kali pada berkas **`start_app.bat`**.
3. Skrip otomatis akan:
   - Mendeteksi dan mengaktifkan service MySQL XAMPP di latar belakang.
   - Menjalankan Web Server lokal PHP di port `8080`.
   - Membuka browser secara otomatis menuju alamat: **`http://localhost:8080`**.
4. Biarkan jendela Command Prompt `start_app.bat` tetap terbuka selama Anda menggunakan aplikasi.

> [!TIP]
> **Opsi Alternatif melalui Apache XAMPP**:
> Jika Anda meletakkan folder proyek di dalam `C:\xampp\htdocs\app-generator-modul`, Anda cukup menyalakan modul Apache dan MySQL dari XAMPP Control Panel, lalu buka browser di alamat:
> `http://localhost/app-generator-modul`

---

### Metode 2: Menggunakan PHP CLI & SQLite (Portabel / Ringan)

Jika di PC tujuan tidak terpasang XAMPP atau Anda menginginkan instalasi portabel yang ringan:

1. Pastikan PHP 8.1+ sudah terpasang dan terdaftar di *Environment Variables (PATH)* PC Anda.
2. Buka Terminal / CMD di dalam folder proyek ini.
3. Jalankan perintah server bawaan PHP:
   ```bash
   php -S 127.0.0.1:8080
   ```
4. Buka browser dan akses **`http://localhost:8080`**.
5. Sistem akan secara otomatis mendeteksi bahwa MySQL tidak aktif dan langsung mengalihkan penyimpanan ke basis data **SQLite** di dalam folder `data/sintesa_vokasi.sqlite`. Seluruh tabel dan data master keahlian akan otomatis diinisialisasi (*auto-seed*).

---

## 💡 Panduan Penggunaan Aplikasi (Step-by-Step)

Berikut panduan langkah demi langkah menyusun dokumen Modul Ajar TEFA:

### 1. Buka Aplikasi di Browser
Akses `http://localhost:8080` di browser Anda. Layar akan terbagi menjadi dua bagian (*Split-Screen*):
- **Panel Kiri**: Formulir parameter input modul ajar & tombol bantuan AI.
- **Panel Kanan**: Kanvas pratinjau kertas A4 bersambung hasil generate.

### 2. Atur Kunci API Google Gemini (Opsional tapi Sangat Direkomendasikan)
Fitur AI cerdas dan pencarian industri internet memerlukan kunci Google Gemini API:
1. Dapatkan kunci API gratis di: [Google AI Studio](https://aistudio.google.com/app/apikey).
2. Di pojok kanan atas aplikasi, klik tombol **`⚙️ Pengaturan API`**.
3. Masukkan kunci API Anda pada kolom *Google Gemini API Key*, lalu klik **Simpan Pengaturan**.
4. Kunci tersimpan aman di database lokal / browser Anda.
*(Jika tidak mengisi kunci API, aplikasi tetap berfungsi normal menggunakan **Smart Fallback Generator** bawaan).*

### 3. Pilih Program, Konsentrasi & Elemen Pembelajaran
1. Pada **Section 2 (Program Keahlian & Alokasi)**, pilih salah satu dari 18 Program Keahlian (contoh: *Teknik Komputer dan Jaringan* atau *Rekayasa Perangkat Lunak*).
2. Pilih **Konsentrasi Keahlian** yang relevan.
3. Dropdown **Elemen Pembelajaran** akan otomatis memuat daftar elemen resmi BSKAP No. 032/H/KR/2024.
4. Anda juga bisa mengklik tombol cepat **Contoh Cepat (1-Klik)** di bagian atas formulir (seperti *TKJ Hotspot*, *RPL Web POS*, *DKV Branding*, dll.) untuk mengisi formulir secara instan.

### 4. Lengkapi Identitas Sekolah & Pengesahan
Pada **Section 1**:
- Masukkan Nama Satuan Pendidikan (misal: *SMK Negeri 1 Surabaya*).
- Masukkan Kota Pengesahan, Nama & NIP Kepala Sekolah, serta Nama & NIP Guru Pengampu.
- *Data ini akan tersimpan otomatis dan dicetak pada lembar pengesahan Bagian F.*

### 5. Gunakan Bantuan Asisten AI (1-Klik)
- **Hubungkan Seluruh Konteks TEFA**: Klik tombol **`✨ Hubungkan Seluruh Konteks TEFA via AI`** pada Section 3. AI akan mengisi Produk, Materi Teknis, Unit TEFA, Klien Riil, Rincian Brief, Sarana Bengkel, dan Mitra Industri DUDI nyata Indonesia secara selaras.
- **Rumuskan Pedagogis**: Buka menu akordion *Opsi Lanjutan*, lalu klik **`✨ Rumuskan CP, TP & Kesiapan via AI`** untuk menyusun capaian pembelajaran dan asesmen diagnostik otomatis.

### 6. Generate Dokumen
1. Klik tombol hijau besar di bagian bawah: **`⚡ Generate RPP TEFA`**.
2. Tunggu beberapa detik hingga proses penalaran selesai.
3. Dokumen lengkap (Bagian A sampai F + 10 Lampiran Operasional) akan dirender langsung di kanvas kertas A4 panel sebelah kanan.

### 7. Sunting Langsung di Pratinjau (*Inline Editing*)
- Dokumen pada panel kanan dapat langsung diklik dan diedit (*contenteditable*).
- Gunakan toolbar di atas kertas untuk memformat teks: **Tebal (B)**, **Miring (I)**, **Heading (H2/H3)**, atau membuat **Daftar Bullet/Angka**.
- Jika Anda melakukan perbaikan teks, klik tombol **`💾 Simpan`** pada toolbar untuk menyimpan hasil editan ke database.

### 8. Unduh Word (.doc) atau Cetak PDF
- **Unduh Microsoft Word**: Klik tombol biru **`📥 Unduh Word (.doc)`**. Dokumen akan terunduh dalam format Word dengan format margin 1 inci, font Calibri, tabel rapi, dan halaman terpisah persis seperti pratinjau.
- **Cetak ke Kertas / PDF**: Klik tombol **`🖨️ Cetak / PDF`** untuk mencetak langsung atau menyimpannya sebagai file `.pdf` siap edar.

---

## 🐙 Panduan Upload & Sinkronisasi ke GitHub

Repositori resmi proyek ini terhubung ke:
`https://github.com/01dell18-gif/generate-modul-tefa.git`

### 1. Upload Pertama Kali ke GitHub (Jika Membuat Repo Baru)

Jika Anda ingin mengunggah ke repositori GitHub baru dari nol, ikuti langkah berikut:

```bash
# 1. Buka terminal di folder proyek
cd "D:\Coba\App Generate Modul"

# 2. Konfigurasi identitas Git Anda (jika belum pernah)
git config --global user.name "Nama Anda"
git config --global user.email "emailanda@example.com"

# 3. Inisialisasi Git (jika belum ada folder .git)
git init

# 4. Pastikan file .gitignore sudah ada agar data lokal/rahasia tidak ikut terunggah
# 5. Tambahkan seluruh berkas ke staging area
git add .

# 6. Buat commit perdana
git commit -m "feat: rilis awal aplikasi generator modul tefa vokasi"

# 7. Tentukan branch utama sebagai 'main'
git branch -M main

# 8. Hubungkan remote repository GitHub
git remote add origin https://github.com/01dell18-gif/generate-modul-tefa.git

# 9. Push berkas ke GitHub
git push -u origin main
```

---

### 2. Alur Update Rutin (Setiap Kali Ada Perubahan Kode)

> [!IMPORTANT]
> **PENTING**: Setiap kali Anda selesai menambahkan fitur baru, memperbaiki bug, atau mengubah dokumen, lakukan langkah push ke GitHub berikut:

```bash
# 1. Cek status berkas apa saja yang telah diubah
git status

# 2. Masukkan seluruh perubahan ke Git staging
git add .

# 3. Buat catatan commit yang jelas tentang apa yang diubah
git commit -m "update: [tuliskan fitur atau perbaikan yang baru saja dibuat]"
# Contoh: git commit -m "fix: penyesuaian margin tabel pengesahan pada ekspor docx"

# 4. Kirim pembaruan ke repositori GitHub
git push origin main
```

#### Cara Mengambil Pembaruan Terbaru di PC Lain (Pull)
Jika Anda bekerja secara bergantian di beberapa PC, sebelum mulai mengedit di PC lain, tarik kode terbaru dengan perintah:
```bash
git pull origin main
```

---

## ❓ Troubleshooting & Solusi Kendala

| Gejala Masalah | Penyebab Umum | Solusi Cepat |
| :--- | :--- | :--- |
| **Port 8080 sudah digunakan aplikasi lain** | Port 8080 sedang dipakai software lain di PC Anda. | Edit file `start_app.bat`, ganti angka `8080` menjadi port lain (misal: `8090` atau `8888`), lalu simpan dan jalankan ulang. |
| **Tombol Generate memakai Smart Generator bukan AI** | Kunci API Gemini belum diatur atau koneksi internet terputus. | Buka menu `⚙️ Pengaturan API`, masukkan API Key Gemini yang valid. Jika kuota API habis, sistem akan otomatis beralih ke Smart Generator agar modul tetap dapat diproduksi. |
| **Pesan error koneksi database** | Service MySQL XAMPP belum aktif. | Buka XAMPP Control Panel dan klik tombol **Start** pada modul MySQL. Jika tetap gagal, biarkan sistem otomatis beralih ke SQLite lokal (`data/`). |
| **Tampilan tabel Word terpotong saat dibuka di MS Word** | Lebar kolom melebihi batas cetak. | Sistem telah menggunakan CSS khusus ekspor dengan lebar 100% dan margin 1 inci standar. Buka file di Microsoft Word dan pastikan tampilan dalam mode *Print Layout*. |
| **Perubahan CSS / JS tidak muncul di browser** | Cache browser masih menyimpan versi lama. | Lakukan *Hard Reload* dengan menekan **Ctrl + F5** (Windows) atau **Cmd + Shift + R** (Mac). Aplikasi sudah dilengkapi fitur *cache-busting* otomatis. |

---

## 📝 Catatan Pemeliharaan & Riwayat Pembaruan

> [!NOTE]
> **Prosedur Pemeliharaan Proyek**:
> Setiap kali ada penambahan fitur, perubahan skema database, atau penyesuaian aturan kurikulum:
> 1. Perbarui catatan di bagian **Riwayat Pembaruan** di bawah ini.
> 2. Perbarui nomor versi di `config/app.php` dan `index.php` jika diperlukan.
> 3. Lakukan `git add .`, buat commit deskriptif, dan lakukan `git push` ke GitHub.

### Riwayat Versi:
- **v1.3.1**:
  - Pembaruan judul resmi aplikasi menjadi **GEMA FYJ - Generate Modul Pembelajaran Aktif** secara konsisten di antarmuka web, skrip peluncur `start_app.bat`, header stylesheet/script, dan template dokumen ekspor Word.
- **v1.3.0-PROD (Maret 2026)**:
  - Penambahan integrasi dokumen legalitas & pengesahan formal dua kolom (Kepala Sekolah & Guru Pengampu).
  - Pustaka referensi nasional 18 program keahlian lengkap dengan elemen BSKAP 032/H/KR/2024.
  - Dukungan Dual-Driver Database (MySQL dengan auto-fallback cerdas ke SQLite).
  - Penyempurnaan True WYSIWYG A4 Continuous Paper Canvas dan True Word Exporter (.doc).
- **v1.2.0**:
  - Penambahan asisten AI terpadu untuk penyelarasan konteks TEFA dan formulasi CP/TP.
  - Integrasi 10 lampiran operasional lengkap (LKPD, SOP K3, QC, Berita Acara, Portofolio).
- **v1.0.0**:
  - Rilis awal fondasi sistem modul ajar vokasi berbasis Dual-Engine (Gemini AI + Fallback Engine).

---

**Dikelola dan Dikembangkan untuk:**
Kemajuan Pendidikan Vokasi SMK Indonesia.  
*Kurikulum Merdeka — Pembelajaran Mendalam — Teaching Factory (TEFA).*
