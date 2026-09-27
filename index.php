<?php
// index.php
require_once __DIR__ . '/config/app.php';

// Prevent browser from caching index.php during updates
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$cssVer = file_exists(__DIR__ . '/assets/css/style.css') ? filemtime(__DIR__ . '/assets/css/style.css') : time();
$jsVer  = file_exists(__DIR__ . '/assets/js/app.js') ? filemtime(__DIR__ . '/assets/js/app.js') : time();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GEMA FYJ — Generate Modul Pembelajaran Aktif</title>
    <meta name="description" content="Platform cerdas penyusunan Modul Ajar & RPP Pembelajaran Mendalam berbasis Teaching Factory (TEFA) untuk SMK Kurikulum Merdeka dengan Dual-Engine AI dan True WYSIWYG Word Exporter.">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $cssVer ?>">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚡</text></svg>">
</head>
<body>

<div class="app-container">
    <!-- Top Navigation Bar -->
    <header class="app-header">
        <div class="brand-section">
            <div class="brand-logo" title="GEMA FYJ">⚡</div>
            <div class="brand-info">
                <div class="brand-title">
                    GEMA FYJ
                    <span class="badge-version">v1.0.0-PROD</span>
                </div>
                <div class="brand-subtitle">Generate Modul Pembelajaran Aktif</div>
            </div>
        </div>

        <div class="header-actions">
            <!-- Global Counter -->
            <div class="counter-box" title="Total dokumen RPP yang telah diproduksi sistem">
                <span>📊 Total RPP Dibuat:</span>
                <span class="counter-num" id="counterNum">0</span>
            </div>

            <button type="button" class="btn-nav" data-open-modal="#modalHistory">
                <span>📁</span> Riwayat RPP
            </button>
            <button type="button" class="btn-nav" data-open-modal="#modalAnalytics">
                <span>📈</span> Analitik
            </button>
            <button type="button" class="btn-nav" data-open-modal="#modalMaster">
                <span>🎓</span> Master Keahlian
            </button>
            <button type="button" class="btn-nav" data-open-modal="#modalSettings">
                <span>⚙️</span> Pengaturan API
            </button>
            <button type="button" class="btn-nav" data-open-modal="#modalAbout">
                <span>ℹ️</span> Panduan
            </button>
        </div>
    </header>

    <!-- Main Content: Split Screen -->
    <main class="main-content">
        
        <!-- Left Panel: Interactive Form -->
        <aside class="form-panel">
            <div class="panel-header">
                <div class="panel-title">
                    <span>📝</span> Parameter Modul Ajar TEFA
                </div>
                <div class="preset-dropdown-wrap">
                    <select id="selPreset" class="form-control" style="font-size:11px; padding:4px 8px; width:auto;">
                        <option value="">⚡ Muat Contoh Presets</option>
                        <option value="tkj">TKJ: Router Hotspot MikroTik</option>
                        <option value="rpl">RPL: Aplikasi Kasir Web POS</option>
                        <option value="dkv">DKV: Kemasan Box Produk UMKM</option>
                        <option value="tsm">TSM: Tune Up Injeksi & CVT</option>
                        <option value="mesin">Mesin: Poros Bertingkat CNC</option>
                        <option value="kuliner">Kuliner: Bento Box Nusantara</option>
                        <option value="farmasi">Farmasi: Minyak Aromaterapi Herbal</option>
                        <option value="mapel_inggris">Mapel Umum: B. Inggris Edotel</option>
                        <option value="mapel_matematika">Mapel Umum: Matematika HPP TEFA</option>
                    </select>
                </div>
            </div>

            <!-- Quick Presets Chips -->
            <div style="padding: 10px 20px 0 20px;">
                <div style="font-size:11px; color:var(--text-muted); margin-bottom:4px;">Contoh Cepat (1-Klik):</div>
                <div class="preset-chips">
                    <span class="chip" data-preset="tkj">TKJ Hotspot</span>
                    <span class="chip" data-preset="rpl">RPL Web POS</span>
                    <span class="chip" data-preset="dkv">DKV Branding</span>
                    <span class="chip" data-preset="tsm">TSM Servis</span>
                    <span class="chip" data-preset="mesin">Mesin CNC</span>
                    <span class="chip" data-preset="kuliner">Kuliner Bento</span>
                    <span class="chip" data-preset="farmasi">Farmasi Herbal</span>
                    <span class="chip" data-preset="mapel_inggris">B. Inggris</span>
                    <span class="chip" data-preset="mapel_matematika">Matematika</span>
                </div>
            </div>

            <!-- Reference Catalog Trigger Banner -->
            <div style="padding: 10px 20px 0 20px;">
                <div class="reference-catalog-banner">
                    <div class="ref-banner-content">
                        <div class="ref-banner-badge">🏛️ Database Spektrum Keahlian SMK</div>
                        <div class="ref-banner-title">Daftar Konsentrasi & Mapel Tersimpan</div>
                        <div class="ref-banner-sub">Rujukan: BSKAP 032/H/KR/2024 & Kepmendikbud 244/M/2024</div>
                    </div>
                    <button type="button" class="btn-ref-catalog" data-open-modal="#modalReferenceCatalog" title="Buka seluruh referensi konsentrasi keahlian & mata pelajaran">
                        <span>🔍 Buka Katalog</span>
                    </button>
                </div>
            </div>

            <!-- Form Body with Scrolling -->
            <form id="rppForm" class="form-scroll-body">
                <input type="hidden" id="activeDocId" name="id" value="">

                <!-- Section 1: Identitas Sekolah, Guru & Pengesahan -->
                <div class="form-section">
                    <div class="section-legend">
                        <span>🏫</span> Identitas Satuan, Pengesahan & Guru
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="inpSatuan">Satuan Pendidikan</label>
                            <input type="text" id="inpSatuan" name="satuan" class="form-control" placeholder="Contoh: SMK Negeri 1 Surabaya">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="inpTempatPengesahan">Kota / Tempat Pengesahan</label>
                            <input type="text" id="inpTempatPengesahan" name="tempat_pengesahan" class="form-control" placeholder="Contoh: Surabaya / Yogyakarta">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="inpNamaKepsek">Nama Kepala Sekolah</label>
                            <input type="text" id="inpNamaKepsek" name="nama_kepsek" class="form-control" placeholder="Nama lengkap & gelar Kepala Sekolah">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="inpNipKepsek">NIP Kepala Sekolah</label>
                            <input type="text" id="inpNipKepsek" name="nip_kepsek" class="form-control" placeholder="18 digit NIP Kepala Sekolah">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="inpGuru">Nama Guru Pengampu</label>
                            <input type="text" id="inpGuru" name="guru" class="form-control" placeholder="Nama lengkap & gelar Guru Pengampu">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="inpNipGuru">NIP Guru Pengampu</label>
                            <input type="text" id="inpNipGuru" name="nip_guru" class="form-control" placeholder="18 digit NIP Guru Pengampu">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="inpTanggalPengesahan">Tanggal Pengesahan Dokumen</label>
                        <input type="text" id="inpTanggalPengesahan" name="tanggal_pengesahan" class="form-control" placeholder="Contoh: 15 Juli 2026">
                    </div>
                </div>

                <!-- Section 2: Program & Kurikulum -->
                <div class="form-section">
                    <div class="section-legend">
                        <span>📚</span> Program Keahlian & Alokasi
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="selProgram">Program Keahlian / Mapel Umum <span class="required">*</span></label>
                        <select id="selProgram" name="program" class="form-control" required>
                            <option value="">Memuat program keahlian...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="selKonsentrasi">Konsentrasi Keahlian <span class="required">*</span></label>
                        <select id="selKonsentrasi" name="konsentrasi" class="form-control" required>
                            <option value="">-- Pilih Program Lebih Dahulu --</option>
                        </select>
                        <div class="form-field-info"><span>🏛️ Rujukan Resmi:</span> Lampiran III BSKAP No. 032/H/KR/2024 & Kepmendikbudristek 244/M/2024</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="selElemen">
                            Elemen Pembelajaran <span class="required">*</span>
                            <span class="hint">Sesuai Konsentrasi</span>
                        </label>
                        <select id="selElemen" name="elemen" class="form-control" required>
                            <option value="">-- Pilih Konsentrasi Lebih Dahulu --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="label-with-ai">
                            <label class="form-label" for="inpMapel" style="margin-bottom:0;">Mata Pelajaran</label>
                            <span class="hint" id="mapelCountHint">Rekomendasi Kurikulum Merdeka</span>
                        </div>
                        <input type="text" id="inpMapel" name="mapel" list="mapelList" class="form-control" placeholder="Pilih dari daftar rekomendasi atau ketik sendiri...">
                        <datalist id="mapelList"></datalist>
                        <div id="quickMapelChips" class="quick-mapel-chips"></div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="selFase">Fase / Kelas</label>
                            <select id="selFase" name="fase" class="form-control">
                                <option value="Fase E / Kelas X">Fase E / Kelas X</option>
                                <option value="Fase F / Kelas XI">Fase F / Kelas XI</option>
                                <option value="Fase F / Kelas XII" selected>Fase F / Kelas XII</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="selSemester">Semester</label>
                            <select id="selSemester" name="semester" class="form-control">
                                <option value="Ganjil" selected>Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="inpTahun">Tahun Pelajaran</label>
                            <input type="text" id="inpTahun" name="tahun" class="form-control" value="2026/2027">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="inpAlokasi">Alokasi Waktu</label>
                            <input type="text" id="inpAlokasi" name="alokasi" class="form-control" value="12 JP (3 pertemuan @ 4 JP)">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Konteks TEFA & Pesanan Industri -->
                <div class="form-section">
                    <div class="section-legend">
                        <span>🏭</span> Konteks TEFA & Pesanan Riil
                    </div>

                    <!-- AI Instant Assistant Banner: Section 3 Konteks TEFA Terpadu -->
                    <div class="ai-assist-banner">
                        <div class="ai-assist-banner-info">
                            <span class="ai-banner-title">🏭 Rekomendasi TEFA Terpadu (Saling Terhubung):</span>
                            <span class="ai-banner-sub">Sinkronisasi otomatis Produk, Materi, Unit TEFA, Klien, Brief, Sarana & DUDI via AI & Web</span>
                        </div>
                        <button type="button" id="btnSuggestAllTefa" class="btn-ai-banner" data-ai-suggest="all_tefa" title="Hubungkan dan lengkapi otomatis seluruh Konteks TEFA via AI">
                            <span>✨ Hubungkan Seluruh Konteks TEFA via AI</span>
                        </button>
                    </div>

                    <div class="form-group">
                        <div class="label-with-ai">
                            <label class="form-label" for="inpProduk" style="margin-bottom:0;">
                                Produk atau Jasa TEFA <span class="required">*</span>
                                <span class="hint">Sugesti otomatis</span>
                            </label>
                            <button type="button" class="btn-ai-sparkle" data-ai-suggest="produk" title="Klik untuk rekomendasi produk/jasa riil dari AI & Internet">
                                <span>✨ Saran AI</span>
                            </button>
                        </div>
                        <input type="text" id="inpProduk" name="produk" list="produkList" class="form-control" placeholder="Pilih dari daftar atau ketik produk kustom Anda" required>
                        <datalist id="produkList"></datalist>
                    </div>

                    <div class="form-group">
                        <div class="label-with-ai">
                            <label class="form-label" for="inpMateri" style="margin-bottom:0;">Materi / Topik Teknis Spesifik</label>
                            <button type="button" class="btn-ai-sparkle" data-ai-suggest="materi" title="Klik untuk rekomendasi topik teknis yang selaras dengan produk">
                                <span>✨ Saran AI</span>
                            </button>
                        </div>
                        <input type="text" id="inpMateri" name="materi" class="form-control" placeholder="Kosongkan jika ingin dirumuskan otomatis dari nama produk">
                    </div>

                    <div class="form-group">
                        <div class="label-with-ai">
                            <label class="form-label" for="inpKonteks" style="margin-bottom:0;">Konteks Unit Produksi / TEFA Sekolah</label>
                            <button type="button" class="btn-ai-sparkle" data-ai-suggest="konteks_tefa" title="Klik untuk saran nama unit TEFA/studio sekolah yang relevan">
                                <span>✨ Saran AI</span>
                            </button>
                        </div>
                        <input type="text" id="inpKonteks" name="konteks_tefa" class="form-control" placeholder="Contoh: Unit Produksi TKJ Net Solution SMK Negeri 1">
                    </div>

                    <div class="form-group">
                        <div class="label-with-ai">
                            <label class="form-label" for="inpKlien" style="margin-bottom:0;">
                                Klien / Konsumen Pemesan
                                <span class="hint">Riil / Komite / UMKM</span>
                            </label>
                            <button type="button" class="btn-ai-sparkle" data-ai-suggest="klien" title="Klik untuk rekomendasi profil pemesan riil dari AI">
                                <span>✨ Saran AI</span>
                            </button>
                        </div>
                        <input type="text" id="inpKlien" name="klien" list="klienList" class="form-control" placeholder="Pihak pemesan produk atau jasa">
                        <datalist id="klienList"></datalist>
                    </div>

                    <div class="form-group">
                        <div class="label-with-ai">
                            <label class="form-label" for="inpBrief" style="margin-bottom:0;">Detail Brief Order Klien</label>
                            <button type="button" class="btn-ai-sparkle" data-ai-suggest="brief" title="Klik untuk saran brief order dari AI">
                                <span>✨ Saran AI</span>
                            </button>
                        </div>
                        <textarea id="inpBrief" name="brief" class="form-control" rows="3" placeholder="Deskripsikan permintaan spesifik pemesan, batas waktu, dan kriteria keberhasilan..."></textarea>
                    </div>

                    <div class="form-group">
                        <div class="label-with-ai">
                            <label class="form-label" for="inpSarana" style="margin-bottom:0;">Sarana & Prasarana Bengkel / Studio</label>
                            <button type="button" class="btn-ai-sparkle" data-ai-suggest="sarana" title="Klik untuk rekomendasi sarana bengkel/software dari AI">
                                <span>✨ Saran AI</span>
                            </button>
                        </div>
                        <input type="text" id="inpSarana" name="sarana" class="form-control" placeholder="Alat, mesin kerja, instrumen uji laboratorium...">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="label-with-ai">
                                <label class="form-label" for="inpMitra" style="margin-bottom:0;">Mitra Industri (DUDI)</label>
                                <button type="button" class="btn-ai-sparkle" data-ai-suggest="mitra" title="Klik untuk rekomendasi mitra industri nyata dari AI/Internet">
                                    <span>✨ Saran AI</span>
                                </button>
                            </div>
                            <input type="text" id="inpMitra" name="mitra" class="form-control" placeholder="Nama mitra resmi DUDI">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="inpPortofolio">Platform Portofolio</label>
                            <input type="text" id="inpPortofolio" name="portofolio" class="form-control" value="Google Sites">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Asesmen Kesiapan & Capaian Pembelajaran Khusus -->
                <div class="form-section">
                    <button type="button" id="btnAccordionCp" class="accordion-toggle">
                        <span class="icon">▸</span>
                        <span>Opsi Lanjutan: CP, TP & Kesiapan Siswa (Opsional)</span>
                    </button>
                    <div id="accordionBodyCp" class="accordion-body">
                        <!-- AI Assistant Banner: Section 4 CP, TP & Kesiapan Siswa -->
                        <div class="ai-assist-banner pedagogis">
                            <div class="ai-assist-banner-info">
                                <span class="ai-banner-title">🎯 Rekomendasi Pedagogis Terpadu:</span>
                                <span class="ai-banner-sub">Formulasi CP BSKAP Kemendikbudristek, TP Terpadu TEFA, & Asesmen Kesiapan Siswa</span>
                            </div>
                            <button type="button" id="btnSuggestAllCpTp" class="btn-ai-banner pedagogis" data-ai-suggest="all_cp_tp" title="Rumuskan CP, TP, dan Kesiapan Siswa secara terpadu via AI">
                                <span>✨ Rumuskan CP, TP & Kesiapan via AI</span>
                            </button>
                        </div>

                        <div class="form-group">
                            <div class="label-with-ai">
                                <label class="form-label" for="inpKesiapan" style="margin-bottom:0;">Kesiapan Awal Peserta Didik</label>
                                <button type="button" class="btn-ai-sparkle" data-ai-suggest="kesiapan" title="Klik untuk rekomendasi asesmen diagnostik & prasyarat siswa dari AI">
                                    <span>✨ Saran AI</span>
                                </button>
                            </div>
                            <textarea id="inpKesiapan" name="kesiapan" class="form-control" placeholder="Uraikan pemahaman awal dan keterampilan prasyarat peserta didik..."></textarea>
                        </div>
                        <div class="form-group">
                            <div class="label-with-ai">
                                <label class="form-label" for="inpCp" style="margin-bottom:0;">Capaian Pembelajaran (CP) Baku</label>
                                <button type="button" class="btn-ai-sparkle" data-ai-suggest="cp" title="Klik untuk referensi rumusan CP baku resmi Kemendikbudristek dari AI/Internet">
                                    <span>✨ Saran AI</span>
                                </button>
                            </div>
                            <textarea id="inpCp" name="cp" class="form-control" placeholder="Tempelkan rumusan CP resmi mata pelajaran jika ada..."></textarea>
                        </div>
                        <div class="form-group">
                            <div class="label-with-ai">
                                <label class="form-label" for="inpTp" style="margin-bottom:0;">Tujuan Pembelajaran (TP) Khusus</label>
                                <button type="button" class="btn-ai-sparkle" data-ai-suggest="tp" title="Klik untuk rumusan TP terpadu pesanan TEFA dari AI">
                                    <span>✨ Saran AI</span>
                                </button>
                            </div>
                            <textarea id="inpTp" name="tp" class="form-control" placeholder="Rumusan TP khusus jika sudah ditentukan tim kurikulum..."></textarea>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Sticky Action Footer -->
            <div class="form-actions-footer">
                <button type="submit" form="rppForm" id="btnGenerate" class="btn-generate">
                    <span>⚡</span>
                    <span class="btn-text">Generate RPP TEFA</span>
                </button>
                <button type="button" id="btnReset" class="btn-secondary" title="Kosongkan Formulir">
                    <span>↺</span> Reset
                </button>
            </div>
        </aside>

        <!-- Right Panel: Live WYSIWYG A4 Paper Preview -->
        <section class="preview-panel">
            <!-- Floating Preview Toolbar -->
            <div class="preview-toolbar">
                <div class="toolbar-left">
                    <div id="sourceHint" class="source-indicator">
                        Hasil modul ajar akan tampil di sini.
                    </div>
                    
                    <button type="button" id="btnEditable" class="btn-tool btn-edit-mode" title="Klik untuk mengaktifkan / mematikan mode sunting langsung">
                        <span>✏️</span> Sunting: ON
                    </button>

                    <!-- Zoom Controls -->
                    <select id="zoomSelect" class="form-control" style="width: auto; padding: 4px 8px; font-size: 11px;">
                        <option value="0.75">Skala: 75%</option>
                        <option value="0.90">Skala: 90%</option>
                        <option value="1.00" selected>Skala: 100% (A4 Asli)</option>
                        <option value="1.10">Skala: 110%</option>
                    </select>
                </div>

                <div class="toolbar-right">
                    <!-- Text Formatting Tools -->
                    <button type="button" id="btnBold" class="btn-tool" title="Tebal (Bold)"><strong>B</strong></button>
                    <button type="button" id="btnItalic" class="btn-tool" title="Miring (Italic)"><em>I</em></button>
                    <button type="button" id="btnUnderline" class="btn-tool" title="Garis Bawah (Underline)"><u>U</u></button>
                    <button type="button" id="btnHeading2" class="btn-tool" title="Heading 2">H2</button>
                    <button type="button" id="btnHeading3" class="btn-tool" title="Heading 3">H3</button>
                    <button type="button" id="btnUl" class="btn-tool" title="Bullet List">• List</button>
                    <button type="button" id="btnOl" class="btn-tool" title="Numbered List">1. List</button>

                    <span style="width:1px; height:20px; background:var(--border-color); margin:0 4px;"></span>

                    <button type="button" id="btnCopyHtml" class="btn-tool" title="Salin Kode HTML">
                        <span>📋</span> Salin
                    </button>
                    <button type="button" id="btnPrint" class="btn-tool" title="Cetak / Simpan PDF">
                        <span>🖨️</span> Cetak / PDF
                    </button>
                    <button type="button" id="btnSaveUpdate" class="btn-tool" title="Simpan Suntingan ke Database">
                        <span>💾</span> Simpan
                    </button>

                    <!-- Export DOCX -->
                    <button type="button" id="btnDownload" class="btn-download-word" disabled title="Unduh berkas Microsoft Word siap cetak margin 1 inci">
                        <span>📥</span> Unduh Word (.doc)
                    </button>
                </div>
            </div>

            <!-- Viewport with A4 Paper Simulation -->
            <div class="paper-viewport">
                <article id="preview" class="paper-container" contenteditable="true" spellcheck="false" style="background:#ffffff !important; background-color:#ffffff !important; width:210mm !important; min-height:297mm !important; height:auto !important; display:block !important; margin:0 auto 60px auto !important; box-sizing:border-box !important;">
                    <div class="placeholder-empty">
                        <div class="placeholder-icon">📄</div>
                        <h3>Simulasi Kertas A4 Siap Cetak (WYSIWYG)</h3>
                        <p>Pilih Program Keahlian, lengkapi konteks pesanan TEFA di panel kiri, lalu klik <strong>Generate RPP TEFA</strong> untuk menyusun Modul Ajar lengkap dengan 10 Lampiran Operasional.</p>
                    </div>
                </article>
            </div>
        </section>
    </main>
