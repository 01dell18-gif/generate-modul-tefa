<?php
// engine/GeminiClient.php

class GeminiClient {
    public static function generateRpp(array $input, string $apiKey, string $model = 'gemini-1.5-flash', float $temperature = 0.25): array {
        if (empty($apiKey)) {
            return [
                'ok' => false,
                'html' => null,
                'error' => 'API Key Gemini belum dikonfigurasi.'
            ];
        }

        $prompt = self::buildPrompt($input);

        // Normalize model name
        $model = trim($model) ?: 'gemini-1.5-flash';
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

        $payload = [
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ],
            "generationConfig" => [
                "temperature" => $temperature,
                "maxOutputTokens" => 8192,
                "topP" => 0.85
            ]
        ];

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 35,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if (!$response) {
            return [
                'ok' => false,
                'html' => null,
                'error' => 'Koneksi ke Gemini AI gagal: ' . ($curlErr ?: 'Timeout')
            ];
        }

        $resData = json_decode($response, true);

        if ($httpCode !== 200) {
            $apiMsg = $resData['error']['message'] ?? ('HTTP Error ' . $httpCode);
            return [
                'ok' => false,
                'html' => null,
                'error' => "Gemini API Error: " . $apiMsg
            ];
        }

        $candidateText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
        if (empty($candidateText)) {
            return [
                'ok' => false,
                'html' => null,
                'error' => 'Respons Gemini AI tidak memuat konten.'
            ];
        }

        // Clean markdown backticks wrapper if model returns ```html ... ```
        $cleanHtml = preg_replace('/^```(?:html)?\s*/i', '', trim($candidateText));
        $cleanHtml = preg_replace('/\s*```$/i', '', $cleanHtml);

