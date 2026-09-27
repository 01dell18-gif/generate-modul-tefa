<?php
// api/generate.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../middleware/RateLimiter.php';
require_once __DIR__ . '/../engine/SmartGenerator.php';
require_once __DIR__ . '/../engine/GeminiClient.php';

// Rate Limiting: 6 requests per 5 minutes per IP
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (strpos($ip, ',') !== false) {
    $ip = trim(explode(',', $ip)[0]);
}

$rateLimitMax = (int)getAppSetting($pdo, 'rate_limit_max', 6);
$rateLimitWin = (int)getAppSetting($pdo, 'rate_limit_window', 300);

// Allow bypass rate limit if local dev or user sends their own custom API key
$hasUserKey = !empty($_SERVER['HTTP_X_GEMINI_KEY']);
if (!$hasUserKey && !RateLimiter::check($pdo, $ip, 'generate', $rateLimitMax, $rateLimitWin)) {
    http_response_code(429);
    echo json_encode([
        "ok" => false, 
        "error" => "Permintaan generate terlalu sering (Batas {$rateLimitMax}x per 5 menit). Mohon tunggu beberapa saat atau gunakan API Key Anda sendiri."
    ]);
    exit;
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (!$input || !is_array($input)) {
    echo json_encode(["ok" => false, "error" => "Data formulir masukan tidak valid."]);
    exit;
}

$apiKey = getGeminiApiKey($pdo);
// Allow passing api_key in payload
if (!empty($input['api_key'])) {
    $apiKey = trim($input['api_key']);
}

$model = getAppSetting($pdo, 'ai_model', 'gemini-1.5-flash');
$temperature = (float)getAppSetting($pdo, 'ai_temperature', 0.25);

$generatedHtml = null;
$sumber = 'smart';
$aiError = null;

if (!empty($apiKey)) {
    $aiRes = GeminiClient::generateRpp($input, $apiKey, $model, $temperature);
    if ($aiRes['ok'] && !empty($aiRes['html'])) {
        $generatedHtml = $aiRes['html'];
        $sumber = 'ai';
    } else {
        $aiError = $aiRes['error'] ?? 'Gagal memanggil Gemini AI.';
    }
} else {
    $aiError = 'Kunci API Gemini tidak disetel. Menggunakan Smart Generator cadangan.';
}

// Fallback to SmartGenerator if AI is not used or failed
if (empty($generatedHtml)) {
    $generatedHtml = SmartGenerator::generate($input);
    $sumber = 'smart';
}

echo json_encode([
    "ok"       => true,
    "html"     => $generatedHtml,
    "sumber"   => $sumber,
    "ai_error" => $aiError
], JSON_UNESCAPED_UNICODE);
