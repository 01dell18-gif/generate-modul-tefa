/* ==========================================================================
   GEMA FYJ - Generate Modul Pembelajaran Aktif - Frontend Application Logic
   ========================================================================== */
(function () {
    'use strict';

    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => document.querySelectorAll(sel);

    // DOM Elements
    const form        = $('#rppForm');
    const preview     = $('#preview');
    const btnGenerate = $('#btnGenerate');
    const btnDownload = $('#btnDownload');
    const btnReset    = $('#btnReset');
    const btnEditable = $('#btnEditable');
    const sourceHint  = $('#sourceHint');
    const counterNum  = $('#counterNum');
    const toast       = $('#toast');
    const zoomSelect  = $('#zoomSelect');
    const activeDocId = $('#activeDocId');

    let REF = {};
    let hasContent = false;
    let currentSavedId = null;
    let APP_SETTINGS = {};

    // Toast Notification Helper
    let toastTimer = null;
    function showToast(msg, type = 'ok') {
        if (!toast) return;
        toast.textContent = msg;
        toast.className = 'toast ' + type;
        toast.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => { toast.hidden = true; }, 4000);
    }

    // Live Global Counter
    function loadCounter() {
        fetch('api/counter.php')
            .then((r) => r.json())
            .then((d) => {
                if (d && typeof d.total !== 'undefined') {
                    animateCounter(d.total);
                }
            })
            .catch(() => {});
    }

    function animateCounter(target) {
        target = parseInt(target, 10) || 0;
        let cur = parseInt(counterNum.textContent, 10) || 0;
        if (cur === target) return;
        const step = Math.max(1, Math.ceil(Math.abs(target - cur) / 25));
        const tick = () => {
            if (cur < target) { cur = Math.min(target, cur + step); }
            else if (cur > target) { cur = Math.max(target, cur - step); }
            counterNum.textContent = cur.toLocaleString('id-ID');
            if (cur !== target) requestAnimationFrame(tick);
        };
        tick();
    }

    // Load Master Referensi for Dropdowns
    function loadReferensi() {
        fetch('api/referensi.php')
            .then((r) => r.json())
            .then((d) => {
                if (!d || !d.ok) return;
                REF = d.data || {};
                populateProgramDropdown();
            })
            .catch((err) => {
                console.error("Gagal memuat referensi:", err);
            });
    }

    function populateProgramDropdown() {
        const selProgram = $('#selProgram');
        if (!selProgram) return;
        const currentVal = selProgram.value;
        selProgram.innerHTML = '<option value="">-- Pilih Program Keahlian / Umum --</option>';

        // Group into Kejuruan and Mapel Umum
        const optKejuruan = document.createElement('optgroup');
        optKejuruan.label = "Program Keahlian SMK (Kejuruan)";
        const optUmum = document.createElement('optgroup');
        optUmum.label = "Mata Pelajaran Umum SMK (Terintegrasi TEFA)";

        Object.keys(REF).forEach((prog) => {
            const opt = document.createElement('option');
            opt.value = prog;
            opt.textContent = prog;
            if (REF[prog].kategori === 'umum') {
                optUmum.appendChild(opt);
            } else {
                optKejuruan.appendChild(opt);
            }
        });

        selProgram.appendChild(optKejuruan);
        selProgram.appendChild(optUmum);
        if (currentVal && REF[currentVal]) {
            selProgram.value = currentVal;
        }
    }

    function onProgramChange() {
        const prog = $('#selProgram').value;
        const selKons = $('#selKonsentrasi');
        const dlProduk = $('#produkList');
        const dlKlien  = $('#klienList');
        const inpMapel = $('input[name="mapel"]');

        selKons.innerHTML = '<option value="">-- Pilih Konsentrasi Keahlian --</option>';
        dlProduk.innerHTML = '';
        dlKlien.innerHTML = '';

        if (!REF[prog]) {
            inpMapel.placeholder = "Contoh: Konsentrasi Keahlian...";
            return;
        }

        const pData = REF[prog];

        // Populate Konsentrasi
        (pData.konsentrasi || []).forEach((k) => {
            const o = document.createElement('option');
            o.value = k;
            o.textContent = k;
            selKons.appendChild(o);
        });

        // If only 1 konsentrasi, auto-select it
        if ((pData.konsentrasi || []).length === 1) {
            selKons.value = pData.konsentrasi[0];
        }

        // Recommend mapel placeholder & datalist
        const dlMapel = $('#mapelList');
        if (dlMapel) {
            dlMapel.innerHTML = '';
            (pData.mapel || []).forEach((m) => {
                const o = document.createElement('option');
                o.value = m;
                dlMapel.appendChild(o);
            });
        }

        // Render Quick Mapel Chips
        const quickMapelWrap = $('#quickMapelChips');
        if (quickMapelWrap) {
            quickMapelWrap.innerHTML = '';
            (pData.mapel || []).forEach((m) => {
                const chip = document.createElement('span');
                chip.className = 'chip-mapel';
                chip.textContent = m;
                chip.title = 'Klik untuk langsung memilih: ' + m;
                chip.addEventListener('click', () => {
                    inpMapel.value = m;
                    highlightField(inpMapel);
                });
                quickMapelWrap.appendChild(chip);
            });
        }

        const mapelHint = $('#mapelCountHint');
        if (mapelHint) {
            mapelHint.textContent = (pData.mapel ? pData.mapel.length : 0) + ' Mata Pelajaran Tersimpan';
        }

        if (pData.mapel && pData.mapel.length > 0) {
            inpMapel.placeholder = "Rekomendasi: " + pData.mapel[0];
            if (!inpMapel.value) {
                inpMapel.value = pData.mapel[0];
            }
        }

        // Populate Produk & Jasa Datalist
        const items = [].concat(pData.produk || [], pData.jasa || []);
        items.forEach((p) => {
            const o = document.createElement('option');
            o.value = p;
            dlProduk.appendChild(o);
        });

        // Populate Klien Datalist
        (pData.klien || []).forEach((k) => {
            const o = document.createElement('option');
            o.value = k;
            dlKlien.appendChild(o);
        });

        // Trigger Elemen dropdown update
        onKonsentrasiChange();
    }

    function onKonsentrasiChange() {
        const prog = $('#selProgram').value;
        const kons = $('#selKonsentrasi').value;
        const selElemen = $('#selElemen');
        if (!selElemen) return;

        selElemen.innerHTML = '<option value="">-- Pilih Elemen Pembelajaran --</option>';

        if (!REF[prog] || !REF[prog].elemen) return;

        const elemData = REF[prog].elemen;
        let list = [];

        if (Array.isArray(elemData)) {
            list = elemData;
        } else if (typeof elemData === 'object') {
            if (elemData[kons]) {
                list = elemData[kons];
            } else {
                // If mapel umum or single entry
                const keys = Object.keys(elemData);
                if (keys.length > 0) {
                    list = elemData[keys[0]];
                }
            }
        }

        list.forEach((el) => {
            const o = document.createElement('option');
            o.value = el;
            o.textContent = el;
            selElemen.appendChild(o);
        });

        if (list.length > 0) {
            selElemen.value = list[0];
        }
    }

    // Collect Input from Form
    function collectInput() {
        const data = {};
        new FormData(form).forEach((v, k) => {
            data[k] = (v || '').toString().trim();
        });
        return data;
    }

    const PRESETS = {
        tkj: {
            satuan: "SMK Negeri 1 Surabaya",
            tempat_pengesahan: "Surabaya",
            nama_kepsek: "Drs. H. Sugiono, M.Pd.",
            nip_kepsek: "19680512 199303 1 005",
            guru: "Budi Santoso, S.Pd., M.T.",
            nip_guru: "19840215 200902 1 003",
            tanggal_pengesahan: "15 Juli 2026",
            program: "Teknik Jaringan Komputer dan Telekomunikasi",
            konsentrasi: "Teknik Komputer dan Jaringan",
            elemen: "Administrasi Server Jaringan",
            mapel: "Konsentrasi Keahlian TKJ",
            fase: "Fase F / Kelas XII",
            semester: "Ganjil",
            tahun: "2026/2027",
            alokasi: "12 JP (3 pertemuan @ 4 JP)",
            produk: "Unit Server Mini PC Hotspot MikroTik Pre-Configured",
            materi: "Konfigurasi MikroTik Gateway, Bandwidth Management, & Billing Hotspot",
            konteks_tefa: "Unit Produksi TKJ Net Solution SMK Negeri 1",
            klien: "UMKM Warung Kopi & Kafe Sekitar Sekolah",
            brief: "Perakitan router MikroTik teruji dengan sistem voucher login untuk 50 user simultan dan isolasi bandwidth per klien",
            sarana: "Lab Jaringan Komputer, Kabel Tester, RouterBOARD 750Gr3, Crimping Tool, PC Server",
            mitra: "PT. Citra Telematika Mandiri",
            portofolio: "Google Sites Portofolio Siswa TKJ",
            kesiapan: "Peserta didik memahami konsep dasar IP addressing dan topologi LAN"
        },
        dkv: {
            satuan: "SMK Negeri 4 Yogyakarta",
            tempat_pengesahan: "Yogyakarta",
            nama_kepsek: "Drs. H. Sukamto, M.Pd.",
            nip_kepsek: "19690412 199412 1 002",
            guru: "Rina Anggraini, S.Sn., M.Ds.",
            nip_guru: "19870824 201101 2 014",
            tanggal_pengesahan: "18 Juli 2026",
            program: "Desain Komunikasi Visual",
            konsentrasi: "Desain Komunikasi Visual",
            elemen: "Karya Desain Berbasis Vektor dan Bitmap",
            mapel: "Desain Publikasi & Kemasan",
            fase: "Fase F / Kelas XI",
            semester: "Ganjil",
            tahun: "2026/2027",
            alokasi: "12 JP (3 pertemuan @ 4 JP)",
            produk: "Desain Identitas Merek & Kemasan Produk UMKM (Packaging Box & Label)",
            materi: "Prinsip Desain Kemasan, Tipografi Kemasan, dan Die-cut Dieline",
            konteks_tefa: "Studio Kreatif TEFA Visual Arts SMKN 4",
            klien: "UMKM Binaan Keripik Tempe Mbok Nem",
            brief: "Pembuatan redesain logo, kemasan box ramah lingkungan dengan die-cut custom, label botol stiker tahan air, serta panduan warna",
            sarana: "Studio Komputer Grafis, Pen Tablet Wacom, Software Adobe Illustrator, Printer Proof Digital",
            mitra: "PT. Kreasi Grafika Nusantara",
            portofolio: "Behance / Google Sites Portofolio Siswa DKV",
            kesiapan: "Peserta didik menguasai dasar vektor dan penggunaan software desain grafis"
        },
        tsm: {
            satuan: "SMK Negeri 2 Bandung",
            tempat_pengesahan: "Bandung",
            nama_kepsek: "Dr. Dadang Sudrajat, M.M.",
            nip_kepsek: "19670105 199103 1 008",
            guru: "Asep Hidayat, S.T.",
            nip_guru: "19850610 201001 1 012",
            tanggal_pengesahan: "20 Juli 2026",
            program: "Teknik Otomotif",
            konsentrasi: "Teknik Sepeda Motor",
            elemen: "Perawatan dan Perbaikan Mesin Sepeda Motor (Injeksi & Karburator)",
            mapel: "Pemeliharaan Mesin Sepeda Motor",
            fase: "Fase F / Kelas XII",
            semester: "Genap",
            tahun: "2026/2027",
            alokasi: "12 JP (3 pertemuan @ 4 JP)",
            produk: "Paket Jasa Servis Ringan / Tune Up Injeksi & Servis CVT Express",
            materi: "Pembersihan Throttle Body, Kalibrasi Sensor TPS, dan Pemeriksaan Roller CVT",
            konteks_tefa: "Bengkel Mitra AHASS TEFA SMKN 2",
            klien: "Komunitas Pengemudi Ojek Online & Warga Sekitar Sekolah",
            brief: "Paket servis cepat 45 menit: pembersihan throttle body, injector cleaner, pengecekan ketebalan v-belt/roller CVT, dan ganti oli mesin",
            sarana: "Bike Lift Hidrolik, Scanner Injeksi OBD-2, Ultrasonic Cleaner, Torque Wrench, Kompresor Udara",
            mitra: "PT. Daya Adicipta Motora (Astra Honda Motor)",
            portofolio: "Google Sites & Lembar Kerja Servis Siswa TSM",
            kesiapan: "Peserta didik telah lulus dasar motor bakar dan keselamatan kerja bengkel otomotif"
        },
        farmasi: {
            satuan: "SMK Farmasi Surabaya",
            tempat_pengesahan: "Surabaya",
            nama_kepsek: "Dra. Hj. Nurul Hidayati, M.Kes.",
            nip_kepsek: "19660318 199203 2 004",
            guru: "Apt. Siti Nurhaliza, S.Farm.",
            nip_guru: "19900314 201502 2 006",
            tanggal_pengesahan: "22 Juli 2026",
            program: "Layanan Penunjang Kefarmasian Klinis dan Komunitas",
            konsentrasi: "Farmasi Klinis dan Komunitas",
            elemen: "Peracikan Sediaan Obat Tradisional dan Herbal",
            mapel: "Pelayanan Farmasi & Peracikan Obat",
            fase: "Fase F / Kelas XII",
            semester: "Ganjil",
            tahun: "2026/2027",
            alokasi: "12 JP (3 pertemuan @ 4 JP)",
            produk: "Minyak Aromaterapi Herbal Roll-on & Salep Pelega Otot",
            materi: "Teknik Pencampuran Bahan Alami, Uji Homogenitas Salep, dan Pelabelan Obat Tradisional",
            konteks_tefa: "Apotek Mini & Laboratorium Formulasi TEFA Sekolah",
            klien: "Posyandu Lansia Binaan & Koperasi Sekolah",
            brief: "Pembuatan 100 botol roll-on minyak aromaterapi peppermint dan ekstrak jahe merah dengan standar higienis CPOTB mini",
            sarana: "Timbangan Analitik, Mortir & Stamper, Beaker Glass, Penangas Air, Botol Roll-on Higienis",
            mitra: "PT. Kimia Farma Apotek",
            portofolio: "Google Sites Portofolio Praktik Farmasi Siswa",
            kesiapan: "Peserta didik menguasai cara menimbang bahan dan perhitungan dosis sediaan farmasi"
        },
        mapel_inggris: {
            satuan: "SMK Negeri 1 Denpasar",
            tempat_pengesahan: "Denpasar",
            nama_kepsek: "I Ketut Wijaya, M.Pd.",
            nip_kepsek: "19691102 199403 1 007",
            guru: "Made Sukarta, S.Pd., M.Hum.",
            nip_guru: "19910520 201801 2 008",
            tanggal_pengesahan: "16 Juli 2026",
            program: "Mata Pelajaran Umum SMK (Terintegrasi TEFA)",
            konsentrasi: "Integrasi TEFA Fase F (Kelas XI)",
            elemen: "Writing & Presenting (Bilingual Product Catalog & Quotation Email)",
            mapel: "Bahasa Inggris (Kejuruan)",
            fase: "Fase F / Kelas XI",
            semester: "Ganjil",
            tahun: "2026/2027",
            alokasi: "12 JP (3 pertemuan @ 4 JP)",
            produk: "Bilingual Product Catalog & Presentasi Pitching Penawaran Kamar Edotel",
            materi: "Handling Guest Inquiries, Drafting Formal Quotation Letter, & Professional Pitching",
            konteks_tefa: "Unit Usaha Edotel Wisata SMKN 1 Denpasar",
            klien: "Tamu Asing Wisatawan & Agen Biro Perjalanan",
            brief: "Penyusunan brosur katalog dwibahasa untuk paket kamar Edotel serta simulasi percakapan penanganan reservasi tamu asing via telepon dan email",
            sarana: "Lab Bahasa Multimedia, Komputer Terkoneksi Internet, Mikrofon Podcast, LCD Projector",
            mitra: "The Trans Resort Bali",
            portofolio: "Google Sites Showcase Audio/Video Siswa",
            kesiapan: "Peserta didik memahami ungkapan perkenalan dan percakapan dasar sehari-hari"
        },
        mapel_matematika: {
            satuan: "SMK Negeri 5 Malang",
            tempat_pengesahan: "Malang",
            nama_kepsek: "Drs. Supriyanto, M.T.",
            nip_kepsek: "19650914 199003 1 004",
            guru: "Dra. Endang Purwanti",
            nip_guru: "19750819 200312 2 002",
            tanggal_pengesahan: "15 Juli 2026",
            program: "Mata Pelajaran Umum SMK (Terintegrasi TEFA)",
            konsentrasi: "Integrasi TEFA Fase F (Kelas XI)",
            elemen: "Bilangan & Aljabar (Kalkulasi HPP, RAB, dan BEP Produksi)",
            mapel: "Matematika (Kejuruan)",
            fase: "Fase F / Kelas XI",
            semester: "Ganjil",
            tahun: "2026/2027",
            alokasi: "12 JP (3 pertemuan @ 4 JP)",
            produk: "Kalkulator RAB, Estimasi HPP, & Analisis BEP Produksi Berbasis Spreadsheet",
            materi: "Perhitungan Biaya Bahan Baku, Biaya Tenaga Kerja Langsung, Overhead, dan Titik Impas (BEP)",
            konteks_tefa: "Teaching Factory Bakery & Boga SMKN 5",
            klien: "Unit Bisnis Bakery Sekolah & UMKM Roti Mitra",
            brief: "Penyusunan model spreadsheet dinamis untuk menghitung HPP per buah roti, menetapkan harga jual kompetitif dengan margin 35%, dan menentukan volume BEP",
            sarana: "Lab Komputer, Software Microsoft Excel / Google Sheets, Proyektor",
            mitra: "Kadin Kota Malang & Asosiasi UMKM",
            portofolio: "Google Sites & Berkas Spreadsheet Portofolio Siswa",
            kesiapan: "Peserta didik telah memahami operasi aljabar dasar dan persentase"
        },
        rpl: {
            satuan: "SMK Negeri 2 Surakarta",
            tempat_pengesahan: "Surakarta",
            nama_kepsek: "Drs. H. Sri Waluyo, M.Eng.",
            nip_kepsek: "19670311 199203 1 009",
            guru: "Fajar Nugraha, S.Kom., M.Cs.",
            nip_guru: "19890422 201503 1 003",
            tanggal_pengesahan: "20 Juli 2026",
            program: "Pengembangan Perangkat Lunak dan Gim",
            konsentrasi: "Rekayasa Perangkat Lunak",
            elemen: "Pemrograman Web (Frontend & Backend)",
            mapel: "Konsentrasi Keahlian Rekayasa Perangkat Lunak",
            fase: "Fase F / Kelas XI",
            semester: "Ganjil",
            tahun: "2026/2027",
            alokasi: "12 JP (3 pertemuan @ 4 JP)",
            produk: "Aplikasi Web Kasir & Manajemen Stok (POS) Berbasis Framework Laravel/Vue",
            materi: "Arsitektur MVC, Relasi Database MySQL Eloquent, Pembuatan RESTful API, & Integrasi Cetak Struk Bluetooth",
            konteks_tefa: "Software House Edukasi SMKN 2",
            klien: "Koperasi Pegawai & UMKM Toko Kelontong Sekitar Sekolah",
            brief: "Pembangunan aplikasi kasir web teruji dengan modul pencatatan transaksi harian, laporan laba rugi otomatis, dan cetak struk kasir dalam 2 minggu kerja",
            sarana: "Lab Komputer Software Engineering, PC Core i7, Visual Studio Code, XAMPP / Docker, Node.js, Printer Thermal",
            mitra: "PT. Gameloft Indonesia / Agensi Digital Rekanan",
            portofolio: "GitHub Repository & Google Sites Portofolio Siswa RPL",
            kesiapan: "Peserta didik telah memahami logika pemrograman dasar, sintaks PHP, dan kueri SQL dasar"
        },
        mesin: {
            satuan: "SMK Negeri 1 Cilegon",
            tempat_pengesahan: "Cilegon",
            nama_kepsek: "Ir. H. Bambang Sutrisno, M.M.",
            nip_kepsek: "19650821 199102 1 003",
            guru: "Heri Prasetyo, S.T., M.T.",
            nip_guru: "19830718 200801 1 005",
            tanggal_pengesahan: "17 Juli 2026",
            program: "Teknik Mesin",
            konsentrasi: "Teknik Pemesinan",
            elemen: "Pemesinan NC/CNC (Bubut & Milling CNC) serta CAM",
            mapel: "Teknik Pemesinan NC/CNC dan CAM",
            fase: "Fase F / Kelas XII",
            semester: "Ganjil",
            tahun: "2026/2027",
            alokasi: "12 JP (3 pertemuan @ 4 JP)",
            produk: "Poros Bertingkat & Bushing Baja Presisi Komponen Mesin Industri",
            materi: "Pemrograman Kode G-Code CNC Bubut, Penentuan Parameter Pemotongan (Cutting Speed & Feed Rate), dan Pengukuran Dial Indicator",
            konteks_tefa: "Bengkel Manufaktur Presisi TEFA SMKN 1 Cilegon",
            klien: "PT. Krakatau Steel Rekanan / Bengkel Rekayasa Mesin",
            brief: "Pembubutan 50 pcs poros bertingkat material ST-41 dengan toleransi dimensi presisi ±0.02 mm, kekasaran permukaan Ra 0.8 µm, dan uji suai dalam 4 hari kerja",
            sarana: "Mesin CNC Lathe GSK/Siemens, Mesin Bubut Konvensional, Jangka Sorong Digital 0.01 mm, Mikrometer Luar, Toolset Pahat Insert Karbida",
            mitra: "PT. Komatsu Indonesia / Asosiasi Fabrikasi Logam",
            portofolio: "Lembar Gambar Kerja CAD & Logbook Inspeksi QC Siswa",
            kesiapan: "Peserta didik memahami pembacaan gambar teknik proyeksi Amerika/Eropa dan keselamatan kerja bengkel mesin"
        },
        kuliner: {
            satuan: "SMK Negeri 3 Denpasar",
            tempat_pengesahan: "Denpasar",
            nama_kepsek: "Drs. I Gusti Ngurah Agung, M.Pd.",
            nip_kepsek: "19680325 199303 1 006",
            guru: "Ni Made Rai Sukaseni, S.Pd., M.Par.",
            nip_guru: "19861012 201001 2 015",
            tanggal_pengesahan: "18 Juli 2026",
            program: "Kuliner",
            konsentrasi: "Kuliner",
            elemen: "Pengolahan Makanan dan Minuman Tradisional Nusantara",
            mapel: "Konsentrasi Keahlian Kuliner",
            fase: "Fase F / Kelas XI",
            semester: "Ganjil",
            tahun: "2026/2027",
            alokasi: "12 JP (3 pertemuan @ 4 JP)",
            produk: "Paket Bento Box Nasi Liwet Nusantara Komplit untuk Konsumsi Rapat Dinas",
            materi: "Teknik Olah Bumbu Dasar Nusantara, Standar Porsi Gizi, Higiene Sanitasi HACCP Mini, & Plating Estetis Ramah Lingkungan",
            konteks_tefa: "Unit Usaha Katering TEFA Boga SMKN 3 Denpasar",
            klien: "Panitia Rapat Koordinasi Dinas Pendidikan Provinsi",
            brief: "Pembuatan 75 porsi bento box nasi liwet ayam suwir bumbu rujak, tumis daun pepaya teri tidak pahit, sambal terasi, dan lalapan segar dengan standar higienis tanpa MSG berlebih tepat waktu pukul 11.30 WITA",
            sarana: "Dapur Komersial Standar Industri, Kompor Gas High Pressure, Chiller & Freezer, Bain Marie Penghangat, Bento Box Ecofriendly, APD Chef Hat & Apron",
            mitra: "Indonesian Chef Association (ICA) / The Trans Resort",
            portofolio: "Google Sites Portofolio Foto Hidangan & Analisis Food Cost Siswa",
            kesiapan: "Peserta didik menguasai teknik memotong dasar (knife skills), prinsip kebersihan dapur, dan penimbangan resep standar"
        }
    };

    function applyPreset(presetKey) {
        const p = PRESETS[presetKey];
        if (!p) return;

        $('#selProgram').value = p.program;
        onProgramChange();

        // Delay to allow cascading options to settle
        setTimeout(() => {
            if (p.konsentrasi) {
                $('#selKonsentrasi').value = p.konsentrasi;
                onKonsentrasiChange();
            }
            setTimeout(() => {
                Object.keys(p).forEach((key) => {
                    const el = form.elements[key];
                    if (el) el.value = p[key];
                });
                showToast('Preset "' + presetKey.toUpperCase() + '" berhasil dimuat ke formulir.', 'ok');
            }, 30);
        }, 50);
    }

    // Generate Action
    function generate(e) {
        if (e) e.preventDefault();
        const input = collectInput();

        if (!input.program) {
            showToast('Silakan pilih Program Keahlian terlebih dahulu.', 'err');
            $('#selProgram').focus();
            return;
        }

        btnGenerate.disabled = true;
        btnGenerate.querySelector('.btn-text').textContent = 'Membuat RPP...';
        preview.innerHTML = `
            <div class="placeholder-empty">
                <div style="font-size:32px; margin-bottom:12px; animation:spin 1.5s linear infinite;">⏳</div>
                <h3>Sedang Menyusun RPP TEFA...</h3>
                <p>Mengorkestrasi Dual-Engine (AI & Smart Generator) dengan Bagian A s.d. G dan 10 Lampiran Operasional lengkap. Mohon tunggu sejenak.</p>
            </div>
        `;

        // Check if custom user API key is in localStorage
        const customKey = localStorage.getItem('sintesa_custom_gemini_key') || '';
        const headers = { 'Content-Type': 'application/json' };
        if (customKey) {
            headers['X-Gemini-Key'] = customKey;
        }

        const payload = Object.assign({}, input);
        if (customKey) {
            payload.api_key = customKey;
        }

        fetch('api/generate.php', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(payload)
        })
            .then((r) => {
                if (r.status === 429) {
                    throw new Error("Batas permintaan generate tercapai (maksimal 6x per 5 menit).");
                }
                return r.json();
            })
            .then((d) => {
                if (!d || !d.ok || !d.html) {
                    throw new Error(d && d.error ? d.error : 'Respons generator tidak valid.');
                }

                preview.innerHTML = d.html;
                preview.style.backgroundColor = '#ffffff';
                preview.style.background = '#ffffff';
                preview.style.height = 'auto';
                preview.style.minHeight = '297mm';
                preview.style.display = 'block';
                preview.style.margin = '0 auto 60px auto';
                hasContent = true;
                btnDownload.disabled = false;
                updateSourceHint(d);

                // Auto-save to database
                saveResult(input, d.html, d.sumber || 'smart');

                // Smooth scroll paper to top
                $('.paper-viewport').scrollTo({ top: 0, behavior: 'smooth' });
                showToast('RPP Berhasil Digenerate!', 'ok');
            })
            .catch((err) => {
                showToast('Gagal generate: ' + err.message, 'err');
                preview.innerHTML = `
                    <div class="placeholder-empty">
                        <div style="font-size:32px; margin-bottom:12px; color:var(--danger)">⚠️</div>
                        <h3>Terjadi Kesalahan</h3>
                        <p>${err.message}</p>
                    </div>
                `;
            })
            .finally(() => {
                btnGenerate.disabled = false;
                btnGenerate.querySelector('.btn-text').textContent = 'Generate RPP TEFA';
            });
    }

    function updateSourceHint(d) {
        if (!sourceHint) return;
        if (d.sumber === 'ai') {
            sourceHint.innerHTML = '<span class="badge ai">Dibuat oleh AI Gemini</span> Dokumen siap sunting langsung.';
        } else {
            let msg = '<span class="badge smart">Smart Fallback Engine</span> ';
            msg += d.ai_error
                ? 'AI tidak tersedia (' + d.ai_error.substring(0, 45) + '...). Memakai generator cadangan berstandar resmi.'
                : 'Dibuat memakai generator cadangan terverifikasi kurikulum.';
            sourceHint.innerHTML = msg;
        }
    }

    function saveResult(input, html, sumber) {
        fetch('api/save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ input: input, html: html, sumber: sumber })
        })
            .then((r) => r.json())
            .then((d) => {
                if (d && d.ok) {
                    currentSavedId = d.id;
                    if (typeof d.total !== 'undefined') animateCounter(d.total);
                }
            })
            .catch(() => {});
    }

    // Download DOCX
    function downloadWord() {
        if (!hasContent) {
            showToast('Belum ada dokumen yang dihasilkan.', 'err');
            return;
        }
        const html = preview.innerHTML;
        const input = collectInput();
        let judul = 'RPP-TEFA';
        if (input.mapel) judul += '-' + input.mapel.replace(/[^a-zA-Z0-9]/g, '_');
        if (input.satuan) judul += '-' + input.satuan.replace(/[^a-zA-Z0-9]/g, '_');

        $('#docxHtml').value = html;
        $('#docxJudul').value = judul.substring(0, 75);
        $('#docxForm').submit();
        showToast('Menyiapkan dokumen Microsoft Word (.doc A4)...', 'ok');
    }

    // Toggle Contenteditable
    function toggleEditable() {
        const on = preview.getAttribute('contenteditable') === 'true';
        preview.setAttribute('contenteditable', on ? 'false' : 'true');
        btnEditable.innerHTML = on 
            ? '<span>✏️</span> Sunting: OFF' 
            : '<span>✏️</span> Sunting: ON';
        btnEditable.classList.toggle('off', on);
        showToast(on ? 'Mode sunting dimatikan.' : 'Mode sunting aktif. Anda dapat mengetik langsung di kertas A4.', 'ok');
    }

    // Rich Text Format Helpers for WYSIWYG
    function formatDoc(cmd, val = null) {
        document.execCommand(cmd, false, val);
        preview.focus();
    }

    // Zoom Paper Controls
    function handleZoom() {
        const zoom = zoomSelect.value;
        preview.style.transform = `scale(${zoom})`;
    }

    // Reset Form & Preview
    function resetAll() {
        if (confirm('Apakah Anda yakin ingin mengosongkan seluruh isian formulir dan preview?')) {
            form.reset();
            onProgramChange();
            if ($('#selElemen')) $('#selElemen').innerHTML = '<option value="">-- Pilih Konsentrasi Lebih Dahulu --</option>';

            // Restore school & signature defaults if configured in app_settings
            if (APP_SETTINGS) {
                if (APP_SETTINGS.default_satuan && APP_SETTINGS.default_satuan.value) $('#inpSatuan').value = APP_SETTINGS.default_satuan.value;
                if (APP_SETTINGS.default_tempat_pengesahan && APP_SETTINGS.default_tempat_pengesahan.value) $('#inpTempatPengesahan').value = APP_SETTINGS.default_tempat_pengesahan.value;
                if (APP_SETTINGS.default_nama_kepsek && APP_SETTINGS.default_nama_kepsek.value) $('#inpNamaKepsek').value = APP_SETTINGS.default_nama_kepsek.value;
                if (APP_SETTINGS.default_nip_kepsek && APP_SETTINGS.default_nip_kepsek.value) $('#inpNipKepsek').value = APP_SETTINGS.default_nip_kepsek.value;
                if (APP_SETTINGS.default_nama_guru && APP_SETTINGS.default_nama_guru.value) $('#inpGuru').value = APP_SETTINGS.default_nama_guru.value;
                if (APP_SETTINGS.default_nip_guru && APP_SETTINGS.default_nip_guru.value) $('#inpNipGuru').value = APP_SETTINGS.default_nip_guru.value;
                if (APP_SETTINGS.default_tanggal_pengesahan && APP_SETTINGS.default_tanggal_pengesahan.value) $('#inpTanggalPengesahan').value = APP_SETTINGS.default_tanggal_pengesahan.value;
            }

            preview.innerHTML = `
                <div class="placeholder-empty">
                    <div class="placeholder-icon">📄</div>
                    <h3>Simulasi Kertas A4 Siap Cetak</h3>
                    <p>Pilih Program Keahlian, sesuaikan detail pesanan klien TEFA di panel kiri, lalu klik <strong>Generate RPP TEFA</strong> untuk menghasilkan modul ajar lengkap.</p>
                </div>
            `;
            hasContent = false;
            currentSavedId = null;
            btnDownload.disabled = true;
            sourceHint.textContent = 'Hasil akan tampil di sini secara real-time.';
            showToast('Formulir berhasil dikosongkan.', 'ok');
        }
    }

    // Copy Raw HTML
    function copyHtml() {
        if (!hasContent) {
            showToast('Belum ada dokumen yang dihasilkan.', 'err');
            return;
        }
        navigator.clipboard.writeText(preview.innerHTML)
            .then(() => showToast('HTML dokumen berhasil disalin ke clipboard!', 'ok'))
            .catch(() => showToast('Gagal menyalin HTML.', 'err'));
    }

    // Save Updated Content to Database
    function updateCurrentDoc() {
        if (!currentSavedId) {
            // Save as new if not yet saved
            saveResult(collectInput(), preview.innerHTML, 'smart');
            showToast('Dokumen baru berhasil disimpan ke database.', 'ok');
            return;
        }

        fetch(`api/rpp.php?id=${currentSavedId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ html_content: preview.innerHTML })
        })
            .then((r) => r.json())
            .then((d) => {
                if (d && d.ok) {
                    showToast('Perubahan dokumen berhasil diperbarui di database.', 'ok');
                } else {
                    throw new Error(d.error || 'Gagal update.');
                }
            })
            .catch((err) => showToast('Gagal menyimpan: ' + err.message, 'err'));
    }

    // =========================================================================
    // AI Instant Suggestion Engine for Brief, Sarana, and Mitra
    // =========================================================================
    function getAiSuggestion(field, targetBtn) {
        const input = collectInput();
        if (!input.program && !input.produk) {
            showToast('Pilih Program Keahlian atau ketik Produk terlebih dahulu agar saran AI relevan.', 'err');
            return;
        }

        if (targetBtn) {
            targetBtn.classList.add('loading');
            targetBtn.dataset.origText = targetBtn.innerHTML;
            targetBtn.innerHTML = '<span>⏳</span> Berpikir...';
        }

        const customKey = localStorage.getItem('sintesa_custom_gemini_key') || '';
        const headers = { 'Content-Type': 'application/json' };
        if (customKey) {
            headers['X-Gemini-Key'] = customKey;
        }

        fetch('api/suggest.php', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                field: field,
                api_key: customKey,
                program: input.program,
                konsentrasi: input.konsentrasi,
                elemen: input.elemen,
                fase: input.fase,
                semester: input.semester,
                produk: input.produk,
                materi: input.materi,
                konteks_tefa: input.konteks_tefa,
                klien: input.klien,
                brief: input.brief,
                sarana: input.sarana,
                mitra: input.mitra,
                kesiapan: input.kesiapan,
                cp: input.cp,
                tp: input.tp
            })
        })
            .then((r) => r.json())
            .then((d) => {
                if (!d || !d.ok) throw new Error(d ? d.error : 'Gagal memproses saran AI.');

                // 1. All TEFA Context Group
                if ((field === 'all_tefa' || field === 'all') && d.suggestions) {
                    const s = d.suggestions;
                    if (s.produk) $('#inpProduk').value = s.produk;
                    if (s.materi) $('#inpMateri').value = s.materi;
                    if (s.konteks_tefa) $('#inpKonteks').value = s.konteks_tefa;
                    if (s.klien) $('#inpKlien').value = s.klien;
                    if (s.brief) $('#inpBrief').value = s.brief;
                    if (s.sarana) $('#inpSarana').value = s.sarana;
                    if (s.mitra) $('#inpMitra').value = s.mitra;

                    highlightField($('#inpProduk'));
                    highlightField($('#inpMateri'));
                    highlightField($('#inpKonteks'));
                    highlightField($('#inpKlien'));
                    highlightField($('#inpBrief'));
                    highlightField($('#inpSarana'));
                    highlightField($('#inpMitra'));
                    showToast('✨ Seluruh Konteks TEFA berhasil dihubungkan & dilengkapi oleh AI!', 'ok');

                // 2. All CP & TP Pedagogical Group
                } else if (field === 'all_cp_tp' && d.suggestions) {
                    const s = d.suggestions;
                    ensureAccordionCpOpen();
                    if (s.kesiapan) $('#inpKesiapan').value = s.kesiapan;
                    if (s.cp) $('#inpCp').value = s.cp;
                    if (s.tp) $('#inpTp').value = s.tp;

                    highlightField($('#inpKesiapan'));
                    highlightField($('#inpCp'));
                    highlightField($('#inpTp'));
                    showToast('✨ CP Baku BSKAP, TP Terpadu TEFA, & Kesiapan Siswa berhasil dirumuskan!', 'ok');

                // 3. Single Specific Field
                } else if (d.suggestion) {
                    const fieldMap = {
                        produk: { el: $('#inpProduk'), label: 'Produk/Jasa TEFA' },
                        materi: { el: $('#inpMateri'), label: 'Materi Teknis' },
                        konteks_tefa: { el: $('#inpKonteks'), label: 'Unit Produksi TEFA' },
                        klien: { el: $('#inpKlien'), label: 'Klien Pemesan' },
                        brief: { el: $('#inpBrief'), label: 'Detail Brief Order' },
                        sarana: { el: $('#inpSarana'), label: 'Sarana & Prasarana' },
                        mitra: { el: $('#inpMitra'), label: 'Mitra Industri (DUDI)' },
                        kesiapan: { el: $('#inpKesiapan'), label: 'Kesiapan Awal Peserta Didik', accordion: true },
                        cp: { el: $('#inpCp'), label: 'Capaian Pembelajaran (CP) Baku', accordion: true },
                        tp: { el: $('#inpTp'), label: 'Tujuan Pembelajaran (TP) Khusus', accordion: true }
                    };

                    const target = fieldMap[field];
                    if (target && target.el) {
                        if (target.accordion) ensureAccordionCpOpen();
                        target.el.value = d.suggestion;
                        highlightField(target.el);
                        showToast(`✨ Rekomendasi AI untuk ${target.label} berhasil diterapkan!`, 'ok');
                    }
                }
            })
            .catch((err) => {
                showToast('Gagal saran AI: ' + err.message, 'err');
            })
            .finally(() => {
                if (targetBtn) {
                    targetBtn.classList.remove('loading');
                    targetBtn.innerHTML = targetBtn.dataset.origText || '<span>✨ Saran AI</span>';
                }
            });
    }

    function ensureAccordionCpOpen() {
        const body = $('#accordionBodyCp');
        if (body && !body.classList.contains('open')) {
            body.classList.add('open');
            const icon = $('#btnAccordionCp')?.querySelector('.icon');
            if (icon) icon.textContent = '▾';
        }
    }

    function highlightField(el) {
        if (!el) return;
        el.style.transition = 'box-shadow 0.3s ease, border-color 0.3s ease, transform 0.2s ease';
        el.style.borderColor = 'var(--accent)';
        el.style.boxShadow = '0 0 0 3px var(--accent-glow)';
        setTimeout(() => {
            el.style.borderColor = '';
            el.style.boxShadow = '';
        }, 1500);
    }

    // =========================================================================
    // REFERENCE CATALOG (SPEKTRUM SMK & MAPEL BSKAP 032/H/KR/2024)
    // =========================================================================
    let currentRefFilter = 'all';

    function renderReferenceCatalog() {
        const container = $('#refCardsContainer');
        if (!container) return;
        const searchInput = $('#refCatalogSearch');
        const q = (searchInput ? searchInput.value : '').toLowerCase().trim();

        const programs = Object.keys(REF);
        if (programs.length === 0) {
            container.innerHTML = '<p style="color:var(--text-muted); padding:20px; text-align:center;">Memuat data referensi dari database...</p>';
            return;
        }

        let filtered = programs.filter((prog) => {
            const data = REF[prog];
            const pLow = prog.toLowerCase();

            // Category filter
            if (currentRefFilter !== 'all') {
                if (currentRefFilter === 'tik' && !(pLow.includes('jaringan') || pLow.includes('perangkat lunak') || pLow.includes('visual') || pLow.includes('broadcasting'))) return false;
                if (currentRefFilter === 'teknik' && !(pLow.includes('otomotif') || pLow.includes('mesin') || pLow.includes('pengelasan') || pLow.includes('listrik') || pLow.includes('elektronika'))) return false;
                if (currentRefFilter === 'bisnis' && !(pLow.includes('perkantoran') || pLow.includes('pemasaran') || pLow.includes('akuntansi') || pLow.includes('perbankan'))) return false;
                if (currentRefFilter === 'pariwisata' && !(pLow.includes('hotel') || pLow.includes('kuliner') || pLow.includes('busana'))) return false;
                if (currentRefFilter === 'kesehatan' && !pLow.includes('farmasi')) return false;
                if (currentRefFilter === 'umum' && data.kategori !== 'umum') return false;
            }

            // Search filter
            if (q) {
                const searchCorpus = (prog + ' ' + (data.konsentrasi || []).join(' ') + ' ' + (data.mapel || []).join(' ')).toLowerCase();
                if (!searchCorpus.includes(q)) return false;
            }
            return true;
        });

        if (filtered.length === 0) {
            container.innerHTML = `<p style="color:var(--text-muted); padding:30px; text-align:center; grid-column:1/-1;">Tidak ditemukan Konsentrasi Keahlian atau Mata Pelajaran yang cocok dengan kata kunci "${q}".</p>`;
            return;
        }

        container.innerHTML = '';
        filtered.forEach((prog) => {
            const d = REF[prog];
            const card = document.createElement('div');
            card.className = 'ref-card';

            const konsListHtml = (d.konsentrasi || []).map((k) => `<span class="ref-tag highlight">${k}</span>`).join('');
            const mapelListHtml = (d.mapel || []).map((m) => `<span class="ref-tag">${m}</span>`).join('');
            const prodListHtml = (d.produk || []).slice(0, 3).map((p) => `<span class="ref-tag" style="background:rgba(16,185,129,0.12); color:#6ee7b7;">${p}</span>`).join('');

            card.innerHTML = `
                <div>
                    <div class="ref-card-header">
                        <div class="ref-card-title">${prog}</div>
                        <span class="ref-card-badge ${d.kategori === 'umum' ? 'umum' : 'kejuruan'}">${d.kategori === 'umum' ? 'Mapel Umum' : 'Kejuruan'}</span>
                    </div>
                    
                    <div class="ref-section-label">🎯 Konsentrasi Keahlian (${(d.konsentrasi || []).length}):</div>
                    <div class="ref-tags-wrap">${konsListHtml}</div>

                    <div class="ref-section-label">📚 Mata Pelajaran (${(d.mapel || []).length}):</div>
                    <div class="ref-tags-wrap">${mapelListHtml}</div>

                    <div class="ref-section-label">💡 Produk/Jasa TEFA:</div>
                    <div class="ref-tags-wrap">${prodListHtml}</div>
                </div>

                <button type="button" class="btn-apply-ref" data-select-prog="${prog}">
                    <span>⚡ Gunakan Referensi Program Ini</span>
                </button>
            `;

            // Button listener to apply reference
            card.querySelector('.btn-apply-ref').addEventListener('click', () => {
                applyProgramReference(prog);
            });

            container.appendChild(card);
        });
    }

    function applyProgramReference(prog) {
        const selProgram = $('#selProgram');
        if (!selProgram || !REF[prog]) return;

        selProgram.value = prog;
        onProgramChange();

        // Close modal
        $('#modalReferenceCatalog').hidden = true;

        // Visual toast & focus
        highlightField($('#selProgram'));
        highlightField($('#selKonsentrasi'));
        highlightField($('#inpMapel'));
        highlightField($('#selElemen'));

        const d = REF[prog];
        const firstKons = (d.konsentrasi && d.konsentrasi.length > 0) ? d.konsentrasi[0] : '';
        const firstMapel = (d.mapel && d.mapel.length > 0) ? d.mapel[0] : '';

        showToast(`✨ Referensi "${prog}" & "${firstKons}" berhasil dimuat ke formulir!`, 'ok');
    }

    // =========================================================================
    // MODALS: Riwayat, Analitik, Master Data, Pengaturan, Katalog
    // =========================================================================

    function initModals() {
        // Modal toggles
        $$('[data-open-modal]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-open-modal');
                const modal = $(targetId);
                if (!modal) return;
                modal.hidden = false;

                // Load modal content based on target
                if (targetId === '#modalReferenceCatalog') renderReferenceCatalog();
                if (targetId === '#modalHistory') loadHistory();
                if (targetId === '#modalAnalytics') loadAnalytics();
                if (targetId === '#modalMaster') loadMasterKeahlianList();
                if (targetId === '#modalSettings') loadSettings();
            });
        });

        // Catalog Search Input
        const refSearch = $('#refCatalogSearch');
        if (refSearch) {
            refSearch.addEventListener('input', () => {
                renderReferenceCatalog();
            });
        }

        // Catalog Filter Tabs
        $$('[data-ref-filter]').forEach((tab) => {
            tab.addEventListener('click', () => {
                $$('[data-ref-filter]').forEach((t) => t.classList.remove('active'));
                tab.classList.add('active');
                currentRefFilter = tab.getAttribute('data-ref-filter');
                renderReferenceCatalog();
            });
        });

        // Close triggers
        $$('[data-close-modal]').forEach((btn) => {
            btn.addEventListener('click', () => {
                btn.closest('.modal-backdrop').hidden = true;
            });
        });

        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                $$('.modal-backdrop').forEach((m) => { m.hidden = true; });
            }
        });
    }

    // --- History Modal Logic ---
    let historyPage = 1;
    function loadHistory(page = 1) {
        historyPage = page;
        const historyBody = $('#historyTableBody');
        const searchVal = $('#historySearch').value.trim();
        historyBody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px;">Memuat riwayat RPP...</td></tr>';

        fetch(`api/rpp.php?page=${page}&limit=8&search=${encodeURIComponent(searchVal)}`)
            .then((r) => r.json())
            .then((d) => {
                if (!d || !d.ok || !d.data || d.data.length === 0) {
                    historyBody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px; color:var(--text-muted)">Belum ada riwayat dokumen tersimpan.</td></tr>';
                    $('#historyPagination').innerHTML = '';
                    return;
                }

                historyBody.innerHTML = '';
                d.data.forEach((row) => {
                    const tr = document.createElement('tr');
                    const badgeClass = row.sumber === 'ai' ? 'ai' : 'smart';
                    const badgeLabel = row.sumber === 'ai' ? 'AI' : 'Smart';
                    const dateFormatted = (row.created_at || '').substring(0, 16);

                    tr.innerHTML = `
                        <td><strong>#${row.id}</strong></td>
                        <td>
                            <div style="font-weight:600; color:#fff;">${row.mata_pelajaran || row.produk_jasa || 'RPP TEFA'}</div>
                            <div style="font-size:11px; color:var(--text-secondary);">${row.satuan_pendidikan || '-'} &bull; ${row.nama_guru || '-'}</div>
                        </td>
                        <td>
                            <div style="font-size:12px;">${row.program_keahlian || '-'}</div>
                            <span class="badge ${row.kategori_mapel === 'umum' ? 'smart' : 'ai'}" style="font-size:9px;">${row.kategori_mapel || 'kejuruan'}</span>
                        </td>
                        <td><span class="badge ${badgeClass}">${badgeLabel}</span></td>
                        <td style="font-size:11px; color:var(--text-muted);">${dateFormatted}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button class="btn-tool" title="Buka di Editor" onclick="window.sintesaApp.loadRppIntoEditor(${row.id})">Buka</button>
                                <button class="btn-tool" style="color:var(--danger)" title="Hapus" onclick="window.sintesaApp.deleteRpp(${row.id})">Hapus</button>
                            </div>
                        </td>
                    `;
                    historyBody.appendChild(tr);
                });

                // Render Pagination
                renderHistoryPagination(d.pagination);
            })
            .catch((err) => {
                historyBody.innerHTML = `<tr><td colspan="6" style="color:var(--danger); padding:20px;">Gagal memuat: ${err.message}</td></tr>`;
            });
    }

    function renderHistoryPagination(pag) {
        const pagWrap = $('#historyPagination');
        if (!pag || pag.total_pages <= 1) {
            pagWrap.innerHTML = '';
            return;
        }

        let html = '';
        if (pag.current_page > 1) {
            html += `<button class="btn-tool" onclick="window.sintesaApp.loadHistory(${pag.current_page - 1})">&laquo; Sebelumnya</button> `;
        }
        html += `<span style="font-size:12px; margin:0 8px; color:var(--text-secondary);">Halaman ${pag.current_page} dari ${pag.total_pages} (${pag.total_records} data)</span> `;
        if (pag.current_page < pag.total_pages) {
            html += `<button class="btn-tool" onclick="window.sintesaApp.loadHistory(${pag.current_page + 1})">Berikutnya &raquo;</button>`;
        }
        pagWrap.innerHTML = html;
    }

    function loadRppIntoEditor(id) {
        showToast('Memuat dokumen #' + id + ' ke editor...', 'ok');
        fetch(`api/rpp.php?id=${id}`)
            .then((r) => r.json())
            .then((d) => {
                if (!d || !d.ok || !d.data) throw new Error('Dokumen tidak ditemukan.');
                const row = d.data;

                // Load HTML to preview
                preview.innerHTML = row.html_content;
                preview.style.backgroundColor = '#ffffff';
                preview.style.background = '#ffffff';
                preview.style.height = 'auto';
                preview.style.minHeight = '297mm';
                preview.style.display = 'block';
                preview.style.margin = '0 auto 60px auto';
                hasContent = true;
                currentSavedId = row.id;
                btnDownload.disabled = false;
                updateSourceHint({ sumber: row.sumber });

                // Fill form if raw_input available
                if (row.raw_input && typeof row.raw_input === 'object') {
                    Object.keys(row.raw_input).forEach((k) => {
                        const el = form.elements[k];
                        if (el) el.value = row.raw_input[k];
                    });
                    if (row.raw_input.program) {
                        $('#selProgram').value = row.raw_input.program;
                        onProgramChange();
                        setTimeout(() => {
                            if (row.raw_input.konsentrasi) {
                                $('#selKonsentrasi').value = row.raw_input.konsentrasi;
                            }
                        }, 50);
                    }
                }

                // Explicitly sync signature & endorsement columns if present on record
                if (row.satuan && form.elements['satuan']) form.elements['satuan'].value = row.satuan;
                if (row.tempat_pengesahan && form.elements['tempat_pengesahan']) form.elements['tempat_pengesahan'].value = row.tempat_pengesahan;
                if (row.nama_kepsek && form.elements['nama_kepsek']) form.elements['nama_kepsek'].value = row.nama_kepsek;
                if (row.nip_kepsek && form.elements['nip_kepsek']) form.elements['nip_kepsek'].value = row.nip_kepsek;
                if (row.guru && form.elements['guru']) form.elements['guru'].value = row.guru;
                if (row.nip_guru && form.elements['nip_guru']) form.elements['nip_guru'].value = row.nip_guru;
                if (row.tanggal_pengesahan && form.elements['tanggal_pengesahan']) form.elements['tanggal_pengesahan'].value = row.tanggal_pengesahan;

                // Close history modal
                $('#modalHistory').hidden = true;
                showToast(`Dokumen #${id} berhasil dimuat ke editor!`, 'ok');
                $('.paper-viewport').scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch((err) => showToast('Gagal memuat: ' + err.message, 'err'));
    }

    function deleteRpp(id) {
        if (!confirm(`Hapus dokumen RPP #${id} secara permanen?`)) return;
        fetch(`api/rpp.php?id=${id}`, { method: 'DELETE' })
            .then((r) => r.json())
            .then((d) => {
                if (d && d.ok) {
                    showToast(`Dokumen #${id} berhasil dihapus.`, 'ok');
                    loadHistory(historyPage);
                    if (typeof d.total !== 'undefined') animateCounter(d.total);
                    if (currentSavedId === id) currentSavedId = null;
                } else {
                    throw new Error(d.error || 'Gagal menghapus.');
                }
            })
            .catch((err) => showToast('Gagal: ' + err.message, 'err'));
    }

    // --- Analytics Modal Logic ---
    function loadAnalytics() {
        const wrap = $('#analyticsContent');
        wrap.innerHTML = '<p style="padding:20px; text-align:center;">Memuat data analitik...</p>';

        fetch('api/admin/analytics.php')
            .then((r) => r.json())
            .then((d) => {
                if (!d || !d.ok) throw new Error('Gagal memuat analitik.');
                const m = d.metrics;

                let majorsHtml = '';
                (m.popular_majors || []).forEach((item) => {
                    const pct = m.total_generated > 0 ? Math.round((item.count / m.total_generated) * 100) : 0;
                    majorsHtml += `
                        <div style="margin-bottom:12px;">
                            <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                                <span style="font-weight:600; color:#fff;">${item.program}</span>
                                <span style="color:var(--text-muted); font-family:monospace;">${item.count} RPP (${pct}%)</span>
                            </div>
                            <div class="progress-bar-wrap" style="height:6px; margin:0;">
                                <div class="progress-bar-fill ai" style="width:${pct}%;"></div>
                            </div>
                        </div>
                    `;
                });

                wrap.innerHTML = `
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <span class="metric-label">Total RPP Dibuat</span>
                            <span class="metric-val">${(m.total_generated || 0).toLocaleString('id-ID')}</span>
                            <span class="metric-sub">Secara kumulatif di database</span>
                        </div>
                        <div class="metric-card">
                            <span class="metric-label">Produksi AI Gemini</span>
                            <span class="metric-val" style="color:#10b981;">${m.source_ai_percentage}%</span>
                            <span class="metric-sub">${m.source_ai_count} dokumen dibuat AI</span>
                        </div>
                        <div class="metric-card">
                            <span class="metric-label">Smart Fallback Engine</span>
                            <span class="metric-val" style="color:#3b82f6;">${m.source_smart_fallback_percentage}%</span>
                            <span class="metric-sub">${m.source_smart_count} dokumen fallback</span>
                        </div>
                    </div>

                    <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:16px; margin-bottom:20px;">
                        <h4 style="font-size:13px; font-weight:700; margin-bottom:10px; color:#fff;">Distribusi Engine Generator</h4>
                        <div class="progress-bar-wrap" style="height:12px;">
                            <div class="progress-bar-fill ai" style="width:${m.source_ai_percentage}%;" title="AI: ${m.source_ai_percentage}%"></div>
                            <div class="progress-bar-fill smart" style="width:${m.source_smart_fallback_percentage}%;" title="Fallback: ${m.source_smart_fallback_percentage}%"></div>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:11px; margin-top:6px; color:var(--text-secondary);">
                            <span>🟢 AI Gemini (${m.source_ai_percentage}%)</span>
                            <span>🔵 Smart Fallback (${m.source_smart_fallback_percentage}%)</span>
                        </div>
                    </div>

                    <div style="background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:16px;">
                        <h4 style="font-size:13px; font-weight:700; margin-bottom:14px; color:#fff;">Distribusi Program Keahlian Terpopuler</h4>
                        ${majorsHtml || '<p style="color:var(--text-muted); font-size:12px;">Belum ada data program.</p>'}
                    </div>
                `;
            })
            .catch((err) => {
                wrap.innerHTML = `<p style="color:var(--danger); padding:20px;">Gagal: ${err.message}</p>`;
            });
    }

    // --- Master Keahlian Modal Logic ---
    function loadMasterKeahlianList() {
        const wrap = $('#masterKeahlianTableBody');
        wrap.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:16px;">Memuat master keahlian...</td></tr>';

        fetch('api/admin/keahlian.php')
            .then((r) => r.json())
            .then((d) => {
                if (!d || !d.ok || !d.data) throw new Error('Gagal memuat master keahlian.');
                wrap.innerHTML = '';

                d.data.forEach((row) => {
                    const tr = document.createElement('tr');
                    const produkCount = (row.produk_list || []).length;
                    const mapelCount = (row.mapel_default || []).length;

                    tr.innerHTML = `
                        <td><strong>${row.id}</strong></td>
                        <td>
                            <div style="font-weight:600; color:#fff;">${row.program_keahlian}</div>
                            <div style="font-size:11px; color:var(--text-secondary);">${(row.konsentrasi_list || []).join(', ')}</div>
                        </td>
                        <td><span class="badge ${row.kategori === 'umum' ? 'smart' : 'ai'}">${row.kategori}</span></td>
                        <td style="font-size:12px; color:var(--text-secondary);">${mapelCount} Mapel &bull; ${produkCount} Produk TEFA</td>
                        <td>
                            <button class="btn-tool" style="color:var(--danger)" onclick="window.sintesaApp.deleteMasterKeahlian(${row.id})">Hapus</button>
                        </td>
                    `;
                    wrap.appendChild(tr);
                });
            })
            .catch((err) => {
                wrap.innerHTML = `<tr><td colspan="5" style="color:var(--danger); padding:16px;">Gagal: ${err.message}</td></tr>`;
            });
    }

    function addMasterKeahlian() {
        const prog = prompt('Masukkan Nama Program Keahlian Baru:');
        if (!prog || !prog.trim()) return;
        const kat = confirm('Apakah ini kategori Mata Pelajaran Umum? Klik OK untuk UMUM, atau Cancel untuk KEJURUAN.') ? 'umum' : 'kejuruan';
        const kons = prompt('Masukkan Nama Konsentrasi Keahlian (pisahkan dengan koma):', prog);

        const payload = {
            kategori: kat,
            program_keahlian: prog.trim(),
            konsentrasi_list: (kons || '').split(',').map((s) => s.trim()).filter(Boolean),
            mapel_default: [`Konsentrasi Keahlian ${prog.trim()}`],
            produk_list: [`Produk Inovasi ${prog.trim()}`],
            jasa_list: [`Jasa Layanan ${prog.trim()}`],
            klien_list: ['Warga Sekitar', 'Unit Usaha Sekolah']
        };

        fetch('api/admin/keahlian.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then((r) => r.json())
            .then((d) => {
                if (d && d.ok) {
                    showToast('Master keahlian baru berhasil ditambahkan!', 'ok');
                    loadMasterKeahlianList();
                    loadReferensi(); // Refresh form dropdown
                } else {
                    throw new Error(d.error || 'Gagal menambahkan.');
                }
            })
            .catch((err) => showToast('Gagal: ' + err.message, 'err'));
    }

    function deleteMasterKeahlian(id) {
        if (!confirm(`Hapus master keahlian #${id}?`)) return;
        fetch(`api/admin/keahlian.php?id=${id}`, { method: 'DELETE' })
            .then((r) => r.json())
            .then((d) => {
                if (d && d.ok) {
                    showToast('Master keahlian berhasil dihapus.', 'ok');
                    loadMasterKeahlianList();
                    loadReferensi();
                } else {
                    throw new Error(d.error || 'Gagal menghapus.');
                }
            })
            .catch((err) => showToast('Gagal: ' + err.message, 'err'));
    }

    // --- Settings Modal Logic ---
    function loadSettings() {
        fetch('api/settings.php')
            .then((r) => r.json())
            .then((d) => {
                if (!d || !d.ok) return;
                const s = d.settings || {};
                APP_SETTINGS = s;

                if (s.gemini_api_key) $('#setApiKey').value = s.gemini_api_key.value || '';
                if (s.ai_model) $('#setModel').value = s.ai_model.value || 'gemini-1.5-flash';
                if (s.ai_temperature) $('#setTemp').value = s.ai_temperature.value || '0.25';
                if (s.rate_limit_max) $('#setRateLimit').value = s.rate_limit_max.value || '6';

                // Signature & School Defaults in Modal Settings
                if (s.default_satuan && $('#setDefaultSatuan')) $('#setDefaultSatuan').value = s.default_satuan.value || '';
                if (s.default_tempat_pengesahan && $('#setDefaultTempat')) $('#setDefaultTempat').value = s.default_tempat_pengesahan.value || '';
                if (s.default_nama_kepsek && $('#setDefaultNamaKepsek')) $('#setDefaultNamaKepsek').value = s.default_nama_kepsek.value || '';
                if (s.default_nip_kepsek && $('#setDefaultNipKepsek')) $('#setDefaultNipKepsek').value = s.default_nip_kepsek.value || '';
                if (s.default_nama_guru && $('#setDefaultNamaGuru')) $('#setDefaultNamaGuru').value = s.default_nama_guru.value || '';
                if (s.default_nip_guru && $('#setDefaultNipGuru')) $('#setDefaultNipGuru').value = s.default_nip_guru.value || '';
                if (s.default_tanggal_pengesahan && $('#setDefaultTanggal')) $('#setDefaultTanggal').value = s.default_tanggal_pengesahan.value || '';

                // Prefill active form Section 1 inputs if they are currently blank
                const setIfEmpty = (selector, val) => {
                    const el = $(selector);
                    if (el && !el.value.trim() && val) {
                        el.value = val;
                    }
                };
                if (s.default_satuan) setIfEmpty('#inpSatuan', s.default_satuan.value);
                if (s.default_tempat_pengesahan) setIfEmpty('#inpTempatPengesahan', s.default_tempat_pengesahan.value);
                if (s.default_nama_kepsek) setIfEmpty('#inpNamaKepsek', s.default_nama_kepsek.value);
                if (s.default_nip_kepsek) setIfEmpty('#inpNipKepsek', s.default_nip_kepsek.value);
                if (s.default_nama_guru) setIfEmpty('#inpGuru', s.default_nama_guru.value);
                if (s.default_nip_guru) setIfEmpty('#inpNipGuru', s.default_nip_guru.value);
                if (s.default_tanggal_pengesahan) setIfEmpty('#inpTanggalPengesahan', s.default_tanggal_pengesahan.value);

                // Check local storage override
                const localKey = localStorage.getItem('sintesa_custom_gemini_key');
                if (localKey) {
                    $('#setApiKey').placeholder = 'Tersimpan lokal di browser Anda';
                }
            })
            .catch(() => {});
    }

    function saveSettings(e) {
        if (e) e.preventDefault();
        const apiKey = $('#setApiKey').value.trim();
        const model = $('#setModel').value;
        const temp = $('#setTemp').value;
        const rateLimit = $('#setRateLimit').value;

        const defaultSatuan = $('#setDefaultSatuan') ? $('#setDefaultSatuan').value.trim() : '';
        const defaultTempat = $('#setDefaultTempat') ? $('#setDefaultTempat').value.trim() : '';
        const defaultNamaKepsek = $('#setDefaultNamaKepsek') ? $('#setDefaultNamaKepsek').value.trim() : '';
        const defaultNipKepsek = $('#setDefaultNipKepsek') ? $('#setDefaultNipKepsek').value.trim() : '';
        const defaultNamaGuru = $('#setDefaultNamaGuru') ? $('#setDefaultNamaGuru').value.trim() : '';
        const defaultNipGuru = $('#setDefaultNipGuru') ? $('#setDefaultNipGuru').value.trim() : '';
        const defaultTanggal = $('#setDefaultTanggal') ? $('#setDefaultTanggal').value.trim() : '';

        const payload = {
            ai_model: model,
            ai_temperature: temp,
            rate_limit_max: rateLimit,
            default_satuan: defaultSatuan,
            default_tempat_pengesahan: defaultTempat,
            default_nama_kepsek: defaultNamaKepsek,
            default_nip_kepsek: defaultNipKepsek,
            default_nama_guru: defaultNamaGuru,
            default_nip_guru: defaultNipGuru,
            default_tanggal_pengesahan: defaultTanggal
        };

        // Also save to localStorage for client-header override
        if (apiKey && apiKey.indexOf('...') === -1) {
            localStorage.setItem('sintesa_custom_gemini_key', apiKey);
            payload.gemini_api_key = apiKey;
        } else if (!apiKey) {
            localStorage.removeItem('sintesa_custom_gemini_key');
            payload.gemini_api_key = '';
        }

        fetch('api/settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then((r) => r.json())
            .then((d) => {
                if (d && d.ok) {
                    showToast('Pengaturan & Standar Pengesahan berhasil disimpan ke database!', 'ok');
                    $('#modalSettings').hidden = true;

                    // Immediately synchronize active form fields if user updated defaults
                    if (defaultSatuan) $('#inpSatuan').value = defaultSatuan;
                    if (defaultTempat) $('#inpTempatPengesahan').value = defaultTempat;
                    if (defaultNamaKepsek) $('#inpNamaKepsek').value = defaultNamaKepsek;
                    if (defaultNipKepsek) $('#inpNipKepsek').value = defaultNipKepsek;
                    if (defaultNamaGuru) $('#inpGuru').value = defaultNamaGuru;
                    if (defaultNipGuru) $('#inpNipGuru').value = defaultNipGuru;
                    if (defaultTanggal) $('#inpTanggalPengesahan').value = defaultTanggal;

                    // Update cached settings in memory
                    loadSettings();
                } else {
                    throw new Error(d.error || 'Gagal menyimpan.');
                }
            })
            .catch((err) => showToast('Gagal: ' + err.message, 'err'));
    }

    // =========================================================================
    // EVENT LISTENERS INITIALIZATION
    // =========================================================================

    form.addEventListener('submit', generate);
    btnDownload.addEventListener('click', downloadWord);
    btnReset.addEventListener('click', resetAll);
    btnEditable.addEventListener('click', toggleEditable);
    $('#selProgram').addEventListener('change', onProgramChange);
    if ($('#selKonsentrasi')) {
        $('#selKonsentrasi').addEventListener('change', onKonsentrasiChange);
    }
    zoomSelect.addEventListener('change', handleZoom);

    // AI Suggestion Buttons
    $$('[data-ai-suggest]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const fld = btn.getAttribute('data-ai-suggest');
            getAiSuggestion(fld, btn);
        });
    });

    const btnSuggestAll = $('#btnSuggestAll');
    if (btnSuggestAll) {
        btnSuggestAll.addEventListener('click', (e) => {
            e.preventDefault();
            getAiSuggestion('all', btnSuggestAll);
        });
    }

    // Accordion for CP/TP
    $('#btnAccordionCp').addEventListener('click', () => {
        const body = $('#accordionBodyCp');
        body.classList.toggle('open');
        $('#btnAccordionCp').querySelector('.icon').textContent = body.classList.contains('open') ? '▾' : '▸';
    });

    // Formatting Toolbar listeners
    $('#btnBold').addEventListener('click', () => formatDoc('bold'));
    $('#btnItalic').addEventListener('click', () => formatDoc('italic'));
    $('#btnUnderline').addEventListener('click', () => formatDoc('underline'));
    $('#btnUl').addEventListener('click', () => formatDoc('insertUnorderedList'));
    $('#btnOl').addEventListener('click', () => formatDoc('insertOrderedList'));
    $('#btnHeading2').addEventListener('click', () => formatDoc('formatBlock', '<h2>'));
    $('#btnHeading3').addEventListener('click', () => formatDoc('formatBlock', '<h3>'));
    $('#btnPrint').addEventListener('click', () => window.print());
    $('#btnCopyHtml').addEventListener('click', copyHtml);
    $('#btnSaveUpdate').addEventListener('click', updateCurrentDoc);

    // Preset selector change
    $('#selPreset').addEventListener('change', (e) => {
        if (e.target.value) {
            applyPreset(e.target.value);
            e.target.value = '';
        }
    });

    // Preset chips click
    $$('[data-preset]').forEach((chip) => {
        chip.addEventListener('click', () => {
            const pKey = chip.getAttribute('data-preset');
            applyPreset(pKey);
        });
    });

    // History search enter
    $('#historySearch').addEventListener('input', () => {
        loadHistory(1);
    });

    // Settings Form submit
    $('#settingsForm').addEventListener('submit', saveSettings);
    $('#btnAddMaster').addEventListener('click', addMasterKeahlian);

    // Expose global methods for inline HTML onclick handlers
    window.sintesaApp = {
        loadHistory,
        loadRppIntoEditor,
        deleteRpp,
        deleteMasterKeahlian
    };

    // Initial Bootstrap
    initModals();
    loadCounter();
    loadReferensi();
    loadSettings();

    // Auto-load TKJ sample initially as default placeholder demo if desired
    // (Keeps empty placeholder by default as per PRD)
})();
