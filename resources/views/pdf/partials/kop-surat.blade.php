<style>
    body {
        font-family: 'Helvetica', Arial, sans-serif;
        font-size: 9px;
        color: #1e293b;
        margin: 0;
        padding: 0;
        line-height: 1.4;
    }

    .kop-table {
        width: 100%;
        border-collapse: collapse;
        border-bottom: 3px double #1e3a8a;
        margin-bottom: 10px;
    }
    .kop-table td {
        border: none !important;
        padding: 0 0 6px 0;
        vertical-align: middle;
    }
    .kop-logo-left { width: 72px; text-align: left; }
    .kop-logo-right { width: 72px; text-align: right; }
    .kop-logo-left img, .kop-logo-right img { height: 58px; width: auto; }
    .kop-text { text-align: center; padding: 0 8px; }
    .school-gov { font-size: 9.5px; font-weight: bold; color: #1e293b; text-transform: uppercase; }
    .school-dept { font-size: 10.5px; font-weight: bold; color: #1e293b; text-transform: uppercase; }
    .school-subdept { font-size: 9px; font-weight: bold; color: #1e293b; text-transform: uppercase; }
    .school-name { font-size: 14px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; margin: 2px 0; letter-spacing: 0.4px; }
    .school-addr { font-size: 7.8px; color: #475569; margin: 1px 0; }

    .doc-header { text-align: center; margin: 10px 0 8px 0; }
    .doc-header h2 { font-size: 12.5px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin: 0; letter-spacing: 0.4px; }
    .doc-header .doc-sub { font-size: 9px; color: #475569; margin-top: 2px; }

    .meta-box {
        width: 100%;
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-left: 4px solid #1e3a8a;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    .meta-box td { border: none !important; padding: 3px 8px; font-size: 8.5px; vertical-align: top; }
    .meta-label { font-weight: bold; color: #475569; width: 120px; }
    .meta-sep { width: 8px; text-align: center; color: #64748b; }
    .meta-val { color: #0f172a; }

    .kpi-table { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 0 -6px 10px -6px; }
    .kpi-card {
        border: 1px solid #cbd5e1;
        border-top: 3px solid #1e3a8a;
        background-color: #ffffff;
        padding: 6px 8px;
        text-align: center;
    }
    .kpi-card.green { border-top-color: #059669; }
    .kpi-card.red { border-top-color: #dc2626; }
    .kpi-card.amber { border-top-color: #d97706; }
    .kpi-label { font-size: 7.5px; font-weight: bold; text-transform: uppercase; color: #64748b; letter-spacing: 0.3px; }
    .kpi-value { font-size: 14px; font-weight: bold; color: #0f172a; margin-top: 2px; }

    table.data-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
    table.data-table thead { display: table-header-group; }
    table.data-table tr { page-break-inside: avoid; }
    table.data-table th {
        background-color: #1e3a8a;
        color: #ffffff;
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
        padding: 6px 5px;
        border: 1px solid #1e3a8a;
        text-align: left;
    }
    table.data-table td {
        padding: 5px 5px;
        border: 1px solid #cbd5e1;
        font-size: 8.2px;
        vertical-align: top;
        color: #1e293b;
    }
    table.data-table tbody tr:nth-child(even) { background-color: #f8fafc; }

    .text-center { text-align: center !important; }
    .text-right { text-align: right !important; }
    .font-bold { font-weight: bold; }
    .muted { color: #64748b; font-size: 7.5px; }

    .badge { display: inline-block; padding: 2px 5px; border-radius: 3px; font-size: 7.5px; font-weight: bold; }
    .badge-green { background-color: #d1fae5; color: #065f46; }
    .badge-red { background-color: #fee2e2; color: #991b1b; }
    .badge-amber { background-color: #fef3c7; color: #92400e; }
    .badge-blue { background-color: #dbeafe; color: #1e40af; }
    .badge-slate { background-color: #e2e8f0; color: #334155; }

    .footer-sig { width: 100%; margin-top: 18px; page-break-inside: avoid; border-collapse: collapse; }
    .footer-sig td { border: none !important; vertical-align: bottom; padding: 0; }
    .footer-note { font-size: 7.5px; color: #64748b; font-style: italic; }
    .sig-box { width: 220px; text-align: center; font-size: 8.5px; color: #1e293b; }
    .sig-space { height: 48px; }
    .sig-name { font-weight: bold; text-decoration: underline; color: #0f172a; }
</style>

<table class="kop-table">
    <tr>
        <td class="kop-logo-left">
            @if(!empty($logo_left_path))
                <img src="{{ $logo_left_path }}" alt="Logo Provinsi Jawa Barat">
            @endif
        </td>
        <td class="kop-text">
            <div class="school-gov">{{ $report_kop_baris_1 ?? 'PEMERINTAH DAERAH PROVINSI JAWA BARAT' }}</div>
            <div class="school-dept">{{ $report_kop_baris_2 ?? 'DINAS PENDIDIKAN' }}</div>
            <div class="school-subdept">{{ $report_kop_baris_3 ?? 'CABANG DINAS PENDIDIKAN WILAYAH XIII' }}</div>
            <div class="school-name">{{ $report_kop_baris_4 ?? 'SMK NEGERI 1 CIAMIS' }}</div>
            <div class="school-addr">{{ $report_kop_baris_5 ?? '' }}</div>
            <div class="school-addr">{{ $report_kop_baris_6 ?? '' }}</div>
            <div class="school-addr">{{ $report_kop_baris_7 ?? '' }}</div>
        </td>
        <td class="kop-logo-right">
            @if(!empty($logo_right_path))
                <img src="{{ $logo_right_path }}" alt="Logo SMKN 1 Ciamis">
            @endif
        </td>
    </tr>
</table>
