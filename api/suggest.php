<?php
// api/suggest.php
// AI & Web-Grounding Instant Assistant for Konteks TEFA and Opsi Lanjutan CP/TP

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

$raw = file_get_contents('php://input');
$in = json_decode($raw, true);

if (!$in || !is_array($in)) {
    echo json_encode(["ok" => false, "error" => "Payload tidak valid."]);
    exit;
}

$field       = $in['field'] ?? 'all_tefa'; 
$program     = trim($in['program'] ?? '');
$konsentrasi = trim($in['konsentrasi'] ?? '');
$elemen      = trim($in['elemen'] ?? '');
$fase        = trim($in['fase'] ?? 'Fase F / Kelas XII');
$semester    = trim($in['semester'] ?? 'Ganjil');

$produk      = trim($in['produk'] ?? '');
$materi      = trim($in['materi'] ?? '');
$konteks     = trim($in['konteks_tefa'] ?? '');
$klien       = trim($in['klien'] ?? '');
$brief       = trim($in['brief'] ?? '');
$sarana      = trim($in['sarana'] ?? '');
$mitra       = trim($in['mitra'] ?? '');

$kesiapan    = trim($in['kesiapan'] ?? '');
$cp          = trim($in['cp'] ?? '');
$tp          = trim($in['tp'] ?? '');

$apiKey = getGeminiApiKey($pdo);
if (!empty($in['api_key'])) {
    $apiKey = trim($in['api_key']);
}

$sumber = 'smart';
$result = [];

// Determine group of fields requested
$isTefaGroup = in_array($field, ['all_tefa', 'all', 'produk', 'materi', 'konteks_tefa', 'klien', 'brief', 'sarana', 'mitra']);
$isCpTpGroup = in_array($field, ['all_cp_tp', 'kesiapan', 'cp', 'tp']);

// Try Gemini AI with Internet Grounding if API Key is available
if (!empty($apiKey)) {
    $aiRes = callGeminiWithGrounding($apiKey, $pdo, $field, [
        'program'     => $program,
        'konsentrasi' => $konsentrasi,
        'elemen'      => $elemen,
        'fase'        => $fase,
        'semester'    => $semester,
        'produk'      => $produk,
        'materi'      => $materi,
        'konteks_tefa'=> $konteks,
        'klien'       => $klien,
        'brief'       => $brief,
        'sarana'      => $sarana,
        'mitra'       => $mitra,
        'kesiapan'    => $kesiapan,
        'cp'          => $cp,
        'tp'          => $tp
    ]);

    if (!empty($aiRes) && is_array($aiRes)) {
        $result = $aiRes;
        $sumber = 'ai';
    }
}

// Fallback to Smart Vocational & BSKAP Kurikulum Merdeka Matrix
$fallback = getSmartComprehensiveFallback($field, $program, $konsentrasi, $elemen, $fase, [
    'produk'      => $produk,
    'materi'      => $materi,
    'konteks_tefa'=> $konteks,
    'klien'       => $klien,
    'brief'       => $brief,
    'sarana'      => $sarana,
    'mitra'       => $mitra,
    'kesiapan'    => $kesiapan,
    'cp'          => $cp,
    'tp'          => $tp
]);

// Merge results with fallback for any missing key
foreach ($fallback as $k => $val) {
    if (empty($result[$k])) {
        $result[$k] = $val;
    }
}

// Handle Responses
if ($field === 'all_tefa' || $field === 'all') {
    echo json_encode([
        "ok"          => true,
        "field"       => "all_tefa",
        "suggestions" => [
            "produk"       => $result['produk'] ?? '',
            "materi"       => $result['materi'] ?? '',
            "konteks_tefa" => $result['konteks_tefa'] ?? '',
            "klien"        => $result['klien'] ?? '',
            "brief"        => $result['brief'] ?? '',
            "sarana"       => $result['sarana'] ?? '',
            "mitra"        => $result['mitra'] ?? ''
        ],
        "sumber"      => $sumber
    ], JSON_UNESCAPED_UNICODE);
} elseif ($field === 'all_cp_tp') {
    echo json_encode([
        "ok"          => true,
        "field"       => "all_cp_tp",
        "suggestions" => [
            "kesiapan" => $result['kesiapan'] ?? '',
            "cp"       => $result['cp'] ?? '',
            "tp"       => $result['tp'] ?? ''
        ],
        "sumber"      => $sumber
    ], JSON_UNESCAPED_UNICODE);
} else {
    // Single field request
    $val = $result[$field] ?? ($fallback[$field] ?? '');
    echo json_encode([
        "ok"         => true,
        "field"      => $field,
        "suggestion" => $val,
        "sumber"     => $sumber
    ], JSON_UNESCAPED_UNICODE);
}

