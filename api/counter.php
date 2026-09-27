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
