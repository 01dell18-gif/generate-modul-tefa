<?php
// api/settings.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

$method = $_SERVER['REQUEST_METHOD'];
if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
}

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT key_name, key_value, description FROM app_settings");
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $val = $r['key_value'];
            // Mask API key for safety
            if ($r['key_name'] === 'gemini_api_key' && !empty($val)) {
                $val = substr($val, 0, 6) . '...' . substr($val, -4);
            }
            $settings[$r['key_name']] = [
                'value' => $val,
                'description' => $r['description']
            ];
        }
        echo json_encode(["ok" => true, "settings" => $settings, "has_gemini_key" => !empty(getGeminiApiKey($pdo))]);
    } catch (Exception $e) {
        echo json_encode(["ok" => false, "error" => $e->getMessage()]);
    }
} elseif ($method === 'POST' || $method === 'PUT') {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true);

    if (!$body || !is_array($body)) {
        echo json_encode(["ok" => false, "error" => "Payload tidak valid."]);
        exit;
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO app_settings (key_name, key_value) VALUES (?, ?) 
                               ON DUPLICATE KEY UPDATE key_value = VALUES(key_value)");

        // If SQLite, handle upsert
        if ($db_driver === 'sqlite') {
            $stmt = $pdo->prepare("INSERT INTO app_settings (key_name, key_value) VALUES (?, ?) 
                                   ON CONFLICT(key_name) DO UPDATE SET key_value = excluded.key_value");
        }

        foreach ($body as $key => $val) {
            // If masked key was sent back unchanged, skip updating it
            if ($key === 'gemini_api_key' && strpos($val, '...') !== false) {
                continue;
            }
            $stmt->execute([$key, (string)$val]);
        }
        $pdo->commit();

        echo json_encode(["ok" => true, "message" => "Pengaturan berhasil diperbarui."]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(["ok" => false, "error" => "Gagal memperbarui pengaturan: " . $e->getMessage()]);
    }
}
