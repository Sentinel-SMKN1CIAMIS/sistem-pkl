<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratinjau Laporan Jurnal PKL - {{ $siswa->nama_lengkap }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #e2e8f0;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }

        .toolbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #0f172a;
            color: #e2e8f0;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #1e293b;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
        }
        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .toolbar-left .icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .toolbar-left .icon svg { width: 17px; height: 17px; color: #818cf8; }
        .toolbar-title { font-weight: 700; font-size: 13.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .toolbar-sub { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }
        .toolbar-actions { display: flex; gap: 8px; flex-shrink: 0; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            line-height: 1;
        }
        .btn svg { width: 15px; height: 15px; }
        .btn-ghost {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
        }
        .btn-ghost:hover { background: #334155; color: #fff; }
        .btn-primary {
            background: #6366f1;
            color: #fff;
        }
        .btn-primary:hover { background: #4f46e5; transform: translateY(-1px); }
        .btn-success {
            background: #10b981;
            color: #fff;
        }
        .btn-success:hover { background: #059669; transform: translateY(-1px); }

        .page-wrap {
            padding: 28px 16px 48px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .page-info {
            width: 210mm;
            max-width: 100%;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 12px;
            color: #475569;
        }
        .page-info .chip {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            padding: 4px 12px;
            font-weight: 600;
            color: #334155;
            white-space: nowrap;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            padding: 17mm 17mm 15mm;
            box-shadow: 0 15px 40px rgba(15, 23, 42, 0.18);
            border-radius: 2px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 15mm 12mm 15mm;
            }
            body { background: #fff; }
            .toolbar, .page-info { display: none !important; }
            .page-wrap { padding: 0; }
            .page {
                width: 100%;
                min-height: auto;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <div class="toolbar-left">
            <div class="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div class="toolbar-title">Pratinjau Cetak: Laporan Jurnal Kegiatan PKL</div>
                <div class="toolbar-sub">Jurnal-PKL-{{ $siswa->nis }}.pdf &middot; {{ $jurnals->count() }} entri jurnal divalidasi</div>
            </div>
        </div>
        <div class="toolbar-actions">
            <a href="{{ route('siswa.jurnal.index') }}" onclick="handleKembali(event)" class="btn btn-ghost">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <a href="{{ route('siswa.jurnal.export') }}" class="btn btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh PDF
            </a>
            <button onclick="window.print()" class="btn btn-success">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak
            </button>
        </div>
    </div>

    <div class="page-wrap">
        <div class="page-info">
            <span>Dokumen akan dicetak pada kertas <strong>A4 (Portrait)</strong> berukuran standar.</span>
            <span class="chip">{{ $jurnals->count() }} catatan kegiatan</span>
        </div>
        <div class="page">
            @include('siswa.jurnal.document', ['siswa' => $siswa, 'jurnals' => $jurnals])
        </div>
    </div>

    <script>
        function handleKembali(e) {
            e.preventDefault();
            // Close the tab if opened in a new tab/window
            window.close();
            // Fallback if window.close() was blocked by browser (e.g. opened directly without target=_blank)
            setTimeout(function() {
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href = "{{ route('siswa.jurnal.index') }}";
                }
            }, 120);
        }
    </script>
</body>
</html>