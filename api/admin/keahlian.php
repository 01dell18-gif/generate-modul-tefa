<?php
// api/admin/keahlian.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->query("SELECT * FROM master_keahlian ORDER BY kategori DESC, program_keahlian ASC");
            $rows = $stmt->fetchAll();
            foreach ($rows as &$r) {
                $r['mapel_default']    = json_decode($r['mapel_default'], true) ?? [];
                $r['konsentrasi_list'] = json_decode($r['konsentrasi_list'], true) ?? [];
                $r['produk_list']      = json_decode($r['produk_list'], true) ?? [];
                $r['jasa_list']        = json_decode($r['jasa_list'], true) ?? [];
                $r['klien_list']       = json_decode($r['klien_list'], true) ?? [];
            }
            echo json_encode(["ok" => true, "data" => $rows]);
        } catch (Exception $e) {
            echo json_encode(["ok" => false, "error" => $e->getMessage()]);
        }
        break;

    case 'POST':
        $raw = file_get_contents('php://input');
        $in = json_decode($raw, true);

        if (!$in || empty($in['program_keahlian'])) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "Nama program keahlian wajib diisi."]);
            exit;
        }

        try {
            $sql = "INSERT INTO master_keahlian (kategori, program_keahlian, mapel_default, konsentrasi_list, produk_list, jasa_list, klien_list)
                    VALUES (:kategori, :prog, :mapel, :kons, :prod, :jasa, :klien)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':kategori' => in_array($in['kategori'] ?? '', ['kejuruan', 'umum']) ? $in['kategori'] : 'kejuruan',
                ':prog'     => trim($in['program_keahlian']),
                ':mapel'    => json_encode(array_values((array)($in['mapel_default'] ?? [])), JSON_UNESCAPED_UNICODE),
                ':kons'     => json_encode(array_values((array)($in['konsentrasi_list'] ?? [])), JSON_UNESCAPED_UNICODE),
                ':prod'     => json_encode(array_values((array)($in['produk_list'] ?? [])), JSON_UNESCAPED_UNICODE),
                ':jasa'     => json_encode(array_values((array)($in['jasa_list'] ?? [])), JSON_UNESCAPED_UNICODE),
                ':klien'    => json_encode(array_values((array)($in['klien_list'] ?? [])), JSON_UNESCAPED_UNICODE),
            ]);

            echo json_encode(["ok" => true, "id" => (int)$pdo->lastInsertId(), "message" => "Master keahlian berhasil ditambahkan."]);
        } catch (Exception $e) {
            echo json_encode(["ok" => false, "error" => "Gagal menambahkan: " . $e->getMessage()]);
        }
        break;

    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "ID master keahlian diperlukan."]);
            exit;
        }

        $raw = file_get_contents('php://input');
        $in = json_decode($raw, true);

        try {
            $sql = "UPDATE master_keahlian SET 
                        kategori = :kategori,
                        program_keahlian = :prog,
                        mapel_default = :mapel,
                        konsentrasi_list = :kons,
                        produk_list = :prod,
                        jasa_list = :jasa,
                        klien_list = :klien
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id'       => $id,
                ':kategori' => in_array($in['kategori'] ?? '', ['kejuruan', 'umum']) ? $in['kategori'] : 'kejuruan',
                ':prog'     => trim($in['program_keahlian'] ?? ''),
                ':mapel'    => json_encode(array_values((array)($in['mapel_default'] ?? [])), JSON_UNESCAPED_UNICODE),
                ':kons'     => json_encode(array_values((array)($in['konsentrasi_list'] ?? [])), JSON_UNESCAPED_UNICODE),
                ':prod'     => json_encode(array_values((array)($in['produk_list'] ?? [])), JSON_UNESCAPED_UNICODE),
                ':jasa'     => json_encode(array_values((array)($in['jasa_list'] ?? [])), JSON_UNESCAPED_UNICODE),
                ':klien'    => json_encode(array_values((array)($in['klien_list'] ?? [])), JSON_UNESCAPED_UNICODE),
            ]);

            echo json_encode(["ok" => true, "message" => "Master keahlian berhasil diperbarui."]);
        } catch (Exception $e) {
            echo json_encode(["ok" => false, "error" => "Gagal memperbarui: " . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "ID master keahlian diperlukan."]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM master_keahlian WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["ok" => true, "message" => "Master keahlian ID {$id} berhasil dihapus."]);
        } catch (Exception $e) {
            echo json_encode(["ok" => false, "error" => "Gagal menghapus: " . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["ok" => false, "error" => "Metode HTTP tidak didukung."]);
        break;
}
