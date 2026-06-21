<x-app-layout>
    <x-slot name="header">Review & Pemetaan Tujuan Pembelajaran</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.kompetensi.import-pdf.form') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Scan PDF
        </a>
    </div>

    <form action="{{ route('pokja.kompetensi.import-pdf.store') }}" method="POST" class="space-y-8" x-data="{ activeTab: 'AKL' }">
        @csrf

        {{-- Top Bar Actions --}}
        <div class="glass-card p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Review Hasil Ekstraksi</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Silakan tinjau dan sunting data hasil scan otomatis sebelum disimpan ke database.</p>
            </div>
            <div class="flex items-center gap-6">
                <label class="relative inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" name="clear_old" value="1" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Bersihkan data lama per jurusan sebelum impor
                    </span>
                </label>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                    <i data-lucide="check-square" class="w-5 h-5"></i>
                    Simpan ke Database
                </button>
            </div>
        </div>

        {{-- Main Mapping Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Tabs / Sidebar --}}
            <div class="lg:col-span-1 space-y-3">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-2">Jurusan dari PDF</p>
                <div class="glass-card p-2 flex flex-row lg:flex-col overflow-x-auto lg:overflow-x-visible gap-1 whitespace-nowrap">
                    @foreach($parsedData as $key => $section)
                        <button type="button" 
                                @click="activeTab = '{{ $key }}'"
                                :class="activeTab === '{{ $key }}' ? 'bg-blue-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                                class="w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all flex items-center justify-between gap-3">
                            <span class="flex items-center gap-2">
                                <i data-lucide="folder" class="w-4 h-4"></i>
                                {{ $key }} ({{ $section['konsentrasi'] }})
                            </span>
                            <span class="px-1.5 py-0.5 text-[10px] bg-black/20 text-white rounded-md">
                                @php
                                    $tpCount = 0;
                                    foreach($section['elemens'] as $el) $tpCount += count($el['tps']);
                                @endphp
                                {{ $tpCount }} TP
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Contents --}}
            <div class="lg:col-span-3">
                @foreach($parsedData as $key => $section)
                    <div x-show="activeTab === '{{ $key }}'" class="space-y-6 animate-in fade-in duration-200" style="display: none;">
                        {{-- Mapped concentration selector --}}
                        <div class="glass-card p-6 border-l-4 border-l-emerald-500 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" name="sections[{{ $key }}][import]" value="1" checked id="import_{{ $key }}"
                                       class="w-5 h-5 text-blue-600 bg-slate-100 border-slate-300 rounded-lg focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 mt-1">
                                <div>
                                    <label for="import_{{ $key }}" class="font-bold text-slate-800 dark:text-slate-100 cursor-pointer">Import Kurikulum Jurusan Ini</label>
                                    <p class="text-xs text-slate-500">Uncheck jika tidak ingin mengimpor kurikulum jurusan ini.</p>
                                </div>
                            </div>
                            <div class="w-full md:w-80">
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2">Petakan ke Jurusan di Database:</label>
                                <select name="sections[{{ $key }}][konsentrasi_keahlian_id]" class="w-full px-3 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm">
                                    @foreach($concentrations as $con)
                                        <option value="{{ $con->id }}" {{ $section['mapped_id'] == $con->id ? 'selected' : '' }}>
                                            {{ $con->nama }} ({{ $con->programKeahlian->nama }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Loop through Elemen --}}
                        @foreach($section['elemens'] as $elIdx => $elemen)
                            <div class="glass-card p-6 space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Elemen Kompetensi:</label>
                                        <input type="text" name="sections[{{ $key }}][elemens][{{ $elIdx }}][nama]" value="{{ $elemen['nama'] }}"
                                               class="w-full px-4 py-2 bg-slate-100/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Capaian Pembelajaran (CP):</label>
                                        <textarea name="sections[{{ $key }}][elemens][{{ $elIdx }}][cp]" rows="3"
                                                  class="w-full px-4 py-2.5 bg-slate-100/50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm leading-relaxed resize-none">{{ $elemen['cp'] }}</textarea>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-slate-200/50 dark:border-slate-700/50">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Daftar Tujuan Pembelajaran (TP) yang Di-impor:</p>
                                    
                                    @if(empty($elemen['tps']))
                                        <p class="text-sm text-slate-500 italic">Tidak ada Tujuan Pembelajaran yang ditemukan untuk elemen ini.</p>
                                    @else
                                        <div class="space-y-3">
                                            @foreach($elemen['tps'] as $tpIdx => $tp)
                                                <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800/40 hover:border-blue-500/20 transition-all group">
                                                    <input type="checkbox" name="sections[{{ $key }}][elemens][{{ $elIdx }}][tps][{{ $tpIdx }}][selected]" value="1" checked
                                                           class="w-5 h-5 text-blue-600 bg-slate-100 border-slate-300 rounded-lg focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 mt-1 shrink-0">
                                                    <input type="text" name="sections[{{ $key }}][elemens][{{ $elIdx }}][tps][{{ $tpIdx }}][text]" value="{{ $tp }}"
                                                           class="w-full px-3 py-1 bg-transparent border-0 border-b border-transparent hover:border-slate-200/50 dark:hover:border-slate-700/50 focus:border-blue-500 focus:ring-0 text-sm text-slate-700 dark:text-slate-300">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </form>
</x-app-layout>
