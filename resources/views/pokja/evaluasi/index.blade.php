<x-app-layout>
    <x-slot name="header">Evaluasi Progres Siswa</x-slot>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-black uppercase tracking-widest">Total Siswa</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $totalSiswa }}</p>
                </div>
            </div>
            <div class="h-1.5 w-full bg-white dark:bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500" style="width: 100%"></div>
            </div>
        </div>

        <div class="glass-card p-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i data-lucide="file-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-black uppercase tracking-widest">Laporan Masuk</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $laporanMasuk }}</p>
                </div>
            </div>
            <div class="h-1.5 w-full bg-white dark:bg-slate-800 rounded-full overflow-hidden">
                @php $laporanPercentage = $totalSiswa > 0 ? ($laporanMasuk / $totalSiswa) * 100 : 0 @endphp
                <div class="h-full bg-emerald-500" style="width: {{ $laporanPercentage }}%"></div>
            </div>
        </div>

        <div class="glass-card p-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-black uppercase tracking-widest">Rata-rata Jurnal Valid</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($rataJurnal, 0) }}</p>
                </div>
            </div>
            <div class="h-1.5 w-full bg-white dark:bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-amber-500" style="width: 70%"></div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card mb-6 p-4">
        <form action="{{ route('pokja.evaluasi.index') }}" method="GET" class="flex gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau NIS..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-white/50 dark:bg-slate-800/50 border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 transition-all text-slate-800 dark:text-slate-200">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20">
                FILTER
            </button>
        </form>
    </div>

    <!-- Data Table -->
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/50 dark:bg-slate-800/50 border-b border-slate-200/50 dark:border-slate-700/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Siswa</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Progress Jurnal</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center whitespace-nowrap">Kehadiran</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Laporan Akhir</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Status Evaluasi</th>
                        @if(auth()->user()->role !== 'kepala_sekolah')
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center whitespace-nowrap">Rapor</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($siswas as $siswa)
                        <tr class="hover:bg-white dark:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $siswa->nama_lengkap }}</p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono tracking-wider">{{ $siswa->nis }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-1.5 bg-white dark:bg-slate-800 rounded-full overflow-hidden min-w-[100px]">
                                        @php 
                                            $journalPercent = $siswa->total_jurnal > 0 ? ($siswa->valid_jurnal / $siswa->total_jurnal) * 100 : 0;
                                        @endphp
                                        <div class="h-full bg-blue-500" style="width: {{ $journalPercent }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $siswa->valid_jurnal }} dari {{ $siswa->total_jurnal }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="px-3 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-mono text-slate-700 dark:text-slate-300">
                                    {{ $siswa->absensi_count }} HARI
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($siswa->laporan)
                                    <div class="flex items-center gap-2 text-emerald-400">
                                        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                        <span class="text-xs font-bold uppercase tracking-tighter">Sudah Unggah</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-red-500/50">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                        <span class="text-xs font-bold uppercase tracking-tighter italic">Belum Ada</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $isReady = $siswa->valid_jurnal >= 10 && $siswa->laporan; // Simplified logic
                                @endphp
                                @if($isReady)
                                    <span class="px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 text-[9px] font-black border border-emerald-500/20 uppercase">Siap Selesai</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-[9px] font-black border border-amber-500/20 uppercase">Sedang PKL</span>
                                @endif
                            </td>
                            @if(auth()->user()->role !== 'kepala_sekolah')
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <button class="p-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-600 dark:text-slate-400 hover:text-blue-400">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'kepala_sekolah' ? 5 : 6 }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="clipboard-list" class="w-12 h-12 text-slate-700"></i>
                                    <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada data evaluasi yang tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswas->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
