<!DOCTYPE html>
<html>
<head>
    <title>Data Akun Pembimbing DUDI - SMKN 1 Ciamis</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 30px 40px;
        }
        
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            font-size: 9px; 
            color: #334155; 
            margin: 0; 
            padding: 0; 
            line-height: 1.4;
        }

        /* Kop Surat (Header) Styles */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .kop-table td {
            border: none !important;
            padding: 0;
        }
        .kop-logo-left {
            width: 70px;
            text-align: left;
            vertical-align: middle;
        }
        .kop-logo-left img {
            height: 60px;
            width: auto;
        }
        .kop-logo-right {
            width: 70px;
            text-align: right;
            vertical-align: middle;
        }
        .kop-logo-right img {
            height: 60px;
            width: auto;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
            padding: 0 10px;
        }
        .school-gov { font-size: 9px; font-weight: bold; color: #1e293b; margin: 0; text-transform: uppercase; }
        .school-dept { font-size: 10px; font-weight: bold; color: #1e293b; margin: 0; text-transform: uppercase; }
        .school-subdept { font-size: 8.5px; font-weight: bold; color: #1e293b; margin: 0; text-transform: uppercase; }
        .school-name { font-size: 13px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; margin: 2px 0; letter-spacing: 0.5px; }
        .school-address, .school-contact, .school-postal { font-size: 7.5px; color: #64748b; margin: 1px 0; }

        /* Document Title */
        .doc-title { 
            text-align: center; 
            margin: 12px 0 8px 0; 
        }
        .doc-title h2 { 
            font-size: 12px; 
            font-weight: bold; 
            color: #0f172a; 
            text-transform: uppercase; 
            margin: 0; 
            letter-spacing: 0.5px;
        }
        .doc-subtitle { 
            font-size: 8.5px; 
            color: #475569; 
            margin-top: 3px; 
            font-weight: 500;
        }

        /* Metadata Box */
        .meta-table {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #1e3a8a;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .meta-table td {
            border: none !important;
        }
        .meta-subtable {
            width: 100%;
        }
        .meta-subtable td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 8px;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 120px;
        }
        .meta-sep {
            width: 10px;
            color: #475569;
            text-align: center;
        }
        .meta-value {
            color: #0f172a;
        }

        /* Info / Website Banner */
        .info-banner { 
            background: #eff6ff; 
            border: 1px solid #bfdbfe;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
            padding: 6px 10px; 
            margin-bottom: 10px; 
        }
        .info-banner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-banner-table td {
            border: none !important;
            vertical-align: middle;
            font-size: 8px;
            padding: 0;
        }
        .info-banner-icon {
            width: 70px;
        }
        .badge {
            background-color: #3b82f6;
            color: #ffffff;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7.5px;
            text-transform: uppercase;
        }
        .info-banner-text {
            color: #1e3a8a;
        }
        .info-banner-text .url {
            color: #1d4ed8;
            font-weight: bold;
        }
        .info-banner-note {
            text-align: right;
            color: #64748b;
            font-style: italic;
            font-size: 7.5px;
        }

        /* Data Table - Portrait Layout */
        table.data { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 12px; 
        }
        table.data th, table.data td { 
            border: 1px solid #cbd5e1; 
            padding: 5px 6px; 
            font-size: 7.5px;
            vertical-align: middle;
            white-space: nowrap;
        }
        table.data th { 
            background-color: #1e3a8a; 
            color: #ffffff; 
            text-align: center; 
            font-weight: bold; 
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-size: 7.5px;
        }
        table.data tr:nth-child(even) {
            background-color: #f8fafc;
        }
        table.data tr {
            page-break-inside: avoid;
        }
        table.data td.multi-line {
            white-space: normal;
            line-height: 1.3;
        }
        table.data td.multi-line div {
            font-size: 7px;
            margin: 1px 0;
        }

        .text-center { text-align: center; }
        .font-medium { font-weight: 500; color: #0f172a; }
        .code-font { 
            font-family: 'Courier New', Courier, monospace; 
            font-weight: bold;
            background: #f1f5f9;
            padding: 1px 4px;
            border-radius: 2px;
        }
        
        /* Note Box */
        .note-box { 
            background: #fffbeb; 
            border: 1px solid #fef3c7; 
            border-left: 4px solid #f59e0b; 
            border-radius: 4px; 
            padding: 6px 10px; 
            font-size: 7.5px; 
            color: #78350f;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .note-box p { margin: 1px 0; }
        .note-title {
            font-weight: bold;
            font-size: 8px;
            margin-bottom: 3px;
        }

        /* Footer & Signature Table */
        .footer-table {
            width: 100%;
            margin-top: 10px;
            page-break-inside: avoid;
        }
        .footer-table td {
            border: none !important;
            vertical-align: bottom;
            padding: 0;
        }
        .footer-left {
            font-size: 7px;
            color: #94a3b8;
            font-style: italic;
        }
        .footer-right {
            width: 180px;
        }
        .signature-box {
            text-align: center;
            font-size: 8px;
            color: #334155;
        }
        .sig-date {
            margin-bottom: 3px;
        }
        .sig-title {
            font-weight: bold;
            color: #1e3a8a;
        }
        .sig-space {
            height: 40px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo-left">
                <img src="{{ public_path('jabar.png') }}" alt="Logo Provinsi Jawa Barat">
            </td>
            <td class="kop-text">
                <div class="school-gov">{{ $report_kop_baris_1 }}</div>
                <div class="school-dept">{{ $report_kop_baris_2 }}</div>
                <div class="school-subdept">{{ $report_kop_baris_3 }}</div>
                <div class="school-name">{{ $report_kop_baris_4 }}</div>
                <div class="school-address">{{ $report_kop_baris_5 }}</div>
                <div class="school-contact">{{ $report_kop_baris_6 }}</div>
                <div class="school-postal">{{ $report_kop_baris_7 }}</div>
            </td>
            <td class="kop-logo-right">
                <img src="{{ public_path('smea.png') }}" alt="Logo SMKN 1 Ciamis">
            </td>
        </tr>
    </table>

    <!-- Document Title -->
    <div class="doc-title">
        <h2>Data Akun Pembimbing DUDI</h2>
        <div class="doc-subtitle">Program Praktik Kerja Lapangan (PKL) &mdash; Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</div>
    </div>

    <!-- Metadata Block -->
    <table class="meta-table">
        <tr>
            <td>
                <table class="meta-subtable">
                    <tr>
                        <td class="meta-label">Tanggal Diterbitkan</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-value">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Waktu Unduh</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-value">{{ \Carbon\Carbon::now()->format('H:i:s') }} WIB</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Dokumen Dikeluarkan</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-value">Pokja PKL SMKN 1 Ciamis</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Jumlah Data</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-value"><strong style="color: #1e3a8a;">{{ $mentors->count() }}</strong> Akun Pembimbing</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Info Banner -->
    <div class="info-banner">
        <table class="info-banner-table">
            <tr>
                <td class="info-banner-icon">
                    <span class="badge">Akses Login</span>
                </td>
                <td class="info-banner-text">
                    Gunakan alamat resmi berikut untuk login ke sistem: <span class="url">https://pkl.smkn1ciamis.id</span>
                </td>
                <td class="info-banner-note">
                    *Akun ini resmi diterbitkan oleh pihak sekolah
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Table (Portrait Layout) -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 18%;">Nama Lengkap</th>
                <th style="width: 12%;">Username</th>
                <th style="width: 18%;">Email</th>
                <th style="width: 10%;">Password</th>
                <th style="width: 22%;">Perusahaan (DUDI)</th>
                <th style="width: 16%;">Jabatan / No. HP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mentors as $index => $mentor)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-medium">{{ $mentor->nama_lengkap }}</td>
                    <td class="text-center">{{ $mentor->user->username }}</td>
                    <td>{{ $mentor->user->email }}</td>
                    <td class="text-center code-font">pembimbing123</td>
                    <td>{{ $mentor->dudi->nama }}</td>
                    <td class="multi-line">
                        <div><strong>Jabatan:</strong> {{ $mentor->jabatan ?? '-' }}</div>
                        <div><strong>No. HP:</strong> {{ $mentor->no_hp ?? '-' }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px; color: #64748b;">Belum ada data pembimbing DUDI.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Note Box -->
    <div class="note-box">
        <div class="note-title">Catatan Penting:</div>
        <p>1. Password default untuk seluruh akun pembimbing adalah <strong class="code-font">pembimbing123</strong>.</p>
        <p>2. Pembimbing DUDI <strong>wajib mengganti password</strong> saat pertama kali login demi keamanan akun.</p>
        <p>3. Jika mengalami kendala login atau sinkronisasi data, segera hubungi Pokja PKL SMKN 1 Ciamis.</p>
    </div>

    <!-- Footer & Signature -->
    <table class="footer-table">
        <tr>
            <td class="footer-left">
                Dokumen ini digenerate otomatis oleh Sistem PKL SMKN 1 Ciamis pada {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }} WIB
            </td>
            <td class="footer-right">
                <div class="signature-box">
                    <div class="sig-date">Ciamis, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</div>
                    <div class="sig-title">Pokja PKL SMKN 1 Ciamis,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">({{ auth()->user()->name }})</div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
