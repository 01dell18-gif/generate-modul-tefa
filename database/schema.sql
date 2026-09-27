-- ==========================================================
-- SINTESA VOKASI - Database Schema (DDL)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `sintesa_vokasi` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `sintesa_vokasi`;

-- 1. TABEL RIWAYAT DOKUMEN RPP (rpp_generations)
CREATE TABLE IF NOT EXISTS `rpp_generations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `satuan_pendidikan` VARCHAR(255) NULL,
    `nama_guru` VARCHAR(255) NULL,
    `nip_guru` VARCHAR(100) NULL,
    `nama_kepsek` VARCHAR(255) NULL,
    `nip_kepsek` VARCHAR(100) NULL,
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
    `materi` VARCHAR(255) NULL,
    `konteks_tefa` VARCHAR(255) NULL,
    `klien` VARCHAR(255) NULL,
    `mitra_industri` VARCHAR(255) NULL,
    `platform_portofolio` VARCHAR(255) DEFAULT 'Google Sites',
    `tempat_pengesahan` VARCHAR(150) NULL,
    `tanggal_pengesahan` VARCHAR(100) NULL,
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

-- 2. TABEL MASTER KEAHLIAN & TEFA (master_keahlian)
CREATE TABLE IF NOT EXISTS `master_keahlian` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kategori` ENUM('kejuruan', 'umum') DEFAULT 'kejuruan',
    `program_keahlian` VARCHAR(150) NOT NULL UNIQUE,
    `mapel_default` JSON NOT NULL COMMENT 'Array referensi mata pelajaran',
    `konsentrasi_list` JSON NOT NULL COMMENT 'Array opsi konsentrasi',
    `produk_list` JSON NOT NULL COMMENT 'Array referensi produk fisik TEFA',
    `jasa_list` JSON NOT NULL COMMENT 'Array referensi layanan jasa TEFA',
    `klien_list` JSON NOT NULL COMMENT 'Array profil klien sasaran',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABEL RATE LIMITING (api_rate_limits)
CREATE TABLE IF NOT EXISTS `api_rate_limits` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) NOT NULL,
    `endpoint` VARCHAR(50) NOT NULL,
    `request_time` INT UNSIGNED NOT NULL,
    INDEX `idx_rate_lookup` (`ip_address`, `endpoint`, `request_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABEL KONFIGURASI SISTEM (app_settings)
CREATE TABLE IF NOT EXISTS `app_settings` (
    `key_name` VARCHAR(100) PRIMARY KEY,
    `key_value` TEXT NULL,
    `description` VARCHAR(255) NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `app_settings` (`key_name`, `key_value`, `description`) VALUES
('ai_model', 'gemini-1.5-flash', 'Model AI Google Gemini yang digunakan'),
('ai_temperature', '0.25', 'Tingkat kreativitas respons AI (0.0 - 1.0)'),
('rate_limit_max', '6', 'Maksimal request generate per jendela waktu'),
('rate_limit_window', '300', 'Durasi jendela waktu pembatasan dalam detik (5 menit)'),
('gemini_api_key', '', 'Google Gemini API Key'),
('default_satuan', 'SMK Negeri 1 Surabaya', 'Satuan Pendidikan / Nama Sekolah Default'),
('default_nama_kepsek', 'Drs. H. M. Zainal Arifin, M.Pd.', 'Nama Lengkap & Gelar Kepala Sekolah'),
('default_nip_kepsek', '19680512 199403 1 007', 'NIP Kepala Sekolah'),
('default_nama_guru', 'Pendidik Pengampu, S.Pd.', 'Nama Lengkap & Gelar Guru Pengampu Mapel'),
('default_nip_guru', '19850720 201001 2 015', 'NIP Guru Pengampu Mapel'),
('default_tempat_pengesahan', 'Surabaya', 'Kota / Tempat Pengesahan Dokumen'),
('default_tanggal_pengesahan', '15 Juli 2026', 'Tanggal Pengesahan Dokumen Modul Ajar')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);
