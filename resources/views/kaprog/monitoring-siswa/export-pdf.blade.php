<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Monitoring Siswa {{ $program?->kode ?? '' }}</title>
    <style>
        @page { size: A4 landscape; margin: 22px 28px; }
    </style>
</head>
<body>
    @include('pdf.partials.kop-surat')

    <div class="doc-header">
        <h2>Rekapitulasi Monitoring Langsung Siswa PKL</h2>
        <div class="doc-sub">
            Program Keahlian: {{ $program?->nama ?? '-' }} ({{ $program?->kode ?? '-' }}) &mdash;
            Per Tanggal {{ now()->isoFormat('D MMMM YYYY') }}
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
            <td class="meta-label">Jumlah Siswa</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $students->count() }} siswa</td>
            <td class="meta-label">Tanggal Cetak</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="26" class="text-center">No</th>
                <th>Nama Siswa &amp; NIS</th>
                <th width="65">Kelas</th>
                <th>Tempat PKL (DUDI) &amp; Pembimbing</th>
                <th width="75" class="text-center">Absen Hari Ini</th>
                <th width="70" class="text-center">Total Hadir</th>
                <th width="80" class="text-center">Jurnal (Valid/Total)</th>
                <th width="145">Status Early Warning</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $idx => $s)
                @php
                    $todayAbs = $todayAbsensiBySiswa->get($s->id);
                    $warn = $warningsBySiswa->get($s->id);
                @endphp
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
                    <td class="text-center">
                        @if($todayAbs)
                            @php
                                $st = $todayAbs->status;
                                $cls = $st === 'hadir' ? 'badge-green' : ($st === 'alpha' ? 'badge-red' : 'badge-blue');
                            @endphp
                            <span class="badge {{ $cls }}">{{ strtoupper($st) }}</span>
                        @else
                            <span class="badge badge-slate">Belum Absen</span>
                        @endif
                    </td>
                    <td class="text-center font-bold">{{ $s->total_hadir_count }} hari</td>
                    <td class="text-center font-bold">{{ $s->valid_jurnal_count }} / {{ $s->total_jurnal_count }}</td>
                    <td>
                        @if($warn)
                            @foreach($warn['reasons'] as $reason)
                                <div style="margin-bottom:2px;">
                                    <span class="badge {{ $warn['severity'] === 'critical' ? 'badge-red' : 'badge-amber' }}">{{ $reason }}</span>
                                </div>
                            @endforeach
                        @else
                            <span class="badge badge-green">Aman</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding:16px; color:#64748b;">Tidak ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sig">
        <tr>
            <td class="footer-note">Dicetak otomatis melalui Sistem MAS-PKL.</td>
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
