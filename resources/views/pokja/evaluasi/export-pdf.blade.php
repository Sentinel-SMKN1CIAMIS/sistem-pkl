<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Evaluasi Progres Siswa PKL</title>
    <style>
        @page { size: A4 landscape; margin: 22px 28px; }
    </style>
</head>
<body>
    @include('pdf.partials.kop-surat')

    <div class="doc-header">
        <h2>Rekapitulasi Evaluasi Progres Siswa PKL</h2>
        <div class="doc-sub">Ringkasan Progres Jurnal, Kehadiran, dan Laporan Akhir Siswa</div>
    </div>

    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <div class="kpi-label">Total Siswa</div>
                <div class="kpi-value">{{ $totalSiswa }}</div>
            </td>
            <td class="kpi-card green">
                <div class="kpi-label">Laporan Akhir Masuk</div>
                <div class="kpi-value" style="color:#059669;">{{ $laporanMasuk }}</div>
            </td>
            <td class="kpi-card amber">
                <div class="kpi-label">Rata-rata Jurnal Valid</div>
                <div class="kpi-value" style="color:#d97706;">{{ $rataJurnal }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="26" class="text-center">No</th>
                <th>Nama Siswa &amp; NIS</th>
                <th width="65">Kelas</th>
                <th>Tempat PKL (DUDI)</th>
                <th>Pembimbing Sekolah</th>
                <th width="80" class="text-center">Jurnal Valid / Total</th>
                <th width="65" class="text-center">Kehadiran</th>
                <th width="85" class="text-center">Laporan Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswas as $idx => $siswa)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <div class="font-bold">{{ $siswa->nama_lengkap }}</div>
                        <div class="muted">NIS: {{ $siswa->nis }}</div>
                    </td>
                    <td>
                        <div class="font-bold">{{ $siswa->kelas }}</div>
                        <div class="muted">{{ $siswa->konsentrasiKeahlian?->kode ?? '-' }}</div>
                    </td>
                    <td>{{ $siswa->dudi?->nama ?? '-' }}</td>
                    <td>{{ $siswa->pembimbingSekolah?->nama_lengkap ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $siswa->valid_jurnal }} / {{ $siswa->total_jurnal }}</td>
                    <td class="text-center font-bold">{{ $siswa->absensi_count }} hari</td>
                    <td class="text-center">
                        @if($siswa->laporan)
                            <span class="badge badge-green">Sudah Unggah</span>
                        @else
                            <span class="badge badge-red">Belum Ada</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding:16px; color:#64748b;">Tidak ada data evaluasi siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sig">
        <tr>
            <td class="footer-note">Dicetak otomatis melalui Sistem MAS-PKL pada {{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB.</td>
            <td class="sig-box">
                <div>Ciamis, {{ now()->isoFormat('D MMMM YYYY') }}</div>
                <div class="font-bold" style="margin-top:2px;">Ketua Pokja PKL</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ auth()->user()->name }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
