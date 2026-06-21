<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengantar PKL - {{ $siswa->nama_lengkap }}</title>
    <style>
        /* General Layout */
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .page-container {
            width: 210mm;
            height: 297mm;
            padding: 15mm 20mm;
            background-color: #fff;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow: hidden; /* Strict A4 1-page constraint */
        }

        /* Kop Surat Styles */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 4px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
            position: relative;
        }
        
        .logo-container {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 70px;
            height: 80px;
        }

        .logo-container img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
        
        .kop-text {
            text-align: center;
            flex-grow: 1;
            padding-left: 80px;
            padding-right: 20px;
        }
        
        .kop-text h2 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }
        
        .kop-text h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .kop-text p {
            font-size: 10px;
            margin: 0 0 2px 0;
            line-height: 1.3;
        }
        
        .kop-text .alamat {
            font-style: normal;
        }
        
        /* Content layout styles */
        .surat-meta {
            margin-bottom: 15px;
            font-size: 13.5px;
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
            margin-top: 10px;
            margin-bottom: 15px;
            font-size: 13.5px;
        }

        .penerima-container p {
            margin: 0 0 4px 0;
        }

        .penerima-nama {
            font-weight: bold;
            text-transform: capitalize;
        }

        .isi-surat {
            text-align: justify;
            text-indent: 40px;
            margin-bottom: 12px;
            margin-top: 0;
            font-size: 13.5px;
            line-height: 1.45;
        }

        /* Student Data Table */
        .siswa-table {
            width: 85%;
            margin: 10px auto;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .siswa-table td {
            padding: 5px 10px;
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
            margin-top: auto; /* Push to the very bottom of A4 container */
            padding-bottom: 5mm;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }

        .ttd-container {
            width: 300px;
            text-align: center;
            font-size: 13.5px;
        }

        .ttd-space {
            height: 65px; /* Compact signature space */
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        /* Print Button Utility */
        .no-print-bar {
            width: 210mm;
            background-color: #1e293b;
            color: #fff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: sans-serif;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            box-sizing: border-box;
        }
        
        .print-btn {
            background-color: #10b981;
            color: #fff;
            border: none;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .print-btn:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }
        
        /* Media Print Settings */
        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            body {
                background-color: #fff;
                padding: 0;
                display: block;
            }
            .no-print-bar {
                display: none;
            }
            .page-container {
                box-shadow: none;
                padding: 15mm 20mm;
                width: 210mm;
                height: 297mm;
                overflow: hidden;
            }
        }
    </style>
</head>
<body>

    <!-- Utility bar for printing inside app -->
    <div class="no-print-bar">
        <span style="font-size: 13px; font-weight: 500;">Dokumen Surat Pengantar PKL Resmi. Silakan cetak sekarang.</span>
        <button onclick="window.print()" class="print-btn">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display:inline-block; vertical-align:middle;">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak Surat
        </button>
    </div>

    <!-- Dynamic A4 Page Container -->
    <div class="page-container">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <div class="logo-container">
                <img src="{{ asset('jabar.png') }}" alt="Logo Jawa Barat">
            </div>
            <div class="kop-text">
                <h2>{{ $surat_kop_baris_1 }}</h2>
                <h1>{{ $surat_kop_baris_2 }}</h1>
                <h2>{{ $surat_kop_baris_3 }}</h2>
                <h1 style="font-size: 20px;">{{ $surat_kop_baris_4 }}</h1>
                <p class="alamat">{{ $surat_kop_baris_5 }}</p>
                <p>{{ $surat_kop_baris_6 }}</p>
                <p style="font-weight: bold; font-size: 11px; margin-top: 3px; text-transform: uppercase;">{{ $surat_kop_baris_7 }}</p>
            </div>
        </div>

        <!-- Meta Surat -->
        <div class="surat-meta">
            <table>
                <tr>
                    <td style="width: 12%;">Nomor</td>
                    <td style="width: 3%;">:</td>
                    <td style="width: 50%;">{{ $surat_nomor_format }}</td>
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
            {{ $surat_isi_pembuka }}
        </p>
        
        <p class="isi-surat" style="text-indent: 0;">
            {{ $surat_isi_tengah }}
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
                <td>{{ $siswa->konsentrasiKeahlian?->nama ?? '-' }}</td>
            </tr>
        </table>

        <p class="isi-surat">
            {{ $surat_isi_penutup }}
        </p>

        <p class="isi-surat">
            {{ $surat_isi_salam }}
        </p>

        <!-- Tanda Tangan -->
        <div class="ttd-section">
            <div class="ttd-container">
                <p style="margin: 0 0 2px 0;">Hormat kami,</p>
                <p style="margin: 0 0 2px 0;">{{ $surat_ttd_jabatan }}</p>
                <div class="ttd-space"></div>
                <p class="ttd-nama">{{ $surat_ttd_nama }}</p>
                <p style="margin: 0; font-size: 12px; color: #333;">{{ $surat_ttd_nip }}</p>
            </div>
        </div>
    </div>

</body>
</html>