</div>

<!-- Hidden Form for Word Export -->
<form id="docxForm" action="api/export-docx.php" method="POST" target="_blank" style="display:none;">
    <input type="hidden" name="html" id="docxHtml">
    <input type="hidden" name="judul" id="docxJudul">
</form>

<!-- Toast Notification -->
<div id="toast" class="toast ok" hidden></div>

<!-- =======================================================================
     MODALS
     ======================================================================= -->

<!-- Modal 0: Katalog Referensi Lengkap Konsentrasi & Mapel -->
<div id="modalReferenceCatalog" class="modal-backdrop" hidden>
    <div class="modal-dialog modal-xl">
        <div class="modal-header">
            <div class="modal-title">
                <span>📚</span> Katalog Referensi Konsentrasi Keahlian & Mata Pelajaran SMK (Kurikulum Merdeka)
            </div>
            <button type="button" class="modal-close" data-close-modal>&times;</button>
        </div>
        <div class="modal-body">
            <!-- Regulatory Legal Source Box -->
            <div class="legal-source-box">
                <div class="legal-source-header">
                    <span class="legal-badge">📜 Rujukan Resmi Standar Kurikulum Nasional</span>
                    <span style="font-size:12px; color:var(--text-secondary);">Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi RI</span>
                </div>
                <div class="legal-source-grid">
                    <div class="legal-item">
                        <strong>1. Keputusan Kepala BSKAP No. 032/H/KR/2024</strong>
                        <span>Capaian Pembelajaran (CP) PAUD, Dikdas, & Dikmen — <em>Lampiran III</em> secara khusus memuat standar kompetensi resmi seluruh Konsentrasi Keahlian & Mapel SMK/MAK Kurikulum Merdeka.</span>
                    </div>
                    <div class="legal-item">
                        <strong>2. Kepmendikbudristek No. 244/M/2024</strong>
                        <span>Spektrum Keahlian dan Struktur Kurikulum SMK/MAK yang mengelompokkan Bidang, Program, dan Konsentrasi Keahlian Nasional.</span>
                    </div>
                    <div class="legal-item">
                        <strong>3. Permendikbudristek No. 12 Tahun 2024</strong>
                        <span>Regulasi kurikulum induk yang mewadahi pembelajaran Teaching Factory (TEFA) dan penguatan karakter Profil Pelajar Pancasila.</span>
                    </div>
                </div>
                <div style="margin-top:10px; font-size:11px; color:#93c5fd; display:flex; align-items:center; gap:6px;">
                    <span>🔗 Sumber rujukan regulasi resmi:</span>
                    <a href="https://kurikulum.kemdikbud.go.id/rujukan/regulasi-kurikulum-merdeka" target="_blank" rel="noopener noreferrer" style="color:#60a5fa; text-decoration:underline;">kurikulum.kemdikbud.go.id/rujukan/regulasi-kurikulum-merdeka</a>
                </div>
            </div>

            <!-- Search Bar & Category Filters -->
            <div style="margin-bottom: 12px; display: flex; gap: 10px;">
                <input type="text" id="refCatalogSearch" class="form-control" placeholder="Ketik nama Konsentrasi Keahlian (misal: Rekayasa Perangkat Lunak, Teknik Mesin, Kuliner) atau Mata Pelajaran...">
            </div>

            <div class="ref-filter-wrap" id="refFilterTabs">
                <button type="button" class="ref-filter-btn active" data-ref-filter="all">Semua Program (18)</button>
                <button type="button" class="ref-filter-btn" data-ref-filter="tik">TIK & Desain (3)</button>
                <button type="button" class="ref-filter-btn" data-ref-filter="teknik">Teknologi & Rekayasa (5)</button>
                <button type="button" class="ref-filter-btn" data-ref-filter="bisnis">Bisnis & Manajemen (3)</button>
                <button type="button" class="ref-filter-btn" data-ref-filter="pariwisata">Pariwisata, Kuliner & Busana (3)</button>
                <button type="button" class="ref-filter-btn" data-ref-filter="kesehatan">Kesehatan & Farmasi (1)</button>
                <button type="button" class="ref-filter-btn" data-ref-filter="umum">Mapel Umum TEFA (1)</button>
            </div>

            <!-- Dynamic Grid of Program Cards -->
            <div class="ref-cards-grid" id="refCardsContainer">
                <p style="color:var(--text-secondary); padding:20px; text-align:center;">Memuat data referensi...</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" data-close-modal>Tutup</button>
        </div>
    </div>
