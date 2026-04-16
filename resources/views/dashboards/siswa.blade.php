<x-app-layout>
    <x-slot name="header">Dashboard Siswa</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6 border-l-4 border-blue-500 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Status PKL</p>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 uppercase tracking-tighter">{{ str_replace('_', ' ', auth()->user()->siswa->status_pkl) }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center relative z-10">
                    <i data-lucide="activity" class="w-6 h-6 text-blue-400"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-200/50 dark:border-slate-700/50">
                <p class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-2">
                    <i data-lucide="building-2" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    {{ auth()->user()->siswa->dudi->nama ?? 'Belum Penempatan' }}
                </p>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-emerald-500 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Jurnal Terisi</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['jurnal_total'] }} <span class="text-sm font-normal text-slate-600 dark:text-slate-400">Harian</span></h3>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center relative z-10">
                    <i data-lucide="book-open" class="w-6 h-6 text-emerald-400"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-200/50 dark:border-slate-700/50 relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <span class="text-xs font-semibold inline-block text-emerald-400">Valid: {{ $stats['jurnal_valid'] }}</span>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-slate-200 dark:bg-slate-700">
                    <div style="width: {{ $stats['jurnal_total'] > 0 ? ($stats['jurnal_valid'] / $stats['jurnal_total'] * 100) : 0 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-slate-900 dark:text-white justify-center bg-gradient-to-r from-emerald-400 to-emerald-500"></div>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-purple-500 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Kehadiran</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['absensi_count'] }} <span class="text-sm font-normal text-slate-600 dark:text-slate-400">Hari</span></h3>
                </div>
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center relative z-10">
                    <i data-lucide="check-circle" class="w-6 h-6 text-purple-400"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-200/50 dark:border-slate-700/50">
                <p class="text-sm text-slate-600 dark:text-slate-400">Absensi harian tercatat di sistem.</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <h3 class="text-lg font-medium text-slate-800 dark:text-slate-200 mb-4">Aksi Cepat</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="#" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-blue-600/10 border hover:border-blue-500/30 transition-all group">
            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-blue-500/20 transition-colors">
                <i data-lucide="edit-3" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-blue-400"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:text-white">Isi Jurnal</span>
        </a>
        <a href="#" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-emerald-600/10 border hover:border-emerald-500/30 transition-all group">
            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors">
                <i data-lucide="clipboard-check" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-emerald-400"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:text-white">Isi Absensi</span>
        </a>
        <a href="#" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-purple-600/10 border hover:border-purple-500/30 transition-all group">
            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-purple-500/20 transition-colors">
                <i data-lucide="download-cloud" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-purple-400"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:text-white">Buku Panduan</span>
        </a>
        <a href="#" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-amber-600/10 border hover:border-amber-500/30 transition-all group">
            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-amber-500/20 transition-colors">
                <i data-lucide="bell" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-amber-400 relative">
                    <span class="absolute top-0 right-0 w-1.5 h-1.5 rounded-full bg-red-500 border border-slate-900"></span>
                </i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:text-white">Notifikasi Baru</span>
        </a>
    </div>

</x-app-layout>
