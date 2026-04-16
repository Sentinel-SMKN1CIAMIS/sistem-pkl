<x-app-layout>
    <x-slot name="header">Dashboard Pembimbing Sekolah</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Total Siswa Bimbingan</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['siswa_count'] ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-400"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Jurnal Menunggu Validasi</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['jurnal_pending'] ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-400"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Total Jurnal Bimbingan</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['jurnal_masuk'] ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-square" class="w-6 h-6 text-emerald-400"></i>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
