<x-app-layout>
    <x-slot name="header">Pemetaan Siswa PKL</x-slot>

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-slate-600 dark:text-slate-400">Monitoring status penempatan siswa ke Industri dan Pembimbing.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('shared.pemetaan.maps') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <i data-lucide="map" class="w-4 h-4"></i> Lihat Peta
            </a>
            <div class="px-4 py-2 bg-blue-500/10 border border-blue-500/20 rounded-xl">
                <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest leading-none mb-1">Terpetakan</p>
                <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $terpetakan }} / {{ $totalSiswa }}</p>
            </div>
            <div class="px-4 py-2 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                <p class="text-[10px] text-amber-400 font-black uppercase tracking-widest leading-none mb-1">Belum Lengkap</p>
                <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $totalSiswa - $terpetakan }}</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="glass-card mb-6 p-4">
        <form action="{{ route('pokja.pemetaan.index') }}" method="GET" class="flex gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau NIS..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-white/50 dark:bg-slate-800/50 border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 transition-all text-slate-800 dark:text-slate-200">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
                FILTER
            </button>
        </form>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/50 dark:bg-slate-800/50 border-b border-slate-200/50 dark:border-slate-700/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Siswa</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Tempat PKL (DUDI)</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Pembimbing Sekolah</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Pembimbing DUDI</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($siswas as $siswa)
                        <tr class="hover:bg-white dark:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $siswa->nama_lengkap }}</p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono tracking-wider">{{ $siswa->nis }} | {{ $siswa->konsentrasiKeahlian->nama_konsentrasi ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                @if($siswa->dudi)
                                    <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $siswa->dudi->nama_perusahaan }}</span>
                                @else
                                    <span class="text-slate-600 italic">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                @if($siswa->pembimbingSekolah)
                                    <span class="text-slate-700 dark:text-slate-300">{{ $siswa->pembimbingSekolah->nama_lengkap }}</span>
                                @else
                                    <span class="text-slate-600 italic">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                @if($siswa->pembimbingDudi)
                                    <span class="text-slate-700 dark:text-slate-300">{{ $siswa->pembimbingDudi->nama_lengkap }}</span>
                                @else
                                    <span class="text-slate-600 italic">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $isComplete = $siswa->dudi_id && $siswa->pembimbing_sekolah_id && $siswa->pembimbing_dudi_id;
                                @endphp
                                @if($isComplete)
                                    <span class="px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 text-[9px] font-black border border-emerald-500/20 uppercase tracking-tighter">Lengkap</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg bg-red-500/10 text-red-400 text-[9px] font-black border border-red-500/20 uppercase tracking-tighter">Belum Lengkap</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('pokja.siswa.edit', $siswa->id) }}" class="inline-flex items-center justify-center p-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:bg-slate-700 transition-colors text-slate-600 dark:text-slate-400 hover:text-blue-400">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="users" class="w-12 h-12 text-slate-700"></i>
                                    <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada data siswa ditemukan.</p>
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
