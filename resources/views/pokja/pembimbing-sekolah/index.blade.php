<x-app-layout>
    <x-slot name="header">Kelola Pembimbing Sekolah</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-600 dark:text-slate-400">Daftar guru pembimbing sekolah per konsentrasi keahlian.</p>
        <a href="{{ route('pokja.pembimbing_sekolah.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
            <i data-lucide="user-plus" class="w-5 h-5"></i>
            Tambah Pembimbing
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
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Nama Lengkap</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">NIP / Username</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Tipe Pembimbing</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Konsentrasi Keahlian</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($teachers as $item)
                        <tr class="hover:bg-white dark:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-emerald-400 font-bold">
                                        {{ substr($item->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block truncate">{{ $item->nama_lengkap }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $item->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-slate-700 dark:text-slate-300 block font-mono">{{ $item->nip ?? '-' }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                    <i data-lucide="user" class="w-3 h-3"></i>
                                    {{ $item->user->username }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $tipeColors = [
                                        'produktif' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'normatif' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                        'adaptif' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-md border text-[10px] uppercase font-bold {{ $tipeColors[$item->tipe] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                    {{ $item->tipe }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-slate-600 dark:text-slate-400">
                                    {{ $item->konsentrasiKeahlian->nama }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('pokja.pembimbing_sekolah.show', $item) }}" class="p-2 text-slate-600 dark:text-slate-400 hover:text-emerald-400 transition-colors" title="Lihat Siswa">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('pokja.pembimbing_sekolah.edit', $item) }}" class="p-2 text-slate-600 dark:text-slate-400 hover:text-blue-400 transition-colors" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('pokja.pembimbing_sekolah.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pembimbing ini?')">
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
                                Belum ada data pembimbing sekolah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($teachers->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
