<x-app-layout>
    <x-slot name="header">Kelola Tujuan Pembelajaran (TP)</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-600 dark:text-slate-400">Kelola Elemen Kompetensi dan Tujuan Pembelajaran per Konsentrasi Keahlian.</p>
        @if(auth()->user()->role !== 'kepala_sekolah')
        <div class="flex gap-3">
            <a href="{{ route('pokja.kompetensi.import-pdf.form') }}" class="px-5 py-2.5 bg-slate-800 dark:bg-slate-700/50 hover:bg-slate-700 hover:text-white dark:hover:bg-slate-600 border border-slate-700 dark:border-slate-600/50 text-slate-200 font-medium rounded-xl shadow-lg transition-all flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5 text-emerald-500"></i>
                Scan & Import Buku Pedoman (PDF)
            </a>
            <a href="{{ route('pokja.kompetensi.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                Tambah TP
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Search and Filter -->
    <div class="glass-card p-4 mb-6">
        <form action="{{ route('pokja.kompetensi.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Elemen, CP, atau TP..." 
                       class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
            </div>
            <div class="md:w-64">
                <select name="konsentrasi_keahlian_id" onchange="this.form.submit()" 
                        class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
                    <option value="">Semua Konsentrasi Keahlian</option>
                    @foreach($concentrations as $con)
                        <option value="{{ $con->id }}" {{ request('konsentrasi_keahlian_id') == $con->id ? 'selected' : '' }}>
                            {{ $con->nama }} ({{ $con->kode }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl transition-all text-sm flex items-center gap-2 shadow-lg shadow-blue-500/25">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Cari
                </button>
                @if(request('search') || request('konsentrasi_keahlian_id'))
                    <a href="{{ route('pokja.kompetensi.index') }}" class="px-4 py-2 bg-slate-800 dark:bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-700 transition-all text-sm flex items-center gap-2 border border-slate-700">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Jurusan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Elemen Kompetensi</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Tujuan Pembelajaran (TP)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Capaian (CP)</th>
                        @if(auth()->user()->role !== 'kepala_sekolah')
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right whitespace-nowrap">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($compentencies as $item)
                        <tr class="hover:bg-white dark:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md bg-blue-500/10 border border-blue-500/20 text-[10px] font-bold text-blue-400 uppercase tracking-tighter">
                                    {{ $item->konsentrasiKeahlian->kode }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-800 dark:text-slate-200 leading-relaxed whitespace-nowrap">
                                {{ $item->nama }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 max-w-xs truncate" title="{{ $item->tp }}">
                                {{ $item->tp ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 max-w-xs truncate" title="{{ $item->cp }}">
                                {{ $item->cp ?? '-' }}
                            </td>
                            @if(auth()->user()->role !== 'kepala_sekolah')
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div x-data="{ open: false }" class="relative flex justify-end" x-on:click.away="open = false">
                                    <button x-on:click="open = !open" class="p-1.5 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none">
                                        <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                    </button>
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-8 w-32 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 shadow-lg py-1 z-50 text-left" 
                                         style="display: none;">
                                        <a href="{{ route('pokja.kompetensi.edit', $item) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-500"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('pokja.kompetensi.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-950/20 transition-colors text-left">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'kepala_sekolah' ? 4 : 5 }}" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada data elemen kompetensi / TP.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($compentencies->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $compentencies->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
