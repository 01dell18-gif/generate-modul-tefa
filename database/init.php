<?php
// database/init.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/seed_runner.php';

echo "=== GEMA FYJ DATABASE INITIALIZATION ===" . PHP_EOL;
echo "Driver in use: " . $db_driver . PHP_EOL;

try {
    seedMasterData($pdo, $db_driver);
    $count = $pdo->query("SELECT COUNT(*) FROM master_keahlian")->fetchColumn();
    echo "Master keahlian seeded successfully! Total rows: " . $count . PHP_EOL;

    $settingsCount = $pdo->query("SELECT COUNT(*) FROM app_settings")->fetchColumn();
    echo "App settings verified! Total rows: " . $settingsCount . PHP_EOL;
    echo "Database ready for GEMA FYJ." . PHP_EOL;
} catch (Exception $e) {
    echo "Error initializing: " . $e->getMessage() . PHP_EOL;
}
