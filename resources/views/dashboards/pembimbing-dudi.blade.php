<x-app-layout>
    <x-slot name="header">Dashboard Pembimbing DUDI</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-card p-6 border-t-4 border-blue-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="users" class="w-6 h-6 text-blue-400"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-sm font-medium mb-1">Siswa PKL</p>
                    <h3 class="text-2xl font-bold text-slate-100">8</h3>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6 border-t-4 border-emerald-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="clipboard-check" class="w-6 h-6 text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-sm font-medium mb-1">Hadir Hari Ini</p>
                    <h3 class="text-2xl font-bold text-slate-100">8</h3>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
