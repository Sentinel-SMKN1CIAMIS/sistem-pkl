<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi Siswa PKL</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        .info td { padding: 2px 0; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th { background: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: left; text-transform: uppercase; font-size: 10px; }
        table.data td { border: 1px solid #ccc; padding: 8px; vertical-align: top; }
        .footer { margin-top: 30px; text-align: right; }
        .footer p { margin-bottom: 60px; }
        .status-badge { padding: 2px 5px; border-radius: 3px; font-weight: bold; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rekapitulasi Kehadiran Siswa PKL</h2>
        <p>SMK NEGERI 1 CIAMIS</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="150">Pembimbing Sekolah</td>
                <td>: {{ $teacher->nama_lengkap }}</td>
            </tr>
            @if($start_date || $end_date)
            <tr>
                <td>Periode</td>
                <td>: {{ $start_date ?? '...' }} s/d {{ $end_date ?? '...' }}</td>
            </tr>
            @endif
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->format('d F Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Tanggal</th>
                <th>Nama Siswa</th>
                <th>Industri (DUDI)</th>
                <th width="60">Masuk</th>
                <th width="60">Pulang</th>
                <th width="80">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensis as $index => $item)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $item->siswa->nama_lengkap }}</strong><br>
                        <small>{{ $item->siswa->nis }}</small>
                    </td>
                    <td>{{ $item->siswa->dudi->nama ?? '-' }}</td>
                    <td align="center">{{ $item->waktu_datang ? \Carbon\Carbon::parse($item->waktu_datang)->format('H:i') : '-' }}</td>
                    <td align="center">{{ $item->waktu_pulang ? \Carbon\Carbon::parse($item->waktu_pulang)->format('H:i') : '-' }}</td>
                    <td>
                        @php
                            $statusLabels = [
                                'hadir' => 'Hadir',
                                'izin' => 'Izin',
                                'sakit' => 'Sakit',
                                'alpha' => 'Alpa',
                            ];
                        @endphp
                        {{ $statusLabels[$item->status] ?? ucfirst($item->status) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Ciamis, {{ now()->format('d F Y') }}</p>
        <strong>{{ $teacher->nama_lengkap }}</strong><br>
        <span>NIP. {{ $teacher->nip ?? '.........................' }}</span>
    </div>
</body>
</html>
