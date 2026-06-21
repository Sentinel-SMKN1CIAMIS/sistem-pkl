<x-app-layout>
    <x-slot name="header">Monitoring Jurnal & Siswa Bimbingan</x-slot>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <div class="mb-6">
        <p class="text-slate-600 dark:text-slate-400">Pantau aktivitas pengisian jurnal harian dan kelola absensi siswa bimbingan Anda secara real-time.</p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Quick Stats Grid (4 columns on mobile & desktop) -->
    <div class="grid grid-cols-4 sm:grid-cols-4 gap-3 sm:gap-6 mb-6">
        <!-- Total Siswa -->
        <div class="glass-card p-3 sm:p-6 border-t-4 border-blue-500 bg-white/5 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center sm:justify-between gap-1 transition-all duration-200 hover:scale-[1.02]">
            <div class="text-center sm:text-left">
                <span class="hidden sm:block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Siswa</span>
                <span class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white mt-1 leading-none block">{{ $students->count() }}</span>
                <span class="block sm:hidden text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Siswa</span>
            </div>
            <div class="hidden sm:flex p-2.5 bg-blue-500/10 rounded-xl text-blue-500 shrink-0">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Sudah Isi -->
        <div class="glass-card p-3 sm:p-6 border-t-4 border-emerald-500 bg-white/5 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center sm:justify-between gap-1 transition-all duration-200 hover:scale-[1.02]">
            <div class="text-center sm:text-left">
                <span class="hidden sm:block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sudah Isi</span>
                <span class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white mt-1 leading-none block">{{ $studentsHasFilledToday->count() }}</span>
                <span class="block sm:hidden text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Sudah</span>
            </div>
            <div class="hidden sm:flex p-2.5 bg-emerald-500/10 rounded-xl text-emerald-500 shrink-0">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Belum Isi -->
        <div class="glass-card p-3 sm:p-6 border-t-4 border-rose-500 bg-white/5 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center sm:justify-between gap-1 transition-all duration-200 hover:scale-[1.02]">
            <div class="text-center sm:text-left">
                <span class="hidden sm:block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Belum Isi</span>
                <span class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white mt-1 leading-none block">{{ $studentsNotFilledToday->count() }}</span>
                <span class="block sm:hidden text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Belum</span>
            </div>
            <div class="hidden sm:flex p-2.5 bg-rose-500/10 rounded-xl text-rose-500 shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Menunggu Approval -->
        <div class="glass-card p-3 sm:p-6 border-t-4 border-amber-500 bg-white/5 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center sm:justify-between gap-1 transition-all duration-200 hover:scale-[1.02]">
            <div class="text-center sm:text-left">
                <span class="hidden sm:block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pending</span>
                <span class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white mt-1 leading-none block">{{ $studentsPendingApproval->sum('pending_jurnal_count') }}</span>
                <span class="block sm:hidden text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Pending</span>
            </div>
            <div class="hidden sm:flex p-2.5 bg-amber-500/10 rounded-xl text-amber-500 shrink-0">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Search Form (Responsive Layout) -->
    <div class="glass-card p-4 mb-6">
        <form action="{{ route('pembimbing_sekolah.siswa.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Siswa bimbingan berdasarkan nama atau NIS..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 sm:flex-initial px-6 py-2.5 bg-slate-800 dark:bg-slate-700 text-white font-bold rounded-xl hover:bg-slate-700 transition-all text-sm cursor-pointer shadow-md">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('pembimbing_sekolah.siswa.index') }}" class="px-4 py-2.5 text-slate-500 hover:text-red-400 text-sm flex items-center justify-center gap-2 transition-colors border border-slate-200/50 dark:border-slate-700 rounded-xl bg-slate-100/30">
                        <i data-lucide="x-circle" class="w-4 h-4"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Interactive Tabs with Alpine.js -->
    <div x-data="{ activeTab: 'belum-isi' }" class="space-y-6">
        <!-- Scrollable Tabs container for mobile -->
        <div class="flex flex-nowrap overflow-x-auto no-scrollbar border-b border-slate-200/50 dark:border-slate-700/50 gap-2 pb-0.5">
            <button @click="activeTab = 'belum-isi'" 
                    :class="activeTab === 'belum-isi' ? 'border-rose-500 text-rose-500' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    class="px-4 py-2.5 border-b-2 font-bold text-sm transition-all flex items-center gap-2 cursor-pointer shrink-0">
                Belum Isi
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-500/10 text-rose-500 font-black">{{ $studentsNotFilledToday->count() }}</span>
            </button>
            <button @click="activeTab = 'sudah-isi'" 
                    :class="activeTab === 'sudah-isi' ? 'border-emerald-500 text-emerald-500' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    class="px-4 py-2.5 border-b-2 font-bold text-sm transition-all flex items-center gap-2 cursor-pointer shrink-0">
                Sudah Isi
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-500/10 text-emerald-500 font-black">{{ $studentsHasFilledToday->count() }}</span>
            </button>
            <button @click="activeTab = 'butuh-approval'" 
                    :class="activeTab === 'butuh-approval' ? 'border-amber-500 text-amber-500' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    class="px-4 py-2.5 border-b-2 font-bold text-sm transition-all flex items-center gap-2 cursor-pointer shrink-0">
                Butuh Approval
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-500/10 text-amber-500 font-black">{{ $studentsPendingApproval->count() }}</span>
            </button>
            <button @click="activeTab = 'semua-siswa'" 
                    :class="activeTab === 'semua-siswa' ? 'border-blue-500 text-blue-500' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    class="px-4 py-2.5 border-b-2 font-bold text-sm transition-all flex items-center gap-2 cursor-pointer shrink-0">
                Semua & Rekap
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-500/10 text-blue-500 font-black">{{ $students->count() }}</span>
            </button>
        </div>

        <!-- Tab Content Panes -->
        
        <!-- Tab 1: Belum Isi Hari Ini -->
        <div x-show="activeTab === 'belum-isi'" class="glass-card overflow-hidden">
            <div class="p-5 border-b border-slate-200/30 dark:border-slate-800/50">
                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Siswa Belum Mengisi Jurnal Hari Ini</h4>
                <p class="text-xs text-slate-500 mt-1">Kirim pesan teguran / WhatsApp secara instan untuk mengingatkan siswa.</p>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/30 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Siswa</th>
                            <th class="px-6 py-4">Konsentrasi & Kelas</th>
                            <th class="px-6 py-4">No. HP</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/30 dark:divide-slate-700/50 text-sm">
                        @forelse($studentsNotFilledToday as $item)
                            <tr class="hover:bg-slate-100/50 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-rose-500/10 text-rose-500 font-bold flex items-center justify-center">
                                            {{ substr($item->nama_lengkap, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-slate-100 block">{{ $item->nama_lengkap }}</span>
                                            <span class="text-xs text-slate-500 font-mono">{{ $item->nis }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-slate-800 dark:text-slate-200 block">{{ $item->konsentrasiKeahlian->nama }}</span>
                                    <span class="text-xs text-slate-500">Kelas {{ $item->kelas }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300 font-mono">
                                    {{ $item->no_hp ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($item->no_hp)
                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $item->no_hp);
                                            if (str_starts_with($cleanPhone, '0')) {
                                                $cleanPhone = '62' . substr($cleanPhone, 1);
                                            }
                                            $waMessage = rawurlencode("Halo {$item->nama_lengkap}, saya guru pembimbing sekolah Anda. Mengingatkan agar segera mengisi Jurnal PKL harian hari ini di sistem. Terima kasih.");
                                        @endphp
                                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMessage }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/20">
                                            <i data-lucide="message-circle" class="w-4 h-4"></i> Hubungi via WA
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 italic">No HP tidak tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                    Semua siswa bimbingan Anda sudah mengisi jurnal untuk hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden p-4 space-y-4">
                @forelse($studentsNotFilledToday as $item)
                    <div class="glass-card p-4 flex flex-col gap-4 border border-slate-200/50 dark:border-slate-800 bg-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-rose-500/10 text-rose-500 font-bold flex items-center justify-center shrink-0">
                                {{ substr($item->nama_lengkap, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <span class="font-semibold text-slate-900 dark:text-slate-100 block truncate">{{ $item->nama_lengkap }}</span>
                                <span class="text-xs text-slate-500 font-mono">{{ $item->nis }} | Kelas {{ $item->kelas }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5 text-xs text-slate-600 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-850">
                            <div>Jurusan: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $item->konsentrasiKeahlian->nama }}</span></div>
                            <div>No. HP: <span class="font-mono font-medium text-slate-800 dark:text-slate-200">{{ $item->no_hp ?? '-' }}</span></div>
                        </div>
                        <div class="flex justify-end pt-1">
                            @if($item->no_hp)
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $item->no_hp);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                    $waMessage = rawurlencode("Halo {$item->nama_lengkap}, saya guru pembimbing sekolah Anda. Mengingatkan agar segera mengisi Jurnal PKL harian hari ini di sistem. Terima kasih.");
                                @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMessage }}" target="_blank"
                                   class="w-full text-center inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/20">
                                    <i data-lucide="message-circle" class="w-4 h-4"></i> Hubungi via WA
                                </a>
                            @else
                                <span class="text-xs text-slate-400 italic w-full text-center py-2 bg-slate-100 dark:bg-slate-900/40 rounded-xl">No HP tidak tersedia</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 dark:text-slate-400 italic text-sm">
                        Semua siswa bimbingan Anda sudah mengisi jurnal untuk hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 2: Sudah Isi Hari Ini -->
        <div x-show="activeTab === 'sudah-isi'" class="glass-card overflow-hidden" x-cloak>
            <div class="p-5 border-b border-slate-200/30 dark:border-slate-800/50">
                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Siswa Sudah Mengisi Jurnal Hari Ini</h4>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/30 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Siswa</th>
                            <th class="px-6 py-4">Pekerjaan</th>
                            <th class="px-6 py-4">Status Persetujuan</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/30 dark:divide-slate-700/50 text-sm">
                        @forelse($studentsHasFilledToday as $item)
                            <tr class="hover:bg-slate-100/50 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-emerald-500/10 text-emerald-500 font-bold flex items-center justify-center">
                                            {{ substr($item->nama_lengkap, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-slate-100 block">{{ $item->nama_lengkap }}</span>
                                            <span class="text-xs text-slate-500 font-mono">{{ $item->nis }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-slate-700 dark:text-slate-300 max-w-sm truncate font-medium">{{ $item->today_journal->deskripsi_pekerjaan }}</p>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">Dikirim pada {{ \Carbon\Carbon::parse($item->today_journal->created_at)->format('H:i') }} WIB</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $appStatus = $item->today_journal->approval_status ?? 'pending';
                                        if ($appStatus === 'approved') {
                                            $appClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                        } elseif ($appStatus === 'rejected') {
                                            $appClass = 'bg-red-500/10 text-red-500 border-red-500/20';
                                        } else {
                                            $appClass = 'bg-amber-500/10 text-amber-500 border-amber-500/20';
                                        }
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $appClass }}">
                                        {{ $appStatus }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('pembimbing_sekolah.jurnal.index', ['search' => $item->nama_lengkap]) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200/50 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-all">
                                        Detail Jurnal
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                    Belum ada siswa yang mengisi jurnal hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden p-4 space-y-4">
                @forelse($studentsHasFilledToday as $item)
                    <div class="glass-card p-4 flex flex-col gap-3 border border-slate-200/50 dark:border-slate-800 bg-white/5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-500 font-bold flex items-center justify-center shrink-0">
                                    {{ substr($item->nama_lengkap, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-900 dark:text-slate-100 block truncate">{{ $item->nama_lengkap }}</span>
                                    <span class="text-xs text-slate-500 font-mono">{{ $item->nis }} | Kelas {{ $item->kelas }}</span>
                                </div>
                            </div>
                            @php
                                $appStatus = $item->today_journal->approval_status ?? 'pending';
                                if ($appStatus === 'approved') {
                                    $appClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                } elseif ($appStatus === 'rejected') {
                                    $appClass = 'bg-red-500/10 text-red-500 border-red-500/20';
                                } else {
                                    $appClass = 'bg-amber-500/10 text-amber-500 border-amber-500/20';
                                }
                            @endphp
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase border tracking-wider shrink-0 {{ $appClass }}">
                                {{ $appStatus }}
                            </span>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl text-xs space-y-1.5">
                            <span class="text-slate-400 font-medium block">Pekerjaan Hari Ini:</span>
                            <p class="text-slate-700 dark:text-slate-300 font-semibold leading-relaxed">{{ $item->today_journal->deskripsi_pekerjaan }}</p>
                            <span class="text-[10px] text-slate-400 block pt-1 border-t border-slate-100 dark:border-slate-800/40">Dikirim: {{ \Carbon\Carbon::parse($item->today_journal->created_at)->format('H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-end pt-1">
                            <a href="{{ route('pembimbing_sekolah.jurnal.index', ['search' => $item->nama_lengkap]) }}"
                               class="w-full text-center inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold border border-slate-200/50 dark:border-slate-700 transition-all">
                                Detail Jurnal
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 dark:text-slate-400 italic text-sm">
                        Belum ada siswa yang mengisi jurnal hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 3: Jurnal Butuh Persetujuan -->
        <div x-show="activeTab === 'butuh-approval'" class="glass-card overflow-hidden" x-cloak>
            <div class="p-5 border-b border-slate-200/30 dark:border-slate-800/50">
                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Jurnal Menunggu Persetujuan Anda</h4>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/30 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Siswa</th>
                            <th class="px-6 py-4">Kelas & Jurusan</th>
                            <th class="px-6 py-4">Jurnal Tertunda</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/30 dark:divide-slate-700/50 text-sm">
                        @forelse($studentsPendingApproval as $item)
                            <tr class="hover:bg-slate-100/50 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-amber-500/10 text-amber-500 font-bold flex items-center justify-center">
                                            {{ substr($item->nama_lengkap, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-slate-100 block">{{ $item->nama_lengkap }}</span>
                                            <span class="text-xs text-slate-500 font-mono">{{ $item->nis }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-slate-800 dark:text-slate-200 block font-medium">{{ $item->konsentrasiKeahlian->nama }}</span>
                                    <span class="text-xs text-slate-500">Kelas {{ $item->kelas }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 bg-amber-500/10 text-amber-500 rounded-lg text-xs font-bold border border-amber-500/20">
                                        {{ $item->pending_jurnal_count }} Jurnal Pending
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('pembimbing_sekolah.jurnal.index', ['search' => $item->nama_lengkap]) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/20">
                                        <i data-lucide="check-square" class="w-3.5 h-3.5"></i> Periksa & Approve
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                    Seluruh jurnal bimbingan Anda telah disetujui.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden p-4 space-y-4">
                @forelse($studentsPendingApproval as $item)
                    <div class="glass-card p-4 flex flex-col gap-3 border border-slate-200/50 dark:border-slate-800 bg-white/5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-500 font-bold flex items-center justify-center shrink-0">
                                    {{ substr($item->nama_lengkap, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-900 dark:text-slate-100 block truncate">{{ $item->nama_lengkap }}</span>
                                    <span class="text-xs text-slate-500 font-mono">{{ $item->nis }} | Kelas {{ $item->kelas }}</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-500/10 text-amber-500 border border-amber-500/20 shrink-0">
                                {{ $item->pending_jurnal_count }} Pending
                            </span>
                        </div>
                        <div class="flex flex-col gap-1.5 text-xs text-slate-600 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-850">
                            <div>Konsentrasi: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $item->konsentrasiKeahlian->nama }}</span></div>
                        </div>
                        <div class="flex justify-end pt-1">
                            <a href="{{ route('pembimbing_sekolah.jurnal.index', ['search' => $item->nama_lengkap]) }}"
                               class="w-full text-center inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/20">
                                <i data-lucide="check-square" class="w-3.5 h-3.5"></i> Periksa & Approve
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 dark:text-slate-400 italic text-sm">
                        Seluruh jurnal bimbingan Anda telah disetujui.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 4: Semua Siswa & Rekap -->
        <div x-show="activeTab === 'semua-siswa'" class="glass-card overflow-hidden" x-cloak>
            <div class="p-5 border-b border-slate-200/30 dark:border-slate-800/50">
                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Semua Siswa Bimbingan & Rekapitulasi Jurnal</h4>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/30 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4">Siswa</th>
                            <th class="px-6 py-4">Penempatan DUDI</th>
                            <th class="px-6 py-4">Rekap Jurnal</th>
                            <th class="px-6 py-4">Absensi Hadir</th>
                            <th class="px-6 py-4 text-right">Status Hari Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/30 dark:divide-slate-700/50 text-sm">
                        @forelse($students as $item)
                            <tr class="hover:bg-slate-100/50 dark:hover:bg-slate-800/20 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500 font-bold">
                                            {{ substr($item->nama_lengkap, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-slate-100 block">{{ $item->nama_lengkap }}</span>
                                            <span class="text-xs text-slate-500 font-mono">{{ $item->nis }} | Kelas {{ $item->kelas }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->dudi)
                                        <span class="text-slate-800 dark:text-slate-200 block font-medium">{{ $item->dudi->nama }}</span>
                                        <span class="text-xs text-slate-500">{{ $item->dudi->kota }}</span>
                                    @else
                                        <span class="text-xs text-amber-500/80 bg-amber-500/5 px-2 py-0.5 rounded border border-amber-500/10 font-bold">Belum diplot</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400" title="Total Jurnal">
                                            TOT: {{ $item->jurnal_count }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20" title="Disetujui">
                                            ACC: {{ $item->approved_jurnal_count }}
                                        </span>
                                        @if($item->pending_jurnal_count > 0)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20" title="Pending">
                                                PND: {{ $item->pending_jurnal_count }}
                                            </span>
                                        @endif
                                        @if($item->rejected_jurnal_count > 0)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/10 text-red-500 border border-red-500/20" title="Ditolak">
                                                REJ: {{ $item->rejected_jurnal_count }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700 dark:text-slate-300 font-semibold">
                                    {{ $item->absensi_count }} Hari
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @php
                                        $hariIni = strtolower($item->status_hari_ini);
                                        if ($hariIni === 'masuk kerja') {
                                            $statusClass = 'bg-blue-500/10 text-blue-500 border-blue-500/20';
                                        } elseif ($hariIni === 'pulang kerja') {
                                            $statusClass = 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20';
                                        } elseif ($hariIni === 'selesai') {
                                            $statusClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                        } elseif ($hariIni === 'dibatalkan') {
                                            $statusClass = 'bg-red-500/10 text-red-500 border-red-500/20';
                                        } elseif ($hariIni === 'belum absen') {
                                            $statusClass = 'bg-slate-500/10 text-slate-500 border-slate-500/20';
                                        } else {
                                            $statusClass = 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20';
                                        }
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                        {{ $item->status_hari_ini }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                    Belum ada data siswa bimbingan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden p-4 space-y-4">
                @forelse($students as $item)
                    <div class="glass-card p-4 flex flex-col gap-3 border border-slate-200/50 dark:border-slate-800 bg-white/5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-500 font-bold flex items-center justify-center shrink-0">
                                    {{ substr($item->nama_lengkap, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-900 dark:text-slate-100 block truncate">{{ $item->nama_lengkap }}</span>
                                    <span class="text-xs text-slate-500 font-mono">{{ $item->nis }} | Kelas {{ $item->kelas }}</span>
                                </div>
                            </div>
                            @php
                                $hariIni = strtolower($item->status_hari_ini);
                                if ($hariIni === 'masuk kerja') {
                                    $statusClass = 'bg-blue-500/10 text-blue-500 border-blue-500/20';
                                } elseif ($hariIni === 'pulang kerja') {
                                    $statusClass = 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20';
                                } elseif ($hariIni === 'selesai') {
                                    $statusClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                } elseif ($hariIni === 'dibatalkan') {
                                    $statusClass = 'bg-red-500/10 text-red-500 border-red-500/20';
                                } elseif ($hariIni === 'belum absen') {
                                    $statusClass = 'bg-slate-500/10 text-slate-500 border-slate-500/20';
                                } else {
                                    $statusClass = 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20';
                                }
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider border shrink-0 {{ $statusClass }}">
                                {{ $item->status_hari_ini }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1.5 text-xs text-slate-600 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-850">
                            <div class="flex justify-between">
                                <span>Penempatan DUDI:</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">
                                    @if($item->dudi)
                                        {{ $item->dudi->nama }} ({{ $item->dudi->kota }})
                                    @else
                                        <span class="text-amber-500 font-bold">Belum diplot</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>Absensi Hadir:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $item->absensi_count }} Hari</span>
                            </div>
                            <div class="flex flex-col gap-1 mt-1.5">
                                <span class="text-slate-400 font-medium">Rekap Jurnal:</span>
                                <div class="flex flex-wrap items-center gap-1 mt-0.5">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        TOT: {{ $item->jurnal_count }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                        ACC: {{ $item->approved_jurnal_count }}
                                    </span>
                                    @if($item->pending_jurnal_count > 0)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                            PND: {{ $item->pending_jurnal_count }}
                                        </span>
                                    @endif
                                    @if($item->rejected_jurnal_count > 0)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-red-500/10 text-red-500 border border-red-500/20">
                                            REJ: {{ $item->rejected_jurnal_count }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 dark:text-slate-400 italic text-sm">
                        Belum ada data siswa bimbingan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