// =========================================================================
// AI Engine with Google Search Grounding
// =========================================================================
function callGeminiWithGrounding(string $apiKey, PDO $pdo, string $field, array $data): ?array {
    $model = getAppSetting($pdo, 'ai_model', 'gemini-1.5-flash');
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

    $prompt = buildAssistantPrompt($field, $data);

    // Payload with Google Search Grounding for live internet data lookup
    $payloadWithTools = [
        "contents" => [["parts" => [["text" => $prompt]]]],
        "tools" => [
            ["googleSearch" => new stdClass()]
        ],
        "generationConfig" => [
            "temperature" => 0.35,
            "maxOutputTokens" => 2048
        ]
    ];

    $res = executeCurlGemini($apiUrl, $payloadWithTools);
    if ($res === null) {
        // Fallback retry without tools in case specific API key/tier restricts search grounding
        $payloadNoTools = [
            "contents" => [["parts" => [["text" => $prompt]]]],
            "generationConfig" => [
                "temperature" => 0.35,
                "maxOutputTokens" => 2048
            ]
        ];
        $res = executeCurlGemini($apiUrl, $payloadNoTools);
    }

    if (!$res) return null;

    $candidateText = $res['candidates'][0]['content']['parts'][0]['text'] ?? '';
    if (empty($candidateText)) return null;

    $candidateText = preg_replace('/^```(?:json)?\s*/i', '', trim($candidateText));
    $candidateText = preg_replace('/\s*```$/i', '', $candidateText);

    $parsed = json_decode($candidateText, true);
    return is_array($parsed) ? $parsed : null;
}

function executeCurlGemini(string $url, array $payload): ?array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp && $code === 200) {
        return json_decode($resp, true);
    }
    return null;
}

function buildAssistantPrompt(string $field, array $d): string {
    $prog = $d['program'] ?: 'Kejuruan SMK';
    $kons = $d['konsentrasi'] ?: $prog;
    $elem = $d['elemen'] ?: 'Elemen Kejuruan Terkait';
    $fase = $d['fase'] ?: 'Fase F / Kelas XII';
    $prod = $d['produk'] ?: '';
    $mat  = $d['materi'] ?: '';
    $unit = $d['konteks_tefa'] ?: '';
    $klien= $d['klien'] ?: '';
    $brief= $d['brief'] ?: '';
    $sar  = $d['sarana'] ?: '';
    $mit  = $d['mitra'] ?: '';

    return <<<PROMPT
Anda adalah Pakar Asisten AI Kurikulum SMK TEFA (Teaching Factory) & Kurikulum Merdeka Kemendikbudristek.
Gunakan bantuan pencarian internet jika diperlukan untuk memastikan akurasi data regulasi BSKAP dan data DUDI (Dunia Usaha / Dunia Industri) nyata di Indonesia.

DATA INPUT SAAT INI:
- Program Keahlian: {$prog}
- Konsentrasi Keahlian: {$kons}
- Elemen Pembelajaran: {$elem}
- Fase/Kelas: {$fase}
- Produk/Jasa Eksisting: {$prod}
- Materi Teknis Eksisting: {$mat}
- Unit TEFA Eksisting: {$unit}
- Klien Eksisting: {$klien}
- Brief Order Eksisting: {$brief}
- Sarana Eksisting: {$sar}
- Mitra DUDI Eksisting: {$mit}

TUGAS ANDA:
Berikan rekomendasi teks yang SALING TERHUBUNG (koheren, logis, harmonis, dan realistis) sesuai standar industri dan kurikulum.

1. Jika meminta "all_tefa" atau "all", kembalikan JSON dengan key:
   - "produk": Nama produk atau jasa riil bernilai ekonomi yang dibuat pada unit produksi TEFA sekolah.
   - "materi": Topik teknis spesifik yang dipelajari siswa untuk menyelesaikan produk tersebut.
   - "konteks_tefa": Nama unit usaha/bengkel/studio TEFA sekolah yang relevan.
   - "klien": Pihak pemesan riil (UMKM, masyarakat, instansi dinas, atau industri).
   - "brief": Uraian 2-3 kalimat memuat spesifikasi teknis, jumlah pesanan, toleransi kualitas industri, dan tenggat waktu kerja.
   - "sarana": Daftar peralatan bengkel/studio, instrumen ukur/kalibrasi, software, dan APD K3 (satu kalimat dipisah koma).
   - "mitra": 1-2 nama perusahaan DUDI nyata di Indonesia yang relevan sebagai mitra kerja sama industri.

2. Jika meminta "all_cp_tp", kembalikan JSON dengan key:
   - "cp": Rumusan resmi Capaian Pembelajaran baku BSKAP Kemendikbudristek untuk elemen "{$elem}" pada fase "{$fase}".
   - "tp": 3-4 butir Tujuan Pembelajaran operasional (berurutan dengan KKO Taksonomi Bloom revisi) yang mengintegrasikan kompetensi teknis CP dengan pengerjaan produk TEFA "{$prod}".
   - "kesiapan": Uraian pemahaman awal, keterampilan prasyarat, dan hasil asesmen diagnostik yang wajib dikuasai peserta didik sebelum memulai proyek pesanan ini.

3. Jika meminta satu field spesifik ("produk", "materi", "konteks_tefa", "klien", "brief", "sarana", "mitra", "kesiapan", "cp", "tp"), kembalikan JSON dengan key field tersebut yang selaras dengan data input lainnya.

KEMBALIKAN HANYA FORMAT JSON MURNI TANPA MARKDOWN ATAU PENJELASAN LAIN:
PROMPT;
}