        return [
            'ok' => true,
            'html' => $cleanHtml,
            'error' => null
        ];
    }

    private static function buildPrompt(array $in): string {
        $satuan            = $in['satuan'] ?? '-';
        $guru              = $in['guru'] ?? '-';
        $nipGuru           = $in['nip_guru'] ?? '';
        $namaKepsek        = $in['nama_kepsek'] ?? '';
        $nipKepsek         = $in['nip_kepsek'] ?? '';
        $tempatPengesahan  = $in['tempat_pengesahan'] ?? '';
        $tanggalPengesahan = $in['tanggal_pengesahan'] ?? '';
        $program           = $in['program'] ?? '-';
        $konsentrasi       = $in['konsentrasi'] ?? '-';
        $elemen            = $in['elemen'] ?? '-';
        $mapel             = $in['mapel'] ?? '-';
        $fase              = $in['fase'] ?? 'Fase F / Kelas XII';
        $semester          = $in['semester'] ?? 'Ganjil';
        $tahun             = $in['tahun'] ?? '2026/2027';
        $alokasi           = $in['alokasi'] ?? '12 JP (3 pertemuan @ 4 JP)';
        $produk            = $in['produk'] ?? '-';
        $materi            = $in['materi'] ?? '-';
        $konteks           = $in['konteks_tefa'] ?? '-';
        $klien             = $in['klien'] ?? '-';
        $brief             = $in['brief'] ?? '-';
        $sarana            = $in['sarana'] ?? '-';
        $mitra             = $in['mitra'] ?? 'Mitra DUDI Pasangan';
        $portofolio        = $in['portofolio'] ?? 'Google Sites';
        $kesiapan          = $in['kesiapan'] ?? 'Peserta didik memahami pengenalan dasar kejuruan';
        $cpUser            = $in['cp'] ?? '';
        $tpUser            = $in['tp'] ?? '';

        return <<<PROMPT
Anda adalah Konsultan Kurikulum Vokasi (SMK) Kemendikbudristek dan Fasilitator Nasional Teaching Factory (TEFA).
Tugas Anda adalah menyusun dokumen lengkap: RENCANA PELAKSANAAN PEMBELAJARAN (RPP) / PERENCANAAN PEMBELAJARAN MENDALAM BERBASIS TEACHING FACTORY (TEFA) dalam format HTML MURNI (hanya tag-tag konten seperti <h1>, <h2>, <h3>, <p>, <ul>, <ol>, <li>, <table>, <thead>, <tbody>, <tr>, <th>, <td>, <div class="page-break"></div>, dll. JANGAN menyertakan tag <html>, <head>, atau <body>, dan JANGAN membungkus dengan blok markdown ```html atau ```).

PANDUAN KHUSUS KONTEN & KURIKULUM:
1. JIKA MATA PELAJARAN UMUM (Bahasa Indonesia, Bahasa Inggris, Matematika, IPAS, Informatika, Pendidikan Pancasila, Sejarah, PJOK, Seni Budaya, PKK):
   Kontekstualisasikan pembelajaran secara mendalam untuk mendukung unit produksi/pesanan TEFA sekolah yang dipilih (Contoh: B. Inggris untuk front desk & catalog produk TEFA, Matematika untuk kalkulasi HPP/BEP pesanan, IPAS untuk uji mutu bahan/K3 limbah bengkel, B. Indonesia untuk penyusunan SOP & negosiasi klien).
2. JIKA MATA PELAJARAN KEJURUAN (TKJ, DKV, MPLB, Bisnis Digital, Akuntansi, Perbankan, TSM, Perhotelan, Farmasi, dll.):
   Susun alur kerja nyata berstandar industri: Telaah order brief -> Perencanaan alat/bahan -> Eksekusi produksi sesuai SOP -> Quality Control (QC) ketat -> Finishing & Pengemasan -> Serah terima klien (Berita Acara) -> Publikasi Portofolio Digital.

STRUKTUR DOKUMEN WAJIB (BAGIAN A s.d. G LENGKAP):
- JUDUL UTAMA:
  <h1>RENCANA PELAKSANAAN PEMBELAJARAN (RPP)</h1>
  <h1>PERENCANAAN PEMBELAJARAN MENDALAM BERBASIS TEACHING FACTORY (TEFA)</h1>
- BAGIAN A: IDENTITAS DAN KONTEKS PEMBELAJARAN (Tabel 16 Baris Rinci: Satuan Pendidikan, Nama Guru, Mata Pelajaran, Program Keahlian, Konsentrasi Keahlian, Fase/Kelas, Semester, Tahun Pelajaran, Alokasi Waktu, Materi/Topik, Konteks TEFA, Produk/Jasa, Klien/Konsumen, Brief/Pesanan, Sarana/Prasarana, Mitra Industri, Platform Portofolio).
- BAGIAN B: IDENTIFIKASI
  1. Kesiapan Peserta Didik (Dilengkapi Asesmen Diagnostik Awal: Tujuan, Teknik, Instrumen, Aspek yang Diperiksa, Tindak Lanjut).
  2. Karakteristik Materi (Konsep Utama, Keterampilan Prosedural, Tingkat Kesulitan, Penerapan di Dunia Kerja, Potensi Miskonsepsi, Etika Budaya Kerja & K3).
  3. Dimensi Profil Lulusan / DPL (Centang selektif DPL 1 s.d. 8 dengan uraian indikator perilaku kontekstual).
- BAGIAN C: DESAIN PEMBELAJARAN
  1. Capaian Pembelajaran (CP)
  2. Tujuan Pembelajaran (TP)
  3. Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) (5 Poin Kriteria Terukur)
  4. Pemahaman Bermakna
  5. Pertanyaan Pemantik (4 Pertanyaan Mendalam)
  6. Praktik Pedagogis (Model TEFA, Diferensiasi, Coaching)
  7. Kemitraan Pembelajaran
  8. Lingkungan Pembelajaran (Budaya 5R, Zonasi Kerja)
  9. Pemanfaatan Digital
