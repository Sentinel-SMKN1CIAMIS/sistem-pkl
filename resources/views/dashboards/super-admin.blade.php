<x-app-layout>
    <x-slot name="header">Dashboard Super Admin</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6">
            <h3 class="text-lg font-medium text-slate-200 mb-2">System Status</h3>
            <div class="flex items-center gap-2 mt-4">
                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                <span class="text-slate-300">All systems operational</span>
            </div>
            <p class="text-sm text-slate-400 mt-2">Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p>
        </div>
        
        <div class="glass-card p-6">
            <h3 class="text-lg font-medium text-slate-200 mb-2">Total Pengguna</h3>
            <div class="text-3xl font-bold text-blue-400 mt-2">850</div>
            <p class="text-sm text-slate-400 mt-1">Akun aktif dalam sistem</p>
        </div>
    </div>
</x-app-layout>
