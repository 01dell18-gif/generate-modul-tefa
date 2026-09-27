<?php
// api/save.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || empty($data['html'])) {
    echo json_encode(["ok" => false, "error" => "Payload tidak lengkap atau konten dokumen kosong."]);
    exit;
}

$input  = $data['input'] ?? [];
$html   = $data['html'];
$sumber = in_array($data['sumber'] ?? '', ['ai', 'smart']) ? $data['sumber'] : 'smart';
$ip     = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (strpos($ip, ',') !== false) {
    $ip = trim(explode(',', $ip)[0]);
}
$ua     = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Determine kategori mapel
$prog = $input['program'] ?? '';
$kategoriMapel = ($input['kategori'] ?? '') === 'umum' || (stripos($prog, 'Umum') !== false) ? 'umum' : 'kejuruan';

try {
    $sql = "INSERT INTO rpp_generations (
                satuan_pendidikan, nama_guru, nip_guru, nama_kepsek, nip_kepsek,
                mata_pelajaran, kategori_mapel,
                program_keahlian, konsentrasi_keahlian, elemen_pembelajaran, fase_kelas, 
                semester, tahun_pelajaran, alokasi_waktu, 
                produk_jasa, materi, konteks_tefa, klien, mitra_industri, platform_portofolio, 
                tempat_pengesahan, tanggal_pengesahan,
                brief, raw_input, html_content, sumber, ip_address, user_agent
            ) VALUES (
                :satuan, :guru, :nip_guru, :nama_kepsek, :nip_kepsek,
                :mapel, :kategori,
                :program, :konsentrasi, :elemen, :fase, 
                :semester, :tahun, :alokasi, 
                :produk, :materi, :konteks, :klien, :mitra, :portofolio, 
                :tempat, :tanggal,
                :brief, :raw_input, :html, :sumber, :ip, :ua
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':satuan'      => $input['satuan'] ?? null,
        ':guru'        => $input['guru'] ?? null,
        ':nip_guru'    => $input['nip_guru'] ?? null,
        ':nama_kepsek' => $input['nama_kepsek'] ?? null,
        ':nip_kepsek'  => $input['nip_kepsek'] ?? null,
        ':mapel'       => $input['mapel'] ?? null,
        ':kategori'    => $kategoriMapel,
        ':program'     => $input['program'] ?? null,
        ':konsentrasi' => $input['konsentrasi'] ?? null,
        ':elemen'      => $input['elemen'] ?? null,
        ':fase'        => $input['fase'] ?? null,
        ':semester'    => in_array($input['semester'] ?? '', ['Ganjil', 'Genap']) ? $input['semester'] : null,
        ':tahun'       => $input['tahun'] ?? null,
        ':alokasi'     => $input['alokasi'] ?? null,
        ':produk'      => $input['produk'] ?? null,
        ':materi'      => $input['materi'] ?? null,
        ':konteks'     => $input['konteks_tefa'] ?? null,
        ':klien'       => $input['klien'] ?? null,
        ':mitra'       => $input['mitra'] ?? null,
        ':portofolio'  => $input['portofolio'] ?? 'Google Sites',
        ':tempat'      => $input['tempat_pengesahan'] ?? null,
        ':tanggal'     => $input['tanggal_pengesahan'] ?? null,
        ':brief'       => $input['brief'] ?? null,
        ':raw_input'   => json_encode($input, JSON_UNESCAPED_UNICODE),
        ':html'        => $html,
        ':sumber'      => $sumber,
        ':ip'          => $ip,
        ':ua'          => substr($ua, 0, 500)
    ]);

    $insertId = (int)$pdo->lastInsertId();
    $countStmt = $pdo->query("SELECT COUNT(*) AS total FROM rpp_generations");
    $total = (int)$countStmt->fetchColumn();

    echo json_encode([
        "ok"      => true,
        "message" => "Dokumen berhasil disimpan ke riwayat.",
        "id"      => $insertId,
        "total"   => $total
    ]);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => "Gagal menyimpan dokumen: " . $e->getMessage()]);
}
