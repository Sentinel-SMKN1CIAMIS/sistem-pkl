<x-app-layout>
    <x-slot name="header">Kelola Data DUDI</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-600 dark:text-slate-400">Dunia Usaha & Dunia Industri (DUDI) per Jurusan.</p>
        <a href="{{ route('pokja.dudi.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
            Tambah DUDI
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card overflow-hidden text-slate-700 dark:text-slate-300">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Nama Industri</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Jurusan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Pimpinan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($dudis as $item)
                        <tr class="hover:bg-white dark:bg-slate-800/20 transition-colors group text-slate-700 dark:text-slate-300">
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block">{{ $item->nama }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 italic">{{ $item->bidang_usaha ?? 'Bidang usaha -' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-md bg-blue-500/10 border border-blue-500/20 text-xs text-blue-400">
                                    {{ $item->konsentrasiKeahlian->nama }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $item->kota }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $item->nama_pimpinan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('pokja.dudi.edit', $item) }}" class="p-2 text-slate-600 dark:text-slate-400 hover:text-blue-400 transition-colors" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('pokja.dudi.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data DUDI ini?')">
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
                                Belum ada data DUDI.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dudis->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $dudis->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
