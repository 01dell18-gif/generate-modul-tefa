<?php
// api/rpp.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Allow DELETE or PUT method override via header or query if needed
if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
}

switch ($method) {
    case 'GET':
        if ($id) {
            // Read single RPP detail
            try {
                $stmt = $pdo->prepare("SELECT * FROM rpp_generations WHERE id = ?");
                $stmt->execute([$id]);
                $row = $stmt->fetch();

                if (!$row) {
                    http_response_code(404);
                    echo json_encode(["ok" => false, "error" => "Dokumen RPP tidak ditemukan."]);
                    exit;
                }

                $row['raw_input'] = json_decode($row['raw_input'] ?? '{}', true);
                echo json_encode(["ok" => true, "data" => $row]);
            } catch (Exception $e) {
                echo json_encode(["ok" => false, "error" => $e->getMessage()]);
            }
        } else {
            // Read list with pagination and filters
            $page     = max(1, (int)($_GET['page'] ?? 1));
            $limit    = min(100, max(1, (int)($_GET['limit'] ?? 10)));
            $offset   = ($page - 1) * $limit;
            $search   = trim($_GET['search'] ?? '');
            $program  = trim($_GET['program'] ?? '');
            $kategori = trim($_GET['kategori'] ?? '');

            $where = [];
            $params = [];

            if ($search !== '') {
                $where[] = "(satuan_pendidikan LIKE ? OR nama_guru LIKE ? OR mata_pelajaran LIKE ? OR produk_jasa LIKE ? OR klien LIKE ?)";
                $s = "%{$search}%";
                $params = array_merge($params, [$s, $s, $s, $s, $s]);
            }

            if ($program !== '') {
                $where[] = "program_keahlian = ?";
                $params[] = $program;
            }

            if ($kategori !== '') {
                $where[] = "kategori_mapel = ?";
                $params[] = $kategori;
            }

            $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            try {
                // Count total records
                $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM rpp_generations {$whereSql}");
                $cntStmt->execute($params);
                $totalRecords = (int)$cntStmt->fetchColumn();
                $totalPages = ceil($totalRecords / $limit);

                // Fetch list
                $listSql = "SELECT id, satuan_pendidikan, nama_guru, mata_pelajaran, kategori_mapel, 
                                   program_keahlian, konsentrasi_keahlian, elemen_pembelajaran, fase_kelas, semester, tahun_pelajaran,
                                   produk_jasa, klien, sumber, created_at, updated_at
                            FROM rpp_generations
                            {$whereSql}
                            ORDER BY id DESC
                            LIMIT {$limit} OFFSET {$offset}";

                $stmt = $pdo->prepare($listSql);
                $stmt->execute($params);
                $rows = $stmt->fetchAll();

                echo json_encode([
                    "ok" => true,
                    "data" => $rows,
                    "pagination" => [
                        "current_page"  => $page,
                        "per_page"      => $limit,
                        "total_records" => $totalRecords,
                        "total_pages"   => $totalPages
                    ]
                ]);
            } catch (Exception $e) {
                echo json_encode(["ok" => false, "error" => $e->getMessage()]);
            }
        }
        break;

    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "ID dokumen diperlukan untuk pembaruan."]);
            exit;
        }

        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true);

        if (!$body || empty($body['html_content'])) {
            echo json_encode(["ok" => false, "error" => "Konten dokumen html_content tidak boleh kosong."]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE rpp_generations SET html_content = :html, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
            $stmt->execute([
                ':html' => $body['html_content'],
                ':id'   => $id
            ]);

            echo json_encode(["ok" => true, "message" => "Dokumen RPP berhasil diperbarui."]);
        } catch (Exception $e) {
            echo json_encode(["ok" => false, "error" => "Gagal memperbarui dokumen: " . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "ID dokumen diperlukan untuk penghapusan."]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM rpp_generations WHERE id = ?");
            $stmt->execute([$id]);

            $countStmt = $pdo->query("SELECT COUNT(*) AS total FROM rpp_generations");
            $total = (int)$countStmt->fetchColumn();

            echo json_encode([
                "ok"      => true,
                "message" => "Dokumen ID {$id} berhasil dihapus.",
                "total"   => $total
            ]);
        } catch (Exception $e) {
            echo json_encode(["ok" => false, "error" => "Gagal menghapus dokumen: " . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["ok" => false, "error" => "Metode HTTP tidak didukung."]);
        break;
}
