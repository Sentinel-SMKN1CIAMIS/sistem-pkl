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
                                <span class="text-slate-800 dark:text-slate-200">{{ $siswa->dudi->nama }}</span>
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
