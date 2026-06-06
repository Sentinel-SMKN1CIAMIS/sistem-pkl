<!DOCTYPE html>
<html>
<head>
    <title>Portofolio PKL - {{ $siswa->nama_lengkap }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; padding: 0; text-transform: uppercase; font-size: 18px; }
        .header h3 { margin: 5px 0 0 0; font-size: 14px; font-weight: normal; }
        
        .info-table { width: 100%; border: none; margin-bottom: 20px; }
        .info-table td { padding: 4px 0; }
        
        .tp-section { margin-bottom: 30px; page-break-inside: avoid; }
        .tp-title { background-color: #f2f2f2; padding: 10px; border: 1px solid #000; font-weight: bold; margin-bottom: 10px; }
        
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #000; padding: 8px; text-align: left; }
        table.data th { background-color: #f9f9f9; text-align: center; }
        
        .footer { margin-top: 40px; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { text-align: center; width: 50%; }
        .space { height: 80px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PORTOFOLIO PRAKTIK KERJA LAPANGAN (PKL)</h2>
        <h3>BERDASARKAN TUJUAN PEMBELAJARAN (TP)</h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="150">Nama Peserta Didik</td>
            <td width="10">:</td>
            <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
            <td width="120">NIS</td>
            <td width="10">:</td>
            <td>{{ $siswa->nis }}</td>
        </tr>
        <tr>
            <td>Konsentrasi Keahlian</td>
            <td>:</td>
            <td>{{ $siswa->konsentrasiKeahlian->nama ?? '-' }}</td>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $siswa->kelas ?? '-' }}</td>
        </tr>
        <tr>
            <td>Dunia Kerja Tempat PKL</td>
            <td>:</td>
            <td colspan="4">{{ $siswa->dudi->nama ?? '-' }}</td>
        </tr>
    </table>

    @forelse($jurnalsByTp as $tpId => $jurnals)
        @php
            $tp = $jurnals->first()->tujuanPembelajaran;
        @endphp
        <div class="tp-section">
            <div class="tp-title">
                Tujuan Pembelajaran: {{ $tp->tp ?? $tp->nama }}
                @if($tp->cp) <br><span style="font-weight: normal; font-size: 10px;">CP: {{ $tp->cp }}</span> @endif
            </div>
            
            <table class="data">
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th width="120">Tanggal</th>
                        <th>Aktivitas / Kegiatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurnals as $index => $row)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $row->deskripsi_pekerjaan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div style="text-align: center; padding: 50px; border: 1px dashed #ccc;">
            Belum ada data jurnal dengan Tujuan Pembelajaran yang divalidasi.
        </div>
    @endforelse

    <div class="footer">
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    Pembimbing Sekolah
                    <div class="space"></div>
                    <strong><u>{{ $siswa->pembimbingSekolah->nama_lengkap ?? '................................' }}</u></strong><br>
                    NIP. {{ $siswa->pembimbingSekolah->nip ?? '-' }}
                </td>
                <td>
                    Disetujui Oleh,<br>
                    Instruktur / Mentor DUDI
                    <div class="space"></div>
                    <strong><u>{{ $siswa->pembimbingDudi->nama ?? '................................' }}</u></strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