</div>

<!-- Modal 1: Riwayat Dokumen RPP -->
<div id="modalHistory" class="modal-backdrop" hidden>
    <div class="modal-dialog">
        <div class="modal-header">
            <div class="modal-title">
                <span>📁</span> Riwayat Dokumen RPP Tersimpan
            </div>
            <button type="button" class="modal-close" data-close-modal>&times;</button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom: 14px; display: flex; gap: 10px;">
                <input type="text" id="historySearch" class="form-control" placeholder="Cari berdasarkan sekolah, guru, mata pelajaran, atau produk...">
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:70px;">ID</th>
                            <th>Mata Pelajaran & Satuan</th>
                            <th>Program Keahlian</th>
                            <th style="width:75px;">Engine</th>
                            <th style="width:130px;">Waktu Dibuat</th>
                            <th style="width:110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <tr><td colspan="6" style="text-align:center; padding:20px;">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="historyPagination" style="margin-top: 16px; display:flex; justify-content:center; align-items:center;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" data-close-modal>Tutup</button>
        </div>
    </div>
</div>

<!-- Modal 2: Analitik & Statistik -->
<div id="modalAnalytics" class="modal-backdrop" hidden>
    <div class="modal-dialog">
        <div class="modal-header">
            <div class="modal-title">
                <span>📈</span> Dashboard Analitik & Statistik Produksi RPP
            </div>
            <button type="button" class="modal-close" data-close-modal>&times;</button>
        </div>
        <div class="modal-body" id="analyticsContent">
            <p>Memuat statistik...</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" data-close-modal>Tutup</button>
        </div>
    </div>
