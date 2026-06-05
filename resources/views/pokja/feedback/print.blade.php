<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback PKL - {{ $feedback->pembimbingDudi->dudi->nama }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }
        
        /* Kop Surat Styles */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 4px double #000;
            padding-bottom: 10px;
            margin-bottom: 30px;
            position: relative;
        }
        
        .logo-container {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 80px;
            height: 90px;
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
            padding-right: 90px;
        }
        
        .kop-text h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }
        
        .kop-text h1 {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .kop-text p {
            font-size: 11px;
            margin: 0 0 2px 0;
            font-style: italic;
        }
        
        .kop-text .alamat {
            font-style: normal;
        }
        
        /* Title Styles */
        .title-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .title-container h3 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        
        .title-container p {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
            vertical-align: top;
            font-size: 13px;
        }
        
        th {
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            background-color: #f2f2f2;
        }
        
        .col-dudi {
            width: 22%;
        }
        
        .col-evaluasi {
            width: 44%;
        }
        
        .col-saran {
            width: 34%;
        }
        
        /* Signature Styles */
        .ttd-container {
            float: right;
            width: 280px;
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
        }
        
        .ttd-space {
            height: 70px;
        }
        
        .ttd-nama {
            font-weight: bold;
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
            background-color: #2563eb;
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
            background-color: #1d4ed8;
        }
        
        @media print {
            .no-print-bar {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Utility bar for printing inside app -->
    <div class="no-print-bar">
        <span>Dokumen ini siap cetak sesuai format resmi.</span>
        <button onclick="window.print()" class="print-btn">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display:inline-block; vertical-align:middle;">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak Dokumen
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
            <h1 style="font-size: 22px;">SMK NEGERI 1 CIAMIS</h1>
            <p class="alamat" style="font-style: normal; font-size: 11px;">Jalan : Jl. Jenderal Sudirman Nomor : 269 Telepon : (0265) 771204</p>
            <p style="font-style: normal; font-size: 11px;">Faksimile : (0265) 771204/777719 Website : www.smkn1ciamis.sch.id E-mail : surat@smkn1cms.net</p>
            <p style="font-weight: bold; font-style: normal; font-size: 12px; margin-top: 4px; text-transform: uppercase;">Ciamis – 46215</p>
        </div>
    </div>

    <!-- Title -->
    <div class="title-container">
        <h3>FEEDBACK</h3>
        <p>KEGIATAN PRAKTEK KERJA LAPANGAN (PKL)</p>
        <p>TAHUN PELAJARAN {{ $feedback->created_at->format('Y') }}/{{ $feedback->created_at->format('Y') + 1 }}</p>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th class="col-dudi">NAMA DU/DI</th>
                <th class="col-evaluasi">URAIAN EVALUASI PKL</th>
                <th class="col-saran">SARAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="col-dudi" style="font-weight: bold;">
                    {{ $feedback->pembimbingDudi->dudi->nama }}
                </td>
                <td class="col-evaluasi">
                    {{ $feedback->isi_feedback }}
                </td>
                <td class="col-saran">
                    {{ $feedback->saran ?? '-' }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Signature -->
    <div class="ttd-container">
        <p>{{ $feedback->pembimbingDudi->dudi->kota ?? 'Ciamis' }}, {{ $feedback->created_at->translatedFormat('d F Y') }}</p>
        <p style="margin-top: 5px;">Pembimbing PKL DU/DI,</p>
        <div class="ttd-space"></div>
        <p class="ttd-nama" style="text-decoration: underline; font-weight: bold;">{{ $feedback->pembimbingDudi->nama_lengkap }}</p>
        <p style="margin: 0; font-size: 12px; font-weight: normal;">{{ $feedback->pembimbingDudi->jabatan }}</p>
    </div>

</body>
</html>
