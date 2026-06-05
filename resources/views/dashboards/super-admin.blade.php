<x-app-layout>
    <x-slot name="header">Dashboard Super Admin</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Total Pengguna</h3>
            <div class="text-4xl font-bold text-slate-900 dark:text-slate-100 mt-2">{{ \App\Models\User::count() }}</div>
            <div class="mt-4 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold border border-blue-500/20">TOTAL AKUN</span>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-amber-500">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Total DUDI</h3>
            <div class="text-4xl font-bold text-slate-900 dark:text-slate-100 mt-2">{{ \App\Models\Dudi::count() }}</div>
            <div class="mt-4 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-[10px] font-bold border border-amber-500/20">MITRA INDUSTRI</span>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-emerald-500">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">System Status</h3>
            <div class="flex items-center gap-3 mt-4">
                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                <span class="text-emerald-400 font-bold text-sm">ONLINE</span>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-4 font-mono uppercase tracking-tighter">PHP v{{ PHP_VERSION }} • Laravel v{{ Illuminate\Foundation\Application::VERSION }}</p>
        </div>
    </div>

    @php
        $roleCounts = \App\Models\User::groupBy('role')
            ->selectRaw('role, count(*) as total')
            ->pluck('total', 'role')
            ->toArray();
        
        $logCounts = \App\Models\ActivityLog::groupBy('action')
            ->selectRaw('action, count(*) as total')
            ->pluck('total', 'action')
            ->toArray();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Chart 1: Distribusi Akun Pengguna -->
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Distribusi Akun Pengguna</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="userRolesChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Statistik Aktivitas Sistem -->
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Statistik Aktivitas Sistem</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="systemActivitiesChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // User Roles Chart (Doughnut)
            const ctxRoles = document.getElementById('userRolesChart').getContext('2d');
            new Chart(ctxRoles, {
                type: 'doughnut',
                data: {
                    labels: ['Siswa PKL', 'Pembimbing Sekolah', 'Pembimbing DUDI', 'Pokja PKL', 'Super Admin'],
                    datasets: [{
                        data: [
                            {{ $roleCounts['siswa'] ?? 0 }},
                            {{ $roleCounts['pembimbing_sekolah'] ?? 0 }},
                            {{ $roleCounts['pembimbing_dudi'] ?? 0 }},
                            {{ $roleCounts['pokja'] ?? 0 }},
                            {{ $roleCounts['super_admin'] ?? 0 }}
                        ],
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899', '#64748b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#94a3b8',
                                font: { family: 'Plus Jakarta Sans, sans-serif', weight: 'bold' }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });

            // System Activities Chart (Bar)
            const ctxActivities = document.getElementById('systemActivitiesChart').getContext('2d');
            new Chart(ctxActivities, {
                type: 'bar',
                data: {
                    labels: ['Login', 'Logout', 'Created', 'Updated', 'Deleted'],
                    datasets: [{
                        label: 'Frekuensi Tindakan',
                        data: [
                            {{ $logCounts['LOGIN'] ?? 0 }},
                            {{ $logCounts['LOGOUT'] ?? 0 }},
                            {{ $logCounts['CREATED'] ?? 0 }},
                            {{ $logCounts['UPDATED'] ?? 0 }},
                            {{ $logCounts['DELETED'] ?? 0 }}
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ef4444'],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                        y: { grid: { color: 'rgba(148, 163, 184, 0.1)' }, ticks: { color: '#94a3b8', stepSize: 1 } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
