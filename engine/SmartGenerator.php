<?php
// engine/SmartGenerator.php

class SmartGenerator {
    public static function generate(array $in): string {
        $val = function($key, $default = '') use ($in) {
            $v = trim($in[$key] ?? '');
            return $v !== '' ? htmlspecialchars($v, ENT_QUOTES, 'UTF-8') : $default;
        };

        $satuan            = $val('satuan', 'SMK Negeri 1 Surabaya');
        $guru              = $val('guru', 'Pendidik Pengampu, S.Pd.');
        $nipGuru           = $val('nip_guru', '');
        $namaKepsek        = $val('nama_kepsek', '');
        $nipKepsek         = $val('nip_kepsek', '');
        $tempatPengesahan  = $val('tempat_pengesahan', '');
        $tanggalPengesahan = $val('tanggal_pengesahan', '');
        $program           = $val('program', 'Teknik Jaringan Komputer dan Telekomunikasi');
        $konsentrasi  = $val('konsentrasi', 'Teknik Komputer dan Jaringan');
        $elemen       = $val('elemen', 'Elemen Pembelajaran Terkait');
        $mapel        = $val('mapel', 'Konsentrasi Keahlian ' . $konsentrasi);
        $fase         = $val('fase', 'Fase F / Kelas XII');
        $semester     = $val('semester', 'Ganjil');
        $tahun        = $val('tahun', '2026/2027');
        $alokasi      = $val('alokasi', '12 JP (3 pertemuan @ 4 JP)');
        $produk       = $val('produk', 'Paket Jasa Instalasi & Konfigurasi Hotspot Router MikroTik');
        $materi       = $val('materi', 'Perencanaan, Perakitan, dan Pengujian ' . $produk);
        $konteksTefa  = $val('konteks_tefa', 'Unit Produksi Teaching Factory (TEFA) ' . $program . ' di Sekolah');
        $klien        = $val('klien', 'UMKM Binaan & Kantor Layanan Sekitar Sekolah');
        $brief        = $val('brief', 'Pengerjaan pesanan ' . $produk . ' sesuai spesifikasi fungsional, tenggat waktu, dan standar kepuasan klien');
        $sarana       = $val('sarana', 'Bengkel/Studio Praktik Kejuruan, Alat Uji Terkalibrasi, PC Server, dan Perangkat Kerja Standar Industri');
        $mitra        = $val('mitra', 'Mitra DUDI Rekanan Resmi Sekolah');
        $portofolio   = $val('portofolio', 'Google Sites / Web Showcase Portofolio Siswa');
        $kesiapan     = $val('kesiapan', 'Peserta didik memiliki pemahaman dasar konsep kerja dan kesiapan melaksanakan praktikum berbasis order nyata');

        // Deteksi apakah mapel umum
        $isMapelUmum = (stripos($program, 'Umum') !== false || stripos($mapel, 'Bahasa') !== false || stripos($mapel, 'Matematika') !== false || stripos($mapel, 'IPAS') !== false || stripos($mapel, 'Pancasila') !== false || stripos($mapel, 'Informatika') !== false);

        $cpDefault = $isMapelUmum
            ? "Peserta didik mampu menerapkan capaian elemen " . $elemen . " pada materi " . $mapel . " secara kontekstual untuk mendukung penyelesaian produk/jasa " . $produk . " pada alur kerja Teaching Factory sesuai standar mutu industri."
            : "Peserta didik mampu menerapkan capaian elemen " . $elemen . " dalam merencanakan, memproduksi, melakukan pengujian mutu (QC), serta menyerahkan hasil pengerjaan " . $produk . " sesuai alur operasional Teaching Factory dan standar industri.";
        $cp = $val('cp', $cpDefault);

        $tpDefault = $isMapelUmum
            ? "Melalui model Teaching Factory, peserta didik mampu memanfaatkan keterampilan elemen " . $elemen . " pada " . $mapel . " dalam menganalisis brief, menyusun dokumen penunjang, bernegosiasi/berkomunikasi dengan klien " . $klien . ", serta mendokumentasikan portofolio digital secara mandiri dan bertanggung jawab."
            : "Melalui model Teaching Factory pada elemen " . $elemen . ", peserta didik mampu menganalisis order, menyiapkan sarana/bahan, memproduksi " . $produk . ", menerapkan SOP K3, melaksanakan quality control, serta menyerahkan produk kepada klien " . $klien . " secara profesional.";
        $tp = $val('tp', $tpDefault);

        ob_start();
        ?>
        <h1>RENCANA PELAKSANAAN PEMBELAJARAN (RPP)</h1>
        <h1>PERENCANAAN PEMBELAJARAN MENDALAM BERBASIS TEACHING FACTORY (TEFA)</h1>

        <h2>A. IDENTITAS DAN KONTEKS PEMBELAJARAN</h2>
        <table>
            <tbody>
                <tr><th style="width:32%;">Satuan Pendidikan</th><td><?= $satuan ?></td></tr>
                <tr><th>Nama Guru Pengampu</th><td><?= $guru ?></td></tr>
                <tr><th>Mata Pelajaran</th><td><?= $mapel ?></td></tr>
                <tr><th>Program Keahlian</th><td><?= $program ?></td></tr>
                <tr><th>Konsentrasi Keahlian</th><td><?= $konsentrasi ?></td></tr>
                <tr><th>Elemen Pembelajaran</th><td><strong><?= $elemen ?></strong></td></tr>
                <tr><th>Fase / Kelas</th><td><?= $fase ?></td></tr>
                <tr><th>Semester</th><td><?= $semester ?></td></tr>
                <tr><th>Tahun Pelajaran</th><td><?= $tahun ?></td></tr>
                <tr><th>Alokasi Waktu</th><td><?= $alokasi ?></td></tr>
                <tr><th>Materi / Topik Pembelajaran</th><td><?= $materi ?></td></tr>
                <tr><th>Konteks TEFA / Unit Produksi</th><td><?= $konteksTefa ?></td></tr>
                <tr><th>Produk atau Jasa</th><td><?= $produk ?></td></tr>
                <tr><th>Klien / Konsumen Sasaran</th><td><?= $klien ?></td></tr>
                <tr><th>Brief / Ringkasan Pesanan</th><td><?= $brief ?></td></tr>
                <tr><th>Sarana dan Prasarana</th><td><?= $sarana ?></td></tr>
                <tr><th>Mitra Industri Pasangan</th><td><?= $mitra ?></td></tr>
                <tr><th>Platform Portofolio Digital</th><td><?= $portofolio ?></td></tr>
            </tbody>
        </table>

        <h2>B. IDENTIFIKASI</h2>
        <h3>1. Kesiapan Peserta Didik</h3>
        <p><?= $kesiapan ?>. Sebelum memasuki alur produksi TEFA, dilakukan pemetaan kompetensi awal peserta didik guna mengelompokkan peran kerja (project manager, tim teknis, tim QC, dan tim dokumentasi).</p>
        <p><strong>Rancangan Asesmen Diagnostik Awal:</strong></p>
        <ul>
            <li><strong>Tujuan Asesmen:</strong> Memetakan penguasaan konsep dasar dan kesiapan psikomotorik terkait <?= $materi ?>.</li>
            <li><strong>Teknik Asesmen:</strong> Tanya jawab kontekstual studi kasus klien, pre-test singkat, dan observasi keterampilan awal di lab/bengkel.</li>
            <li><strong>Instrumen:</strong> Lembar ceklis kesiapan kerja industri dan rubrik observasi sikap kerja 5R (Ringkas, Rapi, Resik, Rawat, Rajin).</li>
            <li><strong>Aspek yang Diperiksa:</strong> Pemahaman alur brief pesanan, penguasaan SOP alat, kedisiplinan K3, dan motivasi kerja tim.</li>
            <li><strong>Tindak Lanjut:</strong> Peserta didik yang telah siap diberikan peran pengarah teknis/QC, sementara yang membutuhkan bimbingan diberikan *scaffolding* pendampingan teman sebaya (*peer coaching*).</li>
        </ul>

        <h3>2. Karakteristik Materi</h3>
        <ul>
            <li><strong>Konsep Utama:</strong> Pemahaman prinsip kerja, spesifikasi teknis, dan standar kualitas industri dalam memproduksi <?= $produk ?>.</li>
            <li><strong>Keterampilan Prosedural:</strong> Langkah operasional terstandar: pembacaan brief order &rarr; perhitungan kebutuhan bahan &rarr; eksekusi pembuatan &rarr; pengujian kendali mutu (QC) &rarr; serah terima klien.</li>
            <li><strong>Tingkat Kesulitan:</strong> Kontekstual tingkat tinggi (mengintegrasikan hard skills teknis dengan soft skills komunikasi klien dan kepatuhan tenggat waktu).</li>
            <li><strong>Penerapan di Dunia Kerja:</strong> Berhubungan langsung dengan pesanan riil pada unit produksi sekolah dan pasar industri pasangan.</li>
            <li><strong>Potensi Miskonsepsi:</strong> Peserta didik menganggap pekerjaan selesai hanya saat produk terwujud, tanpa melakukan kalibrasi dan validasi kesesuaian brief klien.</li>
            <li><strong>Etika, Budaya Kerja, dan K3:</strong> Kepatuhan mutlak terhadap penggunaan APD, kerapian ruang kerja industri, etika kerahasiaan data pesanan, dan kejujuran pelaporan mutu.</li>
        </ul>

        <h3>3. Dimensi Profil Lulusan (DPL) / Profil Pelajar Pancasila</h3>
        <p>Penetapan dimensi profil lulusan dilakukan secara terukur dan relevan dengan karakteristik proyek:</p>
        <p>[ ] DPL 1 Keimanan dan Ketakwaan terhadap Tuhan Yang Maha Esa<br>
           [ ] DPL 2 Kewargaan Global<br>
           [✓] <strong>DPL 3 Penalaran Kritis:</strong> Menganalisis parameter pesanan klien, memecahkan kendala teknis saat proses produksi, serta mengevaluasi deviasi toleransi ukuran.<br>
           [✓] <strong>DPL 4 Kreativitas:</strong> Menemukan solusi efisiensi bahan, inovasi tata letak/desain penyajian, serta visualisasi portofolio digital yang menarik.<br>
           [✓] <strong>DPL 5 Kolaborasi:</strong> Bekerja sama dalam tim divisi kerja industri, saling mendukung antar-peran, dan menjalin komunikasi efektif.<br>
           [✓] <strong>DPL 6 Kemandirian:</strong> Bertanggung jawab penuh terhadap workstation/tugas kerja masing-masing sesuai target waktu (deadline).<br>
           [ ] DPL 7 Kesehatan Jasmani<br>
           [✓] <strong>DPL 8 Komunikasi:</strong> Menyampaikan progres pengerjaan, mempresentasikan hasil karya, dan melakukan penyerahan produk dengan santun dan profesional kepada klien.</p>

        <h2>C. DESAIN PEMBELAJARAN</h2>
        <h3>1. Capaian Pembelajaran (CP)</h3>
        <p><?= $cp ?></p>

        <h3>2. Tujuan Pembelajaran (TP)</h3>
        <p><?= $tp ?></p>

        <h3>3. Kriteria Ketercapaian Tujuan Pembelajaran (KKTP)</h3>
        <ol>
            <li>Mampu mengidentifikasi dan menguraikan spesifikasi kebutuhan order dari brief klien secara rinci.</li>
            <li>Mampu menyusun lembar rencana kebutuhan alat, bahan, kalkulasi estimasi waktu, serta jadwal pembagian peran tim.</li>
            <li>Mampu mengeksekusi tahapan pembuatan <?= $produk ?> sesuai SOP teknis dan standar K3 industri.</li>
            <li>Mampu melakukan uji coba kendali mutu (Quality Control) dan melakukan revisi perbaikan apabila ditemukan cacat produksi (*defect*).</li>
            <li>Mampu menyusun dokumentasi portofolio digital otentik pada platform <?= $portofolio ?> serta mempresentasikan berita acara serah terima dengan klien.</li>
        </ol>

        <h3>4. Pemahaman Bermakna</h3>
        <p>Pembelajaran TEFA membuktikan bahwa kompetensi kejuruan yang dipelajari memiliki nilai guna dan nilai ekonomi riil. Peserta didik memahami bahwa kepuasan konsumen ditentukan oleh integritas kualitas produk, kepatuhan prosedur keselamatan, dan ketepatan waktu serah terima.</p>

        <h3>5. Pertanyaan Pemantik</h3>
        <ol>
            <li>Mengapa analisis brief klien yang cermat menjadi penentu utama keberhasilan produksi <?= $produk ?>?</li>
            <li>Bagaimana cara memastikan setiap tahapan kerja kita mematuhi standar K3 dan tidak menimbulkan kerugian bahan baku (*zero waste*)?</li>
            <li>Apa tindakan profesional yang harus kita ambil apabila pada tahap Quality Control ditemukan ketidaksesuaian dengan ekspektasi klien?</li>
            <li>Bagaimana bukti portofolio digital otentik dapat menjadi portofolio karier profesional yang meyakinkan dunia industri (*DUDI*)?</li>
        </ol>

        <h3>6. Praktik Pedagogis</h3>
        <p>Pendekatan pembelajaran berpusat pada siswa (*Student-Centered Deep Learning*) dengan Model <strong>Teaching Factory (TEFA 6 Langkah/Sintaks)</strong> yang dipadukan dengan Project-Based Learning. Menggunakan diferensiasi proses berbasis kesiapan kerja, simulasi alur kerja industri, rotasi workstation, coaching berkala, dan umpan balik formatif.</p>

        <h3>7. Kemitraan Pembelajaran</h3>
        <p>Melibatkan <strong><?= $klien ?></strong> sebagai pemberi brief dan penilai autentik, serta supervisi berkala dari instruktur/guru tamu <strong><?= $mitra ?></strong> guna menjaga keselarasan standar industri mutakhir.</p>

        <h3>8. Lingkungan Pembelajaran</h3>
        <p>Bengkel/studio didesain menyerupai tata letak pabrik/kantor profesional (zonasi area penerimaan order, area pengerjaan, area inspeksi QC, dan area pengemasan). Menerapkan budaya kerja 5R dan papan informasi manajemen visual (*Kanban board*).</p>

        <h3>9. Pemanfaatan Digital</h3>
        <p>Pemanfaatan platform cloud untuk formulir brief pesanan, pelaporan jurnal kerja harian digital, dokumentasi video tutorial micro-skills, serta kurasi portofolio publik di <strong><?= $portofolio ?></strong>.</p>

        <h2>D. PENGALAMAN BELAJAR DAN SINTAKS TEFA</h2>
        <h3>1. Kegiatan Awal (Berkesadaran, Bermakna) — Orientasi & Briefing TEFA (Waktu: 15-20% Alokasi)</h3>
        <p><em>Fokus: Membangun kesadaran profesional bahwa peserta didik hadir sebagai insan kerja industri berintegritas.</em></p>
        <table>
            <thead>
                <tr><th style="width:50%;">Aktivitas Guru / Instruktur</th><th>Aktivitas Peserta Didik</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <ul>
                            <li>Memimpin doa pembuka dan memandu yel-yel budaya kerja industri / SMK Hebat.</li>
                            <li>Melakukan presensi dengan format cek kehadiran personel industri.</li>
                            <li>Menggelar *morning briefing*: memaparkan lembar brief pesanan dari klien <strong><?= $klien ?></strong> terkait pengerjaan <strong><?= $produk ?></strong>.</li>
                            <li>Menegaskan standar mutu, batas toleransi kesalahan, tenggat waktu (deadline), dan regulasi K3.</li>
                            <li>Membimbing pembagian tim kerja (Manajer Proyek, Teknisi, QC, Dokumentator).</li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li>Mengikuti briefing pagi dengan sikap tegap dan fokus profesional.</li>
                            <li>Mencermati dokumen brief order dan mencatat poin-poin krusial permintaan klien.</li>
                            <li>Mengajukan pertanyaan klarifikasi jika terdapat rincian brief yang belum spesifik.</li>
                            <li>Menempati workstation kelompok kerja sesuai penugasan peran.</li>
                            <li>Memeriksa kelengkapan APD dan kesiapan sarana kerja di meja praktik.</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        <p><em>Bukti Portofolio Dokumen Tahap Awal: Dokumen lembar order brief klien, absensi tim kerja, dan ceklis kelayakan alat awal.</em></p>

        <h3>2. Kegiatan Inti (Bermakna, Mendalam, Menggembirakan)</h3>
        <p><strong>a. Tahap Memahami — Analisis Brief & Riset Kebutuhan Klien</strong></p>
        <p><em>Fokus: Penguasaan konsep produk, telaah standar teknis, dan kalkulasi sumber daya sebelum menyentuh bahan baku.</em></p>
        <table>
            <thead><tr><th style="width:50%;">Aktivitas Guru</th><th>Aktivitas Peserta Didik</th></tr></thead>
            <tbody>
                <tr>
                    <td>
                        <ul>
                            <li>Menyediakan sampel produk referensi berstandar industri.</li>
                            <li>Mendampingi peserta didik menganalisis lembar brief menggunakan metode 5W+1H teknis.</li>
                            <li>Memantik diskusi kalkulasi efisiensi material dan mitigasi kendala teknis.</li>
                            <li>Memvalidasi Lembar Kerja Analisis Brief (LKPD 1) sebelum memberikan persetujuan produksi (*go/no-go*).</li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li>Membaca dan membedah dokumen brief pesanan secara kolaboratif.</li>
                            <li>Mengidentifikasi preferensi klien, batasan fungsional, dan estetika produk.</li>
                            <li>Mencari referensi teknis industri dan membuat sketsa konsep/diagram alir pengerjaan.</li>
                            <li>Mengisi LKPD Analisis Brief dan mempresentasikannya kepada guru untuk memperoleh *Work Order Ticket*.</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        <p><em>Bukti Portofolio: LKPD Analisis Brief terisi lengkap, moodboard/sketsa desain teknis, dan lembar kalkulasi kebutuhan.</em></p>

        <p><strong>b. Tahap Mengaplikasi — Perencanaan Operasional, K3, & Produksi Nyata</strong></p>
        <p><em>Fokus: Penerapan keterampilan vokasi, disiplin SOP manufaktur/layanan, ketepatan teknik, dan keselamatan kerja.</em></p>
        <table>
            <thead><tr><th style="width:50%;">Aktivitas Guru</th><th>Aktivitas Peserta Didik</th></tr></thead>
            <tbody>
                <tr>
                    <td>
                        <ul>
                            <li>Mengawasi kepatuhan penggunaan APD dan penerapan SOP K3 di seluruh workstation.</li>
                            <li>Melakukan *mobile coaching*: mengobservasi ketelitian langkah kerja siswa dan memberikan umpan balik langsung tanpa memotong kreativitas.</li>
                            <li>Memfasilitasi pemecahan masalah jika siswa menemui kendala mesin/material.</li>
                            <li>Mengingatkan manajemen waktu produksi sesuai time-schedule industri.</li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li>Menyiapkan alat dan bahan baku sesuai spesifikasi lembar perencanaan (Lampiran 2).</li>
                            <li>Melaksanakan tahapan produksi <strong><?= $produk ?></strong> berpedoman pada SOP kerja resmi.</li>
                            <li>Mencatat progres dan parameter pengerjaan pada Jurnal Kerja Harian (Lampiran 5).</li>
                            <li>Mengabadikan foto/video tahapan kritis produksi sebagai dokumentasi portofolio otentik.</li>
                            <li>Menjaga kebersihan workstation (*clean as you go*).</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        <p><em>Bukti Portofolio: Logbook/jurnal kerja, dokumentasi foto/video tahapan eksekusi teknis, serta draft produk awal sebelum uji.</em></p>

        <p><strong>c. Tahap Merefleksi — Quality Control (QC), Umpan Balik, & Reworking</strong></p>
        <p><em>Fokus: Pengecekan standar mutu objektif, penerimaan feedback kritis, dan budaya pantang menyerahkan produk cacat.</em></p>
        <table>
            <thead><tr><th style="width:50%;">Aktivitas Guru</th><th>Aktivitas Peserta Didik</th></tr></thead>
            <tbody>
                <tr>
                    <td>
                        <ul>
                            <li>Membimbing tim QC internal siswa menggunakan instrumen Checklist QC Industri (Lampiran 6).</li>
                            <li>Bertindak sebagai perwakilan klien atau mengundang klien langsung untuk pengujian fungsi.</li>
                            <li>Memberikan feedback spesifik terhadap aspek yang perlu penyempurnaan (*rework/finishing*).</li>
                            <li>Mengarahkan siswa membandingkan kualitas hasil kerja dengan kriteria KKTP.</li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li>Melakukan inspeksi menyeluruh terhadap produk yang telah dirakit/diselesaikan.</li>
                            <li>Menguji fungsi, ketahanan, estetika, dan kesesuaian dimensi dengan lembar brief.</li>
                            <li>Mencatat hasil inspeksi pada form QC dan mendiskusikan temuan cacat produksi.</li>
                            <li>Melakukan perbaikan (*finishing/revisi*) hingga produk dinyatakan <strong>PASS / LOLOS QC</strong>.</li>
                            <li>Menyusun catatan evaluasi deviasi mutu.</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        <p><em>Bukti Portofolio: Lembar checklist Quality Control yang telah ditandatangani, catatan revisi, dan foto perbandingan before-after.</em></p>

        <h3>3. Kegiatan Penutup (Berkesadaran) — Packaging, Delivery ke Klien, & Refleksi Akhir (Waktu: 15% Alokasi)</h3>
        <p><em>Fokus: Penyerahan produk profesional, penandatanganan berita acara, apresiasi proses, dan refleksi metakognisi.</em></p>
        <table>
            <thead><tr><th style="width:50%;">Aktivitas Guru</th><th>Aktivitas Peserta Didik</th></tr></thead>
            <tbody>
                <tr>
                    <td>
                        <ul>
                            <li>Memfasilitasi sesi serah terima produk kepada klien <strong><?= $klien ?></strong>.</li>
                            <li>Menyaksikan penandatanganan Berita Acara Serah Terima (Lampiran 7).</li>
                            <li>Memberikan apresiasi atas kegigihan dan pencapaian kompetensi seluruh tim.</li>
                            <li>Memandu sesi refleksi diri peserta didik (Lampiran 9).</li>
                            <li>Menutup sesi dengan evaluasi budaya kerja 5R dan doa bersama.</li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li>Melakukan pengemasan (*packaging*) produk secara higienis, rapi, dan berlabel resmi.</li>
                            <li>Mempresentasikan hasil pengerjaan kepada klien dan menjelaskan keunggulan fitur/produk.</li>
                            <li>Menyerahkan produk dan meminta tanda tangan berita acara serah terima.</li>
                            <li>Mengisi lembar refleksi diri mengenai pembelajaran bermakna yang didapatkan.</li>
                            <li>Mengunggah seluruh artefak kerja ke laman portofolio digital <strong><?= $portofolio ?></strong>.</li>
                            <li>Membersihkan dan merapikan workstation ke kondisi semula.</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        <p><em>Bukti Portofolio: Berita acara serah terima bertandatangan klien, lembar refleksi diri, link portofolio digital publik.</em></p>

        <h2>E. INTEGRASI PORTOFOLIO DIGITAL</h2>
        <p><strong>Tujuan Utama:</strong> Membangun rekam jejak digital (*digital credentials*) unjuk kerja siswa yang dapat diakses langsung oleh industri pasangan dan penguji sertifikasi kompetensi.<br>
        <strong>Platform Publikasi:</strong> <?= $portofolio ?><br>
        <strong>Jenis Bukti Karya Wajib:</strong>
        <ol>
            <li>Dokumen telaah brief pesanan dan rencana kalkulasi biaya/bahan.</li>
            <li>Dokumentasi visual foto dan klip video beresolusi tinggi saat melaksanakan proses produksi nyata.</li>
            <li>Sertifikat kendali mutu (Lembar Lolos QC industri).</li>
            <li>Foto produk akhir yang telah dikemas siap serah terima.</li>
            <li>Berita Acara penyerahan produk dan testimoni kepuasan klien.</li>
            <li>Esai refleksi individu mengenai pengalaman belajar mendalam berbasis industri.</li>
        </ol>
        <strong>Struktur Halaman Web Portofolio:</strong> Memuat Header Identitas Siswa/NISN, Latar Belakang Proyek TEFA, Galeri Proses & Hasil, Lembar Uji Mutu, dan Kontak Profesional LinkedIn/Email.</p>

        <h2>F. ASESMEN PEMBELAJARAN</h2>
        <h3>1. Asesmen Awal (Diagnostik)</h3>
        <p>Pemetaan kesiapan kognitif dan psikomotorik sebelum pembagian kelompok TEFA melalui kuis singkat dan wawancara orientasi brief.</p>

        <h3>2. Asesmen Formatif</h3>
        <p>Penilaian berkelanjutan selama proses pembelajaran: keaktifan telaah brief, kedisiplinan APD K3 di bengkel, keterampilan teknis penanganan alat, kekompakan kerja tim, dan ketelitian Quality Control.</p>

        <h3>3. Asesmen Sumatif / Unjuk Kerja Nyata</h3>
        <p>Penilaian holistik atas keberhasilan menghasilkan produk <strong><?= $produk ?></strong> yang memenuhi brief pesanan, tepat waktu, lolos uji mutu, serta kelengkapan publikasi portofolio digital.</p>

        <h3>4. Rubrik Penilaian Unjuk Kerja TEFA</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:20%;">Kriteria Penilaian</th>
                    <th style="width:20%;">Perlu Bimbingan (1)</th>
                    <th style="width:20%;">Cukup (2)</th>
                    <th style="width:20%;">Baik (3)</th>
                    <th style="width:20%;">Sangat Baik (4)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Analisis Brief & Kebutuhan Klien</strong></td>
                    <td>Tidak mampu memahami brief order dan salah mengidentifikasi kebutuhan klien.</td>
                    <td>Memahami sebagian brief; butuh arahan intensif untuk menyusun daftar kebutuhan.</td>
                    <td>Mampu menerjemahkan brief klien ke dalam spesifikasi teknis dengan tepat dan mandiri.</td>
                    <td>Menganalisis brief secara komprehensif, mengantisipasi risiko teknis, dan memberi saran nilai tambah ke klien.</td>
                </tr>
                <tr>
                    <td><strong>Penerapan SOP & K3 Produksi</strong></td>
                    <td>Mengabaikan APD, tidak mematuhi urutan SOP kerja, dan membahayakan workstation.</td>
                    <td>Menggunakan APD namun sesekali melanggar alur SOP; memerlukan teguran pengawas.</td>
                    <td>Disiplin mengenakan APD lengkap dan menjalankan setiap prosedur kerja sesuai SOP.</td>
                    <td>Menjadi teladan K3 di bengkel, menerapkan budaya 5R sempurna, dan mengingatkan rekan tim.</td>
                </tr>
                <tr>
                    <td><strong>Kualitas Produk Akhir</strong></td>
                    <td>Produk cacat berat, gagal fungsi total, dan ditolak oleh klien pemesan.</td>
                    <td>Produk berfungsi namun memiliki cacat minor dan dimensi tidak presisi; butuh revisi mayor.</td>
                    <td>Produk berfungsi prima, tampilan rapi, presisi, dan sesuai dengan seluruh brief klien.</td>
                    <td>Kualitas produk setara standar industri komersial, finishing sempurna, dan melampaui ekspektasi klien.</td>
                </tr>
                <tr>
                    <td><strong>Kolaborasi & Manajemen Waktu</strong></td>
                    <td>Pasif, memicu konflik kelompok, dan penyelesaian jauh melebihi tenggat waktu.</td>
                    <td>Mengerjakan tugas sebatas instruksi; penyelesaian tepat di batas akhir dengan ketergesaan.</td>
                    <td>Bekerja sama secara aktif, komunikatif, dan menyelesaikan pekerjaan sesuai jadwal terencana.</td>
                    <td>Menunjukkan kepemimpinan kolaboratif yang solid, memotivasi tim, dan selesai lebih cepat dari target waktu.</td>
                </tr>
                <tr>
                    <td><strong>Refleksi Diri & Penyelesaian Masalah</strong></td>
                    <td>Tidak mampu mengidentifikasi kekurangan diri dan menolak umpan balik evaluator.</td>
                    <td>Menyadari kesalahan namun bingung merumuskan strategi perbaikan untuk masa depan.</td>
                    <td>Menyampaikan refleksi objektif atas kendala yang dihadapi serta solusi yang telah diterapkan.</td>
                    <td>Refleksi mendalam, kritis terhadap alur kerja pribadi, dan memiliki rencana aksi konkrit untuk peningkatan karier.</td>
                </tr>
            </tbody>
        </table>

        <h3>5. Rubrik Penilaian Portofolio Digital</h3>
        <table>
            <thead><tr><th style="width:35%;">Indikator Penilaian</th><th>Deskripsi Kriteria Keberhasilan</th></tr></thead>
            <tbody>
                <tr><td>Kelengkapan Artefak Proyek</td><td>Memuat seluruh tahapan: brief, kalkulasi bahan, logbook, hasil QC, dan berita acara penyerahan.</td></tr>
                <tr><td>Keaslian & Bukti Unjuk Kerja</td><td>Menampilkan foto dan rekaman video otentik proses kerja siswa yang bersangkutan (bebas plagiasi).</td></tr>
                <tr><td>Kerapian Tata Letak & Navigasi</td><td>Struktur laman web terorganisasi intuitif, tipografi mudah dibaca, dan tautan dokumen responsif.</td></tr>
                <tr><td>Kejelasan Narasi Teknis</td><td>Uraian langkah kerja, terminologi kejuruan, dan spesifikasi alat dipaparkan dengan bahasa baku dan jelas.</td></tr>
                <tr><td>Refleksi Pembelajaran</td><td>Memuat intisari pengalaman belajar, pemahaman bermakna, dan aspirasi penerapan di industri nyata.</td></tr>
            </tbody>
        </table>

        <table class="ttd">
            <tr>
                <td>
                    Mengetahui,<br>
                    Kepala <?= $satuan ?><br><br><br><br><br>
                    <strong>( <?= !empty($namaKepsek) ? $namaKepsek : '………………………………………………' ?> )</strong><br>
                    NIP. <?= !empty($nipKepsek) ? $nipKepsek : '……………………………………………' ?>
                </td>
                <td>
                    Disahkan di: <?= !empty($tempatPengesahan) ? $tempatPengesahan : '....................' ?>, Tanggal: <?= !empty($tanggalPengesahan) ? $tanggalPengesahan : '..............' ?><br>
                    Guru Pengampu Kejuruan / TEFA<br><br><br><br><br>
                    <strong>( <?= !empty($guru) ? $guru : '………………………………………………' ?> )</strong><br>
                    NIP. <?= !empty($nipGuru) ? $nipGuru : '……………………………………………' ?>
                </td>
            </tr>
        </table>

        <!-- ==================== G. 10 LAMPIRAN OPERASIONAL LENGKAP ==================== -->
        
        <div class="page-break"></div>
        <h2>G. LAMPIRAN OPERASIONAL PEMBELAJARAN TEFA</h2>
        <p>Seluruh instrumen lampiran di bawah ini merupakan bagian tak terpisahkan dari modul ajar TEFA dan dapat digandakan untuk implementasi di bengkel/studio.</p>

        <h3>Lampiran 1. Lembar Kerja Peserta Didik (LKPD) — Analisis Brief Klien</h3>
        <p><strong>Nama Produk/Jasa:</strong> <?= $produk ?> &nbsp;&nbsp;|&nbsp;&nbsp; <strong>Klien Pemesan:</strong> <?= $klien ?><br>
        <strong>Kelompok Kerja:</strong> ........................................................... &nbsp;&nbsp;|&nbsp;&nbsp; <strong>Kelas / Fase:</strong> <?= $fase ?></p>
        <table>
            <thead>
                <tr>
                    <th style="width:6%; text-align:center;">No</th>
                    <th style="width:44%;">Pertanyaan Analitis Brief</th>
                    <th>Hasil Telaah & Rencana Solusi Tim Siswa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">1</td>
                    <td>Apa spesifikasi inti dan fungsi utama yang diminta oleh klien?</td>
                    <td><?= $produk ?> — Meliputi pemenuhan fungsi operasional andal, daya tahan optimal, dan kemudahan penggunaan bagi klien.</td>
                </tr>
                <tr>
                    <td style="text-align:center;">2</td>
                    <td>Siapa target pengguna akhir (*end-user*) dari produk ini dan bagaimana karakteristiknya?</td>
                    <td><?= $klien ?> — Memerlukan solusi siap guna (*turn-key*) yang praktis, berkualitas standar industri, dan berbiaya efisien.</td>
                </tr>
                <tr>
                    <td style="text-align:center;">3</td>
                    <td>Standar kualitas, estetika, atau toleransi ukuran apa yang harus dipenuhi?</td>
                    <td>Kerapian sambungan/pemasangan, presisi parameter, kesesuaian brief 100%, dan nihil cacat visual (*zero-scratch*).</td>
                </tr>
                <tr>
                    <td style="text-align:center;">4</td>
                    <td>Alat kerja utama, mesin bengkel, serta software apa yang wajib disiapkan?</td>
                    <td><?= $sarana ?>.</td>
                </tr>
                <tr>
                    <td style="text-align:center;">5</td>
                    <td>Apa potensi kendala teknis saat produksi dan bagaimana mitigasi pencegahannya?</td>
                    <td>Keterbatasan pasokan bahan dan kesalahan kalibrasi awal; dicegah dengan cek ulang bahan sebelum potong/pasang dan uji alat berkala.</td>
                </tr>
                <tr>
                    <td style="text-align:center;">6</td>
                    <td>Referensi standar industri / manual book apa yang dijadikan rujukan pengerjaan?</td>
                    <td>Standar Operasional Prosedur (SOP) Industri Mitra <?= $mitra ?> dan regulasi teknis kejuruan terkait.</td>
                </tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 2. Lembar Perencanaan Kebutuhan Bahan & Peralatan</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:6%; text-align:center;">No</th>
                    <th style="width:34%;">Nama Bahan / Komponen / Alat</th>
                    <th style="width:25%;">Spesifikasi Teknis</th>
                    <th style="width:15%; text-align:center;">Jumlah</th>
                    <th style="width:20%; text-align:center;">Status Ketersediaan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">1</td>
                    <td>Bahan Baku Utama Modul Produksi</td>
                    <td>Standar OEM / Industri Sesuai Katalog</td>
                    <td style="text-align:center;">1 Paket</td>
                    <td style="text-align:center;">Tersedia di Gudang</td>
                </tr>
                <tr>
                    <td style="text-align:center;">2</td>
                    <td>Perangkat Pengujian / Alat Ukur Kalibrasi</td>
                    <td>Terkalibrasi Standar Lab Industri</td>
                    <td style="text-align:center;">1 Set Lengkap</td>
                    <td style="text-align:center;">Tersedia di Lab</td>
                </tr>
                <tr>
                    <td style="text-align:center;">3</td>
                    <td>Bahan Penolong & Perlengkapan Finishing</td>
                    <td>Kualitas Kelas A / Ramah Lingkungan</td>
                    <td style="text-align:center;">Secukupnya</td>
                    <td style="text-align:center;">Tersedia</td>
                </tr>
                <tr>
                    <td style="text-align:center;">4</td>
                    <td>Material Pengemasan (*Packaging*) Protektif</td>
                    <td>Box Karton / Segel Plastik / Label Brand TEFA</td>
                    <td style="text-align:center;">1 Unit</td>
                    <td style="text-align:center;">Tersedia di Unit TEFA</td>
                </tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 3. Lembar Pembagian Peran Tim & Jadwal Produksi (Time Schedule)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:6%; text-align:center;">No</th>
                    <th style="width:24%;">Nama Peserta Didik</th>
                    <th style="width:25%;">Tanggung Jawab Divisi</th>
                    <th style="width:25%;">Target Pengerjaan</th>
                    <th>Paraf Siswa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">1</td>
                    <td>........................................</td>
                    <td><strong>Manajer Proyek (Leader):</strong> Mengoordinasikan alur kerja, komunikasi dengan klien & guru pembimbing.</td>
                    <td>Pertemuan 1 - 3 (Penuh)</td>
                    <td>................</td>
                </tr>
                <tr>
                    <td style="text-align:center;">2</td>
                    <td>........................................</td>
                    <td><strong>Divisi Teknis / Eksekutor:</strong> Menjalankan proses perakitan/pembuatan fisik produk sesuai SOP.</td>
                    <td>Pertemuan 1 & 2</td>
                    <td>................</td>
                </tr>
                <tr>
                    <td style="text-align:center;">3</td>
                    <td>........................................</td>
                    <td><strong>Divisi Quality Control (QC):</strong> Melakukan inspeksi pengujian mutu, checklist toleransi, dan uji fungsi.</td>
                    <td>Pertemuan 2 & 3</td>
                    <td>................</td>
                </tr>
                <tr>
                    <td style="text-align:center;">4</td>
                    <td>........................................</td>
                    <td><strong>Divisi Dokumentasi & Portofolio:</strong> Merekam foto/video proses kerja dan menyusun publikasi digital.</td>
                    <td>Pertemuan 1 - 3</td>
                    <td>................</td>
                </tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 4. Standar Operasional Prosedur (SOP) & Checklist Keselamatan Kerja (K3)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:6%; text-align:center;">No</th>
                    <th style="width:64%;">Poin Kritis Keselamatan & Kesehatan Kerja (K3)</th>
                    <th style="width:15%; text-align:center;">Terpenuhi</th>
                    <th style="width:15%; text-align:center;">Belum Terpenuhi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">1</td>
                    <td>Mengenakan Alat Pelindung Diri (APD) wajib (Wearpack, Kacamata Pelindung, Sepatu Safety, Masker) sesuai zonasi bengkel.</td>
                    <td style="text-align:center;">[  ]</td>
                    <td style="text-align:center;">[  ]</td>
                </tr>
                <tr>
                    <td style="text-align:center;">2</td>
                    <td>Memeriksa kondisi kelistrikan, tombol emergency stop, dan grounding mesin sebelum menyalakan daya.</td>
                    <td style="text-align:center;">[  ]</td>
                    <td style="text-align:center;">[  ]</td>
                </tr>
                <tr>
                    <td style="text-align:center;">3</td>
                    <td>Menempatkan perkakas tangan (*hand tools*) pada tool-tray rapi dan tidak tercecer di lantai kerja.</td>
                    <td style="text-align:center;">[  ]</td>
                    <td style="text-align:center;">[  ]</td>
                </tr>
                <tr>
                    <td style="text-align:center;">4</td>
                    <td>Penanganan dan penyimpanan bahan kimia/limbah produksi mematuhi petunjuk lembar keselamatan bahan (MSDS).</td>
                    <td style="text-align:center;">[  ]</td>
                    <td style="text-align:center;">[  ]</td>
                </tr>
                <tr>
                    <td style="text-align:center;">5</td>
                    <td>Melakukan pembersihan area kerja (5R: Ringkas, Rapi, Resik, Rawat, Rajin) pada saat jeda dan penutupan praktikum.</td>
                    <td style="text-align:center;">[  ]</td>
                    <td style="text-align:center;">[  ]</td>
                </tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 5. Jurnal Kerja Harian Siswa (Logbook Aktivitas TEFA)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:14%; text-align:center;">Pertemuan / Tgl</th>
                    <th style="width:30%;">Tahapan Aktivitas Produksi yang Dikerjakan</th>
                    <th style="width:26%;">Kendala Teknis yang Muncul di Lapangan</th>
                    <th style="width:20%;">Solusi / Tindak Lanjut Perbaikan</th>
                    <th style="width:10%; text-align:center;">Paraf Guru</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">Pertemuan 1<br>(Orientasi & Brief)</td>
                    <td>Menerima order brief klien, membagi peran tim, dan menyelesaikan LKPD 1.</td>
                    <td>Klarifikasi spesifikasi tegangan/material klien belum detail.</td>
                    <td>Menghubungi perwakilan klien untuk konfirmasi ulang rincian.</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="text-align:center;">Pertemuan 2<br>(Eksekusi Produksi)</td>
                    <td>Perakitan mekanis/konfigurasi sistem <strong><?= $produk ?></strong> sesuai SOP.</td>
                    <td>Ditemukan komponen presisi yang butuh kalibrasi ulang.</td>
                    <td>Melakukan fine-tuning instrumen dipandu instruktur bengkel.</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="text-align:center;">Pertemuan 3<br>(QC & Delivery)</td>
                    <td>Uji coba mutu QC, revisi minor, pengemasan, dan serah terima klien.</td>
                    <td>Cacat visual ringan pada bagian permukaan kemasan.</td>
                    <td>Finishing ulang permukaan dan penggantian label baru berstandar TEFA.</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 6. Checklist Quality Control (QC) Standar Industri</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:6%; text-align:center;">No</th>
                    <th style="width:34%;">Parameter Kualitas Produk TEFA</th>
                    <th style="width:20%;">Toleransi / Standar Acuan</th>
                    <th style="width:12%; text-align:center;">Hasil Uji</th>
                    <th style="width:12%; text-align:center;">Status</th>
                    <th>Catatan Auditor Mutu</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">1</td>
                    <td>Kesesuaian Dimensi & Spesifikasi Brief</td>
                    <td>Toleransi &plusmn; 0.5% dari gambar kerja</td>
                    <td style="text-align:center;">Sesuai</td>
                    <td style="text-align:center;"><strong>PASS</strong></td>
                    <td>Presisi sesuai pesanan.</td>
                </tr>
                <tr>
                    <td style="text-align:center;">2</td>
                    <td>Uji Fungsi Utama (*Operational Test*)</td>
                    <td>100% Fitur berjalan tanpa eror</td>
                    <td style="text-align:center;">Lolos</td>
                    <td style="text-align:center;"><strong>PASS</strong></td>
                    <td>Performa stabil pada uji beban.</td>
                </tr>
                <tr>
                    <td style="text-align:center;">3</td>
                    <td>Kerapian Rakitan / Estetika Visual</td>
                    <td>Bebas goresan, sambungan presisi</td>
                    <td style="text-align:center;">Bersih & Rapi</td>
                    <td style="text-align:center;"><strong>PASS</strong></td>
                    <td>Standar estetik terpenuhi.</td>
                </tr>
                <tr>
                    <td style="text-align:center;">4</td>
                    <td>Kelengkapan Manual & Label Kemasan</td>
                    <td>User manual + kartu garansi TEFA terlampir</td>
                    <td style="text-align:center;">Lengkap</td>
                    <td style="text-align:center;"><strong>PASS</strong></td>
                    <td>Siap diserahkan ke klien.</td>
                </tr>
            </tbody>
        </table>
        <p><strong>Kesimpulan Hasil Uji QC:</strong> [ ✓ ] <strong>DITERIMA (PASSED)</strong> &nbsp;&nbsp;&nbsp;&nbsp; [   ] <strong>DITOLAK / PERLU REVISI (REWORK)</strong><br>
        Auditor QC (Siswa): ............................................ &nbsp;&nbsp;&nbsp;&nbsp; Verifikator (Guru/Instruktur): ............................................</p>

        <div class="page-break"></div>
        <h3>Lampiran 7. Berita Acara Presentasi & Serah Terima Produk ke Klien</h3>
        <table>
            <tbody>
                <tr>
                    <th style="width:32%;">Nama Paket Pekerjaan</th>
                    <td><strong><?= $produk ?></strong></td>
                </tr>
                <tr>
                    <th>Unit Produksi Sekolah</th>
                    <td><?= $konteksTefa ?> — <?= $satuan ?></td>
                </tr>
                <tr>
                    <th>Pihak yang Menyerahkan</th>
                    <td>Tim Siswa TEFA: ................................................................ (Ketua Proyek)</td>
                </tr>
                <tr>
                    <th>Pihak yang Menerima (Klien)</th>
                    <td><strong><?= $klien ?></strong></td>
                </tr>
                <tr>
                    <th>Hari, Tanggal Serah Terima</th>
                    <td>...........................................................................................................</td>
                </tr>
                <tr>
                    <th>Tempat Penyerahan</th>
                    <td>Showroom / Bengkel TEFA <?= $satuan ?></td>
                </tr>
                <tr>
                    <th>Pernyataan Klien</th>
                    <td>Klien telah memeriksa langsung performa, fungsi, dan kelayakan fisik produk. Produk dinyatakan diterima dalam kondisi baik, berfungsi penuh, serta memenuhi kesepakatan pesanan awal.</td>
                </tr>
                <tr>
                    <th>Catatan / Feedback Klien</th>
                    <td>...............................................................................................................................................<br>
                    ...............................................................................................................................................</td>
                </tr>
            </tbody>
        </table>
        <table class="ttd">
            <tr>
                <td>Yang Menyerahkan,<br>Ketua Tim Siswa TEFA<br><br><br><br><strong>( ……………………………… )</strong></td>
                <td>Yang Menerima,<br>Perwakilan Klien / Konsumen<br><br><br><br><strong>( <?= $klien ?> )</strong></td>
            </tr>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 8. Template Dokumentasi Portofolio Digital</h3>
        <p>Gunakan format penyusunan berikut saat mendokumentasikan karya ke laman <strong><?= $portofolio ?></strong>:</p>
        <table>
            <thead>
                <tr>
                    <th style="width:28%;">Bagian Halaman Web</th>
                    <th>Konten yang Wajib Diunggah Peserta Didik</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Hero / Header Identitas</strong></td>
                    <td>Pas foto berseragam kerja rapi, nama lengkap, NISN, konsentrasi keahlian, dan tautan akun LinkedIn/GitHub/Sosial Media profesional.</td>
                </tr>
                <tr>
                    <td><strong>Konteks & Ringkasan Proyek</strong></td>
                    <td>Nama proyek "<?= $produk ?>", profil pemesan "<?= $klien ?>", latar belakang pesanan, dan kontribusi spesifik peserta didik dalam tim.</td>
                </tr>
                <tr>
                    <td><strong>Galeri Proses (*Behind the Scenes*)</strong></td>
                    <td>Foto carousel atau video timelapse proses pengerjaan: tahap telaah brief, perakitan bengkel, penggunaan APD, dan diskusi pemecahan masalah.</td>
                </tr>
                <tr>
                    <td><strong>Bukti Kendali Mutu (QC)</strong></td>
                    <td>Tangkapan layar instrumen pengujian, sertifikat kelolosan QC yang telah ditandatangani auditor mutu, dan tabel toleransi presisi.</td>
                </tr>
                <tr>
                    <td><strong>Showcase Hasil Akhir</strong></td>
                    <td>Foto *product photography* berlatar putih bersih dengan pencahayaan studio atau video demo fungsionalitas durasi 60-90 detik.</td>
                </tr>
                <tr>
                    <td><strong>Refleksi & Testimoni</strong></td>
                    <td>Ringkasan pembelajaran bermakna yang dipetik, kesulitan yang berhasil diatasi, serta scan lembar Berita Acara serah terima bertandatangan klien.</td>
                </tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 9. Lembar Refleksi Diri Peserta Didik (*Student Metacognitive Sheet*)</h3>
        <p><em>Isilah dengan jujur sesuai pengalaman nyata yang Anda rasakan selama melaksanakan proyek TEFA ini.</em></p>
        <table>
            <tbody>
                <tr>
                    <th style="width:38%;">1. Kompetensi teknis baru apa yang paling signifikan saya kuasai setelah menyelesaikan proyek ini?</th>
                    <td>................................................................................................................................................<br>
                    ................................................................................................................................................</td>
                </tr>
                <tr>
                    <th>2. Bagaimana kontribusi nyata saya dalam menjaga kekompakan dan kelancaran kerja kelompok?</th>
                    <td>................................................................................................................................................<br>
                    ................................................................................................................................................</td>
                </tr>
                <tr>
                    <th>3. Kendala paling menantang apa yang saya hadapi saat produksi dan bagaimana saya mengatasinya?</th>
                    <td>................................................................................................................................................<br>
                    ................................................................................................................................................</td>
                </tr>
                <tr>
                    <th>4. Mengapa penerapan budaya K3 dan 5R di bengkel sangat krusial bagi keselamatan kerja saya?</th>
                    <td>................................................................................................................................................<br>
                    ................................................................................................................................................</td>
                </tr>
                <tr>
                    <th>5. Apabila diberi kesempatan mengulang proyek ini, apa inovasi atau perbaikan yang akan saya lakukan?</th>
                    <td>................................................................................................................................................<br>
                    ................................................................................................................................................</td>
                </tr>
            </tbody>
        </table>

        <div class="page-break"></div>
        <h3>Lampiran 10. Rubrik Penilaian Portofolio Digital & Konversi Nilai Akhir</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:25%;">Komponen Penilaian</th>
                    <th style="width:15%; text-align:center;">Bobot Nilai</th>
                    <th style="width:35%;">Indikator Skor Maksimal (100)</th>
                    <th style="width:25%; text-align:center;">Skor Perolehan Siswa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Proses Kerja & Disiplin K3</strong></td>
                    <td style="text-align:center;">25%</td>
                    <td>Kepatuhan SOP, konsistensi APD, dan budaya 5R sempurna.</td>
                    <td style="text-align:center;">...........</td>
                </tr>
                <tr>
                    <td><strong>Kualitas Hasil Produk TEFA</strong></td>
                    <td style="text-align:center;">35%</td>
                    <td>Fungsi 100% sukses, presisi tinggi, dan lolos verifikasi QC.</td>
                    <td style="text-align:center;">...........</td>
                </tr>
                <tr>
                    <td><strong>Kelengkapan Portofolio Digital</strong></td>
                    <td style="text-align:center;">20%</td>
                    <td>Memuat artefak otentik, estetis, dan terdokumentasi lengkap di web.</td>
                    <td style="text-align:center;">...........</td>
                </tr>
                <tr>
                    <td><strong>Kepuasan Klien & Sikap Kerja</strong></td>
                    <td style="text-align:center;">20%</td>
                    <td>Berita acara serah terima disetujui, santun, dan tepat waktu.</td>
                    <td style="text-align:center;">...........</td>
                </tr>
                <tr>
                    <th colspan="3" style="text-align:right;">NILAI AKHIR MODUL TEFA (Skala 0 - 100):</th>
                    <th style="text-align:center;"><strong>........... / 100</strong></th>
                </tr>
            </tbody>
        </table>
        <p><em>Predikat Kelulusan: 90 - 100 (Sangat Kompeten / Industri Ready), 80 - 89 (Kompeten), 70 - 79 (Cukup Kompeten), &lt; 70 (Belum Kompeten / Remidial).</em></p>
        <?php
        return ob_get_clean();
    }
}
