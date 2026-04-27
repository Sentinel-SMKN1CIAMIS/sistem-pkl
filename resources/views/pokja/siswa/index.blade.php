<x-app-layout>
    <x-slot name="header">Kelola Data Siswa PKL</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-600 dark:text-slate-400">Total data siswa yang terdaftar dalam sistem PKL.</p>
        <a href="{{ route('pokja.siswa.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
            <i data-lucide="user-plus" class="w-5 h-5"></i>
            Tambah Siswa
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Konsentrasi Keahlian / Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Penempatan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($students as $item)
                        <tr class="hover:bg-white dark:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-blue-400 font-bold">
                                        {{ substr($item->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block">{{ $item->nama_lengkap }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $item->nis }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-800 dark:text-slate-200 block">{{ $item->konsentrasiKeahlian->nama }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $item->kelas }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->dudi)
                                    <span class="text-sm text-slate-700 dark:text-slate-300 block">{{ $item->dudi->nama }}</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 italic">Guru: {{ $item->pembimbingSekolah->nama_lengkap ?? '-' }}</span>
                                @else
                                    <span class="text-xs text-amber-500/80 bg-amber-500/5 px-2 py-0.5 rounded border border-amber-500/10">Belum diplot</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
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
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('pokja.siswa.edit', $item) }}" class="p-2 text-slate-600 dark:text-slate-400 hover:text-blue-400 transition-colors" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('pokja.siswa.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini? Seluruh data terkait (jurnal, absensi, dll) juga akan terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-600 dark:text-slate-400 hover:text-red-400 transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada data siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