</div>

<!-- Modal 3: Master Data Keahlian -->
<div id="modalMaster" class="modal-backdrop" hidden>
    <div class="modal-dialog">
        <div class="modal-header">
            <div class="modal-title">
                <span>🎓</span> Kelola Master Keahlian & Referensi TEFA
            </div>
            <button type="button" class="modal-close" data-close-modal>&times;</button>
        </div>
        <div class="modal-body">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <p style="font-size:12px; color:var(--text-secondary);">Daftar 9 Konsentrasi Keahlian dan Mata Pelajaran Umum TEFA:</p>
                <button type="button" id="btnAddMaster" class="btn-tool" style="background:var(--primary); color:#fff; border-color:var(--primary);">
                    <span>+</span> Tambah Program Baru
                </button>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">ID</th>
                            <th>Program & Konsentrasi</th>
                            <th style="width:90px;">Kategori</th>
                            <th>Komponen TEFA</th>
                            <th style="width:70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="masterKeahlianTableBody">
                        <tr><td colspan="5" style="text-align:center; padding:16px;">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" data-close-modal>Tutup</button>
        </div>
    </div>
</div>

<!-- Modal 4: Pengaturan API Gemini & Konfigurasi -->
<div id="modalSettings" class="modal-backdrop" hidden>
    <div class="modal-dialog modal-md">
        <div class="modal-header">
            <div class="modal-title">
                <span>⚙️</span> Konfigurasi Google Gemini & Sistem
            </div>
            <button type="button" class="modal-close" data-close-modal>&times;</button>
        </div>
        <form id="settingsForm">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="setApiKey">Google Gemini API Key</label>
                    <input type="password" id="setApiKey" class="form-control" placeholder="Masukkan AIzaSy... (Opsional)">
                    <div style="font-size:11px; color:var(--text-muted); margin-top:5px; line-height:1.4;">
                        <span style="color:#10b981; font-weight:600;">✓ Fungsi API Key:</span> Mengaktifkan kecerdasan buatan Gemini AI & pencarian internet live (Google Search Grounding) untuk merumuskan Konteks TEFA, CP & TP BSKAP, serta menyusun RPP Modul Ajar secara cerdas.<br>
                        <span>🔑 Belum punya API Key? Dapatkan gratis di <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer" style="color:#60a5fa; text-decoration:underline;">Google AI Studio</a>.</span><br>
                        <span style="color:var(--text-secondary);">*Jika dikosongkan, sistem tetap aktif 100% menggunakan Smart Fallback Engine kurikulum lokal.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="setModel">Model AI Gemini</label>
                    <select id="setModel" class="form-control">
                        <option value="gemini-1.5-flash">Gemini 1.5 Flash (Direkomendasikan - Cepat)</option>
                        <option value="gemini-2.0-flash">Gemini 2.0 Flash</option>
                        <option value="gemini-1.5-pro">Gemini 1.5 Pro (Penalaran Mendalam)</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="setTemp">Creativity (Temp)</label>
                        <input type="number" id="setTemp" class="form-control" min="0.0" max="1.0" step="0.05" value="0.25">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="setRateLimit">Batas Generate / 5 Mnt</label>
                        <input type="number" id="setRateLimit" class="form-control" min="1" max="100" value="6">
                    </div>
                </div>

                <div class="modal-section-divider" style="margin: 16px 0 12px; border-top: 1px solid var(--border-color); padding-top: 12px;">
                    <div style="font-weight: 700; font-size: 13px; color: var(--text-primary); margin-bottom: 4px; display:flex; align-items:center; gap:6px;">
                        <span>🏛️</span> Standar Pengesahan & Penandatangan Dokumen (Tersimpan Permanen)
                    </div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-bottom: 12px; line-height:1.4;">
                        Data sekolah, kepala sekolah, dan guru berikut disimpan di database dan otomatis mengisi form formulir & lembar pengesahan RPP.
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="setDefaultSatuan">Nama Satuan / Sekolah Default</label>
                        <input type="text" id="setDefaultSatuan" class="form-control" placeholder="Contoh: SMK Negeri 1 Surabaya">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="setDefaultTempat">Kota / Tempat Pengesahan</label>
                        <input type="text" id="setDefaultTempat" class="form-control" placeholder="Contoh: Surabaya">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="setDefaultNamaKepsek">Nama Kepala Sekolah Default</label>
                        <input type="text" id="setDefaultNamaKepsek" class="form-control" placeholder="Nama lengkap & gelar">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="setDefaultNipKepsek">NIP Kepala Sekolah Default</label>
                        <input type="text" id="setDefaultNipKepsek" class="form-control" placeholder="18 digit NIP Kepala Sekolah">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="setDefaultNamaGuru">Nama Guru Pengampu Default</label>
                        <input type="text" id="setDefaultNamaGuru" class="form-control" placeholder="Nama lengkap & gelar">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="setDefaultNipGuru">NIP Guru Pengampu Default</label>
                        <input type="text" id="setDefaultNipGuru" class="form-control" placeholder="18 digit NIP Guru Pengampu">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="setDefaultTanggal">Tanggal Pengesahan Default</label>
                    <input type="text" id="setDefaultTanggal" class="form-control" placeholder="Contoh: 15 Juli 2026">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-close-modal>Batal</button>
                <button type="submit" class="btn-generate" style="flex:none; padding:8px 16px;">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 5: Panduan & Informasi -->
