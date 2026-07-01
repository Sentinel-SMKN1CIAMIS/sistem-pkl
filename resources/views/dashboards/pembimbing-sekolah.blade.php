<x-app-layout>
    <x-slot name="header">
        <span id="bulk-acc-trigger" class="cursor-default select-none">Dashboard Pembimbing Sekolah</span>
    </x-slot>

    @php
        $pembimbingProfile = auth()->user()->pembimbingSekolah;
        $tipeLabel = '';
        if ($pembimbingProfile) {
            if ($pembimbingProfile->tipe === 'kejuruan') {
                $tipeLabel = 'Kejuruan (Produktif)';
            } elseif ($pembimbingProfile->tipe === 'umum') {
                $tipeLabel = 'Umum (Normatif / Adaptif)';
            } else {
                $tipeLabel = 'Kejuruan & Umum';
            }
        }
    @endphp

    @if($pembimbingProfile)
        <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-blue-600 to-indigo-700 p-5 sm:p-6 text-white shadow-xl shadow-blue-500/10 mb-6">
            <!-- Decorative background glow circles -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center text-white shrink-0 border border-white/20 shadow-inner">
                        <i data-lucide="user-check" class="w-6 h-6 sm:w-7 sm:h-7"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[9px] sm:text-xs font-bold text-blue-200 uppercase tracking-widest block">Selamat Datang</span>
                        <h3 class="text-base sm:text-xl font-black tracking-tight leading-tight">{{ $pembimbingProfile->nama_lengkap }}</h3>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-blue-100 mt-1">
                            <span>NIP: <span class="font-mono font-bold">{{ $pembimbingProfile->nip ?? '-' }}</span></span>
                            <span class="text-blue-300/60 hidden sm:inline">•</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-white/20 text-white border border-white/10 uppercase tracking-wider block sm:inline-block">{{ $tipeLabel }}</span>
                        </div>
                    </div>
                </div>
                @if($pembimbingProfile->konsentrasiKeahlian)
                    <div class="px-4 py-2.5 bg-white/10 backdrop-blur-md border border-white/10 rounded-xl text-xs font-semibold text-blue-100 self-start md:self-center shadow-sm max-w-full">
                        <span class="text-blue-200 block text-[9px] uppercase tracking-wider mb-0.5">Konsentrasi</span>
                        <span class="font-bold text-white block sm:inline leading-snug">{{ $pembimbingProfile->konsentrasiKeahlian->nama }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Counter Stats: Compact grid-cols-3 on mobile to prevent layout bloat -->
    <div class="grid grid-cols-3 sm:grid-cols-3 gap-3 sm:gap-6 mb-8">
        <!-- Total Siswa -->
        <a href="{{ route('pembimbing_sekolah.siswa.index') }}" class="glass-card p-3 sm:p-6 border-t-4 border-blue-500 bg-white/5 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center sm:justify-between gap-2 transition-all duration-200 hover:scale-[1.02] cursor-pointer hover:bg-white/10 dark:hover:bg-slate-800/50 block">
            <div class="text-center sm:text-left">
                <span class="hidden sm:block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Siswa Bimbingan</span>
                <span class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white leading-none">{{ $stats['siswa_count'] ?? 0 }}</span>
                <span class="block sm:hidden text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">Siswa</span>
            </div>
            <div class="hidden sm:flex w-12 h-12 bg-blue-500/10 rounded-xl items-center justify-center text-blue-500 shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </a>

        <!-- Menunggu Validasi -->
        <a href="{{ route('pembimbing_sekolah.jurnal.index') }}" class="glass-card p-3 sm:p-6 border-t-4 border-amber-500 bg-white/5 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center sm:justify-between gap-2 transition-all duration-200 hover:scale-[1.02] cursor-pointer hover:bg-white/10 dark:hover:bg-slate-800/50 block">
            <div class="text-center sm:text-left">
                <span class="hidden sm:block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Jurnal Pending</span>
                <span class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white leading-none">{{ $stats['jurnal_pending'] ?? 0 }}</span>
                <span class="block sm:hidden text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">Pending</span>
            </div>
            <div class="hidden sm:flex w-12 h-12 bg-amber-500/10 rounded-xl items-center justify-center text-amber-500 shrink-0">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
        </a>

        <!-- Total Jurnal -->
        <a href="{{ route('pembimbing_sekolah.siswa.index') }}" class="glass-card p-3 sm:p-6 border-t-4 border-emerald-500 bg-white/5 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center sm:justify-between gap-2 transition-all duration-200 hover:scale-[1.02] cursor-pointer hover:bg-white/10 dark:hover:bg-slate-800/50 block">
            <div class="text-center sm:text-left">
                <span class="hidden sm:block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Total Jurnal</span>
                <span class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white leading-none">{{ $stats['jurnal_masuk'] ?? 0 }}</span>
                <span class="block sm:hidden text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">Jurnal</span>
            </div>
            <div class="hidden sm:flex w-12 h-12 bg-emerald-500/10 rounded-xl items-center justify-center text-emerald-500 shrink-0">
                <i data-lucide="check-square" class="w-6 h-6"></i>
            </div>
        </a>
    </div>

    <!-- Quick Actions (Mobile Only) -->
    <div class="block lg:hidden mt-8 mb-8">
        <h3 class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 gap-3.5">
            <!-- Kehadiran Siswa -->
            <a href="{{ route('pembimbing_sekolah.absensi.index') }}" 
               class="glass-card p-4 flex flex-col items-center justify-center gap-2.5 border border-slate-200/50 dark:border-slate-800 hover:bg-blue-500/5 hover:border-blue-500/30 transition-all duration-300 group rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-blue-500 shrink-0">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <span class="text-[11px] font-bold text-center text-slate-700 dark:text-slate-300 group-hover:text-blue-500 transition-colors uppercase tracking-wider">Daftar Hadir</span>
            </a>
            
            <!-- Persetujuan Absensi -->
            <a href="{{ route('pembimbing_sekolah.absensi.approval.index') }}" 
               class="glass-card p-4 flex flex-col items-center justify-center gap-2.5 border border-slate-200/50 dark:border-slate-800 hover:bg-emerald-500/5 hover:border-emerald-500/30 transition-all duration-300 group rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-emerald-500 shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <span class="text-[11px] font-bold text-center text-slate-700 dark:text-slate-300 group-hover:text-emerald-500 transition-colors uppercase tracking-wider">Izin Absen</span>
            </a>

            <!-- Evaluasi Laporan -->
            <a href="{{ route('pembimbing_sekolah.laporan.index') }}" 
               class="glass-card p-4 flex flex-col items-center justify-center gap-2.5 border border-slate-200/50 dark:border-slate-800 hover:bg-purple-500/5 hover:border-purple-500/30 transition-all duration-300 group rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-purple-500 shrink-0">
                    <i data-lucide="file-check" class="w-5 h-5"></i>
                </div>
                <span class="text-[11px] font-bold text-center text-slate-700 dark:text-slate-300 group-hover:text-purple-500 transition-colors uppercase tracking-wider">Evaluasi Lap.</span>
            </a>

            <!-- Peta DUDI -->
            <a href="{{ route('shared.pemetaan.maps') }}" 
               class="glass-card p-4 flex flex-col items-center justify-center gap-2.5 border border-slate-200/50 dark:border-slate-800 hover:bg-amber-500/5 hover:border-amber-500/30 transition-all duration-300 group rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-amber-500 shrink-0">
                    <i data-lucide="map" class="w-5 h-5"></i>
                </div>
                <span class="text-[11px] font-bold text-center text-slate-700 dark:text-slate-300 group-hover:text-amber-500 transition-colors uppercase tracking-wider">Peta DUDI</span>
            </a>

            <!-- Panduan Interaktif -->
            <a href="{{ route('panduan.interaktif') }}" 
               class="glass-card p-4 flex flex-col items-center justify-center gap-2.5 border border-slate-200/50 dark:border-slate-800 hover:bg-indigo-500/5 hover:border-indigo-500/30 transition-all duration-300 group rounded-2xl col-span-2">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-indigo-500 shrink-0">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </div>
                <span class="text-[11px] font-bold text-center text-slate-700 dark:text-slate-300 group-hover:text-indigo-500 transition-colors uppercase tracking-wider font-semibold">Panduan Interaktif</span>
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

