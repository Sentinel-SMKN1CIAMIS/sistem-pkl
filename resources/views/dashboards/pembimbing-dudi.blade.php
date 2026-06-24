<x-app-layout>
    <x-slot name="header">Dashboard Pembimbing DUDI</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-card p-6 border-t-4 border-blue-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="users" class="w-6 h-6 text-blue-500"></i>
                </div>
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Siswa PKL</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['siswa_count'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        
        <div class="glass-card p-6 border-t-4 border-emerald-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="clipboard-check" class="w-6 h-6 text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Jurnal Pending</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['jurnal_pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    @php
        $mentor = auth()->user()->pembimbingDudi;
        $siswaIds = \App\Models\Siswa::where('dudi_id', $mentor->dudi_id)->pluck('id');
        
        $absensiCounts = \App\Models\Absensi::whereIn('siswa_id', $siswaIds)
            ->groupBy('status')
            ->selectRaw('status, count(*) as total')
            ->pluck('total', 'status')
            ->toArray();
        
        $jurnalStatusCounts = \App\Models\Jurnal::whereIn('siswa_id', $siswaIds)
            ->groupBy('status')
            ->selectRaw('status, count(*) as total')
            ->pluck('total', 'status')
            ->toArray();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Chart 1: Status Kehadiran Siswa -->
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Analisis Kehadiran Siswa</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="dudiAbsensiChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Status Jurnal Siswa -->
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Status Pengisian Jurnal</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="dudiJurnalChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Absensi Chart (Doughnut)
            const ctxAbsensi = document.getElementById('dudiAbsensiChart').getContext('2d');
            new Chart(ctxAbsensi, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                    datasets: [{
                        data: [
                            {{ $absensiCounts['hadir'] ?? 0 }},
                            {{ $absensiCounts['izin'] ?? 0 }},
                            {{ $absensiCounts['sakit'] ?? 0 }},
                            {{ $absensiCounts['alpha'] ?? $absensiCounts['alpa'] ?? 0 }}
                        ],
                        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
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

            // Jurnal Chart (Bar)
            const ctxJurnal = document.getElementById('dudiJurnalChart').getContext('2d');
            new Chart(ctxJurnal, {
                type: 'bar',
                data: {
                    labels: ['Jurnal Valid', 'Menunggu Validasi'],
                    datasets: [{
                        label: 'Jumlah Jurnal',
                        data: [
                            {{ $jurnalStatusCounts['valid'] ?? 0 }},
                            {{ $jurnalStatusCounts['pending'] ?? 0 }}
                        ],
                        backgroundColor: ['#10b981', '#f59e0b'],
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