- BAGIAN D: PENGALAMAN BELAJAR DAN SINTAKS TEFA (Tabel Aktivitas Guru & Siswa Terstruktur):
  1. Kegiatan Awal (Orientasi & Briefing TEFA)
  2. Kegiatan Inti:
     a. Memahami (Analisis Brief & Kebutuhan Klien)
     b. Mengaplikasi (Perencanaan, SOP K3, & Produksi Nyata)
     c. Merefleksi (Quality Control/QC, Feedback, & Reworking)
  3. Kegiatan Penutup (Packaging, Delivery ke Klien, & Refleksi Akhir)
- BAGIAN E: INTEGRASI PORTOFOLIO DIGITAL (Tujuan, Platform, Jenis Bukti Karya, Struktur Web Portofolio).
- BAGIAN F: ASESMEN PEMBELAJARAN (Asesmen Awal, Formatif, Sumatif, Rubrik Unjuk Kerja TEFA Skala 1-4, Rubrik Portofolio Digital, dan Tabel Kolom Tanda Tangan Resmi Kepala Sekolah: {$namaKepsek} [NIP: {$nipKepsek}] & Guru Pengampu: {$guru} [NIP: {$nipGuru}], bertempat di: {$tempatPengesahan}, Tanggal: {$tanggalPengesahan}).
- BAGIAN G: 10 LAMPIRAN OPERASIONAL LENGKAP (Pisahkan tiap lampiran dengan <div class="page-break"></div>):
  1. Lampiran 1: LKPD Analisis Brief (Tabel 6 Pertanyaan Analitis)
  2. Lampiran 2: Lembar Perencanaan Kebutuhan Bahan & Peralatan
  3. Lampiran 3: Lembar Pembagian Peran Tim & Jadwal Produksi (Time Schedule)
  4. Lampiran 4: Standar Operasional Prosedur (SOP) & Checklist Keselamatan Kerja (K3)
  5. Lampiran 5: Jurnal Kerja Harian Siswa (Logbook Aktivitas TEFA)
  6. Lampiran 6: Checklist Quality Control (QC) Standar Industri
  7. Lampiran 7: Berita Acara Presentasi & Serah Terima Produk ke Klien
  8. Lampiran 8: Template Dokumentasi Portofolio Digital
  9. Lampiran 9: Lembar Refleksi Diri Peserta Didik
  10. Lampiran 10: Rubrik Penilaian Portofolio Digital & Konversi Nilai Akhir

DATA INPUT FORMULIR GURU:
- Satuan Pendidikan: {$satuan}
- Nama Kepala Sekolah: {$namaKepsek}
- NIP Kepala Sekolah: {$nipKepsek}
- Nama Guru Pengampu: {$guru}
- NIP Guru Pengampu: {$nipGuru}
- Kota / Tempat Pengesahan: {$tempatPengesahan}
- Tanggal Pengesahan: {$tanggalPengesahan}
- Program Keahlian: {$program}
- Konsentrasi Keahlian: {$konsentrasi}
- Elemen Pembelajaran: {$elemen}
- Mata Pelajaran: {$mapel}
- Fase / Kelas: {$fase}
- Semester: {$semester}
- Tahun Pelajaran: {$tahun}
- Alokasi Waktu: {$alokasi}
- Produk atau Jasa TEFA: {$produk}
- Materi / Topik: {$materi}
- Konteks TEFA: {$konteks}
- Klien / Konsumen: {$klien}
- Brief / Pesanan: {$brief}
- Sarana dan Prasarana: {$sarana}
- Mitra Industri: {$mitra}
- Platform Portofolio: {$portofolio}
- Kesiapan Peserta Didik: {$kesiapan}
- CP Khusus (jika ada): {$cpUser}
- TP Khusus (jika ada): {$tpUser}

Susun dokumen yang sangat rapi, mendalam, berbobot profesional, dan siap pakai bagi guru SMK!
PROMPT;
    }
}
