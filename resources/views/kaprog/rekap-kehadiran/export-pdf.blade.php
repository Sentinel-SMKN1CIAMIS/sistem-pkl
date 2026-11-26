<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran Siswa {{ $program?->kode ?? 'Jurusan' }}</title>
    <style>
        @page { size: A4 landscape; margin: 22px 28px; }
    </style>
</head>
<body>
    @include('pdf.partials.kop-surat')

    <div class="doc-header">
        <h2>Rekapitulasi Kehadiran &amp; Early Warning Siswa PKL</h2>
        <div class="doc-sub">
            Program Keahlian: {{ $program?->nama ?? '-' }} ({{ $program?->kode ?? '-' }}) &mdash;
            Periode {{ $periodStart->isoFormat('MMMM YYYY') }}
        </div>
    </div>

    <table class="meta-box">
        <tr>
            <td class="meta-label">Program Keahlian</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $program?->nama ?? '-' }}</td>
            <td class="meta-label">Filter Kelas</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $selectedKelas === 'semua' ? 'Semua Kelas' : $selectedKelas }}</td>
        </tr>
        <tr>
            <td class="meta-label">Periode Bulan</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $periodStart->isoFormat('MMMM YYYY') }}</td>
            <td class="meta-label">Tanggal Cetak</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</td>
        </tr>
    </table>

    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <div class="kpi-label">Total Siswa</div>
                <div class="kpi-value">{{ $summaryStats['total_siswa'] }}</div>
            </td>
            <td class="kpi-card red">
                <div class="kpi-label">Siswa Berisiko</div>
                <div class="kpi-value" style="color: #dc2626;">{{ $summaryStats['total_berisiko'] }}</div>
            </td>
            <td class="kpi-card green">
                <div class="kpi-label">Total Hadir (Periode)</div>
                <div class="kpi-value" style="color: #059669;">{{ $summaryStats['total_hadir'] }}</div>
            </td>
            <td class="kpi-card amber">
                <div class="kpi-label">Total Alpha (Periode)</div>
                <div class="kpi-value" style="color: #d97706;">{{ $summaryStats['total_alpha'] }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="26" class="text-center">No</th>
                <th>Nama Siswa &amp; NIS</th>
                <th width="65">Kelas</th>
                <th>Tempat PKL (DUDI) &amp; Pembimbing</th>
                <th width="36" class="text-center">Hadir</th>
                <th width="32" class="text-center">Izin</th>
                <th width="34" class="text-center">Sakit</th>
                <th width="34" class="text-center">Libur</th>
                <th width="36" class="text-center">Alpha</th>
                <th width="44" class="text-center">% Hadir</th>
                <th width="145">Status Early Warning</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $idx => $row)
                @php $s = $row['siswa']; $w = $row['warning']; @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <div class="font-bold">{{ $s->nama_lengkap }}</div>
                        <div class="muted">NIS: {{ $s->nis }}</div>
                    </td>
                    <td>
                        <div class="font-bold">{{ $s->kelas }}</div>
                        <div class="muted">{{ $s->konsentrasiKeahlian?->kode ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="font-bold">{{ $s->dudi?->nama ?? 'Belum Ditempatkan' }}</div>
                        <div class="muted">Pembimbing: {{ $s->pembimbingSekolah?->nama_lengkap ?? '-' }}</div>
                    </td>
                    <td class="text-center font-bold" style="color:#059669;">{{ $row['hadir'] }}</td>
                    <td class="text-center" style="color:#1e40af;">{{ $row['izin'] }}</td>
                    <td class="text-center" style="color:#b45309;">{{ $row['sakit'] }}</td>
                    <td class="text-center" style="color:#0369a1;">{{ $row['libur'] }}</td>
                    <td class="text-center font-bold" style="color:#dc2626;">{{ $row['alpha'] }}</td>
                    <td class="text-center font-bold" style="color:{{ $row['rate'] < 75 ? '#dc2626' : '#059669' }};">{{ $row['rate'] }}%</td>
                    <td>
                        @if($w)
                            @foreach($w['reasons'] as $reason)
                                <div style="margin-bottom:2px;">
                                    <span class="badge {{ $w['severity'] === 'critical' ? 'badge-red' : 'badge-amber' }}">{{ $reason }}</span>
                                </div>
                            @endforeach
                        @else
                            <span class="badge badge-green">Aman</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 16px; color: #64748b;">Tidak ada data siswa yang cocok dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sig">
        <tr>
            <td class="footer-note">
                Dicetak otomatis melalui Sistem MAS-PKL &bull; {{ $rows->count() }} data siswa ditampilkan.
            </td>
            <td class="sig-box">
                <div>Ciamis, {{ now()->isoFormat('D MMMM YYYY') }}</div>
                <div class="font-bold" style="margin-top:2px;">Kepala Program Keahlian</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ auth()->user()->name }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
