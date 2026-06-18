<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat PKL - {{ $siswa->nis }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            border: 10px solid #2563eb;
            padding: 40px;
            margin: 20px;
            height: 600px;
            position: relative;
        }
        .inner-border {
            border: 2px solid #3b82f6;
            padding: 40px;
            height: 520px;
        }
        .title {
            font-size: 48px;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 5px;
        }
        .subtitle {
            font-size: 24px;
            color: #475569;
            margin-top: 10px;
            letter-spacing: 2px;
        }
        .content {
            margin-top: 60px;
            font-size: 20px;
            line-height: 1.8;
            color: #334155;
            padding: 0 50px;
        }
        .name {
            font-size: 32px;
            font-weight: bold;
            color: #0f172a;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 80px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
            width: 200px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="inner-border">
            <div class="title">SERTIFIKAT</div>
            <div class="subtitle">PRAKTIK KERJA LAPANGAN</div>
            
            <div class="content">
                {{ $text }}
            </div>
            
            <div class="footer">
                <div class="signature-box">
                    <div>Pimpinan Perusahaan/DUDI</div>
                    <div class="signature-line"></div>
                    <div style="font-weight:bold; margin-top:5px;">{{ $siswa->dudi ? $siswa->dudi->nama_pimpinan : '-' }}</div>
                </div>
                <div class="signature-box">
                    <div>Kepala Sekolah</div>
                    <div class="signature-line"></div>
                    <div style="font-weight:bold; margin-top:5px;">..............................</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
