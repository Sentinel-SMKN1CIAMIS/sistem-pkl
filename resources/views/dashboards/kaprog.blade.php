<x-app-layout>
    <x-slot name="header">Dashboard Kepala Program</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass-card p-8">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center">
                    <i data-lucide="file-bar-chart-2" class="w-6 h-6 text-blue-500"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Laporan PKL</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Pantau perkembangan dan penempatan siswa PKL.</p>
                </div>
            </div>
            <a href="{{ route('kaprog.laporan.index') }}" class="inline-block mt-4 text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                Lihat Laporan Lengkap &rarr;
            </a>
        </div>
    </div>
</x-app-layout>
