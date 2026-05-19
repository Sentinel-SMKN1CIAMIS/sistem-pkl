<x-app-layout>
    <x-slot name="header">Kelola Data DUDI</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-slate-600 dark:text-slate-400">Dunia Usaha & Dunia Industri (DUDI) per Konsentrasi Keahlian.</p>
        <a href="{{ route('pokja.dudi.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
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

    <!-- Filters -->
    <div class="glass-card p-4 mb-6">
        <form action="{{ route('pokja.dudi.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Industri, Kota, atau Bidang Usaha..." 
                       class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
            </div>
            <div class="md:w-64">
                <select name="konsentrasi" onchange="this.form.submit()" 
                        class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    <option value="">Semua Konsentrasi</option>
                    @foreach($concentrations as $c)
                        <option value="{{ $c->id }}" {{ request('konsentrasi') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="hidden md:block px-6 py-2 bg-slate-800 dark:bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-700 transition-all text-sm">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'konsentrasi']))
                <a href="{{ route('pokja.dudi.index') }}" class="px-4 py-2 text-slate-500 hover:text-red-400 text-sm flex items-center gap-2 transition-colors">
                    <i data-lucide="x-circle" class="w-4 h-4"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div class="glass-card overflow-hidden text-slate-700 dark:text-slate-300">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Nama Industri</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Konsentrasi Keahlian</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Lokasi</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Pimpinan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($dudis as $item)
                        <tr class="hover:bg-white dark:bg-slate-800/20 transition-colors group text-slate-700 dark:text-slate-300">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block">{{ $item->nama }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 italic">{{ $item->bidang_usaha ?? 'Bidang usaha -' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @if($item->konsentrasiKeahlians->isNotEmpty())
                                        @foreach($item->konsentrasiKeahlians as $k)
                                            <span class="px-2.5 py-1 rounded-md bg-blue-500/10 border border-blue-500/20 text-xs text-blue-400">
                                                {{ $k->nama }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="px-2.5 py-1 rounded-md bg-blue-500/10 border border-blue-500/20 text-xs text-blue-400">
                                            {{ $item->konsentrasiKeahlian->nama }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ $item->kota }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ $item->nama_pimpinan ?? '-' }}
                            </td>
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
                                        <a href="{{ route('pokja.dudi.edit', $item) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-500"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('pokja.dudi.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data DUDI ini?')">
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
