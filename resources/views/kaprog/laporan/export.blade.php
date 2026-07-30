<!DOCTYPE html>
<html>
<head>
    <title>Rekapitulasi Penempatan PKL - {{ $program->nama ?? 'Program Keahlian' }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 12px; margin-bottom: 20px; font-weight: normal; }
        
        .info-table { width: 100%; border: none; margin-bottom: 15px; }
        .info-table td { padding: 3px 0; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #444; padding: 6px 8px; text-align: left; }
        table.data th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        
        .footer { margin-top: 40px; }
        .signature { float: right; width: 250px; text-align: center; }
        .space { height: 65px; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 10px; border-radius: 4px; font-weight: bold; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <div class="title">Rekapitulasi Penempatan PKL Siswa</div>
    <div class="subtitle">Program Keahlian: {{ $program->nama ?? '-' }}</div>

    <table class="info-table">
        <tr>
            <td width="120">Program Keahlian</td>
            <td width="10">:</td>
            <td>{{ $program->nama ?? '-' }} ({{ $program->kode ?? '-' }})</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $kelas ?? 'Semua Kelas' }}</td>
        </tr>
        <tr>
            <td>Tanggal Unduh</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Nama Siswa & NIS</th>
                <th width="80">Kelas</th>
                <th>Tempat PKL (DUDI)</th>
                <th>Guru Pembimbing</th>
                <th width="70">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswas as $index => $siswa)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $siswa->nama_lengkap }}</strong><br>
                        <small style="color: #666;">NIS: {{ $siswa->nis }}</small>
                    </td>
                    <td>{{ $siswa->kelas ?? '-' }}</td>
                    <td>
                        @if($siswa->dudi)
                            <strong>{{ $siswa->dudi->nama }}</strong><br>
                            <small style="color: #666;">{{ $siswa->dudi->alamat }}</small>
                        @else
                            <span style="color: #999; font-style: italic;">Belum ada tempat</span>
                        @endif
                    </td>
                    <td>
                        @if($siswa->pembimbingSekolah)
                            <div style="margin-bottom: 2px;"><small><strong>KJ:</strong> {{ $siswa->pembimbingSekolah->nama_lengkap }}</small></div>
                        @endif
                        @if($siswa->pembimbingSekolahUmum)
                            <div><small><strong>UM:</strong> {{ $siswa->pembimbingSekolahUmum->nama_lengkap }}</small></div>
                        @endif
                        @if(!$siswa->pembimbingSekolah && !$siswa->pembimbingSekolahUmum)
                            <span style="color: #999; font-style: italic;">Belum diplot</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($siswa->dudi_id)
                            <span class="badge badge-success">Sudah</span>
                        @else
                            <span class="badge badge-warning">Belum</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px;">Belum ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Kepala Program Keahlian,</p>
            <div class="space"></div>
            <p><strong>({{ auth()->user()->name }})</strong></p>
        </div>
    </div>
</body>
</html>
