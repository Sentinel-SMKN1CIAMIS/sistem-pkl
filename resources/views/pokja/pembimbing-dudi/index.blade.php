<x-app-layout>
    <x-slot name="header">Kelola Pembimbing DUDI</x-slot>

    <style>
        .pokja-header-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }
        .pokja-btn {
            width: 100% !important;
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        @media (min-width: 768px) {
            .pokja-header-container {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            .pokja-btn {
                width: auto !important;
            }
        }
    </style>

    <div x-data="{ 
        importPanelOpen: false, 
        guideModalOpen: false,
        selectedMentors: JSON.parse(localStorage.getItem('selectedPembimbingDudi') || '[]'),
        init() {
            this.$watch('selectedMentors', value => {
                localStorage.setItem('selectedPembimbingDudi', JSON.stringify(value));
            });
        },
        toggleAll(checked) {
            if (checked) {
                @foreach($mentors as $item)
                    if (!this.selectedMentors.includes('{{ $item->id }}')) this.selectedMentors.push('{{ $item->id }}');
                @endforeach
            } else {
                let pageIds = [
                    @foreach($mentors as $item)
                        '{{ $item->id }}',
                    @endforeach
                ];
                this.selectedMentors = this.selectedMentors.filter(id => !pageIds.includes(id));
            }
        },
        getPdfExportUrl() {
            let baseUrl = '{{ route('pokja.pembimbing_dudi.export-pdf') }}';
            let params = new URLSearchParams(window.location.search);
            let allIds = JSON.parse(localStorage.getItem('selectedPembimbingDudi') || '[]');
            if (allIds.length > 0) {
                params.set('ids', allIds.join(','));
            }
            return baseUrl + '?' + params.toString();
        },
        getExcelExportUrl() {
            let baseUrl = '{{ route('pokja.pembimbing_dudi.export-excel') }}';
            let params = new URLSearchParams(window.location.search);
            let allIds = JSON.parse(localStorage.getItem('selectedPembimbingDudi') || '[]');
            if (allIds.length > 0) {
                params.set('ids', allIds.join(','));
            }
            return baseUrl + '?' + params.toString();
        }
    }">
        <div class="mb-6 pokja-header-container">
            <div class="flex-1 min-w-0">
                <p class="text-slate-600 dark:text-slate-400 text-sm">Daftar mentor / pembimbing dari pihak industri (DUDI).</p>
            </div>
            @if(auth()->user()->role !== 'kepala_sekolah')
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto shrink-0">
                <button @click="importPanelOpen = !importPanelOpen" class="pokja-btn px-3 py-1.5 text-xs whitespace-nowrap bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg transition-all gap-1.5 cursor-pointer border border-slate-200 dark:border-slate-700">
                    <i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i>
                    Impor
                </button>
                <a href="{{ route('pokja.pembimbing_dudi.create') }}" class="pokja-btn px-3 py-1.5 text-xs whitespace-nowrap bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg shadow-sm shadow-blue-500/20 transition-all gap-1.5">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    Tambah
                </a>

                <div class="w-px h-5 bg-slate-300 dark:bg-slate-600 mx-1"></div>

                <a :href="getPdfExportUrl()" class="pokja-btn px-3 py-1.5 text-xs whitespace-nowrap bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 font-semibold rounded-lg border border-red-200 dark:border-red-800 transition-all gap-1.5" target="_blank">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    PDF
                    <span x-show="selectedMentors.length > 0" class="bg-red-600 text-white text-[9px] px-1 py-0.5 rounded-full font-bold leading-none" x-text="selectedMentors.length" x-cloak></span>
                </a>
                <a :href="getExcelExportUrl()" class="pokja-btn px-3 py-1.5 text-xs whitespace-nowrap bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:hover:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 font-semibold rounded-lg border border-emerald-200 dark:border-emerald-800 transition-all gap-1.5">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                    Excel
                    <span x-show="selectedMentors.length > 0" class="bg-emerald-600 text-white text-[9px] px-1 py-0.5 rounded-full font-bold leading-none" x-text="selectedMentors.length" x-cloak></span>
                </a>

                <template x-teleport="body">
                    <div x-show="selectedMentors.length > 0" class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg text-xs" x-cloak>
                        <span class="text-slate-600 dark:text-slate-400">
                            <span class="font-bold text-blue-600 dark:text-blue-400" x-text="selectedMentors.length"></span> data terpilih
                            <span class="text-slate-400">(dari semua halaman)</span>
                        </span>
                        <button @click="selectedMentors = []; localStorage.removeItem('selectedPembimbingDudi')" class="px-2.5 py-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors font-semibold">
                            <i data-lucide="x-circle" class="w-3.5 h-3.5 inline"></i>
                            Hapus Pilihan
                        </button>
                    </div>
                </template>
            </div>
            @endif
        </div>

        <!-- Inline Import Panel (Directly on the main page layout, occupying full width!) -->
        <div class="w-full mb-6" x-show="importPanelOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             x-cloak>
            <div class="glass-card w-full rounded-2xl overflow-hidden shadow-xl border border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-900 p-6 space-y-4 text-left">
                
                <!-- Panel Header -->
                <div class="pb-4 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="upload-cloud" class="text-blue-500"></i>
                        Impor Data Pembimbing DUDI
                    </h3>
                    <button @click="importPanelOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <!-- Panel Body Form -->
                <form action="{{ route('pokja.pembimbing_dudi.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <!-- Template & Guide Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Template -->
                        <div class="p-4 bg-blue-500/5 border border-blue-500/10 rounded-xl flex items-start gap-3">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6 text-blue-400 shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Gunakan Template Resmi</h4>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    Unduh template standar agar struktur kolom data mentor terisi secara tepat.
                                </p>
                                <a href="{{ route('pokja.import.template', 'pembimbing_dudi') }}" class="inline-flex items-center gap-1.5 mt-2.5 text-[11px] font-bold text-blue-500 hover:text-blue-400 transition-colors bg-blue-500/10 px-3 py-1.5 rounded-lg border border-blue-500/20">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                    Unduh Template Excel (.xlsx)
                                </a>
                            </div>
                        </div>

                        <!-- Guide Toggle -->
                        <div class="p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-xl flex items-start gap-3">
                            <i data-lucide="book-open" class="w-6 h-6 text-emerald-400 shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Butuh Panduan Kolom?</h4>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    Lihat panduan penulisan kolom, relasi perusahaan, dan cari daftar nama DUDI terdaftar resmi.
                                </p>
                                <button type="button" @click="guideModalOpen = true" class="inline-flex items-center gap-1.5 mt-2.5 text-[11px] font-bold text-emerald-500 hover:text-emerald-400 transition-colors bg-emerald-500/10 px-3 py-1.5 rounded-lg border border-emerald-500/20 cursor-pointer">
                                    <i data-lucide="help-circle" class="w-3.5 h-3.5"></i>
                                    Buka Panduan Impor
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Upload Input with reactive state -->
                    <div class="space-y-2" x-data="{ localFileName: '' }">
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Pilih File Excel</label>
                        <div class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all relative group duration-300"
                             :class="localFileName ? 'border-emerald-500/50 bg-emerald-500/5 dark:bg-emerald-500/10' : 'border-slate-300 dark:border-slate-700 hover:border-blue-500 dark:hover:border-blue-500'">
                            <input type="file" name="file" accept=".xlsx, .xls" required 
                                   @change="localFileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <!-- State: Empty -->
                            <div class="flex flex-col items-center gap-2 py-4" x-show="!localFileName">
                                <i data-lucide="file-spreadsheet" class="w-8 h-8 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Klik atau Seret file Excel ke sini</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-500">Format file yang didukung: .xlsx, .xls saja</span>
                            </div>

                            <!-- State: File Chosen -->
                            <div class="flex flex-col items-center gap-2 py-4" x-show="localFileName" x-cloak>
                                <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-500 animate-bounce"></i>
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block truncate max-w-xs" x-text="localFileName"></span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-500">Klik atau seret file lain untuk mengganti berkas</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end gap-3">
                        <button type="button" @click="importPanelOpen = false" class="px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50 rounded-xl transition-colors border border-slate-200/50 dark:border-slate-700/50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            Mulai Impor Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Floating Scrollable Guide Modal (Popup) -->
        <template x-teleport="body">
            <div x-show="guideModalOpen" 
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
                 x-transition.opacity.duration.300ms x-cloak>
                 
                 <div @click.away="guideModalOpen = false" 
                      class="glass-card w-full max-w-3xl rounded-2xl overflow-hidden shadow-2xl border border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-900 animate-fade-in-up text-left max-h-[85vh] flex flex-col"
                      x-data="{ searchQuery: '' }">
                      
                      <!-- Modal Header -->
                      <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30 shrink-0">
                          <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                              <i data-lucide="book-open" class="text-emerald-500"></i>
                              Panduan Impor Data Pembimbing DUDI
                          </h3>
                          <button @click="guideModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                              <i data-lucide="x" class="w-4 h-4"></i>
                          </button>
                      </div>

                      <!-- Modal Body (Scrollable) -->
                      <div class="p-6 overflow-y-auto space-y-6 text-sm text-slate-600 dark:text-slate-400">
                          <!-- Section: Ketentuan Umum -->
                          <div class="space-y-2">
                              <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                                  <i data-lucide="info" class="w-4 h-4 text-blue-500"></i>
                                  Ketentuan Umum Impor Excel:
                              </h4>
                              <ul class="list-disc pl-5 space-y-1 text-xs leading-relaxed">
                                  <li>Sistem menggunakan pengaman transaksi (jika ada <strong>satu baris data saja yang salah/gagal</strong>, seluruh proses akan dibatalkan/rollback demi integritas data).</li>
                                  <li>Template wajib diisi menggunakan format spreadsheet Excel asli (<strong>.xlsx</strong> / <strong>.xls</strong>).</li>
                              </ul>
                          </div>

                          <!-- Section: Aturan Kolom -->
                          <div class="space-y-3">
                              <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                                  <i data-lucide="table" class="w-4 h-4 text-blue-500"></i>
                                  Struktur Kolom data Pembimbing DUDI (Mentor):
                              </h4>
                              <div class="overflow-x-auto rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                                  <table class="w-full text-xs text-left">
                                      <thead class="bg-slate-50 dark:bg-slate-800/50 font-bold text-slate-700 dark:text-slate-300 border-b border-slate-200/50 dark:border-slate-700/50">
                                          <tr>
                                              <th class="px-4 py-2.5">Nama Kolom</th>
                                              <th class="px-4 py-2.5">Validasi & Pengisian</th>
                                              <th class="px-4 py-2.5 text-right">Contoh</th>
                                          </tr>
                                      </thead>
                                      <tbody class="divide-y divide-slate-200/30 dark:divide-slate-700/30">
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">nama_lengkap</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Nama lengkap mentor industri tanpa gelar.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">Eko Prasetyo</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">username</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Username unik tanpa spasi untuk akun login mentor.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">ekoprasetyo</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">email</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Alamat email unik dan aktif mentor.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">eko@example.com</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">password</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Sandi masuk akun awal (min. 6 karakter).</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">mentor123</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">jabatan</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Jabatan/posisi kerja resmi di industri.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">Senior Developer</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">nama_perusahaan</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Harus cocok persis dengan nama DUDI/Perusahaan yang sudah terdaftar di sistem (lihat kolom pencarian di bawah).</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">PT Solusi Digital</td>
                                          </tr>
                                      </tbody>
                                  </table>
                              </div>
                          </div>

                          <!-- Section: Referensi Perusahaan Searchable -->
                          <div class="space-y-3 p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                  <h4 class="text-xs font-bold text-slate-950 dark:text-white flex items-center gap-1.5">
                                      <i data-lucide="search" class="w-4.5 h-4.5 text-emerald-500"></i>
                                      Cari Perusahaan / DUDI Aktif Terdaftar:
                                  </h4>
                                  <div class="relative w-full sm:w-64">
                                      <input type="text" x-model="searchQuery" placeholder="Cari nama perusahaan..." class="w-full text-xs px-3 py-1.5 pl-8 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800/80 focus:outline-none focus:border-emerald-500 text-slate-800 dark:text-slate-200">
                                      <i data-lucide="search" class="absolute left-2.5 top-2.5 w-3.5 h-3.5 text-slate-400"></i>
                                  </div>
                              </div>
                              <p class="text-[11px] text-slate-500 leading-normal">
                                  Kolom <strong>nama_perusahaan</strong> pada berkas Excel Anda wajib diisi sama persis dengan nama perusahaan terdaftar berikut:
                              </p>
                              
                              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 max-h-48 overflow-y-auto pr-1">
                                  @php
                                      $dudis = \App\Models\Dudi::orderBy('nama' . '')->pluck('nama' . '');
                                  @endphp
                                  @forelse($dudis as $d)
                                      <div x-show="searchQuery === '' || '{{ strtolower(addslashes($d)) }}'.includes(searchQuery.toLowerCase())" class="flex items-center justify-between p-2 bg-white dark:bg-slate-900 border border-slate-200/40 dark:border-slate-800/60 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200">
                                          <span class="truncate">{{ $d }}</span>
                                           <button type="button" onclick="navigator.clipboard.writeText('{{ addslashes($d) }}'); alert('Berhasil menyalin nama DUDI!')" class="p-1 text-slate-400 hover:text-emerald-500 rounded transition-colors shrink-0" title="Salin">
                                              <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                          </button>
                                      </div>
                                  @empty
                                      <span class="text-xs text-amber-500 italic col-span-2">Belum ada DUDI/perusahaan yang terdaftar.</span>
                                  @endforelse
                              </div>
                          </div>
                      </div>

                      <!-- Modal Footer -->
                      <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end bg-slate-50/50 dark:bg-slate-800/30 shrink-0">
                          <button type="button" @click="guideModalOpen = false" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                              Mengerti & Tutup
                          </button>
                      </div>
                 </div>
            </div>
        </template>

    @if(session('import_errors'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 text-sm">
            <h4 class="font-bold mb-2 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                Gagal Mengimpor Data Pembimbing DUDI. Silakan periksa beberapa kesalahan berikut:
            </h4>
            <ul class="list-disc pl-5 space-y-1 text-xs">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="glass-card p-4 mb-6" x-data="{ showAdvanced: {{ request()->hasAny(['sort_by', 'sort_dir']) ? 'true' : 'false' }} }">
        <form action="{{ request()->url() }}" method="GET" class="space-y-3">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Pembimbing atau Industri..." 
                           class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                </div>
                
                <div class="flex gap-2 items-center">
                    <button type="button" @click="showAdvanced = !showAdvanced" class="px-3 py-2 text-slate-500 hover:text-blue-500 hover:bg-blue-500/10 rounded-xl transition-colors border border-slate-200/50 dark:border-slate-700/50" title="Filter Lanjutan">
                        <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                    </button>
                    <button type="submit" class="hidden md:block px-6 py-2 bg-slate-800 dark:bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-700 transition-all text-sm">
                        Cari
                    </button>
                    @if(request()->anyFilled(['search', 'sort_by', 'sort_dir']))
                        <a href="{{ request()->url() }}" class="px-3 py-2 text-slate-500 hover:text-red-400 flex items-center justify-center transition-colors border border-slate-200/50 dark:border-slate-700 rounded-xl bg-slate-100/30" title="Reset">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Advanced Filters (Sorting) -->
            <div x-show="showAdvanced" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak
                 class="mt-4 pt-4 border-t border-slate-200/60 dark:border-slate-700/60">
                 
                <div class="flex items-center gap-2 mb-3 px-1">
                    <i data-lucide="sliders" class="w-4 h-4 text-blue-500"></i>
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Pengaturan Lanjutan</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-800/30 p-4 rounded-xl border border-slate-100 dark:border-slate-800/60 shadow-inner">
                    <!-- Sort By -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Urutkan Berdasarkan</label>
                        <select name="sort_by" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300">
                            <option value="created_at" {{ request('sort_by') === 'created_at' || !request('sort_by') ? 'selected' : '' }}>Waktu Ditambahkan</option>
                            <option value="nama_lengkap" {{ request('sort_by') === 'nama_lengkap' ? 'selected' : '' }}>Nama Lengkap (A-Z)</option>
                        </select>
                    </div>

                    <!-- Sort Dir -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Arah Urutan</label>
                        <select name="sort_dir" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300">
                            <option value="desc" {{ request('sort_dir') === 'desc' || !request('sort_dir') ? 'selected' : '' }}>Menurun (Terbaru / Z-A)</option>
                            <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>Menaik (Terlama / A-Z)</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                        @if(auth()->user()->role !== 'kepala_sekolah')
                        <th class="w-12 px-6 py-4">
                            <input type="checkbox" :checked="selectedMentors.length === {{ count($mentors) }} && {{ count($mentors) }} > 0" @change="toggleAll($el.checked)" class="rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 bg-transparent">
                        </th>
                        @endif
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Nama Mentor</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Perusahaan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Jabatan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">No. HP</th>
                        @if(auth()->user()->role !== 'kepala_sekolah')
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right whitespace-nowrap">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($mentors as $item)
                        <tr class="hover:bg-white dark:bg-slate-800/20 transition-colors group" :class="selectedMentors.includes('{{ $item->id }}') ? 'bg-blue-500/5 dark:bg-blue-500/10' : ''">
                            @if(auth()->user()->role !== 'kepala_sekolah')
                            <td class="px-6 py-4 whitespace-nowrap w-12">
                                <input type="checkbox" value="{{ $item->id }}" x-model="selectedMentors" class="rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 bg-transparent">
                            </td>
                            @endif
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-purple-400 font-bold">
                                        {{ substr($item->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block truncate">{{ $item->nama_lengkap }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $item->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md bg-amber-500/10 border border-amber-500/20 text-xs text-amber-400">
                                    {{ $item->dudi->nama }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                {{ $item->jabatan }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ $item->no_hp ?? '-' }}
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
                                        <a href="{{ route('pokja.pembimbing_dudi.edit', $item) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-500"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('pokja.pembimbing_dudi.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun mentor {{ addslashes($item->nama_lengkap) }} ini?')">
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
                            <td colspan="{{ auth()->user()->role === 'kepala_sekolah' ? 4 : 6 }}" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada data pembimbing DUDI.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mentors->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $mentors->links() }}
            </div>
        @endif
    </div>
    </div>
</x-app-layout>
