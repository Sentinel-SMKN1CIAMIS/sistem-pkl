<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran Siswa PKL - {{ $mentor->dudi?->nama ?? 'DUDI' }}</title>
    <style>
        @page { size: A4 landscape; margin: 22px 28px; }
    </style>
</head>
<body>
    @include('pdf.partials.kop-surat')

    <div class="doc-header">
        <h2>Rekapitulasi Kehadiran Siswa PKL di DUDI</h2>
        <div class="doc-sub">{{ $mentor->dudi?->nama ?? '-' }} &mdash; Pembimbing DUDI: {{ $mentor->nama_lengkap }}</div>
    </div>

    <table class="meta-box">
        <tr>
            <td class="meta-label">Nama DUDI</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $mentor->dudi?->nama ?? '-' }}</td>
            <td class="meta-label">Total Catatan</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $absensis->count() }} entri</td>
        </tr>
        <tr>
            <td class="meta-label">Pembimbing DUDI</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $mentor->nama_lengkap }}</td>
            <td class="meta-label">Tanggal Cetak</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="26" class="text-center">No</th>
                <th width="85">Tanggal</th>
                <th>Nama Siswa &amp; NIS</th>
                <th width="65" class="text-center">Status</th>
                <th width="70" class="text-center">Jam Datang</th>
                <th width="70" class="text-center">Jam Pulang</th>
                <th>Keterangan / Alasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensis as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('D MMM YYYY') }}</td>
                    <td>
                        <div class="font-bold">{{ $row->siswa?->nama_lengkap ?? '-' }}</div>
                        <div class="muted">NIS: {{ $row->siswa?->nis ?? '-' }}</div>
                    </td>
                    <td class="text-center">
                        @php
                            $st = $row->status;
                            $cls = $st === 'hadir' ? 'badge-green' : ($st === 'alpha' ? 'badge-red' : 'badge-blue');
                        @endphp
                        <span class="badge {{ $cls }}">{{ strtoupper($st) }}</span>
                    </td>
                    <td class="text-center">{{ $row->waktu_datang ? \Carbon\Carbon::parse($row->waktu_datang)->format('H:i') : '-' }}</td>
                    <td class="text-center">{{ $row->waktu_pulang ? \Carbon\Carbon::parse($row->waktu_pulang)->format('H:i') : '-' }}</td>
                    <td>{{ $row->alasan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding:16px; color:#64748b;">Belum ada data kehadiran siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sig">
        <tr>
            <td class="footer-note">Dicetak otomatis melalui Sistem MAS-PKL.</td>
            <td class="sig-box">
                <div>{{ now()->isoFormat('D MMMM YYYY') }}</div>
                <div class="font-bold" style="margin-top:2px;">Pembimbing DUDI</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $mentor->nama_lengkap }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
