<x-app-layout>
    <x-slot name="header">Dashboard Siswa</x-slot>

    <!-- Force Change Password Modal -->
    <x-force-change-password-modal :forcePasswordChange="$forcePasswordChange ?? false" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6 border-l-4 border-blue-500 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Status Hari Ini</p>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 uppercase tracking-tighter">{{ auth()->user()->siswa->status_hari_ini }}</h3>
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
            <div class="mt-4 pt-4 border-t border-slate-200/50 dark:border-slate-700/50 relative">
                <div class="flex mb-2 items-center justify-between">
                    <span class="text-xs font-semibold inline-block text-emerald-400">Valid: {{ $stats['jurnal_valid'] }}</span>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-slate-200 dark:bg-slate-700">
                    <div @style(['width: ' . ($stats['jurnal_total'] > 0 ? ($stats['jurnal_valid'] / $stats['jurnal_total'] * 100) : 0) . '%']) class="shadow-none flex flex-col text-center whitespace-nowrap text-slate-900 dark:text-white justify-center bg-linear-to-r from-emerald-400 to-emerald-500"></div>
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
        <a href="{{ route('siswa.jurnal.create') }}" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-blue-600/10 border hover:border-blue-500/30 transition-all group">
            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-blue-500/20 transition-colors">
                <i data-lucide="edit-3" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-blue-400"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white">Isi Jurnal</span>
        </a>
        <a href="{{ route('siswa.absensi.index') }}" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-emerald-600/10 border hover:border-emerald-500/30 transition-all group">
            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors">
                <i data-lucide="clipboard-check" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-emerald-400"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white">Isi Absensi</span>
        </a>
        <a href="{{ route('siswa.panduan.index') }}" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-purple-600/10 border hover:border-purple-500/30 transition-all group">
            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-purple-500/20 transition-colors">
                <i data-lucide="download-cloud" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-purple-400"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white">Buku Panduan</span>
        </a>
        <a href="{{ route('notifications.index') }}" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-amber-600/10 border hover:border-amber-500/30 transition-all group">
            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-amber-500/20 transition-colors">
                <i data-lucide="bell" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-amber-400 relative">
                    <span class="absolute top-0 right-0 w-1.5 h-1.5 rounded-full bg-red-500 border border-slate-900"></span>
                </i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white">Notifikasi Baru</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Chart 1: Progress PKL -->
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Persentase Pengisian Jurnal (Valid vs Pending)</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="jurnalSiswaChart" data-valid="{{ $stats['jurnal_valid'] ?? 0 }}" data-total="{{ $stats['jurnal_total'] ?? 0 }}"></canvas>
            </div>
        </div>

        <!-- Chart 2: Kehadiran Siswa -->
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Grafik Kehadiran Mingguan Anda</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="kehadiranSiswaChart" data-week-attendance='{!! json_encode($stats["week_attendance"] ?? [null, null, null, null, null]) !!}'></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Jurnal Siswa Chart (Pie)
            const canvasJurnal = document.getElementById('jurnalSiswaChart');
            const ctxJurnal = canvasJurnal.getContext('2d');
            const validCount = parseInt(canvasJurnal.getAttribute('data-valid')) || 0;
            const totalCount = parseInt(canvasJurnal.getAttribute('data-total')) || 0;
            const pendingCount = totalCount - validCount;

            new Chart(ctxJurnal, {
                type: 'pie',
                data: {
                    labels: ['Jurnal Valid', 'Jurnal Pending'],
                    datasets: [{
                        data: [validCount, pendingCount],
                        backgroundColor: ['#10b981', '#f59e0b'],
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
                    }
                }
            });
            // Kehadiran Siswa Chart (Line)
            const canvasKehadiran = document.getElementById('kehadiranSiswaChart');
            const weekAttendance = JSON.parse(canvasKehadiran.getAttribute('data-week-attendance') || '[null,null,null,null,null]');
            const ctxKehadiran = canvasKehadiran.getContext('2d');
            new Chart(ctxKehadiran, {
                type: 'line',
                data: {
                    labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                    datasets: [{
                        label: 'Kehadiran Anda (Jam Masuk)',
                        data: weekAttendance,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
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
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y;
                                    if (value === null || isNaN(value)) return 'Tidak Hadir / Belum Absen';
                                    const hour = Math.floor(value);
                                    const minutes = Math.round((value - hour) * 60);
                                    return 'Jam Masuk: ' + (hour < 10 ? '0' : '') + hour + ':' + (minutes < 10 ? '0' : '') + minutes;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                        y: { 
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }, 
                            ticks: { 
                                color: '#94a3b8',
                                callback: function(value) {
                                    if (value === null || isNaN(value)) return '';
                                    const hour = Math.floor(value);
                                    const minutes = Math.round((value - hour) * 60);
                                    return (hour < 10 ? '0' : '') + hour + ':' + (minutes < 10 ? '0' : '') + minutes;
                                }
                            } 
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
