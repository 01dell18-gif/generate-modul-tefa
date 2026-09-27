<?php
// api/admin/analytics.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/db.php';

try {
    // Total count
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM rpp_generations");
    $total = (int)$totalStmt->fetchColumn();

    // AI vs Smart count
    $aiStmt = $pdo->query("SELECT COUNT(*) FROM rpp_generations WHERE sumber = 'ai'");
    $aiCount = (int)$aiStmt->fetchColumn();
    $smartCount = max(0, $total - $aiCount);

    $aiPercent = $total > 0 ? round(($aiCount / $total) * 100, 1) : 0;
    $smartPercent = $total > 0 ? round(($smartCount / $total) * 100, 1) : 0;

    // Popular Majors
    $majorStmt = $pdo->query("SELECT program_keahlian AS program, COUNT(*) AS count 
                              FROM rpp_generations 
                              WHERE program_keahlian IS NOT NULL AND program_keahlian != ''
                              GROUP BY program_keahlian 
                              ORDER BY count DESC 
                              LIMIT 8");
    $popularMajors = $majorStmt->fetchAll();

    // Kategori Breakdown
    $katStmt = $pdo->query("SELECT kategori_mapel, COUNT(*) as count 
                            FROM rpp_generations 
                            GROUP BY kategori_mapel");
    $kategoriRows = $katStmt->fetchAll();
    $kategori = ['kejuruan' => 0, 'umum' => 0];
    foreach ($kategoriRows as $kr) {
        $k = $kr['kategori_mapel'] ?: 'kejuruan';
        $kategori[$k] = (int)$kr['count'];
    }

    // Recent 5
    $recStmt = $pdo->query("SELECT id, satuan_pendidikan, nama_guru, mata_pelajaran, program_keahlian, produk_jasa, sumber, created_at 
                            FROM rpp_generations 
                            ORDER BY id DESC 
                            LIMIT 5");
    $recent = $recStmt->fetchAll();

    echo json_encode([
        "ok" => true,
        "metrics" => [
            "total_generated"                  => $total,
            "source_ai_count"                  => $aiCount,
            "source_smart_count"               => $smartCount,
            "source_ai_percentage"             => $aiPercent,
            "source_smart_fallback_percentage" => $smartPercent,
            "popular_majors"                   => $popularMajors,
            "kategori_breakdown"               => $kategori,
            "recent_generations"               => $recent
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
