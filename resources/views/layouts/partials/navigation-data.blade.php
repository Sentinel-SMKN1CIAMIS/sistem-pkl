@php
    $role = auth()->user()?->role;
    $navItems = [];

    if ($role === 'siswa') {
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            ['name' => 'Jurnal Kegiatan', 'route' => 'siswa.jurnal.index', 'icon' => 'book-open'],
            ['name' => 'Daftar Hadir', 'route' => 'siswa.absensi.index', 'icon' => 'calendar-check'],
            ['name' => 'Laporan PKL', 'route' => 'siswa.laporan.index', 'icon' => 'file-text'],
            ['name' => 'Buku Panduan', 'route' => 'siswa.panduan.index', 'icon' => 'library'],
            ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
            ['name' => 'Pusat Bantuan', 'route' => 'siswa.bantuan.index', 'icon' => 'help-circle'],
        ];
    } elseif ($role === 'pembimbing_sekolah') {
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            ['name' => 'Siswa Bimbingan', 'route' => 'pembimbing_sekolah.siswa.index', 'icon' => 'users'],
            ['name' => 'Validasi Jurnal', 'route' => 'pembimbing_sekolah.jurnal.index', 'icon' => 'activity'],
            ['name' => 'Kehadiran Siswa', 'route' => 'pembimbing_sekolah.absensi.index', 'icon' => 'calendar'],
            ['name' => 'Persetujuan Absensi', 'route' => 'pembimbing_sekolah.absensi.approval.index', 'icon' => 'check-circle'],
            ['name' => 'Evaluasi Laporan', 'route' => 'pembimbing_sekolah.laporan.index', 'icon' => 'file-check'],
            ['name' => 'Peta DUDI', 'route' => 'shared.pemetaan.maps', 'icon' => 'map'],
            ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
        ];
    } elseif ($role === 'pembimbing_dudi') {
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            ['name' => 'Siswa PKL', 'route' => 'pembimbing_dudi.siswa.index', 'icon' => 'users'],
            ['name' => 'Jurnal Siswa', 'route' => 'pembimbing_dudi.jurnal.index', 'icon' => 'file-check-2'],
            ['name' => 'Validasi Kehadiran', 'route' => 'pembimbing_dudi.absensi.index', 'icon' => 'clipboard-check'],
            ['name' => 'Feedback Sekolah', 'route' => 'pembimbing_dudi.feedback.index', 'icon' => 'message-square-plus'],
            ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
        ];
    } elseif ($role === 'pokja' || $role === 'kepala_sekolah') {
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            [
                'name' => 'Kelola Akun',
                'icon' => 'users',
                'children' => [
                    ['name' => 'Data Siswa', 'route' => 'pokja.siswa.index', 'icon' => 'graduation-cap'],
                    ['name' => 'Pembimbing Sekolah', 'route' => 'pokja.pembimbing_sekolah.index', 'icon' => 'user-check'],
                    ['name' => 'Pembimbing DUDI', 'route' => 'pokja.pembimbing_dudi.index', 'icon' => 'user-cog'],
                    ['name' => 'Akun Kaprog', 'route' => 'pokja.kaprog.index', 'icon' => 'award'],
                    ['name' => 'Data DUDI', 'route' => 'pokja.dudi.index', 'icon' => 'building-2'],
                ]
            ],
            [
                'name' => 'Akademik & Jurusan',
                'icon' => 'book',
                'children' => [
                    ['name' => 'Program Keahlian', 'route' => 'admin.program_keahlian.index', 'icon' => 'book-open'],
                    ['name' => 'Konsentrasi Keahlian', 'route' => 'admin.konsentrasi_keahlian.index', 'icon' => 'layers'],
                    ['name' => 'Kelola TP/CP', 'route' => 'pokja.kompetensi.index', 'icon' => 'target'],
                ]
            ],
            [
                'name' => 'Pemetaan & Penempatan',
                'icon' => 'network',
                'children' => [
                    ['name' => 'Validasi Pengajuan', 'route' => 'pokja.pengajuan_pkl.index', 'icon' => 'file-check'],
                    ['name' => 'Pemetaan', 'route' => 'pokja.pemetaan.index', 'icon' => 'git-branch'],
                    ['name' => 'Peta DUDI', 'route' => 'pokja.pemetaan.maps', 'icon' => 'map'],
                    ['name' => 'Kelola Zona', 'route' => 'pokja.zona.index', 'icon' => 'compass'],
                ]
            ],
            [
                'name' => 'Monitoring & Evaluasi',
                'icon' => 'eye',
                'children' => [
                    ['name' => 'Monitoring Pembimbing', 'route' => 'pokja.monitoring.index', 'icon' => 'eye'],
                    ['name' => 'Evaluasi PKL', 'route' => 'pokja.evaluasi.index', 'icon' => 'bar-chart-3'],
                    ['name' => 'Feedback DUDI', 'route' => 'pokja.feedback.index', 'icon' => 'message-square'],
                ]
            ],
        ];

        if ($role !== 'kepala_sekolah') {
            $navItems[] = [
                'name' => 'Pengaturan',
                'icon' => 'settings',
                'children' => [
                    ['name' => 'Template Sertifikat', 'route' => 'pokja.pengaturan.sertifikat', 'icon' => 'award'],
                    ['name' => 'Template Surat PKL', 'route' => 'pokja.pengaturan.surat_pengantar', 'icon' => 'file-text'],
                ]
            ];
        }

        $navItems[] = ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'];
    } elseif ($role === 'kaprog') {
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            ['name' => 'Pengajuan PKL', 'route' => 'kaprog.pengajuan_pkl.index', 'icon' => 'file-plus-2'],
            ['name' => 'Peta DUDI', 'route' => 'shared.pemetaan.maps', 'icon' => 'map'],
            ['name' => 'Laporan Kaprog', 'route' => 'kaprog.laporan.index', 'icon' => 'file-bar-chart-2'],
            ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
        ];
    } elseif ($role === 'super_admin') {
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            [
                'name' => 'Kelola Akun',
                'icon' => 'users',
                'children' => [
                    ['name' => 'Data Siswa', 'route' => 'pokja.siswa.index', 'icon' => 'graduation-cap'],
                    ['name' => 'Pembimbing Sekolah', 'route' => 'pokja.pembimbing_sekolah.index', 'icon' => 'user-check'],
                    ['name' => 'Pembimbing DUDI', 'route' => 'pokja.pembimbing_dudi.index', 'icon' => 'user-cog'],
                    ['name' => 'Akun Kaprog', 'route' => 'pokja.kaprog.index', 'icon' => 'award'],
                    ['name' => 'Data DUDI', 'route' => 'pokja.dudi.index', 'icon' => 'building-2'],
                    ['name' => 'Akun Sistem', 'route' => 'admin.users.index', 'icon' => 'user'],
                ]
            ],
            [
                'name' => 'Akademik & Jurusan',
                'icon' => 'book',
                'children' => [
                    ['name' => 'Program Keahlian', 'route' => 'admin.program_keahlian.index', 'icon' => 'book-open'],
                    ['name' => 'Konsentrasi Keahlian', 'route' => 'admin.konsentrasi_keahlian.index', 'icon' => 'layers'],
                    ['name' => 'Kompetensi', 'route' => 'admin.kompetensi.index', 'icon' => 'target'],
                ]
            ],
            ['name' => 'Konfigurasi Sistem', 'route' => 'admin.config.index', 'icon' => 'settings'],
            ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
            ['name' => 'Log Sistem', 'route' => 'admin.logs.index', 'icon' => 'scroll-text'],
        ];
    }
    
    if ($role !== 'pembimbing_sekolah' && auth()->user()?->pembimbingSekolah) {
        $navItems[] = [
            'name' => 'Menu Pembimbing',
            'icon' => 'user-check',
            'children' => [
                ['name' => 'Siswa Bimbingan', 'route' => 'pembimbing_sekolah.siswa.index', 'icon' => 'users'],
                ['name' => 'Validasi Jurnal', 'route' => 'pembimbing_sekolah.jurnal.index', 'icon' => 'activity'],
                ['name' => 'Kehadiran Siswa', 'route' => 'pembimbing_sekolah.absensi.index', 'icon' => 'calendar'],
                ['name' => 'Persetujuan Absensi', 'route' => 'pembimbing_sekolah.absensi.approval.index', 'icon' => 'check-circle'],
                ['name' => 'Evaluasi Laporan', 'route' => 'pembimbing_sekolah.laporan.index', 'icon' => 'file-check'],
            ]
        ];
    }

    $navItems[] = ['name' => 'Panduan Interaktif', 'route' => 'panduan.interaktif', 'icon' => 'sparkles'];
@endphp
