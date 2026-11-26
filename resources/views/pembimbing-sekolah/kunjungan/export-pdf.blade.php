<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Log Kunjungan Lapangan - {{ $teacher->nama_lengkap }}</title>
    <style>
        @page { size: A4 portrait; margin: 22px 26px; }
    </style>
</head>
<body>
    @include('pdf.partials.kop-surat')

    <div class="doc-header">
        <h2>Rekap Log Kunjungan Supervisi DUDI</h2>
        <div class="doc-sub">Pembimbing Sekolah: {{ $teacher->nama_lengkap }}</div>
    </div>

    <table class="meta-box">
        <tr>
            <td class="meta-label">Nama Pembimbing</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $teacher->nama_lengkap }}</td>
            <td class="meta-label">NIP</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $teacher->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Total Kunjungan</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $kunjungans->count() }} kunjungan</td>
            <td class="meta-label">Tanggal Cetak</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ now()->isoFormat('D MMMM YYYY') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="24" class="text-center">No</th>
                <th width="72">Tanggal</th>
                <th width="120">Tempat DUDI</th>
                <th>Catatan Hasil Monitoring</th>
                <th width="105">Kendala</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kunjungans as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $item->tanggal_kunjungan?->isoFormat('D MMM YYYY') }}</td>
                    <td>
                        <div class="font-bold">{{ $item->dudi?->nama ?? '-' }}</div>
                        <div class="muted">{{ $item->dudi?->kota ?? '' }}</div>
                    </td>
                    <td>{{ $item->catatan_monitoring }}</td>
                    <td>{{ $item->kendala ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding:16px; color:#64748b;">Belum ada riwayat kunjungan lapangan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sig">
        <tr>
            <td class="footer-note">Dicetak otomatis melalui Sistem MAS-PKL.</td>
            <td class="sig-box">
                <div>Ciamis, {{ now()->isoFormat('D MMMM YYYY') }}</div>
                <div class="font-bold" style="margin-top:2px;">Guru Pembimbing Sekolah</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $teacher->nama_lengkap }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
