<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengantar PKL - {{ $siswa->nama_lengkap }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 30px 40px;
            line-height: 1.5;
            font-size: 14px;
        }

        /* Kop Surat Styles */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 4px double #000;
            padding-bottom: 12px;
            margin-bottom: 25px;
            position: relative;
        }
        
        .logo-container {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 75px;
            height: 85px;
        }

        .logo-container img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
        
        .kop-text {
            text-align: center;
            flex-grow: 1;
            padding-left: 90px;
            padding-right: 20px;
        }
        
        .kop-text h2 {
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }
        
        .kop-text h1 {
            font-size: 19px;
            font-weight: bold;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .kop-text p {
            font-size: 10px;
            margin: 0 0 2px 0;
        }
        
        .kop-text .alamat {
            font-style: normal;
        }
        
        /* Content layout styles */
        .surat-meta {
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .surat-meta table {
            width: 100%;
            border-collapse: collapse;
        }

        .surat-meta td {
            padding: 1px 0;
            vertical-align: top;
        }

        .penerima-container {
            margin-top: 20px;
            margin-bottom: 25px;
        }

        .penerima-container p {
            margin: 0 0 5px 0;
        }

        .penerima-nama {
            font-weight: bold;
            text-transform: capitalize;
        }

        .isi-surat {
            text-align: justify;
            text-indent: 40px;
            margin-bottom: 20px;
        }

        /* Student Data Table */
        .siswa-table {
            width: 85%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        .siswa-table td {
            padding: 6px 10px;
            vertical-align: top;
            border: 1px solid #000;
        }

        .siswa-table td.label-col {
            width: 35%;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        /* Signature Block */
        .ttd-section {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-container {
            width: 300px;
            text-align: center;
        }

        .ttd-space {
            height: 80px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Print Button Utility */
        .no-print-bar {
            background-color: #1e293b;
            color: #fff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: sans-serif;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        
        .print-btn {
            background-color: #10b981;
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }
        
        .print-btn:hover {
            background-color: #059669;
        }
        
        @media print {
            .no-print-bar {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Utility bar for printing inside app -->
    <div class="no-print-bar">
        <span>Dokumen Surat Pengantar PKL Resmi. Silakan cetak sekarang.</span>
        <button onclick="window.print()" class="print-btn">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display:inline-block; vertical-align:middle;">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak Surat
        </button>
    </div>

    <!-- Kop Surat -->
    <div class="kop-surat">
        <div class="logo-container">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b2/Lambang_Pemerintah_Daerah_Provinsi_Jawa_Barat.svg" alt="Logo Jawa Barat">
        </div>
        <div class="kop-text">
            <h2>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h2>
            <h1>DINAS PENDIDIKAN</h1>
            <h2>CABANG DINAS PENDIDIKAN WILAYAH XIII</h2>
            <h1 style="font-size: 21px;">SMK NEGERI 1 CIAMIS</h1>
            <p class="alamat" style="font-style: normal; font-size: 11px;">Jalan : Jl. Jenderal Sudirman Nomor : 269 Telepon : (0265) 771204</p>
            <p style="font-style: normal; font-size: 11px;">Faksimile : (0265) 771204/777719 Website : www.smkn1ciamis.sch.id E-mail : surat@smkn1cms.net</p>
            <p style="font-weight: bold; font-style: normal; font-size: 12px; margin-top: 4px; text-transform: uppercase;">Ciamis – 46215</p>
        </div>
    </div>

    <!-- Meta Surat -->
    <div class="surat-meta">
        <table>
            <tr>
                <td style="width: 12%;">Nomor</td>
                <td style="width: 3%;">:</td>
                <td style="width: 50%;">421.5 / ............ / SMKN1.CMS / PKL / {{ date('Y') }}</td>
                <td style="width: 35%; text-align: right;">Ciamis, {{ now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>:</td>
                <td style="font-weight: bold; text-decoration: underline;">Permohonan Tempat Praktik Kerja Lapangan (PKL)</td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- Penerima -->
    <div class="penerima-container">
        <p>Kepada Yth.</p>
        <p class="penerima-nama">Pimpinan / HRD {{ $pengajuan->nama_perusahaan }}</p>
        <p>di</p>
        <p style="font-weight: bold;">{{ $pengajuan->alamat ?? $pengajuan->kota ?? 'Tempat' }}</p>
    </div>

    <!-- Isi Surat -->
    <p class="isi-surat">
        Dengan hormat, dalam rangka mempersiapkan tenaga kerja yang terampil dan profesional serta memenuhi tuntutan kurikulum Sekolah Menengah Kejuruan (SMK), siswa tingkat akhir diwajibkan untuk menempuh program Praktik Kerja Lapangan (PKL). Kegiatan ini bertujuan untuk menyelaraskan teori yang diperoleh di sekolah dengan praktik langsung di dunia kerja.
    </p>
    
    <p class="isi-surat" style="text-indent: 0;">
        Berkaitan dengan hal tersebut, kami mengajukan permohonan agar siswa kami berikut ini diperkenankan melaksanakan Praktik Kerja Lapangan (PKL) pada instansi/perusahaan yang Bapak/Ibu pimpin:
    </p>

    <!-- Tabel Data Siswa -->
    <table class="siswa-table">
        <tr>
            <td class="label-col">Nama Lengkap</td>
            <td>{{ $siswa->nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="label-col">NIS</td>
            <td>{{ $siswa->nis }}</td>
        </tr>
        <tr>
            <td class="label-col">Kelas</td>
            <td>{{ $siswa->kelas }}</td>
        </tr>
        <tr>
            <td class="label-col">Konsentrasi Keahlian</td>
            <td>{{ $siswa->konsentrasiKeahlian->nama }}</td>
        </tr>
    </table>

    <p class="isi-surat">
        Pelaksanaan Praktik Kerja Lapangan (PKL) ini direncanakan akan berlangsung pada Tahun Pelajaran {{ $tahunAjaranActive }}. Selama pelaksanaan PKL, siswa diwajibkan mematuhi segala tata tertib dan peraturan yang berlaku di perusahaan/instansi Bapak/Ibu.
    </p>

    <p class="isi-surat">
        Besar harapan kami permohonan ini dapat dipertimbangkan dan dikabulkan. Atas bantuan, perhatian, serta kerja sama yang terjalin selama ini, kami mengucapkan terima kasih.
    </p>

    <!-- Tanda Tangan -->
    <div class="ttd-section">
        <div class="ttd-container">
            <p>Hormat kami,</p>
            <p>Ketua Pokja PKL SMKN 1 Ciamis</p>
            <div class="ttd-space"></div>
            <p class="ttd-nama">......................................................</p>
            <p style="margin: 0; font-size: 12px; color: #555;">NIP. .................................................</p>
        </div>
    </div>

</body>
</html>
