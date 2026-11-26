<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kunjungan Monitoring Lapangan</title>
    <style>
        @page { size: A4 landscape; margin: 22px 28px; }
    </style>
</head>
<body>
    @include('pdf.partials.kop-surat')

    <div class="doc-header">
        <h2>Rekapitulasi Kunjungan Monitoring Lapangan (Supervisi DUDI)</h2>
        <div class="doc-sub">
            {{ !empty($month) ? 'Periode: ' . \Carbon\Carbon::createFromFormat('Y-m', $month)->isoFormat('MMMM YYYY') : 'Seluruh Periode Tercatat' }}
        </div>
    </div>

    <table class="meta-box">
        <tr>
            <td class="meta-label">Total Kunjungan</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $kunjungans->count() }} kunjungan</td>
            <td class="meta-label">Tanggal Cetak</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="26" class="text-center">No</th>
                <th width="78">Tanggal</th>
                <th width="140">Guru Pembimbing</th>
                <th width="140">Tempat PKL (DUDI)</th>
                <th>Catatan Hasil Monitoring</th>
                <th width="130">Kendala Lapangan</th>
                <th width="70" class="text-center">Koordinat GPS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kunjungans as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $item->tanggal_kunjungan?->isoFormat('D MMM YYYY') }}</td>
                    <td>
                        <div class="font-bold">{{ $item->pembimbingSekolah?->nama_lengkap ?? '-' }}</div>
                        <div class="muted">{{ $item->pembimbingSekolah?->nip ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="font-bold">{{ $item->dudi?->nama ?? '-' }}</div>
                        <div class="muted">{{ $item->dudi?->kota ?? '' }}</div>
                    </td>
                    <td>{{ $item->catatan_monitoring }}</td>
                    <td>{{ $item->kendala ?: '-' }}</td>
                    <td class="text-center">
                        @if($item->latitude && $item->longitude)
                            <span class="badge badge-green">Tercatat</span>
                            <div class="muted" style="margin-top:2px;">{{ number_format($item->latitude, 4) }}, {{ number_format($item->longitude, 4) }}</div>
                        @else
                            <span class="badge badge-slate">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding:16px; color:#64748b;">Belum ada data kunjungan monitoring lapangan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sig">
        <tr>
            <td class="footer-note">Dicetak otomatis melalui Sistem MAS-PKL.</td>
            <td class="sig-box">
                <div>Ciamis, {{ now()->isoFormat('D MMMM YYYY') }}</div>
                <div class="font-bold" style="margin-top:2px;">Penanggung Jawab Monitoring</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ auth()->user()->name }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
