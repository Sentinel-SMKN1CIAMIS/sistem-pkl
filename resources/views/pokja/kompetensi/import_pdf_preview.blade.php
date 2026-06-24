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
                {{-- Modern Capsule Toggle Switch --}}
                <label class="inline-flex items-center cursor-pointer select-none group">
                    <input type="checkbox" name="clear_old" value="1" checked class="toggle-checkbox sr-only" id="toggle-clear">
                    <div class="relative inline-flex items-center">
                        <span class="toggle-bg w-14 h-7 bg-gradient-to-r from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-600 rounded-full transition-all duration-300 shadow-inner flex items-center"></span>
                        <span class="toggle-circle absolute left-1 top-1 w-5 h-5 bg-white rounded-full shadow-md transition-all duration-300 flex items-center justify-center">
                            <svg class="toggle-icon w-3 h-3 text-slate-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <span class="ml-3 text-sm font-semibold text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors whitespace-nowrap">
                            Bersihkan data lama per jurusan
                        </span>
                    </div>
                </label>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                    <i data-lucide="check-square" class="w-5 h-5"></i>
                    Simpan ke Database
                </button>
            </div>
        </div>

        <style>
            /* Toggle Switch Styles */
            .toggle-checkbox:checked ~ div .toggle-bg {
                background: linear-gradient(to right, rgb(59, 130, 246), rgb(37, 99, 235));
            }
            .toggle-checkbox:checked ~ div .toggle-circle {
                transform: translateX(1.75rem);
            }
            .toggle-checkbox:checked ~ div .toggle-icon {
                color: rgb(59, 130, 246);
            }
            .toggle-checkbox:focus ~ div .toggle-bg {
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
            }
        </style>

        {{-- Main Mapping Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Tabs / Sidebar --}}
            <div class="lg:col-span-1 space-y-3 min-w-0">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-2">Jurusan dari PDF</p>
                <div class="glass-card p-2 flex flex-row lg:flex-col overflow-x-auto lg:overflow-y-auto gap-1 whitespace-nowrap lg:whitespace-normal">
                    @foreach($parsedData as $key => $section)
                        <button type="button" 
                                @click="activeTab = '{{ $key }}'"
                                :class="activeTab === '{{ $key }}' ? 'bg-blue-600 text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                                class="w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all flex items-center justify-between gap-3 whitespace-normal lg:whitespace-normal">
                            <span class="flex items-start gap-2 min-w-0">
                                <i data-lucide="folder" class="w-4 h-4 mt-0.5 shrink-0"></i>
                                <span class="break-words">{{ $key }} ({{ $section['konsentrasi'] }})</span>
                            </span>
                            <span class="px-1.5 py-0.5 text-[10px] bg-black/20 text-white rounded-md shrink-0">
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
                            <div class="flex items-start gap-4">
                                {{-- Modern Capsule Toggle for Import --}}
                                <label class="inline-flex items-center cursor-pointer select-none group">
                                    <input type="checkbox" name="sections[{{ $key }}][import]" value="1" checked id="import_{{ $key }}" class="toggle-checkbox-{{ $key }} sr-only">
                                    <div class="relative inline-flex items-center">
                                        <div class="relative">
                                            <span class="toggle-bg-{{ $key }} block w-14 h-7 bg-gradient-to-r from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-600 rounded-full transition-all duration-300 shadow-inner"></span>
                                            <span class="toggle-circle-{{ $key }} absolute left-1 top-1 w-5 h-5 bg-white rounded-full shadow-md transition-all duration-300 flex items-center justify-center">
                                                <svg class="toggle-icon-{{ $key }} w-3 h-3 text-slate-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="font-bold text-sm text-slate-800 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Import Kurikulum Jurusan Ini</div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Nonaktifkan switch ini jika tidak ingin mengimpor.</p>
                                        </div>
                                    </div>
                                </label>
                                
                                <style>
                                    .toggle-checkbox-{{ $key }}:checked ~ div .toggle-bg-{{ $key }} {
                                        background: linear-gradient(to right, rgb(16, 185, 129), rgb(5, 150, 105));
                                    }
                                    .toggle-checkbox-{{ $key }}:checked ~ div .toggle-circle-{{ $key }} {
                                        transform: translateX(1.75rem);
                                    }
                                    .toggle-checkbox-{{ $key }}:checked ~ div .toggle-icon-{{ $key }} {
                                        color: rgb(16, 185, 129);
                                    }
                                    .toggle-checkbox-{{ $key }}:focus ~ div .toggle-bg-{{ $key }} {
                                        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
                                    }
                                </style>
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
