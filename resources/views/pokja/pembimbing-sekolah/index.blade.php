<x-app-layout>
    <x-slot name="header">Kelola Pembimbing Sekolah</x-slot>

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
        massResetModalOpen: false,
        isResetting: false,
        resetPercent: 0,
        resetProcessed: 0,
        resetMessage: '',
        confirmText: '',
        showFilters: {{ request()->hasAny(['search', 'tipe', 'konsentrasi_keahlian_id', 'sort_by', 'sort_dir']) && (request('search') != '' || request('tipe') != 'semua' || request('konsentrasi_keahlian_id') != 'semua' || request('sort_by') != '' || request('sort_dir') != '') ? 'true' : 'false' }},

        async startMassReset() {
            if (this.confirmText !== 'RESET') return;
            this.isResetting = true;
            this.resetPercent = 0;
            this.resetProcessed = 0;
            let offset = 0;
            const batchSize = 35;
            const token = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content');

            try {
                while (this.isResetting) {
                    const formData = new FormData();
                    formData.append('offset', offset);
                    formData.append('batch_size', batchSize);

                    const res = await fetch('{{ route('pokja.pembimbing_sekolah.reset_password_massal') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    if (!res.ok) throw new Error('Gagal me-reset password.');

                    const data = await res.json();
                    this.resetPercent = data.percent;
                    this.resetProcessed = data.processed_total;
                    this.resetMessage = data.message;
                    offset = data.offset;

                    if (data.done) {
                        this.resetPercent = 100;
                        await new Promise(r => setTimeout(r, 600));
                        window.location.reload();
                        break;
                    }
                }
            } catch (e) {
                alert(e.message || 'Terjadi kesalahan saat me-reset password.');
                this.isResetting = false;
            }
        }
    }">
        <div class="mb-6 pokja-header-container">
            <div class="flex-1">
                <p class="text-slate-600 dark:text-slate-400 text-sm">Daftar guru pembimbing sekolah per konsentrasi keahlian.</p>
            </div>
            @if(auth()->user()->role !== 'kepala_sekolah')
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto shrink-0">
                <a href="{{ auth()->user()->role === 'super_admin' ? route('admin.custom_report.index') : route('pokja.custom_report.index') }}" class="pokja-btn px-4 py-2 text-sm whitespace-nowrap bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/25 transition-all gap-2">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    Ekspor Kredensial / Data
                </a>
                <button type="button" @click="massResetModalOpen = true" class="pokja-btn px-4 py-2 text-sm whitespace-nowrap bg-amber-600 hover:bg-amber-500 text-white font-medium rounded-xl shadow-lg shadow-amber-500/25 transition-all gap-2 cursor-pointer">
                    <i data-lucide="key-round" class="w-4 h-4"></i>
                    Reset Sandi Masal
                </button>
                <button @click="showFilters = !showFilters" class="pokja-btn px-4 py-2 text-sm whitespace-nowrap bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-xl border border-slate-200 dark:border-slate-700 transition-all gap-2 cursor-pointer shadow-sm">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filter & Cari
                </button>
                <button @click="importPanelOpen = !importPanelOpen" class="pokja-btn px-4 py-2 text-sm whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-xl transition-all gap-2 cursor-pointer border border-slate-700">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                    Impor Pembimbing
                </button>
                <a href="{{ route('pokja.pembimbing_sekolah.create') }}" class="pokja-btn px-4 py-2 text-sm whitespace-nowrap bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Tambah Pembimbing
                </a>
            </div>
            @endif
        </div>

        <!-- Filters Panel -->
        <div class="w-full mb-6" x-show="showFilters" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             x-cloak>
            <form action="{{ route('pokja.pembimbing_sekolah.index') }}" method="GET" class="glass-card rounded-2xl border border-slate-200/50 dark:border-slate-700/50 bg-white/50 dark:bg-slate-800/50 p-4 space-y-4 shadow-sm">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Pencarian</label>
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIP..." class="w-full pl-9 pr-4 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Tipe Guru</label>
                        <select name="tipe" class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300 transition-all">
                            <option value="semua" {{ request('tipe') === 'semua' ? 'selected' : '' }}>Semua Tipe</option>
                            <option value="kejuruan" {{ request('tipe') === 'kejuruan' ? 'selected' : '' }}>Kejuruan</option>
                            <option value="umum" {{ request('tipe') === 'umum' ? 'selected' : '' }}>Umum</option>
                            <option value="keduanya" {{ request('tipe') === 'keduanya' ? 'selected' : '' }}>Keduanya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Konsentrasi Keahlian</label>
                        <select name="konsentrasi_keahlian_id" class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300 transition-all">
                            <option value="semua" {{ request('konsentrasi_keahlian_id') === 'semua' ? 'selected' : '' }}>Semua Konsentrasi</option>
                            @foreach($concentrations as $k)
                                <option value="{{ $k->id }}" {{ request('konsentrasi_keahlian_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Advanced Sorting -->
                <div class="mt-4 pt-4 border-t border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-2 mb-3 px-1">
                        <i data-lucide="sliders" class="w-4 h-4 text-blue-500"></i>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Pengaturan Lanjutan</h4>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-800/30 p-4 rounded-xl border border-slate-100 dark:border-slate-800/60 shadow-inner">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Urutkan Berdasarkan</label>
                            <select name="sort_by" class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300 transition-all">
                                <option value="created_at" {{ request('sort_by') === 'created_at' || !request('sort_by') ? 'selected' : '' }}>Waktu Ditambahkan</option>
                                <option value="nama_lengkap" {{ request('sort_by') === 'nama_lengkap' ? 'selected' : '' }}>Nama Lengkap (A-Z)</option>
                                <option value="nip" {{ request('sort_by') === 'nip' ? 'selected' : '' }}>NIP</option>
                                <option value="tipe" {{ request('sort_by') === 'tipe' ? 'selected' : '' }}>Tipe Pembimbing</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Arah Urutan</label>
                            <select name="sort_dir" class="w-full px-3 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-700 dark:text-slate-300 transition-all">
                                <option value="desc" {{ request('sort_dir') === 'desc' || !request('sort_dir') ? 'selected' : '' }}>Menurun (Terbaru / Z-A)</option>
                                <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>Menaik (Terlama / A-Z)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/50 dark:border-slate-700/50">
                    <a href="{{ route('pokja.pembimbing_sekolah.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors inline-flex items-center justify-center">Reset Filter</a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all inline-flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Terapkan Filter
                    </button>
                </div>
            </form>
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
                        Impor Data Pembimbing Sekolah
                    </h3>
                    <button @click="importPanelOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <!-- Panel Body Form -->
                <form action="{{ route('pokja.pembimbing_sekolah.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <!-- Template & Guide Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Template -->
                        <div class="p-4 bg-blue-500/5 border border-blue-500/10 rounded-xl flex items-start gap-3">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6 text-blue-400 shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Gunakan Template Resmi</h4>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    Unduh template standar agar struktur kolom data guru terisi secara tepat.
                                </p>
                                <a href="{{ route('pokja.import.template', 'pembimbing_sekolah') }}" class="inline-flex items-center gap-1.5 mt-2.5 text-[11px] font-bold text-blue-500 hover:text-blue-400 transition-colors bg-blue-500/10 px-3 py-1.5 rounded-lg border border-blue-500/20">
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
                                    Lihat panduan penulisan kolom, jenis pembimbing (adaptif/produktif), dan nama jurusan resmi di sistem.
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
                            <input type="file" name="file" accept=".xlsx, .xls, .csv" required 
                                   @change="localFileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <!-- State: Empty -->
                            <div class="flex flex-col items-center gap-2 py-4" x-show="!localFileName">
                                <i data-lucide="file-spreadsheet" class="w-8 h-8 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Klik atau Seret file Excel/CSV ke sini</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-500">Format file yang didukung: .xlsx, .xls, .csv</span>
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
                      class="glass-card w-full max-w-3xl rounded-2xl overflow-hidden shadow-2xl border border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-900 animate-fade-in-up text-left max-h-[85vh] flex flex-col">
                      
                      <!-- Modal Header -->
                      <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30 shrink-0">
                          <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                              <i data-lucide="book-open" class="text-emerald-500"></i>
                              Panduan Impor Data Pembimbing Sekolah
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
                                  Struktur Kolom data Pembimbing Sekolah:
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
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">nip</td>
                                              <td class="px-4 py-2.5">Opsional. Angka NIP resmi guru (harus unik di database jika diisi).</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">198501012010011002</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">nama_lengkap</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Nama lengkap guru beserta gelar akademik.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">Drs. H. Hendra Wijaya</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">username</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Nama pendek tanpa spasi (boleh kombinasi strip/garis bawah). Harus unik.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">hendrawijaya</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">email</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Alamat email unik dan aktif untuk login akun.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">hendra@example.com</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">password</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Sandi masuk akun awal (min. 6 karakter).</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">pembimbing123</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">tipe</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Harus diisi salah satu dari 3 tipe: <strong>kejuruan</strong>, <strong>umum</strong>, atau <strong>keduanya</strong>.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">kejuruan</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">konsentrasi_keahlian</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Harus cocok persis dengan salah satu nama Konsentrasi Keahlian di database sekolah.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">Rekayasa Perangkat Lunak</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">kelas_diajar</td>
                                              <td class="px-4 py-2.5">Opsional. Daftar kelas binaan yang dibimbing guru. Dipisah menggunakan koma (`,`) jika lebih dari satu.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">XII RPL 1, XII RPL 2</td>
                                          </tr>
                                      </tbody>
                                  </table>
                              </div>
                          </div>

                          <!-- Section: Referensi Jurusan -->
                          <div class="space-y-3 p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                              <h4 class="text-xs font-bold text-slate-950 dark:text-white flex items-center gap-1.5">
                                  <i data-lucide="layers" class="w-4.5 h-4.5 text-emerald-500"></i>
                                  Daftar Nama Jurusan Resmi di Database (Klik Salin):
                              </h4>
                              <p class="text-[11px] text-slate-500">
                                  Kolom <strong>konsentrasi_keahlian</strong> pada file Excel Anda harus diisi persis menggunakan nama di bawah:
                              </p>
                              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                  @php
                                      $jurusans = \App\Models\KonsentrasiKeahlian::orderBy('nama' . '')->pluck('nama' . '');
                                  @endphp
                                  @forelse($jurusans as $jur)
                                      <div class="flex items-center justify-between p-2 bg-white dark:bg-slate-900 border border-slate-200/40 dark:border-slate-800/60 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200">
                                          <span class="truncate">{{ $jur }}</span>
                                          <button type="button" onclick="navigator.clipboard.writeText('{{ $jur }}'); alert('Berhasil menyalin nama jurusan!')" class="p-1 text-slate-400 hover:text-emerald-500 rounded transition-colors" title="Salin">
                                              <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                          </button>
                                      </div>
                                  @empty
                                      <span class="text-xs text-amber-500 italic">Belum ada jurusan yang terdaftar.</span>
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

        <!-- Mass Reset Modal -->
        <template x-teleport="body">
            <div x-show="massResetModalOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
                 style="display: none;"
                 @keydown.escape.window="if (!isResetting) massResetModalOpen = false">
                 
                 <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col"
                      @click.away="if (!isResetting) massResetModalOpen = false">
                      
                      <!-- Modal Header -->
                      <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between bg-amber-500/10">
                          <div class="flex items-center gap-3">
                              <div class="p-2 rounded-xl bg-amber-500/20 text-amber-500">
                                  <i data-lucide="key-round" class="w-5 h-5"></i>
                              </div>
                              <div>
                                  <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Reset Sandi Masal Guru Pembimbing</h3>
                                  <p class="text-xs text-slate-500 dark:text-slate-400">Sinkronkan username & kata sandi ke NIP resmi masing-masing</p>
                              </div>
                          </div>
                          <button x-show="!isResetting" @click="massResetModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                              <i data-lucide="x" class="w-5 h-5"></i>
                          </button>
                      </div>

                      <!-- Modal Body -->
                      <div class="p-6 space-y-4">
                          <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/50 dark:border-amber-800/40 text-xs text-amber-800 dark:text-amber-300 leading-relaxed space-y-2">
                              <p class="font-semibold flex items-center gap-1.5">
                                  <i data-lucide="info" class="w-4 h-4 shrink-0"></i>
                                  Ketentuan Reset Masal:
                              </p>
                              <ul class="list-disc pl-5 space-y-1">
                                  <li>Setiap akun pembimbing sekolah akan diaktifkan (<code>is_active = true</code>).</li>
                                  <li>Username akun login akan disinkronkan menjadi <strong>NIP</strong> masing-masing guru (atau username saat ini jika tanpa NIP).</li>
                                  <li>Kata sandi akan di-reset menjadi <strong>NIP</strong> masing-masing guru (default) atau <code>smkn1ciamis</code> jika NIP belum tercatat.</li>
                                  <li>Guru akan diminta memperbarui sandi saat login pertama kali demi keamanan.</li>
                                  <li><strong>Tidak mengubah</strong> relasi DUDI tempat bimbingan maupun pemetaan siswa yang sudah ada.</li>
                              </ul>
                          </div>

                          <!-- In-progress Progress Bar -->
                          <div x-show="isResetting" class="space-y-3 pt-2">
                              <div class="flex items-center justify-between text-xs">
                                  <span class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                      <svg class="animate-spin h-3.5 w-3.5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                      </svg>
                                      Memproses Reset Sandi...
                                  </span>
                                  <span class="font-bold text-amber-600 dark:text-amber-400" x-text="resetPercent + '%'"></span>
                              </div>
                              <div class="w-full bg-slate-200 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                                  <div class="bg-amber-500 h-full transition-all duration-300" :style="'width: ' + resetPercent + '%'"></div>
                              </div>
                              <p class="text-xs text-slate-500 dark:text-slate-400 text-center" x-text="resetMessage"></p>
                          </div>

                          <!-- Confirmation Prompt -->
                          <div x-show="!isResetting" class="space-y-3">
                              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                  Ketik <span class="font-mono text-red-500 bg-red-50 dark:bg-red-950/40 px-1.5 py-0.5 rounded border border-red-200 dark:border-red-900">RESET</span> untuk konfirmasi:
                              </label>
                              <input type="text" 
                                     x-model="confirmText" 
                                     placeholder="Ketik RESET di sini..." 
                                     class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                          </div>
                      </div>

                      <!-- Modal Footer -->
                      <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end gap-3 bg-slate-50/50 dark:bg-slate-800/30">
                          <button type="button" 
                                  x-show="!isResetting" 
                                  @click="massResetModalOpen = false" 
                                  class="px-4 py-2 border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                              Batal
                          </button>
                          <button type="button" 
                                  x-show="!isResetting"
                                  :disabled="confirmText !== 'RESET'" 
                                  @click="startMassReset()" 
                                  :class="confirmText === 'RESET' ? 'bg-amber-600 hover:bg-amber-500 text-white cursor-pointer shadow-lg shadow-amber-500/25' : 'bg-slate-200 dark:bg-slate-700 text-slate-400 cursor-not-allowed'"
                                  class="px-5 py-2 text-xs font-bold rounded-xl transition-all">
                              Mulai Reset Masal
                          </button>
                      </div>
                 </div>
            </div>
        </template>
    </div>

    @if(session('import_errors'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 text-sm">
            <h4 class="font-bold mb-2 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                Gagal Mengimpor Data Guru. Silakan periksa beberapa kesalahan berikut:
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

    <div x-data="{
        selectedIds: [],
        selectAll: false,
        toggleAll() {
            if (this.selectAll) {
                this.selectedIds = Array.from(document.querySelectorAll('.guru-checkbox:not(:disabled)')).map(el => el.value);
            } else {
                this.selectedIds = [];
            }
        },
        updateSelectAll() {
            const checkable = document.querySelectorAll('.guru-checkbox:not(:disabled)');
            this.selectAll = checkable.length > 0 && this.selectedIds.length === checkable.length;
        },
        confirmBulkDelete(e) {
            if (e) e.preventDefault();
            if (this.selectedIds.length === 0) return;
            const selectedBoxes = Array.from(document.querySelectorAll('.guru-checkbox:checked'));
            const names = selectedBoxes.map(el => el.getAttribute('data-name') || ('Guru #' + el.value));
            window.confirmDoubleDeletion({
                items: names,
                entityName: 'guru pembimbing',
                onConfirm: () => {
                    this.$refs.bulkDeleteForm.submit();
                }
            });
        }
    }">
        <!-- Bulk Action Bar -->
        <div x-show="selectedIds.length > 0" x-transition.opacity.duration.200ms class="flex items-center gap-3 mb-4" x-cloak>
            <form action="{{ route('pokja.pembimbing_sekolah.bulk_destroy') }}" method="POST" x-ref="bulkDeleteForm" @submit="confirmBulkDelete">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" class="px-4 py-2 text-sm whitespace-nowrap bg-red-600 hover:bg-red-500 text-white font-medium rounded-xl shadow-lg shadow-red-500/25 transition-all gap-2 flex items-center transform hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Hapus Terpilih (<span x-text="selectedIds.length"></span>)
                </button>
            </form>
        </div>

        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                            @if(auth()->user()->role !== 'kepala_sekolah')
                            <th class="w-12 px-6 py-4">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 dark:bg-slate-900 w-4 h-4 cursor-pointer">
                            </th>
                            @endif
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
                            @if(auth()->user()->role !== 'kepala_sekolah')
                            <td class="px-6 py-4 whitespace-nowrap w-12">
                                <input type="checkbox" 
                                       value="{{ $item->id }}" 
                                       data-name="{{ $item->nama_lengkap }}"
                                       x-model="selectedIds" 
                                       @change="updateSelectAll" 
                                       class="guru-checkbox rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 dark:bg-slate-900 w-4 h-4 cursor-pointer">
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-emerald-400 font-bold">
                                        {{ substr($item->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block truncate">{{ $item->nama_lengkap }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $item->user?->email ?: ($item->user?->username ?? '-') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-slate-700 dark:text-slate-300 block font-mono">{{ $item->nip ?? '-' }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                    <i data-lucide="user" class="w-3 h-3"></i>
                                    {{ $item->user?->username ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $tipeColors = [
                                        'kejuruan' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'umum' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'keduanya' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
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
                                    {{ $item->konsentrasiKeahlian?->nama ?? '-' }}
                                </span>
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
                                         class="absolute right-0 mt-8 w-40 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 shadow-lg py-1 z-50 text-left" 
                                         style="display: none;">
                                        <a href="{{ route('pokja.pembimbing_sekolah.show', $item) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                            <i data-lucide="eye" class="w-3.5 h-3.5 text-emerald-500"></i>
                                            Lihat Siswa
                                        </a>
                                        @if(auth()->user()->role !== 'kepala_sekolah')
                                        <a href="{{ route('pokja.pembimbing_sekolah.edit', $item) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-500"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('pokja.pembimbing_sekolah.reset_password', $item) }}" method="POST" onsubmit="return confirm('Reset kata sandi pembimbing {{ addslashes($item->nama_lengkap) }} menjadi NIP default?')">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-amber-600 dark:text-amber-400 hover:bg-amber-50/50 dark:hover:bg-amber-950/20 transition-colors text-left">
                                                <i data-lucide="key" class="w-3.5 h-3.5"></i>
                                                Reset Sandi (NIP)
                                            </button>
                                        </form>
                                        <form action="{{ route('pokja.pembimbing_sekolah.destroy', $item) }}" method="POST" data-confirm-delete="{{ $item->nama_lengkap }}" data-entity="guru pembimbing">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-950/20 transition-colors text-left cursor-pointer">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                Hapus
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'kepala_sekolah' ? 4 : 6 }}" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
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
    </div>
</x-app-layout>
