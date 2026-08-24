<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Pengesahan & Laporan Akhir PKL - {{ $siswa->nama_lengkap }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 18mm 12mm 18mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, Georgia, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000000;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .kop-table td {
            border: none !important;
            padding: 0;
            vertical-align: middle;
        }
        .kop-logo-left {
            width: 65px;
            text-align: left;
        }
        .kop-logo-left img {
            height: 56px;
            width: auto;
        }
        .kop-logo-right {
            width: 65px;
            text-align: right;
        }
        .kop-logo-right img {
            height: 56px;
            width: auto;
        }
        .kop-text {
            text-align: center;
            padding: 0 6px;
        }
        .school-gov { font-size: 9.5pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-dept { font-size: 10.5pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-subdept { font-size: 9pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-name { font-size: 13.5pt; font-weight: bold; text-transform: uppercase; margin: 2px 0; letter-spacing: 0.5px; }
        .school-address, .school-contact, .school-postal { font-size: 7.5pt; margin: 0.5px 0; line-height: 1.25; }

        /* Judul Dokumen Formal */
        .doc-title-container {
            text-align: center;
            margin: 8px 0 14px 0;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .doc-subtitle {
            font-size: 10pt;
            margin-top: 2px;
            font-weight: normal;
        }

        /* Section Headings */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000000;
            padding-bottom: 2px;
            margin-top: 10px;
            margin-bottom: 6px;
        }

        /* Data Tables */
        table.formal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 8px;
            table-layout: fixed;
        }
        table.formal-table td {
            padding: 2.5px 2px;
            vertical-align: top;
            border: none;
        }
        table.formal-table td.lbl {
            font-weight: bold;
        }
        table.formal-table td.sep {
            width: 10px;
            text-align: center;
        }

        /* Grid Table (Borders) */
        table.border-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 4px;
            margin-bottom: 8px;
        }
        table.border-table th, table.border-table td {
            border: 1px solid #000000;
            padding: 4px 6px;
            vertical-align: top;
        }
        table.border-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        /* Signatures Layout */
        .sig-container {
            margin-top: 10px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .sig-date {
            text-align: right;
            font-size: 9.5pt;
            margin-bottom: 6px;
        }
        table.sig-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            table-layout: fixed;
            text-align: center;
        }
        table.sig-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 8px;
            border: none;
        }
        .sig-space {
            height: 44px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .sig-sub {
            font-size: 8.5pt;
            margin-top: 1px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Resmi SMKN 1 Ciamis -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo-left">
                @if(file_exists(public_path('jabar.png')))
                    <img src="{{ public_path('jabar.png') }}" alt="Logo Jawa Barat">
                @endif
            </td>
            <td class="kop-text">
                <div class="school-gov">{{ $report_kop_baris_1 ?? 'PEMERINTAH DAERAH PROVINSI JAWA BARAT' }}</div>
                <div class="school-dept">{{ $report_kop_baris_2 ?? 'DINAS PENDIDIKAN' }}</div>
                <div class="school-subdept">{{ $report_kop_baris_3 ?? 'CABANG DINAS PENDIDIKAN WILAYAH XIII' }}</div>
                <div class="school-name">{{ $report_kop_baris_4 ?? 'SMK NEGERI 1 CIAMIS' }}</div>
                <div class="school-address">{{ $report_kop_baris_5 ?? 'Jl. Jenderal Sudirman Nomor : 269 Telepon : (0265) 771204' }}</div>
                <div class="school-contact">{{ $report_kop_baris_6 ?? 'Faksimile : (0265) 771204/777719 Website : www.smkn1ciamis.sch.id E-mail : surat@smkn1cms.net' }}</div>
                <div class="school-postal">{{ $report_kop_baris_7 ?? 'Ciamis – 46215' }}</div>
            </td>
            <td class="kop-logo-right">
                @if(file_exists(public_path('smea.png')))
                    <img src="{{ public_path('smea.png') }}" alt="Logo SMKN 1 Ciamis">
                @endif
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="doc-title-container">
        <div class="doc-title">LEMBAR PENGESAHAN & LAPORAN AKHIR</div>
        <div class="doc-title" style="font-size: 11pt; margin-top: 1px;">PRAKTIK KERJA LAPANGAN (PKL)</div>
        <div class="doc-subtitle">Tahun Pelajaran {{ $tahunAjaran ?? (date('Y').'/'.(date('Y') + 1)) }}</div>
    </div>

    <!-- A. Identitas Peserta Didik & DUDI -->
    <div class="section-title">A. Identitas Peserta Didik & Tempat Praktik Kerja Lapangan</div>
    <table class="formal-table">
        <tr>
            <td class="lbl" style="width: 22%;">Nama Lengkap Siswa</td>
            <td class="sep">:</td>
            <td style="width: 33%;"><strong>{{ $siswa->nama_lengkap }}</strong></td>
            <td class="lbl" style="width: 18%;">NIS / NISN</td>
            <td class="sep">:</td>
            <td style="width: 27%;">{{ $siswa->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Kelas / Konsentrasi</td>
            <td class="sep">:</td>
            <td>{{ $siswa->kelas ?? '-' }} / {{ $siswa->konsentrasiKeahlian->nama ?? '-' }}</td>
            <td class="lbl">Program Keahlian</td>
            <td class="sep">:</td>
            <td>{{ $siswa->konsentrasiKeahlian?->programKeahlian?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Dunia Kerja / Instansi</td>
            <td class="sep">:</td>
            <td colspan="4"><strong>{{ $siswa->dudi->nama ?? ($siswa->pengajuanPkl?->nama_perusahaan ?? '-') }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">Alamat Instansi</td>
            <td class="sep">:</td>
            <td colspan="4">
                @php
                    $alamat = trim($siswa->dudi->alamat ?? ($siswa->pengajuanPkl?->alamat ?? '-'));
                    $kota = trim($siswa->dudi->kota ?? ($siswa->pengajuanPkl?->kota ?? ''));
                @endphp
                {{ $alamat }}{{ (!empty($kota) && !str_contains(strtolower($alamat), strtolower($kota))) ? ', ' . $kota : '' }}
            </td>
        </tr>
        <tr>
            <td class="lbl">Pembimbing Lapangan</td>
            <td class="sep">:</td>
            <td>
                {{ $siswa->pembimbingDudi->nama_lengkap ?? ($siswa->pembimbing_dudi_nama ?? '-') }}
                @if(!empty($siswa->pembimbing_dudi_jabatan) || !empty($siswa->pembimbingDudi->jabatan))
                    ({{ $siswa->pembimbing_dudi_jabatan ?? $siswa->pembimbingDudi->jabatan }})
                @endif
            </td>
            <td class="lbl">Pembimbing Sekolah</td>
            <td class="sep">:</td>
            <td>{{ $siswa->pembimbingSekolah->nama_lengkap ?? '-' }}</td>
        </tr>
    </table>

    <!-- B. Ringkasan Laporan Akhir PKL -->
    <div class="section-title">B. Ringkasan Laporan Akhir PKL</div>
    <table class="formal-table">
        <tr>
            <td class="lbl" style="width: 22%;">Judul Laporan</td>
            <td class="sep">:</td>
            <td colspan="4"><strong>{{ $laporan->judul ?? 'Belum mengisi judul laporan' }}</strong></td>
        </tr>
        <tr>
            <td class="lbl" style="vertical-align: top;">Ringkasan Pekerjaan</td>
            <td class="sep" style="vertical-align: top;">:</td>
            <td colspan="4" style="text-align: justify; line-height: 1.4;">
                {!! nl2br(e($laporan->deskripsi ?? 'Tidak ada ringkasan yang ditulis.')) !!}
            </td>
        </tr>
    </table>

    <!-- C. Dokumentasi & Tautan Media Sosial / Presentasi -->
    <div class="section-title">C. Dokumentasi & Tautan Media / Presentasi</div>
    @if(!empty($laporan->link_media_sosial) && is_array($laporan->link_media_sosial) && count(array_filter($laporan->link_media_sosial)) > 0)
        <table class="border-table">
            <thead>
                <tr>
                    <th style="width: 32px;">No</th>
                    <th style="width: 160px; text-align: left;">Kategori / Platform</th>
                    <th style="text-align: left;">Tautan URL Lengkap</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach(array_filter($laporan->link_media_sosial) as $link)
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td>
                            @if(str_contains($link, 'youtube.com') || str_contains($link, 'youtu.be'))
                                Video Presentasi (YouTube)
                            @elseif(str_contains($link, 'tiktok.com'))
                                Video Dokumentasi (TikTok)
                            @elseif(str_contains($link, 'drive.google.com'))
                                Berkas Portofolio (Google Drive)
                            @elseif(str_contains($link, 'instagram.com'))
                                Dokumentasi Instagram
                            @else
                                Tautan Media Eksternal
                            @endif
                        </td>
                        <td style="word-break: break-all;">
                            {{ $link }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="font-size: 9pt; font-style: italic; margin-bottom: 6px;">
            Tidak ada tautan media sosial atau berkas eksternal yang dilampirkan.
        </div>
    @endif

    <!-- Status Pengesahan -->
    <table class="formal-table" style="margin-top: 4px; margin-bottom: 6px;">
        <tr>
            <td class="lbl" style="width: 22%;">Status Pengesahan</td>
            <td class="sep">:</td>
            <td colspan="4">
                @php
                    $st = strtolower($laporan->status ?? 'submitted');
                    $statusText = match($st) {
                        'approved' => 'Disetujui oleh Guru Pembimbing Sekolah',
                        'rejected' => 'Perlu Perbaikan / Ditolak',
                        default => 'Telah Diajukan (Menunggu Validasi)',
                    };
                @endphp
                <strong>{{ $statusText }}</strong>
                @if($laporan && $laporan->submitted_at)
                    <span style="font-size: 8.5pt; margin-left: 4px;">
                        (Diajukan pada tanggal: {{ \Carbon\Carbon::parse($laporan->submitted_at)->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB)
                    </span>
                @endif
            </td>
        </tr>
    </table>

    <!-- D. Lembar Pengesahan (Tanda Tangan 4 Pihak) -->
    <div class="sig-container">
        <div class="sig-date">
            Ciamis, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
        </div>
        <table class="sig-table">
            <tr>
                <td>
                    Menyetujui,<br>
                    <strong>Pembimbing Lapangan / DUDI</strong>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $siswa->pembimbingDudi->nama_lengkap ?? ($siswa->pembimbing_dudi_nama ?? '......................................................') }}</div>
                    <div class="sig-sub">{{ $siswa->pembimbing_dudi_jabatan ?? ($siswa->pembimbingDudi->jabatan ?? 'Pembimbing Lapangan') }}</div>
                </td>
                <td>
                    Menyetujui,<br>
                    <strong>Guru Pembimbing Sekolah</strong>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $siswa->pembimbingSekolah->nama_lengkap ?? '......................................................' }}</div>
                    <div class="sig-sub">NIP. {{ $siswa->pembimbingSekolah->nip ?? '-' }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 12px;">
                    Peserta Didik Pelaksana PKL,
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $siswa->nama_lengkap }}</div>
                    <div class="sig-sub">NIS. {{ $siswa->nis ?? '-' }}</div>
                </td>
                <td style="padding-top: 12px;">
                    Mengetahui,<br>
                    <strong>Kepala Program Keahlian</strong>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $kaprog->name ?? '......................................................' }}</div>
                    <div class="sig-sub">NIP. {{ $kaprog->nip ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>