<div id="modalAbout" class="modal-backdrop" hidden>
    <div class="modal-dialog">
        <div class="modal-header">
            <div class="modal-title">
                <span>ℹ️</span> Panduan Pembelajaran Mendalam TEFA & GEMA FYJ
            </div>
            <button type="button" class="modal-close" data-close-modal>&times;</button>
        </div>
        <div class="modal-body" style="font-size:13px; line-height:1.7; color:var(--text-secondary);">
            <h4 style="color:#fff; margin-bottom:8px; font-size:14px;">Tentang GEMA FYJ</h4>
            <p style="margin-bottom:12px;">
                Platform ini dikembangkan khusus untuk mempermudah guru kejuruan dan guru mata pelajaran umum SMK dalam merancang RPP Pembelajaran Mendalam berbasis Teaching Factory (TEFA) yang selaras dengan Kurikulum Merdeka.
            </p>

            <h4 style="color:#fff; margin-bottom:8px; font-size:14px;">Fitur Utama:</h4>
            <ul style="margin-left:20px; margin-bottom:16px;">
                <li><strong>Dual-Engine Orchestration:</strong> Memadukan Google Gemini AI dengan <em>Smart Deterministic Fallback</em> sehingga dokumen selalu terbit 100% tanpa risiko offline.</li>
                <li><strong>Struktur Lengkap Bagian A-G + 10 Lampiran:</strong> Dokumen otomatis memuat LKPD Analisis Brief, Rencana Bahan, Pembagian Tim, SOP K3, Jurnal Kerja, QC Checklist, Berita Acara Klien, Portofolio Digital, Refleksi, dan Rubrik Penilaian.</li>
                <li><strong>True WYSIWYG Word Exporter:</strong> Hasil suntingan di browser langsung diekspor menjadi berkas Microsoft Word (.doc) ukuran A4 dengan margin 1 inci siap cetak.</li>
                <li><strong>Integrasi Mapel Umum:</strong> Mengakomodasi kontekstualisasi mata pelajaran umum (Bahasa Inggris, Matematika, IPAS, dll.) ke proyek nyata unit produksi sekolah.</li>
            </ul>

            <h4 style="color:#fff; margin-bottom:8px; font-size:14px;">Tips Penggunaan:</h4>
            <ol style="margin-left:20px;">
                <li>Gunakan tombol <strong>Muat Contoh Presets</strong> untuk melihat contoh modul nyata (TKJ, DKV, TSM, Farmasi, atau Mapel Umum).</li>
                <li>Anda dapat mengetik dan mengedit teks langsung pada kertas simulasi A4 di sisi kanan.</li>
                <li>Gunakan tombol <strong>Unduh Word (.doc)</strong> setelah selesai untuk mengunduh berkas yang siap dicetak dan ditandatangani.</li>
            </ol>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" data-close-modal>Tutup</button>
        </div>
    </div>
</div>

<script src="assets/js/app.js?v=<?= $jsVer ?>"></script>
</body>
</html>
