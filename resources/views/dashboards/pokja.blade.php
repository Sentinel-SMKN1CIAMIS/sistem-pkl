<x-app-layout>
    <x-slot name="header">Dashboard Pokja PKL</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-card p-6 border-t-4 border-blue-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="graduation-cap" class="w-6 h-6 text-blue-400"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-sm font-medium mb-1">Total Siswa PKL</p>
                    <h3 class="text-2xl font-bold text-slate-100">600</h3>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-t-4 border-amber-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="building-2" class="w-6 h-6 text-amber-400"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-sm font-medium mb-1">Total DUDI</p>
                    <h3 class="text-2xl font-bold text-slate-100">120</h3>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6 border-t-4 border-purple-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="users" class="w-6 h-6 text-purple-400"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-sm font-medium mb-1">Pembimbing Sekolah</p>
                    <h3 class="text-2xl font-bold text-slate-100">45</h3>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6 border-t-4 border-emerald-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="network" class="w-6 h-6 text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-sm font-medium mb-1">Telah Dipetakan</p>
                    <h3 class="text-2xl font-bold text-slate-100">100%</h3>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
