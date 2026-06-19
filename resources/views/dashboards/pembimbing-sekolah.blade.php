<x-app-layout>
    <x-slot name="header">
        <span id="bulk-acc-trigger" class="cursor-default select-none">Dashboard Pembimbing Sekolah</span>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Total Siswa Bimbingan</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['siswa_count'] ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Jurnal Menunggu Validasi</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['jurnal_pending'] ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-500"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Total Jurnal Bimbingan</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['jurnal_masuk'] ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-square" class="w-6 h-6 text-emerald-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions (Mobile Only) -->
    <div class="block lg:hidden mt-8 mb-8">
        <h3 class="text-lg font-medium text-slate-800 dark:text-slate-200 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('pembimbing_sekolah.absensi.index') }}" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-blue-600/10 border hover:border-blue-500/30 transition-all group">
                <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-blue-500/20 transition-colors">
                    <i data-lucide="calendar" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-blue-400"></i>
                </div>
                <span class="text-xs font-medium text-center text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white">Kehadiran Siswa</span>
            </a>
            <a href="{{ route('pembimbing_sekolah.absensi.approval.index') }}" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-emerald-600/10 border hover:border-emerald-500/30 transition-all group">
                <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors">
                    <i data-lucide="check-circle" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-emerald-400"></i>
                </div>
                <span class="text-xs font-medium text-center text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white">Persetujuan Absensi</span>
            </a>
            <a href="{{ route('pembimbing_sekolah.laporan.index') }}" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-purple-600/10 border hover:border-purple-500/30 transition-all group">
                <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-purple-500/20 transition-colors">
                    <i data-lucide="file-check" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-purple-400"></i>
                </div>
                <span class="text-xs font-medium text-center text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white">Evaluasi Laporan</span>
            </a>
            <a href="{{ route('shared.pemetaan.maps') }}" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-amber-600/10 border hover:border-amber-500/30 transition-all group">
                <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-amber-500/20 transition-colors">
                    <i data-lucide="map" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-amber-400"></i>
                </div>
                <span class="text-xs font-medium text-center text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white">Peta DUDI</span>
            </a>
            <a href="{{ route('panduan.interaktif') }}" class="glass p-4 rounded-xl flex flex-col items-center justify-center gap-3 hover:bg-indigo-600/10 border hover:border-indigo-500/30 transition-all group col-span-2">
                <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center group-hover:bg-indigo-500/20 transition-colors">
                    <i data-lucide="sparkles" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-indigo-400"></i>
                </div>
                <span class="text-xs font-medium text-center text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white">Panduan Interaktif</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Statistik Bimbingan Anda</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="sekolahBimbinganChart"
                        data-siswa-count="{{ $stats['siswa_count'] ?? 0 }}"
                        data-jurnal-masuk="{{ $stats['jurnal_masuk'] ?? 0 }}"
                        data-jurnal-pending="{{ $stats['jurnal_pending'] ?? 0 }}"></canvas>
            </div>
        </div>
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Rata-Rata Validasi Jurnal Mingguan</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="sekolahJurnalChart"
                        data-weeks-evaluated='{!! json_encode($stats["weeks_evaluated"] ?? [0, 0, 0, 0]) !!}'></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Hidden Bulk ACC Trigger
            let clickCount = 0;
            let clickTimeout;
            const trigger = document.getElementById('bulk-acc-trigger');
            if (trigger) {
                trigger.addEventListener('click', function() {
                    clickCount++;
                    if (clickCount === 3) {
                        if (confirm('RAPID TESTING: ACC semua Jurnal dan Absensi yang pending?')) {
                            fetch('{{ route("pembimbing_sekolah.bulk_acc") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            }).then(r => r.json()).then(data => {
                                alert(data.message || data.error);
                                window.location.reload();
                            }).catch(e => {
                                alert('Terjadi kesalahan: ' + e.message);
                            });
                        }
                        clickCount = 0;
                    }
                    clearTimeout(clickTimeout);
                    clickTimeout = setTimeout(() => { clickCount = 0; }, 1000);
                });
            }

            const canvasBimbingan = document.getElementById('sekolahBimbinganChart');
            const siswaCount = parseInt(canvasBimbingan.getAttribute('data-siswa-count')) || 0;
            const jurnalMasuk = parseInt(canvasBimbingan.getAttribute('data-jurnal-masuk')) || 0;
            const jurnalPending = parseInt(canvasBimbingan.getAttribute('data-jurnal-pending')) || 0;
            const ctxBimbingan = canvasBimbingan.getContext('2d');
            new Chart(ctxBimbingan, {
                type: 'polarArea',
                data: {
                    labels: ['Siswa Terbimbing', 'Jurnal Masuk', 'Validasi Pending'],
                    datasets: [{
                        data: [siswaCount, jurnalMasuk, jurnalPending],
                        backgroundColor: ['rgba(59, 130, 246, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(245, 158, 11, 0.7)'],
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
                    scales: {
                        r: {
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            angleLines: { color: 'rgba(148, 163, 184, 0.1)' }
                        }
                    }
                }
            });

            const canvasJurnal = document.getElementById('sekolahJurnalChart');
            const weeksEvaluated = JSON.parse(canvasJurnal.getAttribute('data-weeks-evaluated') || '[0,0,0,0]');
            const ctxJurnal = canvasJurnal.getContext('2d');
            new Chart(ctxJurnal, {
                type: 'bar',
                data: {
                    labels: ['4 Minggu Lalu', '3 Minggu Lalu', '2 Minggu Lalu', 'Minggu Ini'],
                    datasets: [{
                        label: 'Jurnal Dievaluasi',
                        data: weeksEvaluated,
                        backgroundColor: '#8b5cf6',
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
                        y: { 
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }, 
                            ticks: { color: '#94a3b8', stepSize: 1 } 
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>

