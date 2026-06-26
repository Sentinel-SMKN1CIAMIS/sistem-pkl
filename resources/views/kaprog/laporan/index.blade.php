<x-app-layout>
    <x-slot name="header">Laporan Keseluruhan PKL Siswa</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="glass-card p-6 border-l-4 border-blue-500">
            <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Total Siswa</h3>
            <span class="text-3xl font-black text-slate-800 dark:text-slate-100">{{ $totalSiswa }}</span>
        </div>
        <div class="glass-card p-6 border-l-4 border-emerald-500">
            <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Sudah Penempatan PKL</h3>
            <span class="text-3xl font-black text-slate-800 dark:text-slate-100">{{ $siswaPkl }}</span>
        </div>
        <div class="glass-card p-6 border-l-4 border-amber-500">
            <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Belum Penempatan</h3>
            <span class="text-3xl font-black text-slate-800 dark:text-slate-100">{{ $siswaBelumPkl }}</span>
        </div>
    </div>

    {{-- Filter & Export Bar --}}
    <div class="glass-card p-4 mb-6">
        <form id="filterForm" method="GET" action="{{ route('kaprog.laporan.index') }}">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="w-full md:w-64">
                        <select name="kelas" onchange="this.form.submit()" 
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasOptions as $k)
                                <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(request()->filled('kelas'))
                        <a href="{{ route('kaprog.laporan.index') }}" class="px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-all flex items-center gap-2">
                            <i data-lucide="x" class="w-4 h-4"></i> Reset
                        </a>
                    @endif
                </div>
                
                {{-- Export Button --}}
                <div class="w-full md:w-auto">
                    <a href="{{ route('kaprog.laporan.export', ['kelas' => request('kelas')]) }}" 
                       class="w-full md:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-550 text-white text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Download Rekap PDF</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="glass-card p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 text-sm">
                        <th class="py-3 px-4 text-slate-500 font-medium">Siswa</th>
                        <th class="py-3 px-4 text-slate-500 font-medium">Kelas</th>
                        <th class="py-3 px-4 text-slate-500 font-medium">Tempat PKL (DUDI)</th>
                        <th class="py-3 px-4 text-slate-500 font-medium">Guru Pembimbing</th>
                        <th class="py-3 px-4 text-slate-500 font-medium">Status Penempatan</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($siswas as $siswa)
                    <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="py-3 px-4 font-medium text-slate-800 dark:text-slate-200">
                            {{ $siswa->nama_lengkap }}
                            <div class="text-xs text-slate-500 font-normal">{{ $siswa->nis }}</div>
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                            {{ $siswa->kelas }}
                        </td>
                        <td class="py-3 px-4">
                            @if($siswa->dudi)
                                <div class="text-slate-800 dark:text-slate-200 font-medium">{{ $siswa->dudi->nama }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $siswa->dudi->alamat }}</div>
                            @else
                                <span class="text-slate-400 italic">Belum ada tempat</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex flex-col gap-1 items-start">
                                @if($siswa->pembimbingSekolah)
                                    <span class="text-xs font-bold text-blue-500 uppercase tracking-wider block">KJ: {{ $siswa->pembimbingSekolah->nama_lengkap }}</span>
                                @endif
                                @if($siswa->pembimbingSekolahUmum)
                                    <span class="text-xs font-bold text-emerald-500 uppercase tracking-wider block">UM: {{ $siswa->pembimbingSekolahUmum->nama_lengkap }}</span>
                                @endif
                                @if(!$siswa->pembimbingSekolah && !$siswa->pembimbingSekolahUmum)
                                    <span class="text-slate-400 italic">Belum diplot</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            @if($siswa->dudi_id)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium">Sudah</span>
                            @else
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-medium">Belum</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500">Belum ada data siswa.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $siswas->links() }}
        </div>
    </div>
</x-app-layout>
