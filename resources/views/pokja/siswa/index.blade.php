<x-app-layout>
    @php
        $getUniqueBadgeClass = function($name) {
            if (!$name) return 'background-color: rgba(148, 163, 184, 0.1); color: rgb(148, 163, 184); border-color: rgba(148, 163, 184, 0.2);';
            $hash = crc32($name);
            $hue = abs($hash) % 360;
            return "--hue: {$hue};";
        };
    @endphp
    <x-slot name="header">Kelola Data Siswa PKL</x-slot>

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

    <div x-data="siswaPageManager()">
        <div class="mb-6 pokja-header-container">
            <div class="flex-1">
                <p class="text-slate-600 dark:text-slate-400 text-sm">Total data siswa yang terdaftar dalam sistem PKL.</p>
            </div>
            @if(auth()->user()->role !== 'kepala_sekolah')
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto shrink-0">
                <button type="button" @click="openBulkReset()" class="pokja-btn px-4 py-2 text-sm whitespace-nowrap bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 font-medium rounded-xl border border-amber-500/30 transition-all gap-2 cursor-pointer shadow-sm">
                    <i data-lucide="key-round" class="w-4 h-4"></i>
                    Reset Password ke NIS
                </button>
                <button @click="importPanelOpen = !importPanelOpen" class="pokja-btn px-4 py-2 text-sm whitespace-nowrap bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-xl transition-all gap-2 cursor-pointer border border-slate-700">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                    Impor Siswa
                </button>
                <a href="{{ route('pokja.siswa.create') }}" class="pokja-btn px-4 py-2 text-sm whitespace-nowrap bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Tambah Siswa
                </a>
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
                        Impor Data Siswa Massal
                    </h3>
                    <button @click="importPanelOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <!-- Panel Body Form -->
                <form action="{{ route('pokja.siswa.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <!-- Template & Guide Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Template -->
                        <div class="p-4 bg-blue-500/5 border border-blue-500/10 rounded-xl flex items-start gap-3">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6 text-blue-400 shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Gunakan Template Resmi</h4>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    Pastikan pengisian kolom sesuai dengan template standar kami agar sistem memprosesnya secara instan.
                                </p>
                                <a href="{{ route('pokja.import.template', 'siswa') }}" class="inline-flex items-center gap-1.5 mt-2.5 text-[11px] font-bold text-blue-500 hover:text-blue-400 transition-colors bg-blue-500/10 px-3 py-1.5 rounded-lg border border-blue-500/20">
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
                                    Lihat panduan penulisan kolom, format jenis kelamin, dan daftar nama jurusan resmi di sistem.
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
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Pilih Berkas Excel / CSV</label>
                        <div class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all relative group duration-300"
                             :class="localFileName ? 'border-emerald-500/50 bg-emerald-500/5 dark:bg-emerald-500/10' : 'border-slate-300 dark:border-slate-700 hover:border-blue-500 dark:hover:border-blue-500'">
                            <input type="file" name="file" accept=".xlsx, .xls, .csv, .txt" required 
                                   @change="localFileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <!-- State: Empty -->
                            <div class="flex flex-col items-center gap-2 py-4" x-show="!localFileName">
                                <i data-lucide="file-spreadsheet" class="w-8 h-8 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Klik atau Seret file Excel/CSV ke sini</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-500">Format yang didukung: .xlsx, .xls, .csv, .txt</span>
                            </div>

                            <!-- State: File Chosen -->
                            <div class="flex flex-col items-center gap-2 py-4" x-show="localFileName" x-cloak>
                                <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-500 animate-bounce"></i>
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block truncate max-w-xs" x-text="localFileName"></span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-500">Klik atau seret file lain untuk mengganti berkas</span>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Jurusan Row -->
                    <div class="space-y-2 p-4 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/50 dark:border-slate-700/50"
                         x-data="{
                             selected: [1, 2, 3, 4, 5, 6, 8, 9], // Semua kecuali DKV (7) secara default
                             selectAll() { this.selected = [1, 2, 3, 4, 5, 6, 7, 8, 9]; },
                             selectNonDkv() { this.selected = [1, 2, 3, 4, 5, 6, 8, 9]; },
                             clearAll() { this.selected = []; }
                         }">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pb-2 border-b border-slate-200/40 dark:border-slate-700/40">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                                    <i data-lucide="filter" class="w-4 h-4 text-blue-500"></i>
                                    Filter Jurusan yang Diimpor
                                </h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Pilih jurusan yang ingin dimasukkan ke sistem. Siswa dari jurusan yang tidak dicentang otomatis dilewati.
                                </p>
                            </div>
                            <div class="flex items-center gap-2 text-[11px]">
                                <button type="button" @click="selectNonDkv()" class="text-blue-500 hover:underline font-semibold cursor-pointer">
                                    Kecualikan DKV (Default)
                                </button>
                                <span class="text-slate-300 dark:text-slate-600">|</span>
                                <button type="button" @click="selectAll()" class="text-slate-600 dark:text-slate-400 hover:text-blue-500 cursor-pointer">
                                    Pilih Semua
                                </button>
                                <span class="text-slate-300 dark:text-slate-600">|</span>
                                <button type="button" @click="clearAll()" class="text-red-500 hover:underline cursor-pointer">
                                    Kosongkan
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 pt-2">
                            @php
                                $allKonsentrasis = \App\Models\KonsentrasiKeahlian::with('programKeahlian')->orderBy('id')->get();
                            @endphp
                            @foreach($allKonsentrasis as $kItem)
                                <label class="flex items-start gap-2.5 p-2 rounded-lg border transition-all cursor-pointer text-xs"
                                       :class="selected.includes({{ $kItem->id }}) 
                                               ? 'bg-blue-50/50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-800/50 text-slate-900 dark:text-slate-100 font-medium' 
                                               : 'bg-white dark:bg-slate-900/50 border-slate-200/60 dark:border-slate-800 text-slate-500 dark:text-slate-400 opacity-60'">
                                    <input type="checkbox" name="selected_jurusan[]" value="{{ $kItem->id }}"
                                           x-model.number="selected"
                                           class="rounded text-blue-600 focus:ring-blue-500 mt-0.5">
                                    <div class="flex-1 min-w-0">
                                        <span class="block truncate font-semibold">
                                            {{ $kItem->kode }} - {{ $kItem->nama }}
                                        </span>
                                        <span class="text-[10px] block text-slate-400 truncate">
                                            {{ $kItem->programKeahlian->nama ?? '' }}
                                        </span>
                                        @if($kItem->kode === 'DKV')
                                            <span class="text-[10px] font-bold text-amber-500 block">
                                                (PKL Beres - Dilewati)
                                            </span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Options: Tahun Ajaran & Verifikasi NIS -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">
                                Tahun Ajaran Default
                            </label>
                            <input type="text" name="tahun_ajaran" value="2025/2026" required
                                   class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-700/60 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-center pt-2 sm:pt-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-300">
                                <input type="checkbox" name="sync_mode" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                                <span><strong>Sinkronisasi & Koreksi NIS Otomatis</strong> (Periksa dan sesuaikan data jika NIS/nama siswa sudah ada di sistem agar tidak ganda)</span>
                            </label>
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
                              Panduan Impor Data Siswa Massal
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
                                  Struktur Kolom data Siswa:
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
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">nis</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Angka unik (NIS). Digunakan juga sebagai username login siswa.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">12345678</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">nama_lengkap</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Nama resmi siswa tanpa gelar.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">Ahmad Fauzi</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">email</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Alamat email unik dan aktif.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">ahmad@example.com</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">password</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Sandi akun awal masuk (min. 6 karakter).</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">password123</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">kelas</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Nama kelas lengkap.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">XII RPL 1</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">jenis_kelamin</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Isi dengan satu huruf tunggal: <strong>L</strong> (Laki-laki) atau <strong>P</strong> (Perempuan).</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">L</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">tahun_ajaran</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Format tahun ajaran.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">2025/2026</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">konsentrasi_keahlian</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Harus cocok persis dengan salah satu nama Konsentrasi Keahlian di bawah.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">Rekayasa Perangkat Lunak</td>
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
                                  Kolom <strong>konsentrasi_keahlian</strong> pada file Excel Anda harus diisi persis sama dengan nama-nama berikut:
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

        <!-- Reset All Passwords Confirmation & Progress Modal -->
        <template x-teleport="body">
            <div x-show="resetAllModalOpen" 
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
                 x-transition.opacity.duration.300ms x-cloak>
                 
                 <div @click.away="closeBulkReset()" 
                      class="glass-card w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl border border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-900 animate-fade-in-up text-left flex flex-col">
                      
                      <!-- Modal Header -->
                      <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center bg-amber-500/5 dark:bg-amber-500/10 shrink-0">
                          <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                              <i data-lucide="key-round" class="text-amber-500 w-5 h-5"></i>
                              Reset Password Semua Akun Siswa
                          </h3>
                          <button type="button" @click="closeBulkReset()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1 rounded-lg">
                              <i data-lucide="x" class="w-4 h-4"></i>
                          </button>
                      </div>

                      <!-- Modal Body -->
                      <div class="p-6 space-y-4 text-sm text-slate-600 dark:text-slate-400">
                          
                          <!-- STATE 1: Idle (Before Starting) -->
                          <div x-show="!resetIsRunning && !resetIsFinished && !resetIsCancelled && !resetError" class="space-y-4">
                              <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 space-y-2">
                                  <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                                      <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500"></i>
                                      Konfirmasi Reset Massal
                                  </div>
                                  <p class="text-xs leading-relaxed">
                                      Apakah Anda yakin ingin mengatur ulang password <strong>seluruh akun siswa</strong> menjadi default kembali sesuai <strong>NIS masing-masing</strong>?
                                  </p>
                              </div>

                              <div class="space-y-2 text-xs">
                                  <h4 class="font-bold text-slate-900 dark:text-slate-100">Dampak dari tindakan ini:</h4>
                                  <ul class="list-disc pl-5 space-y-1.5 leading-relaxed">
                                      <li>Siswa dapat login kembali dengan <strong>Username = NIS</strong> dan <strong>Password = NIS</strong>.</li>
                                      <li>Password yang pernah diubah sendiri oleh siswa akan ditimpa kembali dengan NIS mereka.</li>
                                      <li>Status force change password dinonaktifkan sehingga siswa bisa langsung masuk sistem.</li>
                                  </ul>
                              </div>

                              <p class="text-xs text-slate-500 italic">
                                  * Proses ini dijalankan secara bertahap (batch) dengan indikator progres realtime dan dapat dihentikan kapan saja.
                              </p>
                          </div>

                          <!-- STATE 2: Running (Progress Bar Active) -->
                          <div x-show="resetIsRunning" class="space-y-5 py-2" style="display: none;">
                              <div class="text-center space-y-2">
                                  <div class="inline-flex p-3 rounded-2xl bg-amber-500/10 text-amber-500 dark:text-amber-400 animate-pulse">
                                      <i data-lucide="refresh-cw" class="w-7 h-7 animate-spin"></i>
                                  </div>
                                  <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Mereset Password Siswa...</h4>
                                  <p class="text-xs text-slate-500">Sedang memproses akun siswa secara bertahap. Anda dapat membatalkan sewaktu-waktu.</p>
                              </div>

                              <!-- Progress Bar -->
                              <div class="space-y-2 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/50 dark:border-slate-700/50">
                                  <div class="flex justify-between items-center text-xs font-semibold">
                                      <span class="text-slate-700 dark:text-slate-300">
                                          <span class="font-bold text-amber-600 dark:text-amber-400" x-text="resetProcessed">0</span> dari <span x-text="resetTotal">0</span> Akun Siswa
                                      </span>
                                      <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold" x-text="resetPercent + '%'">0%</span>
                                  </div>
                                  <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden shadow-inner">
                                      <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-emerald-500 h-3 rounded-full transition-all duration-300 ease-out" 
                                           :style="'width: ' + resetPercent + '%'"></div>
                                  </div>
                              </div>
                          </div>

                          <!-- STATE 3: Finished / Success -->
                          <div x-show="resetIsFinished" class="space-y-4 py-3 text-center" style="display: none;">
                              <div class="inline-flex p-3.5 rounded-2xl bg-emerald-500/10 text-emerald-500 dark:text-emerald-400">
                                  <i data-lucide="check-circle-2" class="w-10 h-10"></i>
                              </div>
                              <div>
                                  <h4 class="text-base font-bold text-emerald-600 dark:text-emerald-400">Reset Password Selesai!</h4>
                                  <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                                      Berhasil mengatur ulang password <strong x-text="resetProcessed"></strong> akun siswa menjadi default (NIS masing-masing).
                                  </p>
                              </div>
                          </div>

                          <!-- STATE 4: Cancelled by User -->
                          <div x-show="resetIsCancelled && !resetIsFinished" class="space-y-4 py-3 text-center" style="display: none;">
                              <div class="inline-flex p-3 rounded-2xl bg-slate-500/10 text-slate-500 dark:text-slate-400">
                                  <i data-lucide="ban" class="w-8 h-8"></i>
                              </div>
                              <div>
                                  <h4 class="text-base font-bold text-slate-800 dark:text-slate-200">Proses Dihentikan</h4>
                                  <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                                      Proses reset dihentikan. Sebanyak <strong x-text="resetProcessed"></strong> dari <strong x-text="resetTotal"></strong> akun siswa telah berhasil direset sebelum dibatalkan.
                                  </p>
                              </div>
                          </div>

                          <!-- STATE 5: Error -->
                          <div x-show="resetError" class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-xs space-y-1" style="display: none;">
                              <div class="font-bold flex items-center gap-1.5">
                                  <i data-lucide="alert-circle" class="w-4 h-4"></i> Terjadi Kendala
                              </div>
                              <p x-text="resetError"></p>
                          </div>

                      </div>

                      <!-- Modal Footer -->
                      <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end items-center gap-3 bg-slate-50/50 dark:bg-slate-800/30 shrink-0">
                          
                          <!-- Buttons for Idle -->
                          <template x-if="!resetIsRunning && !resetIsFinished && !resetIsCancelled && !resetError">
                              <div class="flex items-center gap-2">
                                  <button type="button" @click="closeBulkReset()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                                      Batal
                                  </button>
                                  <button type="button" @click="startBulkReset()" class="inline-flex items-center gap-2 px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-amber-500/25 transition-all cursor-pointer">
                                      <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                                      Ya, Reset Semua Password
                                  </button>
                              </div>
                          </template>

                          <!-- Buttons for Running -->
                          <template x-if="resetIsRunning">
                              <div class="flex items-center gap-2">
                                  <button type="button" @click="stopBulkReset()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-red-500/25 transition-colors cursor-pointer">
                                      <i data-lucide="square" class="w-3.5 h-3.5"></i>
                                      Hentikan / Batalkan
                                  </button>
                              </div>
                          </template>

                          <!-- Buttons for Finished -->
                          <template x-if="resetIsFinished">
                              <div class="flex items-center gap-2">
                                  <button type="button" @click="finishBulkReset()" class="inline-flex items-center gap-1.5 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-500/25 transition-colors cursor-pointer">
                                      <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                      Tutup & Segarkan Halaman
                                  </button>
                              </div>
                          </template>

                          <!-- Buttons for Cancelled or Error -->
                          <template x-if="(resetIsCancelled || resetError) && !resetIsRunning && !resetIsFinished">
                              <div class="flex items-center gap-2">
                                  <button type="button" @click="closeBulkReset()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                                      Tutup
                                  </button>
                                  <button type="button" @click="startBulkReset()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-amber-500/25 transition-colors cursor-pointer">
                                      <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                      Lanjutkan Reset
                                  </button>
                              </div>
                          </template>

                      </div>
                 </div>
            </div>
        </template>
    </div>

    @if(session('import_errors'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 text-sm">
            <h4 class="font-bold mb-2 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                Gagal Mengimpor Data Siswa. Silakan periksa beberapa kesalahan berikut:
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

    @if(session('nis_corrections'))
        <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-sm">
            <h4 class="font-bold mb-2 flex items-center gap-2 text-xs uppercase tracking-wider">
                <i data-lucide="check-check" class="w-4 h-4 text-amber-500"></i>
                Data NIS Siswa Berhasil Diverifikasi & Dikoreksi:
            </h4>
            <ul class="list-disc pl-5 space-y-1 text-xs">
                @foreach(session('nis_corrections') as $cor)
                    <li>{{ $cor }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 text-sm flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-4 h-4"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="glass-card p-4 mb-6" x-data="{ showAdvanced: {{ request()->hasAny(['sort_by', 'sort_dir']) ? 'true' : 'false' }} }">
        <form action="{{ route('pokja.siswa.index') }}" method="GET" class="space-y-3">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NIS..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
                </div>
                
                <div class="md:w-64">
                    <select name="konsentrasi" onchange="this.form.submit()" 
                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm text-slate-800 dark:text-slate-200">
                        <option value="">Semua Konsentrasi</option>
                        @foreach($concentrations as $c)
                            <option value="{{ $c->id }}" {{ request('konsentrasi') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 items-center">
                    <button type="button" @click="showAdvanced = !showAdvanced" class="px-3 py-2.5 text-slate-500 hover:text-blue-500 hover:bg-blue-500/10 rounded-xl transition-colors border border-slate-200/50 dark:border-slate-700/50" title="Filter Lanjutan">
                        <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                    </button>
                    <button type="submit" class="flex-1 sm:flex-initial px-6 py-2.5 bg-slate-800 dark:bg-slate-700 text-white font-bold rounded-xl hover:bg-slate-700 transition-all text-sm cursor-pointer shadow-md">
                        Cari
                    </button>
                    @if(request()->anyFilled(['search', 'konsentrasi', 'sort_by', 'sort_dir']))
                        <a href="{{ route('pokja.siswa.index') }}" class="px-3 py-2.5 text-slate-500 hover:text-red-400 flex items-center justify-center transition-colors border border-slate-200/50 dark:border-slate-700 rounded-xl bg-slate-100/30" title="Reset">
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
                            <option value="created_at" {{ request('sort_by') === 'created_at' || !request('sort_by') ? 'selected' : '' }}>Waktu Pendaftaran</option>
                            <option value="nama_lengkap" {{ request('sort_by') === 'nama_lengkap' ? 'selected' : '' }}>Nama Lengkap (A-Z)</option>
                            <option value="nis" {{ request('sort_by') === 'nis' ? 'selected' : '' }}>NIS</option>
                            <option value="kelas" {{ request('sort_by') === 'kelas' ? 'selected' : '' }}>Kelas</option>
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
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Siswa</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Konsentrasi Keahlian / Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Penempatan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Status</th>
                        @if(auth()->user()->role !== 'kepala_sekolah')
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right whitespace-nowrap">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($students as $item)
                        <tr class="hover:bg-white dark:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-blue-400 font-bold">
                                        {{ substr($item->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block">{{ $item->nama_lengkap }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $item->nis }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-slate-800 dark:text-slate-200 block">{{ $item->konsentrasiKeahlian->nama }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $item->kelas }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1.5 items-start">
                                    <!-- DUDI -->
                                    @if($item->dudi)
                                        <span class="text-sm text-slate-700 dark:text-slate-300 font-semibold">{{ $item->dudi->nama }}</span>
                                    @else
                                        <span class="text-xs text-amber-500/80 bg-amber-500/5 px-2 py-0.5 rounded border border-amber-500/10">Belum diplot (DUDI)</span>
                                    @endif

                                    <!-- Guru Pembimbing -->
                                    @if($item->pembimbingSekolah || $item->pembimbingSekolahUmum)
                                        @if($item->pembimbingSekolah)
                                            <span style="{{ $getUniqueBadgeClass($item->pembimbingSekolah->nama_lengkap) }}" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border dynamic-badge">
                                                KJ: {{ $item->pembimbingSekolah->nama_lengkap }}
                                            </span>
                                        @endif
                                        @if($item->pembimbingSekolahUmum)
                                            <span style="{{ $getUniqueBadgeClass($item->pembimbingSekolahUmum->nama_lengkap) }}" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border dynamic-badge">
                                                UM: {{ $item->pembimbingSekolahUmum->nama_lengkap }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-[10px] text-slate-500/80 bg-slate-500/5 px-2 py-0.5 rounded border border-slate-500/10 font-bold uppercase tracking-wider">Guru: Belum diplot</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $hariIni = strtolower($item->status_hari_ini);
                                    if ($hariIni === 'masuk kerja') {
                                        $statusClass = 'bg-blue-500/10 text-blue-500 border-blue-500/20';
                                    } elseif ($hariIni === 'pulang kerja') {
                                        $statusClass = 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20';
                                    } elseif ($hariIni === 'selesai') {
                                        $statusClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                    } elseif ($hariIni === 'dibatalkan') {
                                        $statusClass = 'bg-red-500/10 text-red-500 border-red-500/20';
                                    } elseif ($hariIni === 'belum absen') {
                                        $statusClass = 'bg-slate-500/10 text-slate-500 border-slate-500/20';
                                    } else {
                                        $statusClass = 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20';
                                    }
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                    {{ $item->status_hari_ini }}
                                </span>
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
                                         class="absolute right-0 mt-8 w-44 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 shadow-lg py-1 z-50 text-left" 
                                         style="display: none;">
                                        <a href="{{ route('pokja.siswa.edit', $item) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-500"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('pokja.siswa.reset-password', $item) }}" method="POST" onsubmit="return confirm('Reset password akun siswa {{ addslashes($item->nama_lengkap) }} menjadi default (NIS: {{ $item->nis }})?')">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-amber-600 dark:text-amber-400 hover:bg-amber-50/50 dark:hover:bg-amber-950/20 transition-colors text-left cursor-pointer">
                                                <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                                                Reset Password (NIS)
                                            </button>
                                        </form>
                                        <form action="{{ route('pokja.siswa.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun siswa {{ addslashes($item->nama_lengkap) }} ini? Seluruh data terkait (jurnal, absensi, dll) juga akan terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-950/20 transition-colors text-left cursor-pointer">
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
                                Belum ada data siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    <script>
        function siswaPageManager() {
            return {
                importPanelOpen: false,
                guideModalOpen: false,
                resetAllModalOpen: false,

                // Reset Massal State
                resetIsRunning: false,
                resetIsCancelled: false,
                resetIsFinished: false,
                resetError: null,
                resetProcessed: 0,
                resetTotal: 0,
                resetPercent: 0,
                resetOffset: 0,
                resetBatchSize: 35,

                openBulkReset() {
                    this.resetAllModalOpen = true;
                    this.resetIsRunning = false;
                    this.resetIsCancelled = false;
                    this.resetIsFinished = false;
                    this.resetError = null;
                    this.resetProcessed = 0;
                    this.resetTotal = 0;
                    this.resetPercent = 0;
                    this.resetOffset = 0;
                    this.$nextTick(() => window.lucide?.createIcons());
                },

                closeBulkReset() {
                    if (this.resetIsRunning) {
                        if (!confirm('Proses reset password sedang berjalan. Yakin ingin membatalkan dan menutup jendela ini?')) {
                            return;
                        }
                        this.stopBulkReset();
                    }
                    this.resetAllModalOpen = false;
                },

                stopBulkReset() {
                    this.resetIsCancelled = true;
                    this.resetIsRunning = false;
                    this.$nextTick(() => window.lucide?.createIcons());
                },

                async startBulkReset() {
                    this.resetIsRunning = true;
                    this.resetIsCancelled = false;
                    this.resetIsFinished = false;
                    this.resetError = null;
                    if (this.resetPercent === 100) {
                        this.resetProcessed = 0;
                        this.resetPercent = 0;
                        this.resetOffset = 0;
                    }
                    this.$nextTick(() => window.lucide?.createIcons());

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    try {
                        while (this.resetIsRunning && !this.resetIsCancelled) {
                            const response = await fetch("{{ route('pokja.siswa.reset-passwords') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    offset: this.resetOffset,
                                    batch_size: this.resetBatchSize
                                })
                            });

                            if (!response.ok) {
                                const err = await response.json().catch(() => ({}));
                                throw new Error(err.message || ('Server error HTTP ' + response.status));
                            }

                            const data = await response.json();
                            if (data.status !== 'success') {
                                throw new Error(data.message || 'Gagal memproses batch reset password.');
                            }

                            this.resetTotal = data.total;
                            this.resetProcessed = data.processed_total;
                            this.resetPercent = data.percent;
                            this.resetOffset = data.offset;

                            if (data.done || this.resetOffset >= data.total) {
                                this.resetIsFinished = true;
                                this.resetIsRunning = false;
                                this.resetPercent = 100;
                                this.$nextTick(() => window.lucide?.createIcons());
                                break;
                            }

                            // Jeda singkat agar render UI halus dan responsif
                            await new Promise(res => setTimeout(res, 60));
                        }
                    } catch (err) {
                        this.resetError = err.message || 'Gagal memproses reset password siswa.';
                        this.resetIsRunning = false;
                    } finally {
                        this.$nextTick(() => window.lucide?.createIcons());
                    }
                },

                finishBulkReset() {
                    window.location.reload();
                }
            };
        }
    </script>
</x-app-layout>
