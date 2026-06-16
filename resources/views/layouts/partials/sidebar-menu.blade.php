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
            ['name' => 'Monitoring Jurnal', 'route' => 'pembimbing_sekolah.jurnal.index', 'icon' => 'activity'],
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
    } elseif ($role === 'pokja') {
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
            ['name' => 'Pengaturan', 'route' => 'pokja.pengaturan.sertifikat', 'icon' => 'settings'],
            ['name' => 'Pesan', 'route' => 'pesan.index', 'icon' => 'message-circle'],
        ];
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
    $navItems[] = ['name' => 'Panduan Interaktif', 'route' => 'panduan.interaktif', 'icon' => 'sparkles'];
@endphp

<nav class="space-y-2">
    @foreach ($navItems as $item)
        @if (isset($item['children']))
            @php
                // Check if any child is active
                $isActiveDropdown = false;
                foreach ($item['children'] as $child) {
                    if (request()->routeIs($child['route'])) {
                        $isActiveDropdown = true;
                        break;
                    }
                }
            @endphp
            <div x-data="{ open: {{ $isActiveDropdown ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" 
                        class="{{ $isActiveDropdown
                             ? 'text-blue-600 dark:text-blue-400 bg-blue-600/5 dark:bg-blue-500/5 font-semibold border-blue-500/10'
                             : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50 border-transparent'
                        }} w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group border">
                    <div class="flex items-center gap-3">
                        <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 {{ $isActiveDropdown ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-blue-500 transition-colors' }}"></i>
                        <span>{{ $item['name'] }}</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     class="pl-4 ml-4 border-l border-slate-200 dark:border-slate-800 space-y-1 pt-1">
                    @foreach ($item['children'] as $child)
                        @php
                            $isChildActive = request()->routeIs($child['route']);
                        @endphp
                        <a href="{{ route($child['route']) }}" 
                           class="{{ $isChildActive 
                                ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50 border border-transparent' 
                           }} flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg transition-all duration-200">
                            @if(isset($child['icon']))
                                <i data-lucide="{{ $child['icon'] }}" class="w-3.5 h-3.5 {{ $isChildActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}"></i>
                            @endif
                            <span>{{ $child['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            @php
                $isActive = request()->routeIs($item['route']);
            @endphp
            <a href="{{ route($item['route']) }}" 
               class="{{ $isActive 
                    ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-semibold' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50 border border-transparent' 
               }} flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group">
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-blue-500 transition-colors' }}"></i>
                <span>{{ $item['name'] }}</span>
            </a>
        @endif
    @endforeach
</nav>
