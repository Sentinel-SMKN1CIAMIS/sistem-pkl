<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Monitoring Keaktifan Pembimbing Sekolah</title>
    <style>
        @page { size: A4 landscape; margin: 22px 28px; }
    </style>
</head>
<body>
    @include('pdf.partials.kop-surat')

    <div class="doc-header">
        <h2>Rekapitulasi Monitoring Keaktifan Pembimbing Sekolah</h2>
        <div class="doc-sub">{{ $scopeTitle }} &mdash; Per Tanggal {{ now()->isoFormat('D MMMM YYYY') }}</div>
    </div>

    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <div class="kpi-label">Total Pembimbing</div>
                <div class="kpi-value">{{ $stats['total'] }}</div>
            </td>
            <td class="kpi-card green">
                <div class="kpi-label">Aktif</div>
                <div class="kpi-value" style="color:#059669;">{{ $stats['aktif'] }}</div>
            </td>
            <td class="kpi-card amber">
                <div class="kpi-label">Kurang Aktif</div>
                <div class="kpi-value" style="color:#d97706;">{{ $stats['kurang_aktif'] }}</div>
            </td>
            <td class="kpi-card red">
                <div class="kpi-label">Belum Pernah Login</div>
                <div class="kpi-value" style="color:#dc2626;">{{ $stats['tidak_pernah_login'] }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="26" class="text-center">No</th>
                <th>Nama Pembimbing &amp; NIP</th>
                <th width="70">Tipe</th>
                <th width="90">Konsentrasi</th>
                <th width="52" class="text-center">Siswa</th>
                <th width="75" class="text-center">Jurnal Pending / Total</th>
                <th width="75" class="text-center">Absen Pending / Total</th>
                <th width="95">Login Terakhir</th>
                <th width="90" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mentors as $idx => $mentor)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <div class="font-bold">{{ $mentor->nama_lengkap }}</div>
                        <div class="muted">NIP: {{ $mentor->nip ?? '-' }}</div>
                    </td>
                    <td>{{ ucfirst($mentor->tipe ?? '-') }}</td>
                    <td>{{ $mentor->konsentrasiKeahlian?->kode ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $mentor->siswa_count }}</td>
                    <td class="text-center">{{ $mentor->pending_jurnals_count }} / {{ $mentor->total_jurnals_count }}</td>
                    <td class="text-center">{{ $mentor->pending_absensis_count }} / {{ $mentor->total_absensis_count }}</td>
                    <td>{{ $mentor->user?->last_login_at ? $mentor->user->last_login_at->isoFormat('D MMM YYYY HH:mm') : 'Belum pernah' }}</td>
                    <td class="text-center">
                        @if($mentor->activity_status === 'aktif')
                            <span class="badge badge-green">Aktif</span>
                        @elseif($mentor->activity_status === 'kurang_aktif')
                            <span class="badge badge-amber">Kurang Aktif</span>
                        @else
                            <span class="badge badge-red">Belum Login</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding:16px; color:#64748b;">Tidak ada data pembimbing sekolah.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sig">
        <tr>
            <td class="footer-note">Dicetak otomatis melalui Sistem MAS-PKL pada {{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB.</td>
            <td class="sig-box">
                <div>Ciamis, {{ now()->isoFormat('D MMMM YYYY') }}</div>
                <div class="font-bold" style="margin-top:2px;">{{ $signerTitle }}</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $signerName }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
