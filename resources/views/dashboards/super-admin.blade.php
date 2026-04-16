<x-app-layout>
    <x-slot name="header">Dashboard Super Admin</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-2">Total Pengguna</h3>
            <div class="text-4xl font-bold text-slate-100 mt-2">{{ \App\Models\User::count() }}</div>
            <div class="mt-4 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">TOTAL AKUN</span>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-amber-500">
            <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-2">Total DUDI</h3>
            <div class="text-4xl font-bold text-slate-100 mt-2">{{ \App\Models\Dudi::count() }}</div>
            <div class="mt-4 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-[10px] font-bold border border-amber-500/20">MITRA INDUSTRI</span>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-emerald-500">
            <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-2">System Status</h3>
            <div class="flex items-center gap-3 mt-4">
                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                <span class="text-emerald-400 font-bold text-sm">ONLINE</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-4 font-mono uppercase tracking-tighter">PHP v{{ PHP_VERSION }} • Laravel v{{ Illuminate\Foundation\Application::VERSION }}</p>
        </div>
    </div>
</x-app-layout>
