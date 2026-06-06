<x-app-layout>
    <x-slot name="header">Dashboard Kepala Program</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="glass-card p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Total Siswa PKL</h3>
            <div class="flex items-end gap-3">
                <span class="text-4xl font-black text-slate-800 dark:text-slate-100">{{ $stats['total_siswa'] }}</span>
                <i data-lucide="users" class="w-8 h-8 text-blue-500/20 mb-1"></i>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-emerald-500">
            <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Total DUDI</h3>
            <div class="flex items-end gap-3">
                <span class="text-4xl font-black text-slate-800 dark:text-slate-100">{{ $stats['total_dudi'] }}</span>
                <i data-lucide="building-2" class="w-8 h-8 text-emerald-500/20 mb-1"></i>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-purple-500">
            <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Guru Pembimbing</h3>
            <div class="flex items-end gap-3">
                <span class="text-4xl font-black text-slate-800 dark:text-slate-100">{{ $stats['total_pembimbing'] }}</span>
                <i data-lucide="user-check" class="w-8 h-8 text-purple-500/20 mb-1"></i>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-amber-500">
            <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Pengajuan Menunggu</h3>
            <div class="flex items-end gap-3">
                <span class="text-4xl font-black text-slate-800 dark:text-slate-100">{{ $stats['pengajuan_menunggu'] }}</span>
                <i data-lucide="clock" class="w-8 h-8 text-amber-500/20 mb-1"></i>
            </div>
            <a href="{{ route('kaprog.pengajuan_pkl.index') }}" class="mt-3 inline-block text-xs text-amber-600 dark:text-amber-400 font-medium hover:underline">Lihat Pengajuan &rarr;</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Attendance Rate Widget -->
        <div class="glass-card p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Kehadiran Hari Ini</h3>
                <div class="flex items-end gap-3 mt-4">
                    <span class="text-4xl font-black {{ $stats['attendance_rate'] < 50 ? 'text-red-500' : ($stats['attendance_rate'] < 80 ? 'text-amber-500' : 'text-emerald-500') }}">
                        {{ $stats['attendance_rate'] }}%
                    </span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 mt-4">
                    <div class="h-2.5 rounded-full {{ $stats['attendance_rate'] < 50 ? 'bg-red-500' : ($stats['attendance_rate'] < 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $stats['attendance_rate'] }}%"></div>
                </div>
            </div>
            <div class="mt-6">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Belum Absen ({{ $stats['missing_attendance']->count() }} teratas):</h4>
                <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-1">
                    @forelse($stats['missing_attendance'] as $siswa)
                        <li class="truncate">- {{ $siswa->nama_lengkap }} ({{ $siswa->kelas }})</li>
                    @empty
                        <li class="italic text-emerald-500">Semua siswa sudah absen!</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Journal Rate Widget -->
        <div class="glass-card p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Jurnal Hari Ini</h3>
                <div class="flex items-end gap-3 mt-4">
                    <span class="text-4xl font-black {{ $stats['journal_rate'] < 50 ? 'text-red-500' : ($stats['journal_rate'] < 80 ? 'text-amber-500' : 'text-emerald-500') }}">
                        {{ $stats['journal_rate'] }}%
                    </span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 mt-4">
                    <div class="h-2.5 rounded-full {{ $stats['journal_rate'] < 50 ? 'bg-red-500' : ($stats['journal_rate'] < 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $stats['journal_rate'] }}%"></div>
                </div>
            </div>
            <div class="mt-6">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Belum Isi Jurnal ({{ $stats['missing_journal']->count() }} teratas):</h4>
                <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-1">
                    @forelse($stats['missing_journal'] as $siswa)
                        <li class="truncate">- {{ $siswa->nama_lengkap }} ({{ $siswa->kelas }})</li>
                    @empty
                        <li class="italic text-emerald-500">Semua siswa sudah mengisi jurnal!</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Laporan PKL Shortcut -->
        <div class="glass-card p-6 flex flex-col justify-center items-center text-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-500/10 flex items-center justify-center mb-4 border border-blue-500/20">
                <i data-lucide="file-bar-chart-2" class="w-8 h-8 text-blue-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Laporan PKL</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Pantau rekapitulasi perkembangan dan penempatan siswa PKL secara mendetail.</p>
            <a href="{{ route('kaprog.laporan.index') }}" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all">
                Lihat Laporan Lengkap
            </a>
        </div>
    </div>
</x-app-layout>
