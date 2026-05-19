<x-app-layout>
    <x-slot name="header">Dashboard Pokja PKL</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-card p-6 border-t-4 border-blue-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="graduation-cap" class="w-6 h-6 text-blue-400"></i>
                </div>
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Total Siswa PKL</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['total_siswa'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-t-4 border-amber-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="building-2" class="w-6 h-6 text-amber-400"></i>
                </div>
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Total DUDI</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['total_dudi'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6 border-t-4 border-purple-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="users" class="w-6 h-6 text-purple-400"></i>
                </div>
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Pembimbing Sekolah</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['total_pembimbing'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6 border-t-4 border-emerald-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle" class="w-6 h-6 text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Sistem Aktif</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">100%</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Chart 1: Distribusi Siswa per Kompetensi -->
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Statistik Penempatan Siswa</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="penempatanChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Kehadiran & Jurnal Harian -->
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Grafik Keaktifan Jurnal & Absensi</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="keaktifanChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Penempatan Chart (Doughnut)
            const ctxPenempatan = document.getElementById('penempatanChart').getContext('2d');
            new Chart(ctxPenempatan, {
                type: 'doughnut',
                data: {
                    labels: ['Siswa PKL', 'DUDI Terdaftar', 'Pembimbing'],
                    datasets: [{
                        data: [{{ $stats['total_siswa'] ?? 120 }}, {{ $stats['total_dudi'] ?? 45 }}, {{ $stats['total_pembimbing'] ?? 15 }}],
                        backgroundColor: ['#3b82f6', '#f59e0b', '#a855f7'],
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

            // Keaktifan Chart (Bar)
            const ctxKeaktifan = document.getElementById('keaktifanChart').getContext('2d');
            new Chart(ctxKeaktifan, {
                type: 'bar',
                data: {
                    labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                    datasets: [{
                        label: 'Jurnal Masuk',
                        data: [85, 92, 78, 88, 95],
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    }, {
                        label: 'Absensi Hadir',
                        data: [95, 98, 92, 94, 97],
                        backgroundColor: '#3b82f6',
                        borderRadius: 6
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
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                        y: { grid: { color: 'rgba(148, 163, 184, 0.1)' }, ticks: { color: '#94a3b8' } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
