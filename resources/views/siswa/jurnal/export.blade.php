<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jurnal PKL - {{ $siswa->nama_lengkap }}</title>
    <style>
        @page { margin: 16mm 18mm; }
        body { margin: 0; padding: 0; }
    </style>
</head>
<body>
    @include('siswa.jurnal.document', ['siswa' => $siswa, 'jurnals' => $jurnals])
</body>
</html>