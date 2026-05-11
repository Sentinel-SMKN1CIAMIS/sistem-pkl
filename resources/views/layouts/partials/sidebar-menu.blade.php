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
            ['name' => 'Profil Saya', 'route' => 'siswa.profile.index', 'icon' => 'user-circle'],
        ];
    } elseif ($role === 'pembimbing_sekolah') {
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            ['name' => 'Siswa Bimbingan', 'route' => 'pembimbing_sekolah.siswa.index', 'icon' => 'users'],
            ['name' => 'Monitoring Jurnal', 'route' => 'pembimbing_sekolah.jurnal.index', 'icon' => 'activity'],
            ['name' => 'Kehadiran Siswa', 'route' => 'pembimbing_sekolah.absensi.index', 'icon' => 'calendar'],
            ['name' => 'Evaluasi Laporan', 'route' => 'pembimbing_sekolah.laporan.index', 'icon' => 'file-check'],
        ];
    } elseif ($role === 'pembimbing_dudi') {
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            ['name' => 'Siswa PKL', 'route' => 'pembimbing_dudi.siswa.index', 'icon' => 'users'],
            ['name' => 'Jurnal Siswa', 'route' => 'pembimbing_dudi.jurnal.index', 'icon' => 'file-check-2'],
            ['name' => 'Validasi Kehadiran', 'route' => 'pembimbing_dudi.absensi.index', 'icon' => 'clipboard-check'],
        ];
    } elseif ($role === 'pokja') {
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            ['name' => 'Data Siswa', 'route' => 'pokja.siswa.index', 'icon' => 'graduation-cap'],
            ['name' => 'Data DUDI', 'route' => 'pokja.dudi.index', 'icon' => 'building-2'],
            ['name' => 'Pembimbing Sekolah', 'route' => 'pokja.pembimbing_sekolah.index', 'icon' => 'user-check'],
            ['name' => 'Pembimbing DUDI', 'route' => 'pokja.pembimbing_dudi.index', 'icon' => 'user-cog'],
            ['name' => 'Pemetaan', 'route' => 'pokja.pemetaan.index', 'icon' => 'network'],
            ['name' => 'Monitoring Pembimbing', 'route' => 'pokja.monitoring.index', 'icon' => 'eye'],
            ['name' => 'Evaluasi PKL', 'route' => 'pokja.evaluasi.index', 'icon' => 'bar-chart-3'],
        ];
    } elseif ($role === 'super_admin') {
        $navItems = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
            ['name' => 'Kelola Pengguna', 'route' => 'admin.users.index', 'icon' => 'users'],
            ['name' => 'Konfigurasi Sistem', 'route' => 'admin.config.index', 'icon' => 'settings'],
            ['name' => 'Program Keahlian', 'route' => 'admin.program_keahlian.index', 'icon' => 'book'],
            ['name' => 'Konsentrasi Keahlian', 'route' => 'admin.konsentrasi_keahlian.index', 'icon' => 'layers'],
            ['name' => 'Kompetensi', 'route' => 'admin.kompetensi.index', 'icon' => 'target'],
            ['name' => 'Log Sistem', 'route' => 'admin.logs.index', 'icon' => 'scroll-text'],
        ];
    }
@endphp

<nav class="space-y-1">
    @foreach ($navItems as $item)
        @php
            $isActive = request()->routeIs($item['route']);
        @endphp
        <a href="{{ route($item['route']) }}" 
           class="{{ $isActive 
                ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' 
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50 border border-transparent' 
           }} flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group">
            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-blue-500 transition-colors' }}"></i>
            {{ $item['name'] }}
        </a>
    @endforeach
</nav>
