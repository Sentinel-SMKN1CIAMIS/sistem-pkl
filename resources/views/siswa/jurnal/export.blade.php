<!DOCTYPE html>
<html>
<head>
    <title>Jurnal PKL - {{ $siswa->nama_lengkap }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: left; margin-bottom: 20px; }
        .header table { width: 100%; border: none; }
        .header td { padding: 2px 0; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid black; padding: 8px; text-align: left; }
        table.data th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        
        .footer { margin-top: 30px; }
        .signature { float: right; width: 250px; text-align: center; }
        .space { height: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td width="150">Nama Peserta Didik</td>
                <td width="10">:</td>
                <td>{{ $siswa->nama_lengkap }}</td>
            </tr>
            <tr>
                <td>Dunia Kerja Tempat PKL</td>
                <td>:</td>
                <td>{{ $siswa->dudi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Instruktur / Mentor</td>
                <td>:</td>
                <td>{{ $siswa->pembimbingDudi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Guru Pembimbing</td>
                <td>:</td>
                <td>{{ $siswa->pembimbingSekolah->nama_lengkap ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="120">Hari / Tanggal</th>
                <th>Unit Kerja / Pekerjaan</th>
                <th width="120">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jurnals as $index => $row)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->isoFormat('dddd') }}<br>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $row->kegiatan }}</td>
                    <td>{{ $row->catatan_pembimbing ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Belum ada data jurnal divalidasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Pembimbing Instansi Dunia Kerja,</p>
            <div class="space"></div>
            <p><strong>({{ $siswa->pembimbingDudi->nama ?? '................................' }})</strong></p>
        </div>
    </div>
</body>
</html>
