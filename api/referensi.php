<?php
// api/referensi.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT id, kategori, program_keahlian, mapel_default, konsentrasi_list, elemen_list, produk_list, jasa_list, klien_list 
                         FROM master_keahlian 
                         ORDER BY kategori DESC, program_keahlian ASC");
    $rows = $stmt->fetchAll();

    $data = [];
    foreach ($rows as $row) {
        $data[$row['program_keahlian']] = [
            'id'          => (int)$row['id'],
            'kategori'    => $row['kategori'],
            'mapel'       => is_array($row['mapel_default']) ? $row['mapel_default'] : (json_decode($row['mapel_default'], true) ?? []),
            'konsentrasi' => is_array($row['konsentrasi_list']) ? $row['konsentrasi_list'] : (json_decode($row['konsentrasi_list'], true) ?? []),
            'elemen'      => is_array($row['elemen_list']) ? $row['elemen_list'] : (json_decode($row['elemen_list'] ?? '[]', true) ?? []),
            'produk'      => is_array($row['produk_list']) ? $row['produk_list'] : (json_decode($row['produk_list'], true) ?? []),
            'jasa'        => is_array($row['jasa_list']) ? $row['jasa_list'] : (json_decode($row['jasa_list'], true) ?? []),
            'klien'       => is_array($row['klien_list']) ? $row['klien_list'] : (json_decode($row['klien_list'], true) ?? [])
        ];
    }

    echo json_encode(["ok" => true, "data" => $data], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
