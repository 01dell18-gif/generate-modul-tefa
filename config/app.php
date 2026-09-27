<?php
// config/app.php

define('APP_NAME', 'GEMA FYJ');
define('APP_VERSION', '1.0.0-PROD');

// Retrieve settings from database or fallback to environment variables
function getAppSetting(PDO $pdo, string $key, $default = null) {
    try {
        $stmt = $pdo->prepare("SELECT key_value FROM app_settings WHERE key_name = ? LIMIT 1");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return ($val !== false && $val !== null && $val !== '') ? $val : $default;
    } catch (Throwable $e) {
        return $default;
    }
}

function getGeminiApiKey(PDO $pdo): string {
    // 1. Check HTTP header from request (user custom key in UI)
    $headerKey = $_SERVER['HTTP_X_GEMINI_KEY'] ?? '';
    if (!empty($headerKey)) {
        return trim($headerKey);
    }

    // 2. Check database app_settings
    $dbKey = getAppSetting($pdo, 'gemini_api_key', '');
    if (!empty($dbKey)) {
        return trim($dbKey);
    }

    // 3. Check server environment variable
    $envKey = getenv('GEMINI_API_KEY');
    if (!empty($envKey)) {
        return trim($envKey);
    }

    return '';
}
