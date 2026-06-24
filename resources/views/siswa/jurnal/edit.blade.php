<x-app-layout>
    <x-slot name="header">Edit Jurnal Harian</x-slot>

    <div class="mb-6">
        <a href="{{ route('siswa.jurnal.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <div class="glass-card p-8">
            <form action="{{ route('siswa.jurnal.update', $jurnal) }}" method="POST" id="jurnal-form" class="space-y-6">
                @csrf
                @method('PUT')
                @php
                    $oldTpId = old('cp_id', $jurnal->cp_id);
                    $oldKompetensi = $oldTpId ? \App\Models\Kompetensi::find($oldTpId) : null;
                    $oldElemen = $oldKompetensi ? $oldKompetensi->nama : '';
                    $oldCP = $oldKompetensi ? $oldKompetensi->cp : '';
                @endphp
                <div id="jurnal-metadata" class="hidden"
                     data-kompetensis='@json($kompetensis)'
                     data-old-elemen="{{ $oldElemen }}"
                     data-old-cp="{{ $oldCP }}"
                     data-old-tpid="{{ $oldTpId }}"></div>
                <input type="hidden" name="foto_cropped" id="foto-cropped-input">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tanggal Kegiatan</label>
                        <div class="relative">
                            <input type="date" name="tanggal" id="tanggal" 
                                   value="{{ old('tanggal', $jurnal->tanggal) }}"
                                   min="{{ $minDate }}"
                                   max="{{ $maxDate }}"
                                   required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Anda dapat mengisi jurnal untuk maksimal 7 hari sebelumnya
                            </p>
                        </div>
                    </div>

                    {{-- Hidden inputs to satisfy backend model structure and validation --}}
                    <input type="hidden" name="kompetensi_id" id="kompetensi_id" value="{{ old('kompetensi_id', $jurnal->kompetensi_id) }}">
                    <input type="hidden" name="cp" id="hidden_cp" value="{{ old('cp', $jurnal->cp) }}">
                    <input type="hidden" name="cp_id" id="cp_id" value="{{ old('cp_id', $jurnal->cp_id) }}">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Elemen Kompetensi</label>
                        <div class="relative" id="elemen_wrapper">
                            <button type="button" id="elemen_btn" 
                                    class="w-full flex items-center justify-between px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-left">
                                <span id="elemen_btn_label" class="truncate">Pilih Elemen Kompetensi</span>
                                <span id="elemen_icon_container" class="shrink-0 ml-2">
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500"></i>
                                </span>
                            </button>
                            <div id="elemen_dropdown" class="hidden custom-dropdown absolute left-0 right-0 mt-2 max-h-72 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl z-50 overflow-hidden">
                                <div id="elemen_search_container" class="p-2 border-b border-slate-100 dark:border-slate-800 sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md z-10">
                                    <div class="relative">
                                        <input type="text" id="elemen_search" placeholder="Cari elemen..." autocomplete="off" class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300">
                                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="elemen_options" class="divide-y divide-slate-100 dark:divide-slate-800/50 overflow-y-auto max-h-56">
                                    <!-- Populated dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Capaian Pembelajaran (CP)</label>
                        <div class="relative" id="cp_wrapper">
                            <button type="button" id="cp_btn" disabled
                                    class="w-full flex items-center justify-between px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm text-slate-800 dark:text-slate-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-slate-200/50 dark:disabled:bg-slate-950/30 disabled:text-slate-400 dark:disabled:text-slate-500 text-left relative">
                                <span id="cp_btn_label" class="truncate text-slate-400 dark:text-slate-500">Pilih Elemen Kompetensi Terlebih Dahulu</span>
                                <span id="cp_icon_container" class="shrink-0 ml-2">
                                    <i data-lucide="lock" class="w-4 h-4 text-slate-400/80 dark:text-slate-500/80"></i>
                                </span>
                            </button>
                            <div id="cp_dropdown" class="hidden custom-dropdown absolute left-0 right-0 mt-2 max-h-72 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl z-50 overflow-hidden">
                                <div id="cp_search_container" class="p-2 border-b border-slate-100 dark:border-slate-800 sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md z-10">
                                    <div class="relative">
                                        <input type="text" id="cp_search" placeholder="Cari CP..." autocomplete="off" class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300">
                                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="cp_options" class="divide-y divide-slate-100 dark:divide-slate-800/50 overflow-y-auto max-h-56">
                                    <!-- Populated dynamically -->
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-1 font-medium">* CP disaring berdasarkan Elemen Kompetensi yang Anda pilih.</p>
                        
                        <!-- Full CP Card -->
                        <div id="cp_full_text_container" class="hidden mt-3 p-3.5 bg-blue-500/5 dark:bg-blue-500/10 border border-blue-500/10 dark:border-blue-500/20 text-xs text-slate-700 dark:text-slate-300 rounded-xl leading-relaxed">
                            <span class="font-bold text-blue-600 dark:text-blue-400 block mb-1 text-[10px] uppercase tracking-wider">Capaian Pembelajaran (CP) Lengkap:</span>
                            <p id="cp_full_text" class="whitespace-pre-line"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tujuan Pembelajaran (TP)</label>
                        <div class="relative" id="tp_wrapper">
                            <button type="button" id="tp_btn" disabled
                                    class="w-full flex items-center justify-between px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl text-sm text-slate-800 dark:text-slate-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-slate-200/50 dark:disabled:bg-slate-950/30 disabled:text-slate-400 dark:disabled:text-slate-500 text-left relative">
                                <span id="tp_btn_label" class="truncate text-slate-400 dark:text-slate-500">Pilih Capaian Pembelajaran (CP) Terlebih Dahulu</span>
                                <span id="tp_icon_container" class="shrink-0 ml-2">
                                    <i data-lucide="lock" class="w-4 h-4 text-slate-400/80 dark:text-slate-500/80"></i>
                                </span>
                            </button>
                            <div id="tp_dropdown" class="hidden custom-dropdown absolute left-0 right-0 mt-2 max-h-72 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl z-50 overflow-hidden">
                                <div id="tp_search_container" class="p-2 border-b border-slate-100 dark:border-slate-800 sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md z-10">
                                    <div class="relative">
                                        <input type="text" id="tp_search" placeholder="Cari TP..." autocomplete="off" class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300">
                                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="tp_options" class="divide-y divide-slate-100 dark:divide-slate-800/50 overflow-y-auto max-h-56">
                                    <!-- Populated dynamically -->
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-1 font-medium">* TP disaring berdasarkan Capaian Pembelajaran yang Anda pilih.</p>
                        
                        <!-- Full TP Card -->
                        <div id="tp_full_text_container" class="hidden mt-3 p-3.5 bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-500/10 dark:border-emerald-500/20 text-xs text-slate-700 dark:text-slate-300 rounded-xl leading-relaxed">
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 block mb-1 text-[10px] uppercase tracking-wider">Tujuan Pembelajaran (TP) Lengkap:</span>
                            <p id="tp_full_text" class="whitespace-pre-line"></p>
                        </div>
                    </div>

                    <!-- Status Absensi dan Alasan Alpha -->
                    <div class="md:col-span-2">
                        <div id="attendance-status-box" class="hidden p-4 rounded-xl text-sm items-center gap-3">
                            <span id="attendance-status-text" class="font-medium"></span>
                        </div>
                        
                        <div id="alasan-alpha-container" class="hidden mt-4">
                            <label for="alasan_alpha" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alasan Tidak Melakukan Absensi (Status: Alpha)</label>
                            <textarea name="alasan_alpha" id="alasan_alpha" rows="3"
                                      class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all resize-none"
                                      placeholder="Tulis alasan mengapa Anda tidak melakukan absensi pada tanggal ini..."></textarea>
                            <p class="text-xs text-rose-500 dark:text-rose-400 mt-1 font-medium">
                                *Catatan: Anda tidak melakukan absensi pada tanggal ini. Status absensi Anda akan tetap tercatat sebagai Alpha, namun Anda wajib memberikan alasan untuk dapat menyimpan jurnal.
                            </p>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="kegiatan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Unit Kerja / Pekerjaan</label>
                        <input type="text" name="kegiatan" id="kegiatan" required
                               value="{{ old('kegiatan', $jurnal->deskripsi_pekerjaan) }}"
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all font-mono"
                               placeholder="Sebutkan unit kerja dan apa yang Anda kerjakan hari ini...">
                    </div>

                    {{-- Grid: Foto (left) | Catatan Detail/Kendala (right) --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Foto Bukti Kegiatan</label>
                        
                        {{-- Upload Area (shown when no photo is cropped yet) --}}
                        <div id="foto-upload-area" class="{{ $jurnal->foto_path ? 'hidden' : '' }}">
                            <label for="foto-raw" class="flex flex-col items-center justify-center w-full aspect-square border-2 border-slate-200 dark:border-slate-700 border-dashed rounded-xl cursor-pointer bg-slate-100 dark:bg-slate-900/20 hover:bg-slate-200 dark:hover:bg-slate-900/40 transition-all group">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-14 h-14 rounded-full bg-blue-500/10 flex items-center justify-center mb-3 group-hover:bg-blue-500/20 transition-colors">
                                        <i data-lucide="camera" class="w-7 h-7 text-blue-500"></i>
                                    </div>
                                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Klik untuk ubah foto</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-500 mt-1">JPG, PNG — max 2MB</p>
                                </div>
                                <input id="foto-raw" type="file" class="hidden" accept="image/*" />
                            </label>
                        </div>

                        {{-- Preview Area (shown after crop is saved or if existing photo) --}}
                        <div id="foto-preview-area" class="{{ $jurnal->foto_path ? '' : 'hidden' }}">
                            <div class="relative w-full aspect-square rounded-xl overflow-hidden border-2 border-blue-500/30 shadow-lg shadow-blue-500/10">
                                <img id="foto-preview-img" src="{{ $jurnal->foto_path ? Storage::url($jurnal->foto_path) : '' }}" alt="Preview" class="w-full h-full object-cover">
                                <button type="button" onclick="removeCroppedPhoto()" 
                                        class="absolute top-2 right-2 w-8 h-8 rounded-full bg-red-600/90 hover:bg-red-500 text-white flex items-center justify-center shadow-lg transition-all transform hover:scale-110">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                                <div class="absolute bottom-0 inset-x-0 bg-linear-to-t from-black/60 to-transparent p-3">
                                    <p class="text-xs text-white font-medium flex items-center gap-1.5">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-400"></i>
                                        Foto siap diunggah
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="catatan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Catatan Detail / Kendala</label>
                        <textarea name="catatan" id="catatan" rows="4"
                                  class="w-full h-[calc(100%-2rem)] min-h-[200px] px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all resize-none"
                                  placeholder="Ceritakan proses pengerjaan atau kendala yang dihadapi...">{{ old('catatan', $jurnal->catatan) }}</textarea>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Crop Modal --}}
    <div id="crop-modal" class="fixed inset-0 z-100 hidden items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
        <div class="glass-card max-w-lg w-full p-0 relative rounded-2xl overflow-hidden shadow-2xl animate-in" onclick="event.stopPropagation()">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i data-lucide="crop" class="w-5 h-5 text-blue-500"></i>
                    Potong Foto (1:1)
                </h3>
                <button type="button" onclick="closeCropModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors">
                    <i data-lucide="x" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                </button>
            </div>
            
            {{-- Crop Area --}}
            <div class="p-4 bg-slate-950">
                <div class="max-h-[60vh] overflow-hidden flex items-center justify-center">
                    <img id="crop-source" src="" alt="Source" class="max-w-full">
                </div>
            </div>
            
            {{-- Footer Actions --}}
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="rotateImage()" class="px-3 py-2 rounded-lg bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium flex items-center gap-1.5 transition-colors">
                        <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                        Putar
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeCropModal()" class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 text-sm font-medium transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="saveCrop()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/25 flex items-center gap-1.5 transition-all">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .custom-dropdown {
            display: flex;
            flex-direction: column;
            opacity: 0;
            transform: translateY(-8px) scale(0.98);
            transition: opacity 0.15s cubic-bezier(0.16, 1, 0.3, 1), transform 0.15s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }
        .custom-dropdown.active {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script>
        let cropper = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Cascading Dropdown Master Data & Logic
            const dataEl = document.getElementById('jurnal-metadata');
            const masterKompetensi = JSON.parse(dataEl.getAttribute('data-kompetensis') || '[]');
            const hiddenKompetensiId = document.getElementById('kompetensi_id');
            const hiddenCp = document.getElementById('hidden_cp');
            const hiddenTpId = document.getElementById('cp_id');

            const elemenBtn = document.getElementById('elemen_btn');
            const elemenBtnLabel = document.getElementById('elemen_btn_label');
            const elemenDropdown = document.getElementById('elemen_dropdown');
            const elemenOptions = document.getElementById('elemen_options');
            const elemenSearch = document.getElementById('elemen_search');

            const cpBtn = document.getElementById('cp_btn');
            const cpBtnLabel = document.getElementById('cp_btn_label');
            const cpDropdown = document.getElementById('cp_dropdown');
            const cpOptions = document.getElementById('cp_options');
            const cpSearch = document.getElementById('cp_search');

            const tpBtn = document.getElementById('tp_btn');
            const tpBtnLabel = document.getElementById('tp_btn_label');
            const tpDropdown = document.getElementById('tp_dropdown');
            const tpOptions = document.getElementById('tp_options');
            const tpSearch = document.getElementById('tp_search');

            const cpFullTextContainer = document.getElementById('cp_full_text_container');
            const cpFullText = document.getElementById('cp_full_text');
            const tpFullTextContainer = document.getElementById('tp_full_text_container');
            const tpFullText = document.getElementById('tp_full_text');

            // Old / Existing values for recovery (or current journal data)
            const existingElemen = dataEl.getAttribute('data-old-elemen') || '';
            const existingCP = dataEl.getAttribute('data-old-cp') || '';
            const existingTPId = dataEl.getAttribute('data-old-tpid') || '';

            const data = {};
            masterKompetensi.forEach(item => {
                const elemen = item.nama ? item.nama.trim() : '';
                const cp = item.cp ? item.cp.trim() : 'Umum';
                const tp = item.tp ? item.tp.trim() : (item.nama ? item.nama.trim() : '');

                if (!elemen) return;

                if (!data[elemen]) {
                    data[elemen] = {};
                }
                if (!data[elemen][cp]) {
                    data[elemen][cp] = [];
                }
                data[elemen][cp].push({
                    id: item.id,
                    tp: tp,
                    deskripsi: item.deskripsi
                });
            });

            let selectedElemenVal = '';
            let selectedCPVal = '';

            function truncateText(text, maxLength = 90) {
                if (!text) return '';
                return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
            }

            function openDropdown(dropdown, searchInput) {
                closeAllDropdowns();
                dropdown.classList.remove('hidden');
                dropdown.offsetHeight; // Force reflow
                dropdown.classList.add('active');
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                    
                    const parentContainer = searchInput.closest('.sticky');
                    if (parentContainer && !parentContainer.classList.contains('hidden')) {
                        setTimeout(() => searchInput.focus(), 50);
                    }
                }
            }

            function closeDropdown(dropdown) {
                if (!dropdown.classList.contains('active')) return;
                dropdown.classList.remove('active');
                setTimeout(() => {
                    if (!dropdown.classList.contains('active')) {
                        dropdown.classList.add('hidden');
                    }
                }, 150);
            }

            function closeAllDropdowns() {
                [elemenDropdown, cpDropdown, tpDropdown].forEach(closeDropdown);
            }

            // Click outside to close
            document.addEventListener('click', closeAllDropdowns);

            // Prevent closing when clicking inside the dropdowns
            [elemenDropdown, cpDropdown, tpDropdown].forEach(dropdown => {
                dropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            // Toggle handlers
            elemenBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpened = elemenDropdown.classList.contains('active');
                if (isOpened) {
                    closeDropdown(elemenDropdown);
                } else {
                    openDropdown(elemenDropdown, elemenSearch);
                }
            });

            cpBtn.addEventListener('click', function(e) {
                if (cpBtn.disabled) return;
                e.stopPropagation();
                const isOpened = cpDropdown.classList.contains('active');
                if (isOpened) {
                    closeDropdown(cpDropdown);
                } else {
                    openDropdown(cpDropdown, cpSearch);
                }
            });

            tpBtn.addEventListener('click', function(e) {
                if (tpBtn.disabled) return;
                e.stopPropagation();
                const isOpened = tpDropdown.classList.contains('active');
                if (isOpened) {
                    closeDropdown(tpDropdown);
                } else {
                    openDropdown(tpDropdown, tpSearch);
                }
            });

            // Setup search filtering
            function setupSearch(searchInput, optionsContainer) {
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase().trim();
                    const buttons = optionsContainer.querySelectorAll('button');
                    let hasVisible = false;
                    
                    buttons.forEach(btn => {
                        const text = btn.textContent.toLowerCase();
                        if (text.includes(query)) {
                            btn.style.display = '';
                            hasVisible = true;
                        } else {
                            btn.style.display = 'none';
                        }
                    });

                    let noResults = optionsContainer.querySelector('.no-results-msg');
                    if (!hasVisible) {
                        if (!noResults) {
                            noResults = document.createElement('div');
                            noResults.className = 'no-results-msg p-4 text-xs text-center text-slate-500 dark:text-slate-400 font-medium';
                            noResults.textContent = 'Tidak ada hasil yang cocok';
                            optionsContainer.appendChild(noResults);
                        }
                    } else {
                        if (noResults) {
                            noResults.remove();
                        }
                    }
                });
            }

            setupSearch(elemenSearch, elemenOptions);
            setupSearch(cpSearch, cpOptions);
            setupSearch(tpSearch, tpOptions);

            function populateElemen() {
                elemenOptions.innerHTML = '';
                const selectedElemen = selectedElemenVal;
                const keys = Object.keys(data);
                
                const searchContainer = document.getElementById('elemen_search_container');
                if (searchContainer) {
                    if (keys.length < 10) {
                        searchContainer.classList.add('hidden');
                    } else {
                        searchContainer.classList.remove('hidden');
                    }
                }

                keys.forEach(elemen => {
                    const isSelected = selectedElemen === elemen;
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = `w-full text-left px-4 py-3 text-xs md:text-sm transition-all leading-relaxed flex items-center justify-between border-b border-slate-100 dark:border-slate-800 last:border-b-0 ${
                        isSelected 
                        ? 'bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-semibold' 
                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/40'
                    }`;
                    item.innerHTML = `
                        <span class="flex-1 pr-4">${elemen}</span>
                        ${isSelected ? '<i data-lucide="check" class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0"></i>' : ''}
                    `;
                    item.addEventListener('click', () => {
                        selectElemen(elemen);
                        closeDropdown(elemenDropdown);
                    });
                    elemenOptions.appendChild(item);
                });
                
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons({
                        attrs: { class: 'lucide' },
                        node: elemenOptions
                    });
                }
            }

            function resetCP() {
                cpBtn.disabled = true;
                cpBtnLabel.textContent = 'Pilih Elemen Kompetensi Terlebih Dahulu';
                cpBtnLabel.classList.add('text-slate-400', 'dark:text-slate-500');
                cpOptions.innerHTML = '';
                hiddenCp.value = '';
                selectedCPVal = '';
                
                const cpIconContainer = document.getElementById('cp_icon_container');
                if (cpIconContainer) {
                    cpIconContainer.innerHTML = '<i data-lucide="lock" class="w-4 h-4 text-slate-400/80 dark:text-slate-500/80"></i>';
                }
                
                if (cpFullTextContainer) cpFullTextContainer.classList.add('hidden');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            function resetTP() {
                tpBtn.disabled = true;
                tpBtnLabel.textContent = 'Pilih Capaian Pembelajaran (CP) Terlebih Dahulu';
                tpBtnLabel.classList.add('text-slate-400', 'dark:text-slate-500');
                tpOptions.innerHTML = '';
                hiddenKompetensiId.value = '';
                hiddenTpId.value = '';
                
                const tpIconContainer = document.getElementById('tp_icon_container');
                if (tpIconContainer) {
                    tpIconContainer.innerHTML = '<i data-lucide="lock" class="w-4 h-4 text-slate-400/80 dark:text-slate-500/80"></i>';
                }
                
                if (tpFullTextContainer) tpFullTextContainer.classList.add('hidden');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            function selectElemen(value) {
                const trimmedValue = value ? value.trim() : '';
                selectedElemenVal = trimmedValue;
                elemenBtnLabel.textContent = value;
                elemenBtnLabel.classList.remove('text-slate-400', 'dark:text-slate-500');

                resetCP();
                resetTP();

                if (trimmedValue && data[trimmedValue]) {
                    const cps = Object.keys(data[trimmedValue]);
                    if (cps.length > 0) {
                        cpBtn.disabled = false;
                        cpBtnLabel.textContent = 'Pilih Capaian Pembelajaran (CP)';
                        cpBtnLabel.classList.remove('text-slate-400', 'dark:text-slate-500');
                        
                        const cpIconContainer = document.getElementById('cp_icon_container');
                        if (cpIconContainer) {
                            cpIconContainer.innerHTML = '<i data-lucide="chevron-down" class="w-4 h-4 text-slate-500"></i>';
                        }
                        
                        populateCP(cps);
                    }
                }
                
                populateElemen();
                
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            function populateCP(cps) {
                cpOptions.innerHTML = '';
                const selectedCP = selectedCPVal;
                
                const searchContainer = document.getElementById('cp_search_container');
                if (searchContainer) {
                    if (cps.length < 10) {
                        searchContainer.classList.add('hidden');
                    } else {
                        searchContainer.classList.remove('hidden');
                    }
                }

                cps.forEach(cp => {
                    const isSelected = selectedCP === cp;
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = `w-full text-left px-4 py-3 text-xs md:text-sm transition-all leading-relaxed flex items-center justify-between border-b border-slate-100 dark:border-slate-800 last:border-b-0 ${
                        isSelected 
                        ? 'bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-semibold' 
                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/40'
                    }`;
                    item.innerHTML = `
                        <span class="flex-1 pr-4">${cp}</span>
                        ${isSelected ? '<i data-lucide="check" class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0"></i>' : ''}
                    `;
                    item.addEventListener('click', () => {
                        selectCP(cp);
                        closeDropdown(cpDropdown);
                    });
                    cpOptions.appendChild(item);
                });
                
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons({
                        attrs: { class: 'lucide' },
                        node: cpOptions
                    });
                }
            }

            function selectCP(value) {
                const trimmedValue = value ? value.trim() : '';
                cpBtnLabel.textContent = truncateText(trimmedValue, 60);
                hiddenCp.value = trimmedValue;
                selectedCPVal = trimmedValue;

                cpFullText.textContent = trimmedValue;
                if (cpFullTextContainer) cpFullTextContainer.classList.remove('hidden');

                resetTP();

                const selectedElemen = selectedElemenVal;
                if (selectedElemen && trimmedValue && data[selectedElemen] && data[selectedElemen][trimmedValue]) {
                    const tps = data[selectedElemen][trimmedValue];
                    if (tps.length > 0) {
                        tpBtn.disabled = false;
                        tpBtnLabel.textContent = 'Pilih Tujuan Pembelajaran (TP)';
                        tpBtnLabel.classList.remove('text-slate-400', 'dark:text-slate-500');
                        
                        const tpIconContainer = document.getElementById('tp_icon_container');
                        if (tpIconContainer) {
                            tpIconContainer.innerHTML = '<i data-lucide="chevron-down" class="w-4 h-4 text-slate-500"></i>';
                        }
                        
                        populateTP(tps);
                    }
                }
                
                if (selectedElemen && data[selectedElemen]) {
                    populateCP(Object.keys(data[selectedElemen]));
                }
                
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            function populateTP(tps) {
                tpOptions.innerHTML = '';
                const selectedTPId = hiddenTpId.value;
                
                const searchContainer = document.getElementById('tp_search_container');
                if (searchContainer) {
                    if (tps.length < 10) {
                        searchContainer.classList.add('hidden');
                    } else {
                        searchContainer.classList.remove('hidden');
                    }
                }

                tps.forEach(tpItem => {
                    const isSelected = selectedTPId == tpItem.id;
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = `w-full text-left px-4 py-3 text-xs md:text-sm transition-all leading-relaxed flex items-center justify-between border-b border-slate-100 dark:border-slate-800 last:border-b-0 ${
                        isSelected 
                        ? 'bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-semibold' 
                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/40'
                    }`;
                    item.innerHTML = `
                        <span class="flex-1 pr-4">${tpItem.tp}</span>
                        ${isSelected ? '<i data-lucide="check" class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0"></i>' : ''}
                    `;
                    item.addEventListener('click', () => {
                        selectTP(tpItem.id, tpItem.tp);
                        closeDropdown(tpDropdown);
                    });
                    tpOptions.appendChild(item);
                });
                
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons({
                        attrs: { class: 'lucide' },
                        node: tpOptions
                    });
                }
            }

            function selectTP(id, text) {
                tpBtnLabel.textContent = truncateText(text, 60);
                hiddenKompetensiId.value = id;
                hiddenTpId.value = id;

                tpFullText.textContent = text;
                if (tpFullTextContainer) tpFullTextContainer.classList.remove('hidden');
                
                const selectedElemen = selectedElemenVal;
                const selectedCP = selectedCPVal;
                if (selectedElemen && selectedCP && data[selectedElemen] && data[selectedElemen][selectedCP]) {
                    populateTP(data[selectedElemen][selectedCP]);
                }
                
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            // Add form submit validation for hidden input
            const form = document.getElementById('jurnal-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!hiddenTpId.value) {
                        e.preventDefault();
                        alert('Silakan pilih Tujuan Pembelajaran (TP) terlebih dahulu.');
                    }
                });
            }

            // Initialize dropdowns
            populateElemen();

            // Pre-select if there are existing / old values
            if (existingElemen) {
                selectElemen(existingElemen);
                
                if (existingCP) {
                    selectCP(existingCP);
                    
                    if (existingTPId) {
                        const selectedElemen = selectedElemenVal;
                        const selectedCP = selectedCPVal;
                        if (data[selectedElemen] && data[selectedElemen][selectedCP]) {
                            const found = data[selectedElemen][selectedCP].find(x => x.id == existingTPId);
                            if (found) {
                                selectTP(found.id, found.tp);
                            }
                        }
                    }
                }
            }

            const fileInput = document.getElementById('foto-raw');
            
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Validate file
                if (!file.type.match('image.*')) {
                    alert('File harus berupa gambar (JPG, PNG).');
                    fileInput.value = '';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB.');
                    fileInput.value = '';
                    return;
                }

                // Read file and open crop modal
                const reader = new FileReader();
                reader.onload = function(event) {
                    openCropModal(event.target.result);
                };
                reader.readAsDataURL(file);
            });

            // Attendance check logic
            const tanggalInput = document.getElementById('tanggal');
            const statusBox = document.getElementById('attendance-status-box');
            const statusText = document.getElementById('attendance-status-text');
            const alphaContainer = document.getElementById('alasan-alpha-container');
            const alphaTextarea = document.getElementById('alasan_alpha');

            function checkAttendance(dateStr) {
                if (!dateStr) return;
                
                fetch("{{ route('siswa.jurnal.check-attendance') }}?tanggal=" + dateStr)
                    .then(response => response.json())
                    .then(data => {
                        statusBox.classList.remove('hidden', 'flex', 'bg-emerald-500/10', 'border-emerald-500/20', 'text-emerald-500', 'dark:text-emerald-400', 'bg-amber-500/10', 'border-amber-500/20', 'text-amber-500', 'dark:text-amber-400', 'bg-rose-500/10', 'border-rose-500/20', 'text-rose-500', 'dark:text-rose-400');
                        
                        if (data.exists) {
                            alphaContainer.classList.add('hidden');
                            alphaTextarea.removeAttribute('required');
                            statusBox.classList.add('flex');
                            
                            if (data.status === 'hadir') {
                                statusBox.classList.add('bg-emerald-500/10', 'border', 'border-emerald-500/20', 'text-emerald-600', 'dark:text-emerald-400');
                                statusText.innerHTML = `<span class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><b>Absensi Terdeteksi: Hadir</b> (Masuk: ${data.waktu_datang || '-'} | Pulang: ${data.waktu_pulang || '-'})</span>`;
                            } else if (data.status === 'sakit' || data.status === 'izin') {
                                statusBox.classList.add('bg-amber-500/10', 'border', 'border-amber-500/20', 'text-amber-600', 'dark:text-amber-400');
                                let details = [];
                                if (data.keterangan) details.push(`Ket: ${data.keterangan}`);
                                if (data.alasan) details.push(`Alasan: ${data.alasan}`);
                                let detailStr = details.length > 0 ? ` (${details.join(', ')})` : '';
                                statusText.innerHTML = `<span class="flex items-center gap-2"><svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg><b>Absensi Terdeteksi: ${data.status.toUpperCase()}</b>${detailStr}</span>`;
                            } else if (data.status === 'alpha') {
                                statusBox.classList.add('bg-rose-500/10', 'border', 'border-rose-500/20', 'text-rose-600', 'dark:text-rose-400');
                                statusText.innerHTML = `<span class="flex items-center gap-2"><svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><b>Absensi Terdeteksi: Alpha</b></span>`;
                                
                                alphaContainer.classList.remove('hidden');
                                alphaTextarea.setAttribute('required', 'required');
                                alphaTextarea.value = data.alasan || '';
                            }
                        } else {
                            statusBox.classList.add('bg-rose-500/10', 'border', 'border-rose-500/20', 'text-rose-600', 'dark:text-rose-400', 'flex');
                            statusText.innerHTML = `<span class="flex items-center gap-2"><svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg><b>Anda tidak melakukan absensi pada tanggal ini (Status: Alpha).</b></span>`;
                            
                            alphaContainer.classList.remove('hidden');
                            alphaTextarea.setAttribute('required', 'required');
                            alphaTextarea.value = '';
                        }
                    })
                    .catch(error => {
                        console.error("Error checking attendance:", error);
                    });
            }

            if (tanggalInput) {
                tanggalInput.addEventListener('change', function() {
                    checkAttendance(this.value);
                });
                checkAttendance(tanggalInput.value);
            }

            // Re-initialize lucide icons for dynamically shown elements
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function openCropModal(imageSrc) {
            const modal = document.getElementById('crop-modal');
            const cropSource = document.getElementById('crop-source');
            
            // Destroy previous cropper
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            cropSource.src = imageSrc;
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Initialize Cropper after image loads
            cropSource.onload = function() {
                cropper = new Cropper(cropSource, {
                    aspectRatio: 1 / 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    responsive: true,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    background: true,
                });
            };
        }

        function closeCropModal() {
            const modal = document.getElementById('crop-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            // Reset file input
            document.getElementById('foto-raw').value = '';
        }

        function rotateImage() {
            if (cropper) {
                cropper.rotate(90);
            }
        }

        function saveCrop() {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 800,
                height: 800,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            const croppedDataURL = canvas.toDataURL('image/jpeg', 0.85);

            // Set hidden input value
            document.getElementById('foto-cropped-input').value = croppedDataURL;

            // Show preview
            document.getElementById('foto-preview-img').src = croppedDataURL;
            document.getElementById('foto-upload-area').classList.add('hidden');
            document.getElementById('foto-preview-area').classList.remove('hidden');

            // Re-initialize lucide icons for preview area
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            closeCropModal();
        }

        function removeCroppedPhoto() {
            document.getElementById('foto-cropped-input').value = '';
            document.getElementById('foto-preview-img').src = '';
            document.getElementById('foto-preview-area').classList.add('hidden');
            document.getElementById('foto-upload-area').classList.remove('hidden');
            document.getElementById('foto-raw').value = '';
        }
    </script>
    @endpush
</x-app-layout>
