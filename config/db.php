<?php
// config/db.php
// Dual-Driver Database Connector (MySQL Primary with Auto-fallback to SQLite)

$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_NAME') ?: 'sintesa_vokasi';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$pdo = null;
$db_driver = 'mysql';

// 1. Try MySQL Connection
try {
    // Attempt connecting to the server first
    $initPdo = new PDO("mysql:host={$db_host};port={$db_port};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT            => 2,
    ]);
    
    // Ensure database exists
    $initPdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $initPdo = null;

    // Connect to specific database
    $pdo = new PDO("mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $db_driver = 'mysql';
} catch (Throwable $e) {
    // 2. Fallback to SQLite if MySQL is unavailable
    $db_driver = 'sqlite';
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        @mkdir($dataDir, 0777, true);
    }
    $sqliteFile = $dataDir . '/sintesa_vokasi.sqlite';
    try {
        $pdo = new PDO("sqlite:" . $sqliteFile, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec("PRAGMA foreign_keys = ON;");
    } catch (Throwable $sqle) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
        }
        echo json_encode(["ok" => false, "error" => "Koneksi database gagal (MySQL & SQLite): " . $sqle->getMessage()]);
        exit;
    }
}

// 3. Ensure essential tables exist (Auto-migration)
try {
    if ($db_driver === 'mysql') {
        $pdo->exec("
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
                `materi` VARCHAR(255) NULL,
                `konteks_tefa` VARCHAR(255) NULL,
                `klien` VARCHAR(255) NULL,
                `mitra_industri` VARCHAR(255) NULL,
                `platform_portofolio` VARCHAR(255) DEFAULT 'Google Sites',
                `brief` TEXT NULL,
                `raw_input` JSON NULL,
                `html_content` MEDIUMTEXT NOT NULL,
                `sumber` ENUM('ai', 'smart') DEFAULT 'ai',
                `ip_address` VARCHAR(45) NULL,
                `user_agent` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_program` (`program_keahlian`),
                INDEX `idx_kategori` (`kategori_mapel`),
                INDEX `idx_sumber` (`sumber`),
                INDEX `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `master_keahlian` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `kategori` ENUM('kejuruan', 'umum') DEFAULT 'kejuruan',
                `program_keahlian` VARCHAR(150) NOT NULL UNIQUE,
                `mapel_default` JSON NOT NULL,
                `konsentrasi_list` JSON NOT NULL,
                `produk_list` JSON NOT NULL,
                `jasa_list` JSON NOT NULL,
                `klien_list` JSON NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `api_rate_limits` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `ip_address` VARCHAR(45) NOT NULL,
                `endpoint` VARCHAR(50) NOT NULL,
                `request_time` INT UNSIGNED NOT NULL,
                INDEX `idx_rate_lookup` (`ip_address`, `endpoint`, `request_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `app_settings` (
                `key_name` VARCHAR(100) PRIMARY KEY,
                `key_value` TEXT NULL,
                `description` VARCHAR(255) NULL,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    } else {
        // SQLite Schema
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS rpp_generations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                satuan_pendidikan TEXT,
                nama_guru TEXT,
                mata_pelajaran TEXT,
                kategori_mapel TEXT DEFAULT 'kejuruan',
                program_keahlian TEXT,
                konsentrasi_keahlian TEXT,
                fase_kelas TEXT,
                semester TEXT,
                tahun_pelajaran TEXT,
                alokasi_waktu TEXT,
                produk_jasa TEXT,
                materi TEXT,
                konteks_tefa TEXT,
                klien TEXT,
                mitra_industri TEXT,
                platform_portofolio TEXT DEFAULT 'Google Sites',
                brief TEXT,
                raw_input TEXT,
                html_content TEXT NOT NULL,
                sumber TEXT DEFAULT 'ai',
                ip_address TEXT,
                user_agent TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS master_keahlian (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                kategori TEXT DEFAULT 'kejuruan',
                program_keahlian TEXT NOT NULL UNIQUE,
                mapel_default TEXT NOT NULL,
                konsentrasi_list TEXT NOT NULL,
                produk_list TEXT NOT NULL,
                jasa_list TEXT NOT NULL,
                klien_list TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS api_rate_limits (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_address TEXT NOT NULL,
                endpoint TEXT NOT NULL,
                request_time INTEGER NOT NULL
            );

            CREATE TABLE IF NOT EXISTS app_settings (
                key_name TEXT PRIMARY KEY,
                key_value TEXT,
                description TEXT,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    // Check if master_keahlian has data, if empty seed it automatically
    $cntStmt = $pdo->query("SELECT COUNT(*) FROM master_keahlian");
    if ((int)$cntStmt->fetchColumn() === 0) {
        require_once __DIR__ . '/../database/seed_runner.php';
        seedMasterData($pdo, $db_driver);
    }
} catch (Throwable $tblErr) {
    // Log error silently if table creation already happened
    error_log("Schema auto-migration notice: " . $tblErr->getMessage());
}
