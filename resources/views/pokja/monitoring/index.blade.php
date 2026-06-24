<x-app-layout>
    <x-slot name="header">Monitoring Pembimbing Sekolah</x-slot>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Review performa dan tingkat keaktifan pembimbing sekolah dalam mengawal jurnal & kehadiran siswa PKL secara real-time.</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Pembimbing -->
        <div class="glass-card p-6 border-t-4 border-blue-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-6 h-6 text-blue-500"></i>
                </div>
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Total Pembimbing</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Aktif -->
        <div class="glass-card p-6 border-t-4 border-emerald-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle" class="w-6 h-6 text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Aktif</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['aktif'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Kurang Aktif -->
        <div class="glass-card p-6 border-t-4 border-amber-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-500"></i>
                </div>
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Kurang Aktif</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['kurang_aktif'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Tidak Pernah Login -->
        <div class="glass-card p-6 border-t-4 border-red-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="shield-alert" class="w-6 h-6 text-red-500"></i>
                </div>
                <div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-1">Belum Pernah Login</p>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['tidak_pernah_login'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="glass-card p-6 mb-6" x-data="{ showFilters: {{ request()->anyFilled(['status', 'tipe', 'program_keahlian_id']) ? 'true' : 'false' }} }">
        <form action="{{ route('pokja.monitoring.index') }}" method="GET">
            <!-- Main Search Row -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4">
                <!-- Search Input -->
                <div class="flex-1 relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pembimbing atau NIP..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                </div>

                <!-- Actions Button Row -->
                <div class="flex items-center gap-2 shrink-0">
                    <!-- Toggle Filter Panel Button -->
                    <button type="button" @click="showFilters = !showFilters" 
                            :class="showFilters ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200/50 dark:border-slate-700/50'" 
                            class="px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 h-[42px] cursor-pointer">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                        <span>Filter</span>
                        <span x-show="showFilters" class="ml-1 text-sm leading-none">&times;</span>
                    </button>

                    <!-- Submit Button -->
                    <button type="submit" class="px-6 py-2.5 bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold rounded-xl transition-all text-xs h-[42px] flex items-center justify-center cursor-pointer">
                        Cari
                    </button>

                    <!-- Reset Button -->
                    @if(request()->anyFilled(['search', 'status', 'tipe', 'program_keahlian_id']))
                        <a href="{{ route('pokja.monitoring.index') }}" 
                           class="px-4 py-2.5 text-slate-500 hover:text-red-400 text-xs flex items-center justify-center gap-1.5 transition-colors border border-slate-200/50 dark:border-slate-700/50 rounded-xl h-[42px] shrink-0 bg-white dark:bg-slate-800 cursor-pointer">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Expandable Advanced Filters Panel -->
            <div x-show="showFilters" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="mt-4 p-5 bg-slate-50/50 dark:bg-slate-800/30 border border-slate-200/40 dark:border-slate-700/40 rounded-xl"
                 x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Status Filter -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Status Keaktifan</label>
                        <select name="status" onchange="this.form.submit()" 
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-xs font-semibold">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif (Tepat Waktu & Tertib)</option>
                            <option value="kurang_aktif" {{ request('status') === 'kurang_aktif' ? 'selected' : '' }}>Kurang Aktif (Tunggakan ACC / Jarang Login)</option>
                            <option value="tidak_pernah_login" {{ request('status') === 'tidak_pernah_login' ? 'selected' : '' }}>Belum Login Sama Sekali</option>
                        </select>
                    </div>

                    <!-- Tipe Filter -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Tipe Tugas</label>
                        <select name="tipe" onchange="this.form.submit()" 
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-xs font-semibold">
                            <option value="">Semua Tipe</option>
                            <option value="kejuruan" {{ request('tipe') === 'kejuruan' ? 'selected' : '' }}>Kejuruan (Produktif)</option>
                            <option value="umum" {{ request('tipe') === 'umum' ? 'selected' : '' }}>Umum (Normatif / Adaptif)</option>
                            <option value="keduanya" {{ request('tipe') === 'keduanya' ? 'selected' : '' }}>Kejuruan & Umum (Keduanya)</option>
                        </select>
                    </div>

                    <!-- Program Keahlian Filter -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Program Keahlian</label>
                        <select name="program_keahlian_id" onchange="this.form.submit()" 
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-xs font-semibold">
                            <option value="">Semua Program Keahlian</option>
                            @foreach($programKeahlians as $pk)
                                <option value="{{ $pk->id }}" {{ request('program_keahlian_id') == $pk->id ? 'selected' : '' }}>{{ $pk->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Mentors Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($mentors as $mentor)
            @php
                $statusColors = [
                    'aktif' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                    'kurang_aktif' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                    'tidak_pernah_login' => 'bg-red-500/10 text-red-500 border-red-500/20 animate-pulse'
                ];
                $statusLabels = [
                    'aktif' => 'AKTIF',
                    'kurang_aktif' => 'KURANG AKTIF',
                    'tidak_pernah_login' => 'BELUM LOGIN'
                ];
            @endphp
            <div class="glass-card p-5 sm:p-6 flex flex-col justify-between border-t-2 {{ $mentor->activity_status === 'aktif' ? 'border-emerald-500/30' : ($mentor->activity_status === 'kurang_aktif' ? 'border-amber-500/30' : 'border-red-500/30') }}">
                <div>
                    <!-- Header Info -->
                    <div class="flex items-start justify-between gap-2 mb-4">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-blue-600/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                                <i data-lucide="user-check" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 leading-tight">{{ $mentor->nama_lengkap }}</h3>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono tracking-wider uppercase mt-0.5">{{ $mentor->nip ?: 'NIP TIDAK ADA' }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black border tracking-wider shrink-0 {{ $statusColors[$mentor->activity_status] }}">
                            {{ $statusLabels[$mentor->activity_status] }}
                        </span>
                    </div>

                    <!-- Details Row -->
                    <div class="space-y-2 py-3 border-y border-slate-200/50 dark:border-slate-700/50 mb-4 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 dark:text-slate-400">Konsentrasi Keahlian</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200 text-right truncate max-w-[150px]" title="{{ $mentor->konsentrasiKeahlian?->nama }}">
                                {{ $mentor->konsentrasiKeahlian?->kode ?: ($mentor->konsentrasiKeahlian?->nama ?: '-') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 dark:text-slate-400">Tipe Pembimbing</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200 capitalize">
                                {{ $mentor->tipe === 'keduanya' ? 'Kejuruan & Umum' : $mentor->tipe }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 dark:text-slate-400">Login Terakhir</span>
                            @if($mentor->user?->last_login_at)
                                <span class="font-semibold text-slate-800 dark:text-slate-200" title="{{ $mentor->user->last_login_at->isoFormat('D MMMM YYYY, HH:mm') }}">
                                    {{ $mentor->user->last_login_at->diffForHumans() }}
                                </span>
                            @else
                                <span class="font-semibold text-red-500 dark:text-red-400 animate-pulse-slow">
                                    Belum pernah login
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Task Performance Mini Cards -->
                    <div class="grid grid-cols-3 gap-2 mb-6">
                        <!-- Siswa Bimbingan -->
                        <div class="bg-slate-50/50 dark:bg-slate-800/30 p-2.5 rounded-xl border border-slate-200/40 dark:border-slate-700/40 text-center">
                            <p class="text-[9px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider mb-0.5">Siswa</p>
                            <p class="text-sm font-extrabold text-slate-800 dark:text-slate-200">{{ $mentor->siswa_count }}</p>
                        </div>
                        <!-- Jurnal Pending -->
                        <div class="bg-slate-50/50 dark:bg-slate-800/30 p-2.5 rounded-xl border border-slate-200/40 dark:border-slate-700/40 text-center">
                            <p class="text-[9px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider mb-0.5">Jurnal</p>
                            <div class="flex items-center justify-center gap-1">
                                <span class="text-sm font-extrabold {{ $mentor->pending_jurnals_count > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-slate-800 dark:text-slate-200' }}">
                                    {{ $mentor->pending_jurnals_count }}
                                </span>
                                <span class="text-[10px] text-slate-400">/ {{ $mentor->total_jurnals_count }}</span>
                            </div>
                        </div>
                        <!-- Absensi Pending -->
                        <div class="bg-slate-50/50 dark:bg-slate-800/30 p-2.5 rounded-xl border border-slate-200/40 dark:border-slate-700/40 text-center">
                            <p class="text-[9px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider mb-0.5">Absen</p>
                            <div class="flex items-center justify-center gap-1">
                                <span class="text-sm font-extrabold {{ $mentor->pending_absensis_count > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-slate-800 dark:text-slate-200' }}">
                                    {{ $mentor->pending_absensis_count }}
                                </span>
                                <span class="text-[10px] text-slate-400">/ {{ $mentor->total_absensis_count }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <a href="{{ route('pokja.monitoring.show', $mentor) }}" class="w-full py-2.5 flex items-center justify-center gap-2 text-xs font-bold bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-all border border-slate-200/50 dark:border-slate-700/50 shadow-sm">
                    <i data-lucide="eye" class="w-4 h-4 text-blue-500"></i>
                    LIHAT DETAIL BIMBINGAN
                </a>
            </div>
        @empty
            <div class="col-span-full py-16 text-center glass-card">
                <i data-lucide="users" class="w-12 h-12 text-slate-400 mx-auto mb-3"></i>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Tidak Ada Data Pembimbing</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Tidak ada pembimbing sekolah yang cocok dengan kriteria pencarian Anda.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
