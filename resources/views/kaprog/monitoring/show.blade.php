<x-app-layout>
    <x-slot name="header">Detail Monitoring Pembimbing</x-slot>

    <div class="mb-6">
        <a href="{{ route('kaprog.monitoring.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Halaman Monitoring
        </a>
    </div>

    <!-- Profile Header Card -->
    <div class="glass-card p-6 mb-8 border-l-4 {{ $activityStatus === 'aktif' ? 'border-emerald-500' : ($activityStatus === 'kurang_aktif' ? 'border-amber-500' : 'border-red-500') }}">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <!-- Profile Info -->
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-600/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                    <i data-lucide="user-check" class="w-8 h-8"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ $pembimbingSekolah->nama_lengkap }}</h2>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-sm text-slate-500 dark:text-slate-400 font-medium">
                        <span class="font-mono">{{ $pembimbingSekolah->nip ?: 'NIP: -' }}</span>
                        <span>•</span>
                        <span>{{ $pembimbingSekolah->konsentrasiKeahlian?->nama ?: 'Konsentrasi: -' }}</span>
                        <span>•</span>
                        <span class="capitalize">Tipe: {{ $pembimbingSekolah->tipe === 'keduanya' ? 'Kejuruan & Umum' : $pembimbingSekolah->tipe }}</span>
                    </div>
                </div>
            </div>

            <!-- Activity Badge & Log -->
            <div class="flex flex-col items-start lg:items-end gap-2 shrink-0 w-full lg:w-auto">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Status Keaktifan:</span>
                    @php
                        $statusColors = [
                            'aktif' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                            'kurang_aktif' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                            'tidak_pernah_login' => 'bg-red-500/10 text-red-500 border-red-500/20 animate-pulse'
                        ];
                        $statusLabels = [
                            'aktif' => 'AKTIF (SANGAT BAIK)',
                            'kurang_aktif' => 'KURANG AKTIF (PERLU EVALUASI)',
                            'tidak_pernah_login' => 'BELUM PERNAH LOGIN'
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusColors[$activityStatus] }}">
                        {{ $statusLabels[$activityStatus] }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Login Terakhir: 
                    <span class="font-semibold text-slate-800 dark:text-slate-200">
                        {{ $pembimbingSekolah->user?->last_login_at ? $pembimbingSekolah->user->last_login_at->isoFormat('D MMMM YYYY, HH:mm') . ' (' . $pembimbingSekolah->user->last_login_at->diffForHumans() . ')' : 'Belum pernah login' }}
                    </span>
                </p>
                @if($pembimbingSekolah->no_hp)
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                        No. HP / WhatsApp: 
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pembimbingSekolah->no_hp) }}" target="_blank" class="font-bold text-blue-500 hover:underline inline-flex items-center gap-1">
                            <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                            {{ $pembimbingSekolah->no_hp }}
                        </a>
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Section: Students List (Col span 2) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Advisor Stats Overview -->
            <div class="grid grid-cols-3 gap-4">
                <div class="glass-card p-4 text-center">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-widest mb-1">Siswa Jurusan Anda</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ $students->count() }}</p>
                </div>
                <div class="glass-card p-4 text-center">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-widest mb-1">Pending Jurnal</p>
                    <p class="text-2xl font-black {{ $pendingJurnals > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-emerald-500' }}">
                        {{ $pendingJurnals }}
                    </p>
                </div>
                <div class="glass-card p-4 text-center">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-widest mb-1">Pending Absensi</p>
                    <p class="text-2xl font-black {{ $pendingAbsences > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-emerald-500' }}">
                        {{ $pendingAbsences }}
                    </p>
                </div>
            </div>

            <!-- Students Table Card -->
            <div class="glass-card overflow-hidden">
                <div class="p-6 border-b border-slate-200/50 dark:border-slate-700/50">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="graduation-cap" class="text-blue-500"></i>
                        Daftar Siswa & Detail Validasi
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Periksa kinerja pembimbing dalam memproses jurnal/absensi untuk siswa bimbingannya yang berada di jurusan Anda.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/30 border-b border-slate-200/50 dark:border-slate-700/50">
                                <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Siswa</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">DUDI / Industri</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-center whitespace-nowrap">Jurnal Kegiatan</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-center whitespace-nowrap">Kehadiran / Absensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/50 dark:divide-slate-700/50">
                            @forelse($students as $siswa)
                                <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-800/10 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block">{{ $siswa->nama_lengkap }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $siswa->kelas }} ({{ $siswa->nis }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 dark:text-slate-200 font-medium">
                                        {{ $siswa->dudi?->nama ?: 'Belum ditempatkan' }}
                                    </td>
                                    <!-- Jurnal Stats Column -->
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="flex items-center gap-1 text-xs">
                                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $siswa->total_jurnals_count - $siswa->pending_jurnals_count }} Valid</span>
                                                <span class="text-slate-400">/ {{ $siswa->total_jurnals_count }} Total</span>
                                            </div>
                                            @if($siswa->pending_jurnals_count > 0)
                                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                                    {{ $siswa->pending_jurnals_count }} Pending ACC
                                                 </span>
                                            @else
                                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                    Selesai
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Absensi Stats Column -->
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="flex items-center gap-1 text-xs">
                                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $siswa->total_absensis_count - $siswa->pending_absensis_count }} Valid</span>
                                                <span class="text-slate-400">/ {{ $siswa->total_absensis_count }} Total</span>
                                            </div>
                                            @if($siswa->pending_absensis_count > 0)
                                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                                    {{ $siswa->pending_absensis_count }} Pending ACC
                                                </span>
                                            @else
                                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                    Selesai
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                        Belum ada siswa dari program keahlian Anda yang ditugaskan kepada pembimbing sekolah ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Section: Monitoring & Evaluation Logs (Col span 1) -->
        <div class="space-y-6">
            <!-- Evaluation logs timeline -->
            <div class="glass-card p-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 mb-4">
                    <i data-lucide="scroll-text" class="text-blue-500"></i>
                    Riwayat Catatan Pokja
                </h3>

                <div class="space-y-6 relative before:absolute before:left-3 before:top-2 before:bottom-2 before:w-px before:bg-slate-200 dark:before:bg-slate-700">
                    @forelse($monitoringLogs as $log)
                        @php
                            $badgeColors = [
                                'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                'rejected' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                'approved' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'
                            ];
                            $badgeLabels = [
                                'pending' => 'Peringatan',
                                'rejected' => 'Teguran',
                                'approved' => 'Apresiasi'
                            ];
                            $dotColors = [
                                'pending' => 'bg-amber-500 ring-amber-500/20',
                                'rejected' => 'bg-red-500 ring-red-500/20',
                                'approved' => 'bg-emerald-500 ring-emerald-500/20'
                            ];
                        @endphp
                        <div class="relative pl-8">
                            <!-- Timeline Dot -->
                            <span class="absolute left-1.5 top-1.5 w-3 id-{{ $log->id }} h-3 rounded-full {{ $dotColors[$log->status] ?? 'bg-blue-500' }} ring-4"></span>
                            
                            <!-- Timeline Item Content -->
                            <div class="p-3 bg-slate-50/50 dark:bg-slate-800/30 border border-slate-200/40 dark:border-slate-700/40 rounded-xl">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-wide border px-2 py-0.5 rounded {{ $badgeColors[$log->status] ?? 'bg-blue-500/10 text-blue-400' }}">
                                        {{ $badgeLabels[$log->status] ?? 'Info' }}
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400 font-bold">
                                        {{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('D MMM YYYY') }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium mb-2">{{ $log->catatan }}</p>
                                <div class="flex items-center gap-1.5 pt-1.5 border-t border-slate-200/40 dark:border-slate-700/40 text-[9px] text-slate-500">
                                    <i data-lucide="user" class="w-3 h-3"></i>
                                    <span>Dicatat oleh: <strong>{{ $log->pokjaUser?->name ?: 'Sistem' }}</strong></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-500 dark:text-slate-400 italic text-xs pl-4">
                            Belum ada catatan evaluasi untuk pembimbing ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
