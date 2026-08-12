<style>
    .j-doc {
        font-family: 'Times New Roman', Times, serif;
        font-size: 11pt;
        line-height: 1.55;
        color: #0f172a;
    }
    .j-doc .title {
        font-size: 14.5pt;
        font-weight: 700;
        text-align: center;
        letter-spacing: 0.6pt;
        text-transform: uppercase;
        margin: 0 0 2pt 0;
    }
    .j-doc .subtitle {
        font-size: 12.5pt;
        font-weight: 700;
        text-align: center;
        letter-spacing: 0.4pt;
        text-transform: uppercase;
        margin: 0 0 3pt 0;
    }
    .j-doc .meta-line {
        text-align: center;
        font-size: 10.5pt;
        color: #334155;
        margin: 0 0 10pt 0;
    }
    .j-doc .rule {
        border-top: 1.5pt solid #1e293b;
        margin: 0 0 16pt 0;
    }
    .j-doc .section-title {
        font-size: 11.5pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5pt;
        color: #1e293b;
        margin: 18pt 0 4pt 0;
        padding-bottom: 4pt;
        border-bottom: 0.75pt solid #94a3b8;
    }
    .j-doc table.identity {
        width: 100%;
        border-collapse: collapse;
        font-size: 10.5pt;
    }
    .j-doc table.identity td {
        padding: 1.5pt 0;
        vertical-align: top;
    }
    .j-doc table.identity td.label {
        width: 160pt;
        font-weight: 700;
        color: #334155;
    }
    .j-doc table.identity td.colon {
        width: 12pt;
    }
    .j-doc table.journal {
        width: 100%;
        border-collapse: collapse;
        font-size: 10pt;
        margin-top: 6pt;
    }
    .j-doc table.journal th,
    .j-doc table.journal td {
        border: 0.75pt solid #94a3b8;
        padding: 5pt 6pt;
        vertical-align: top;
        text-align: left;
    }
    .j-doc table.journal th {
        background: #eef2f7;
        font-weight: 700;
        text-align: center;
    }
    .j-doc .weekly-header {
        display: flex;
        justify-content: space-between;
        font-size: 10.5pt;
        font-weight: 700;
        margin-top: 14pt;
        margin-bottom: 4pt;
        color: #1e293b;
        border-bottom: 1pt solid #cbd5e1;
        padding-bottom: 2pt;
    }
    .j-doc .signature {
        width: 250pt;
        text-align: center;
        margin-top: 20pt;
        margin-left: auto;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    .j-doc .signature .signer {
        font-weight: 700;
        text-decoration: underline;
        margin: 0;
    }
    .j-doc .signature .signer-role {
        color: #334155;
        margin: 2pt 0 0 0;
    }
    .j-doc .print-info {
        margin-top: 12pt;
        font-size: 8pt;
        color: #64748b;
        text-align: right;
        page-break-before: avoid;
    }
</style>

<div class="j-doc">
    <p class="title">Laporan Jurnal Kegiatan</p>
    <p class="subtitle">Praktik Kerja Lapangan (PKL)</p>
    <p class="meta-line">Tahun Ajaran {{ $siswa->tahun_ajaran ?? '-' }}</p>

    <div class="rule"></div>

    <table class="identity">
        <tr>
            <td class="label">Nama Peserta Didik</td>
            <td class="colon">:</td>
            <td><strong>{{ $siswa->nama_lengkap }}</strong> &nbsp;(NIS: {{ $siswa->nis }})</td>
        </tr>
        <tr>
            <td class="label">Kelas / Konsentrasi</td>
            <td class="colon">:</td>
            <td>{{ $siswa->kelas ?? '-' }} &mdash; {{ $siswa->konsentrasiKeahlian?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tempat PKL / Industri</td>
            <td class="colon">:</td>
            <td>{{ $siswa->dudi?->nama ?? '-' }}{{ $siswa->unit_pekerjaan ? ' (Bagian: '.$siswa->unit_pekerjaan.')' : '' }}</td>
        </tr>
        <tr>
            <td class="label">Pembimbing Sekolah</td>
            <td class="colon">:</td>
            <td>
                <strong>Guru Kejuruan:</strong> {{ $siswa->pembimbingSekolah?->nama_lengkap ?? '-' }} &nbsp;|&nbsp; 
                <strong>Guru Umum:</strong> {{ $siswa->pembimbingSekolahUmum?->nama_lengkap ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Pembimbing Instansi Dunia Kerja</td>
            <td class="colon">:</td>
            <td>{{ $siswa->pembimbingDudi?->nama_lengkap ?? $siswa->pembimbing_dudi_nama ?? '-' }}{{ ($siswa->pembimbingDudi?->jabatan ?? $siswa->pembimbing_dudi_jabatan) ? ' ('.$siswa->pembimbingDudi?->jabatan ?? $siswa->pembimbing_dudi_jabatan.')' : '' }}</td>
        </tr>
    </table>

    <p class="section-title">Daftar Kegiatan Jurnal</p>

    @php
        $startDate = $jurnals->first() ? \Carbon\Carbon::parse($jurnals->first()->tanggal)->startOfWeek(\Carbon\Carbon::MONDAY) : now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $groupedJurnals = $jurnals->groupBy(function($row) use ($startDate) {
            $rowDate = \Carbon\Carbon::parse($row->tanggal);
            $weekNum = (int) ($startDate->diffInWeeks($rowDate->copy()->startOfWeek(\Carbon\Carbon::MONDAY))) + 1;
            $monthName = $rowDate->locale('id')->translatedFormat('F Y');
            return "Minggu Ke: {$weekNum}|Bulan: {$monthName}";
        });
    @endphp

    @forelse($groupedJurnals as $groupHeader => $rows)
        @php
            [$weekText, $monthText] = explode('|', $groupHeader);
        @endphp

        <div class="weekly-header">
            <span>{{ $weekText }}</span>
            <span>{{ $monthText }}</span>
        </div>

        <table class="journal">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 14%;">Hari / Tanggal</th>
                    <th style="width: 40%;">Uraian Kegiatan / Pekerjaan</th>
                    <th style="width: 18%;">Kompetensi</th>
                    <th style="width: 23%;">Catatan Pembimbing</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $index => $row)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->locale('id')->isoFormat('dddd') }}<br>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $row->deskripsi_pekerjaan }}</td>
                        <td>{{ $row->kompetensi?->nama ?? '-' }}</td>
                        <td>{{ $row->catatan_pembimbing ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <table class="journal">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 14%;">Hari / Tanggal</th>
                    <th style="width: 40%;">Uraian Kegiatan / Pekerjaan</th>
                    <th style="width: 18%;">Kompetensi</th>
                    <th style="width: 23%;">Catatan Pembimbing</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada data jurnal yang divalidasi.</td>
                </tr>
            </tbody>
        </table>
    @endforelse

    <div class="signature">
        <p style="margin: 0 0 2pt 0;">Mengetahui,</p>
        <p style="margin: 0 0 2pt 0;">Pembimbing Instansi Dunia Kerja</p>
        <div style="height: 48pt;"></div>
        <p class="signer">({{ $siswa->pembimbingDudi?->nama_lengkap ?? $siswa->pembimbing_dudi_nama ?? '................................' }})</p>
        <p class="signer-role">{{ $siswa->pembimbingDudi?->jabatan ?? $siswa->pembimbing_dudi_jabatan ?? '' }}</p>
    </div>

    <p class="print-info">Dokumen ini dicetak dari Sistem Informasi PKL pada {{ now()->locale('id')->translatedFormat('d F Y') }}.</p>
</div>