// =========================================================================
// Deterministic Smart Knowledge Matrix (BSKAP & DUDI Real-World Grounding)
// =========================================================================
function getSmartComprehensiveFallback(string $field, string $prog, string $kons, string $elem, string $fase, array $cur): array {
    $textSearch = strtolower($prog . ' ' . $kons . ' ' . $elem . ' ' . ($cur['produk'] ?? ''));

    // Default Fallback
    $res = [
        'produk'       => 'Produk Barang / Jasa Standar TEFA Industri',
        'materi'       => 'Teknik Pembuatan, Pengujian Kualitas, dan Finishing Produk Standar Industri',
        'konteks_tefa' => 'Unit Produksi & Teaching Factory Kejuruan Sekolah',
        'klien'        => 'UMKM Rekanan & Komite Sekolah',
        'brief'        => 'Pengerjaan paket pesanan produk sesuai gambar kerja dan toleransi industri, dengan waktu pengerjaan 3 pertemuan bergaransi.',
        'sarana'       => 'Bengkel Kerja, Alat Ukur Terkalibrasi, Toolset Lengkap, APD K3, Perangkat Komputer',
        'mitra'        => 'Asosiasi Industri Rekanan DUDI Sekolah',
        'kesiapan'     => 'Peserta didik telah lulus materi dasar kejuruan dan memahami prosedur keselamatan kerja (K3LH) di bengkel/studio.',
        'cp'           => "Pada akhir fase {$fase}, peserta didik mampu menerapkan kompetensi keahlian pada elemen {$elem} secara mandiri sesuai standar industri.",
        'tp'           => "1. Menganalisis gambar kerja dan spesifikasi pesanan klien; 2. Menyiapkan alat, bahan, dan keselamatan kerja; 3. Melaksanakan proses produksi sesuai SOP industri; 4. Menguji kualitas hasil kerja dan membuat laporan serah terima."
    ];

    // 1. Teknik Jaringan Komputer & Telekomunikasi (TKJ)
    if (strpos($textSearch, 'jaringan') !== false || strpos($textSearch, 'tkj') !== false || strpos($textSearch, 'komputer') !== false) {
        $res['produk']       = 'Router Hotspot MikroTik Gateway Pre-Configured & Billing Voucher';
        $res['materi']       = 'Konfigurasi IP Addressing, Bandwidth Management (Simple Queue), User Manager Voucher, & Firewall Filter';
        $res['konteks_tefa'] = 'Unit Produksi TKJ Net Solution SMK';
        $res['klien']        = 'Kafe dan Warung Kopi Sekitar Lingkungan Sekolah';
        $res['brief']        = 'Pemesanan 1 unit router gateway hotspot berkapasitas 50 user simultan, pembatasan kecepatan 2 Mbps/user, halaman login custom brand kafe, dan 100 lembar voucher siap cetak dalam 3 hari kerja.';
        $res['sarana']       = 'Lab Jaringan Komputer, RouterBOARD MikroTik (RB750Gr3/RB951), Switch Hub, Kabel UTP Cat6, LAN Cable Tester Digital, Crimping Tool, PC Server, Software Winbox';
        $res['mitra']        = 'PT. Telkom Indonesia Tbk / PT. Citra Telematika Mandiri (MikroTik Academy)';
        $res['kesiapan']     = 'Peserta didik memahami konsep subnetting IPv4, dasar routing static, dan telah terampil melakukan terminasi kabel UTP RJ-45 sesuai standar T568B.';
        $res['cp']           = "Pada akhir Fase F, peserta didik mampu merencanakan pengalamatan jaringan, menginstalasi, mengonfigurasi, dan melakukan troubleshooting perangkat jaringan kabel maupun nirkabel, serta mengadministrasi server jaringan secara aman sesuai standar industri telekomunikasi.";
        $res['tp']           = "1. Menganalisis kebutuhan bandwidth dan topologi jaringan hotspot pesanan klien kafe;\n2. Mengonfigurasi interface, IP address, DHCP server, dan routing gateway pada Router MikroTik;\n3. Mengimplementasikan sistem autentikasi hotspot voucher dan manajemen bandwidth per klien;\n4. Melakukan pengujian koneksi simultan (*stress test*) dan menyusun berita acara serah terima perangkat.";

    // 2. Desain Komunikasi Visual (DKV)
    } elseif (strpos($textSearch, 'visual') !== false || strpos($textSearch, 'dkv') !== false || strpos($textSearch, 'desain') !== false) {
        $res['produk']       = 'Desain Identitas Merek & Kemasan Produk UMKM (Packaging Box & Label Stiker)';
        $res['materi']       = 'Prinsip Desain Kemasan, Anatomi Tipografi, Pola Dieline Die-Cut, Color Management CMYK, dan Mockup 3D';
        $res['konteks_tefa'] = 'Studio Kreatif TEFA Visual Arts SMK';
        $res['klien']        = 'UMKM Kuliner & Makanan Ringan Binaan Sekolah';
        $res['brief']        = 'Pembuatan redesain identitas visual kemasan box lipat ramah lingkungan (food-grade ivory 300gsm), rancangan pola die-cut presisi, label botol tahan air, serta mockup 3D fotorealistik siap cetak dalam 5 hari kerja.';
        $res['sarana']       = 'Studio Komputer Grafis PC/Mac, Drawing Pen Tablet Wacom, Software Adobe Illustrator, Adobe Photoshop, Printer Color Proof A3+, Pantone Color Guide, Cutting Mat';
        $res['mitra']        = 'PT. Kreasi Grafika Nusantara / Asosiasi Desainer Grafis Indonesia (ADGI)';
        $res['kesiapan']     = 'Peserta didik menguasai operasi software desain grafis berbasis vektor dan bitmap serta memahami dasar teori warna dan tipografi.';
        $res['cp']           = "Pada akhir Fase F, peserta didik mampu mengaplikasikan prinsip-prinsip desain komunikasi visual dalam menghasilkan karya desain publikasi, kemasan produk, dan identitas visual berbasis vektor dan bitmap yang komunikatif, estetis, dan memenuhi standar cetak industri grafika.";
        $res['tp']           = "1. Menganalisis *creative brief* pemesan dan merumuskan *moodboard* konsep visual kemasan;\n2. Merancang pola bidang kemasan (*dieline*) dan layout grafis vektor dengan resolusi cetak 300 DPI (CMYK);\n3. Membuat visualisasi mockup 3D produk untuk presentasi *pitching* persetujuan klien;\n4. Memproduksi cetak sampel (*color proofing*) dan menyiapkan file *final artwork* siap cetak massal.";

    // 3. Teknik Otomotif (TSM / TKR)
    } elseif (strpos($textSearch, 'otomotif') !== false || strpos($textSearch, 'motor') !== false || strpos($textSearch, 'tsm') !== false || strpos($textSearch, 'tkr') !== false) {
        $res['produk']       = 'Paket Jasa Servis Ringan / Tune Up Injeksi & Perawatan Berkala CVT';
        $res['materi']       = 'Pembersihan Throttle Body, Kalibrasi Sensor TPS via Scanner OBD, Pengecekan Roller CVT, dan Penggantian Fluida Kerja';
        $res['konteks_tefa'] = 'Bengkel Mitra AHASS TEFA SMK';
        $res['klien']        = 'Komunitas Pengemudi Ojek Online & Kendaraan Warga Sekitar Sekolah';
        $res['brief']        = 'Paket servis cepat 45 menit meliputi tune-up sistem injeksi, pembersihan ruang bakar dengan injector cleaner, pengecekan ketebalan v-belt dan roller CVT, ganti oli mesin, serta laporan hasil print diagnostic scanner.';
        $res['sarana']       = 'Bike Lift Hidrolik, Diagnostic Scanner OBD-2, Ultrasonic Injector Cleaner, Kunci Momen Terkalibrasi, Multimeter Digital, Kompresor Udara, APD Wearpack & Kacamata Pelindung';
        $res['mitra']        = 'PT. Astra Honda Motor (AHASS) / PT. Yamaha Indonesia Motor Manufacturing';
        $res['kesiapan']     = 'Peserta didik memahami siklus kerja mesin 4 tak, dasar kelistrikan bodi otomotif, serta disiplin SOP K3 keselamatan kerja bengkel.';
        $res['cp']           = "Pada akhir Fase F, peserta didik mampu mendiagnosis, merawat, dan memperbaiki mesin kendaraan bermotor, sistem sasis, pemindah tenaga (CVT/transmisi), dan sistem kelistrikan injeksi elektronik sesuai SOP pabrikan otomotif.";
        $res['tp']           = "1. Melakukan pemeriksaan awal keluhan kendaraan pelanggan sesuai lembar penerimaan servis;\n2. Melaksanakan servis berkala sistem injeksi menggunakan scanner diagnostik dan ultrasonic cleaner;\n3. Membongkar, membersihkan, dan mengukur toleransi keausan komponen transmisi CVT sesuai spesifikasi manual servis;\n4. Menguji performa mesin pasca servis (*test ride*) dan menyerahkan kendaraan beserta buku catatan servis.";

    // 4. Manajemen Perkantoran dan Layanan Bisnis (MPLB)
    } elseif (strpos($textSearch, 'perkantoran') !== false || strpos($textSearch, 'mplb') !== false || strpos($textSearch, 'administrasi') !== false) {
        $res['produk']       = 'Jasa Digitalisasi Tata Kelola Kearsipan & Otomasi Administrasi Persuratan';
        $res['materi']       = 'Sistem Pengarsipan Pola Klasifikasi, Alih Media Digitalisasi Berkas (Scanning ADF), dan Pengelolaan Arsip Berbasis Cloud';
        $res['konteks_tefa'] = 'Biro Jasa Administrasi & Kearsipan TEFA Mandiri SMK';
        $res['klien']        = 'Kantor Desa / Kelurahan & Instansi Rekanan Sekolah';
        $res['brief']        = 'Digitalisasi dan penataan 500 berkas fisik surat masuk/keluar, pemberian metadata penomoran otomatis, pembuatan katalog arsip terindeks, dan penyimpanan folder cloud terproteksi dalam 4 hari kerja.';
        $res['sarana']       = 'Lab Perkantoran Modern, Mesin Scanner ADF Berkecepatan Tinggi, Komputer Core i5, Software Google Workspace / DMS, Shredder Penghancur Kertas, Label Maker';
        $res['mitra']        = 'Kantor Notaris & PPAT Rekanan / Dinas Perpustakaan dan Kearsipan Daerah';
        $res['kesiapan']     = 'Peserta didik terampil mengetik cepat 10 jari, memahami format baku surat dinas, serta menguasai software pengolah kata dan spreadsheet.';
        $res['cp']           = "Pada akhir Fase F, peserta didik mampu mengelola dokumen administrasi umum, korespondensi dinas, kearsipan manual maupun digital, serta memberikan pelayanan prima (*service excellence*) di lingkungan kantor modern.";
        $res['tp']           = "1. Mengidentifikasi dan mengklasifikasikan dokumen fisik sesuai sistem abjad dan subjek masalah;\n2. Melakukan proses alih media digital menggunakan scanner ADF beresolusi standar arsip;\n3. Mengunggah dan memberi metadata indeks dokumen pada sistem penyimpanan cloud terstruktur;\n4. Membuat berita acara serah terima digitalisasi arsip dan mempresentasikan panduan temu kembali arsip kepada staf kantor.";

    // 5. Farmasi Klinis & Komunitas
    } elseif (strpos($textSearch, 'farmasi') !== false || strpos($textSearch, 'obat') !== false) {
        $res['produk']       = 'Sediaan Herbal Minyak Aromaterapi Roll-on & Balsem Hangat Alami';
        $res['materi']       = 'Formulasi Bahan Alami CPOTB Mini, Teknik Penimbangan Presisi, Uji Homogenitas Salep/Balsem, dan Pelabelan Etiket Obat';
        $res['konteks_tefa'] = 'Apotek Mini & Laboratorium Formulasi TEFA Sekolah';
        $res['klien']        = 'Posyandu Binaan, Koperasi Sekolah, dan Komunitas Lansia';
        $res['brief']        = 'Produksi 100 botol minyak aromaterapi roll-on ekstrak herbal jahe-peppermint, uji mutu kejernihan dan pH netral kulit, kemasan higienis berlabel etiket komposisi dan nomor batch resmi dalam 4 hari kerja.';
        $res['sarana']       = 'Timbangan Analitik Presisi 4 Desimal, Mortir dan Stamper Porselen, Gelas Ukur Pyrex, Penangas Air Elektrik, pH Meter Digital, APD Jas Lab & Sarung Tangan Steril';
        $res['mitra']        = 'PT. Kimia Farma Apotek / PT. Kalbe Farma Tbk';
        $res['kesiapan']     = 'Peserta didik memahami perhitungan persentase dosis bahan aktif, prinsip sanitasi higienis laboratorium, dan cara membaca resep standar sediaan.';
        $res['cp']           = "Pada akhir Fase F, peserta didik mampu meracik sediaan farmasi padat, semi padat, dan cair serta sediaan herbal tradisional sesuai standar CPOTB dan CPPOB mini, serta mengelola perbekalan farmasi dengan teliti.";
        $res['tp']           = "1. Menganalisis formula resep sediaan dan menghitung kebutuhan bahan baku herbal presisi;\n2. Menyiapkan alat laboratorium steril dan menimbang bahan aktif dengan timbangan analitik;\n3. Melakukan pencampuran dan pengujian mutu homogenitas, organoleptis, serta kestabilan sediaan;\n4. Mengemas sediaan dalam botol roll-on kedap udara dan menempelkan etiket informasi aturan pakai.";

    // 6. Akuntansi & Keuangan Lembaga (AKL)
    } elseif (strpos($textSearch, 'akuntansi') !== false || strpos($textSearch, 'keuangan') !== false || strpos($textSearch, 'akl') !== false) {
        $res['produk']       = 'Laporan Keuangan UMKM & Simulasi Pengisian SPT Pajak Tahunan';
        $res['materi']       = 'Siklus Akuntansi Perusahaan Dagang/Jasa, Jurnal Penyesuaian, Neraca Lajur Berbasis Spreadsheet, dan Pajak PPh Final UMKM';
        $res['konteks_tefa'] = 'Kantor Jasa Akuntansi (KJA) TEFA Mandiri Siswa SMK';
        $res['klien']        = 'Pelaku UMKM Kuliner dan Ritel Sekitar Sekolah';
        $res['brief']        = 'Penyusunan laporan keuangan periode 1 semester untuk mitra UMKM, memuat buku besar otomatis, laporan laba rugi, neraca, serta bukti pemotongan PPh final 0.5% siap lapor dalam 4 hari kerja.';
        $res['sarana']       = 'Lab Komputer Akuntansi, Software Microsoft Excel / Google Sheets Terintegrasi Formula, Aplikasi Accurate / MYOB, Printer Dot-Matrix Bukti Kas';
        $res['mitra']        = 'Kantor Akuntan Publik (KAP) Rekanan / Asosiasi Pengusaha UMKM';
        $res['kesiapan']     = 'Peserta didik memahami persamaan dasar akuntansi, aturan debit-kredit transaksi, dan pembuatan jurnal umum perusahaan dagang.';
        $res['cp']           = "Pada akhir Fase F, peserta didik mampu memproses transaksi keuangan, mengoperasikan komputer akuntansi, menyusun laporan keuangan entitas tanpa akuntabilitas publik (SAK EMKM), serta menyelesaikan kewajiban perpajakan.";
        $res['tp']           = "1. Mengidentifikasi dan memverifikasi bukti transaksi pengeluaran dan pemasukan UMKM;\n2. Menginput transaksi ke dalam spreadsheet akuntansi terotomasi buku besar dan neraca lajur;\n3. Menyusun laporan laba rugi, neraca, dan arus kas sesuai kaidah SAK EMKM;\n4. Memvalidasi hasil laporan dan menghitung estimasi pajak PPh final UMKM bersama tim penilai industri.";

    // 7. Mapel Umum Terintegrasi TEFA (Bahasa Inggris, Matematika, dll)
    } elseif (strpos($textSearch, 'inggris') !== false || strpos($textSearch, 'umum') !== false || strpos($textSearch, 'matematika') !== false) {
        $res['produk']       = 'Bilingual Product Catalog & Presentasi Pitching Penawaran Ekspor/Tamu Asing';
        $res['materi']       = 'Professional Pitching, Drafting Formal Business Quotation Letter, and Handling Customer Inquiries in English';
        $res['konteks_tefa'] = 'Unit Promosi & Hubungan Industri TEFA Sekolah';
        $res['klien']        = 'Mitra Klien Asing & Wisatawan Mancanegara';
        $res['brief']        = 'Penyusunan e-katalog dwibahasa (Indonesia-Inggris) untuk lini produk unggulan TEFA sekolah, disertai video simulasi pitching penawaran produk berdurasi 3 menit dalam 3 hari kerja.';
        $res['sarana']       = 'Lab Bahasa Multimedia, Komputer Terkoneksi Internet, Mikrofon Podcast, Kamera DSLR/Webcam HD, Software Canva/Office';
        $res['mitra']        = 'Kamar Dagang & Industri (KADIN) / Asosiasi Biro Perjalanan Wisata (ASITA)';
        $res['kesiapan']     = 'Peserta didik menguasai kosakata dasar bidang teknologi/kejuruan dan mampu menyusun kalimat formal dalam bahasa Inggris.';
        $res['cp']           = "Pada akhir Fase F, peserta didik mampu menggunakan bahasa Inggris secara lisan dan tulisan untuk berkomunikasi dalam konteks kerja profesional, presentasi penawaran produk bisnis, serta korespondensi perdagangan internasional.";
        $res['tp']           = "1. Menganalisis fitur keunggulan produk TEFA sekolah dan menerjemahkannya ke dalam bahasa promosi bisnis internasional;\n2. Merancang teks katalog produk dwibahasa dan surat penawaran harga (*quotation email*) formal;\n3. Melakukan simulasi percakapan penanganan negosiasi order klien asing secara lisan melalui teknik *role-play*;\n4. Menampilkan video presentasi *pitching* produk di hadapan tim reviewer mitra industri.";
    }

    // Override specific fields if already provided by user in $cur
    if (!empty($cur['produk']) && in_array($field, ['brief', 'sarana', 'tp'])) {
        $prodName = $cur['produk'];
        if ($field === 'brief') {
            $res['brief'] = "Pengerjaan paket pesanan {$prodName} dari {$res['klien']}, dengan spesifikasi teknis presisi, uji fungsi terverifikasi standar industri, dan batas waktu penyelesaian 3 pertemuan sesuai SOP.";
        }
    }

    return $res;
}
