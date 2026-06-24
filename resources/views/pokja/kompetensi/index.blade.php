<x-app-layout>
    <x-slot name="header">Kelola Tujuan Pembelajaran (TP)</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <div class="flex-1">
                <p class="text-slate-600 dark:text-slate-400 text-sm">Kelola Elemen Kompetensi dan Tujuan Pembelajaran per Konsentrasi Keahlian.</p>
            </div>
        @if(auth()->user()->role !== 'kepala_sekolah')
        <div class="flex gap-3">
            <a href="{{ route('pokja.kompetensi.import-pdf.form') }}" class="px-4 py-2 text-sm whitespace-nowrap bg-slate-800 dark:bg-slate-700/50 hover:bg-slate-700 hover:text-white dark:hover:bg-slate-600 border border-slate-700 dark:border-slate-600/50 text-slate-200 font-medium rounded-xl shadow-lg transition-all flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                Scan & Import Buku Pedoman (PDF)
            </a>
            <a href="{{ route('pokja.kompetensi.create') }}" class="px-4 py-2 text-sm whitespace-nowrap bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah TP
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
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

    @if(empty($groupedCompetencies))
        <div class="glass-card p-12 flex flex-col items-center justify-center text-slate-500 dark:text-slate-400">
            <i data-lucide="inbox" class="w-12 h-12 mb-4 text-slate-300 dark:text-slate-600"></i>
            <p class="text-sm font-medium">Belum ada data elemen kompetensi / TP.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($groupedCompetencies as $konsentrasiId => $konsentrasiGroup)
                @php $konsentrasi = $konsentrasiGroup['konsentrasi']; @endphp
                <div class="glass-card overflow-hidden border border-blue-500/20" x-data="{ openKonsentrasi: true }">
                    <!-- Konsentrasi Header -->
                    <button @click="openKonsentrasi = !openKonsentrasi" class="w-full flex items-center justify-between p-4 bg-blue-50/50 dark:bg-blue-900/10 hover:bg-blue-100/50 dark:hover:bg-blue-900/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center shrink-0">
                                <i data-lucide="book" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="text-left">
                                <h3 class="font-bold text-slate-800 dark:text-slate-200">
                                    {{ $konsentrasi ? $konsentrasi->nama : 'Tanpa Konsentrasi' }}
                                </h3>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ $konsentrasi ? $konsentrasi->kode : '-' }}</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-200" :class="openKonsentrasi ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Elemen List -->
                    <div x-show="openKonsentrasi" x-transition>
                        <div class="p-4 space-y-4">
                            @foreach($konsentrasiGroup['elemen'] as $elemenName => $elemenGroup)
                                <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-sm" x-data="{ openElemen: false }">
                                    <button @click="openElemen = !openElemen" class="w-full flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-6 h-6 rounded-md bg-emerald-500/10 flex items-center justify-center shrink-0">
                                                <i data-lucide="layers" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                                            </div>
                                            <div class="text-left text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                {{ $elemenName }}
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400">
                                                {{ count($elemenGroup) }} CP
                                            </span>
                                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openElemen ? 'rotate-180' : ''"></i>
                                        </div>
                                    </button>

                                    <!-- CP List -->
                                    <div x-show="openElemen" x-transition>
                                        <div class="p-3 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 space-y-4">
                                            @foreach($elemenGroup as $cpName => $tpList)
                                                <div class="pl-2 border-l-2 border-indigo-200 dark:border-indigo-800">
                                                    <div class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 mb-2 pl-2 tracking-wide uppercase flex items-center gap-1.5">
                                                        <i data-lucide="target" class="w-3 h-3"></i> CP: {{ $cpName }}
                                                    </div>
                                                    
                                                    <div class="space-y-2 pl-2">
                                                        @foreach($tpList as $item)
                                                            <div class="flex items-start justify-between gap-4 p-3 rounded-lg border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 hover:border-slate-200 dark:hover:border-slate-700 hover:shadow-sm transition-all group">
                                                                <div class="flex-1">
                                                                    <div class="flex items-start gap-2">
                                                                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500 mt-0.5 shrink-0"></i>
                                                                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ $item->tp ?? 'Tanpa TP' }}</p>
                                                                    </div>
                                                                </div>
                                                                @if(auth()->user()->role !== 'kepala_sekolah')
                                                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                                                    <a href="{{ route('pokja.kompetensi.edit', $item) }}" class="p-1.5 text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="Edit">
                                                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                                                    </a>
                                                                    <form action="{{ route('pokja.kompetensi.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus TP ini?')" class="inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Hapus">
                                                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
