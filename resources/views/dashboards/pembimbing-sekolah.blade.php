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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Statistik Bimbingan Anda</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="sekolahBimbinganChart"></canvas>
            </div>
        </div>
        <div class="glass-card p-6 border-t-2 border-slate-200/50 dark:border-slate-700/50">
            <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Rata-Rata Validasi Jurnal Mingguan</h3>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="sekolahJurnalChart"></canvas>
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

            const ctxBimbingan = document.getElementById('sekolahBimbinganChart').getContext('2d');
            new Chart(ctxBimbingan, {
                type: 'polarArea',
                data: {
                    labels: ['Siswa Terbimbing', 'Jurnal Masuk', 'Validasi Pending'],
                    datasets: [{
                        data: [{{ $stats['siswa_count'] ?? 10 }}, {{ $stats['jurnal_masuk'] ?? 45 }}, {{ $stats['jurnal_pending'] ?? 5 }}],
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
                    }
                }
            });

            const ctxJurnal = document.getElementById('sekolahJurnalChart').getContext('2d');
            new Chart(ctxJurnal, {
                type: 'bar',
                data: {
                    labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                    datasets: [{
                        label: 'Jurnal Terevaluasi',
                        data: [25, 34, 42, {{ $stats['jurnal_masuk'] ?? 45 }}],
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
                        y: { grid: { color: 'rgba(148, 163, 184, 0.1)' }, ticks: { color: '#94a3b8' } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
