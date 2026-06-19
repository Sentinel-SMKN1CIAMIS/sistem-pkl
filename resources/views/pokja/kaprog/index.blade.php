<x-app-layout>
    <x-slot name="header">Kelola Akun Kaprog</x-slot>

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

    <div x-data="{ importPanelOpen: false, guideModalOpen: false }">
        <div class="mb-6 pokja-header-container">
            <p class="text-slate-600 dark:text-slate-400">Daftar Kepala Program Keahlian (Kaprog) per program keahlian.</p>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <button @click="importPanelOpen = !importPanelOpen" class="pokja-btn px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-xl transition-all gap-2 cursor-pointer border border-slate-700">
                    <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                    Impor Kaprog
                </button>
                <a href="{{ route('pokja.kaprog.create') }}" class="pokja-btn px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl shadow-lg shadow-blue-500/25 transition-all gap-2">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                    Tambah Kaprog
                </a>
            </div>
        </div>

        <!-- Inline Import Panel -->
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
                        Impor Data Akun Kaprog
                    </h3>
                    <button @click="importPanelOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <!-- Panel Body Form -->
                <form action="{{ route('pokja.kaprog.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <!-- Template & Guide Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Template -->
                        <div class="p-4 bg-blue-500/5 border border-blue-500/10 rounded-xl flex items-start gap-3">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6 text-blue-400 shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100">Gunakan Template Resmi</h4>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    Unduh template standar agar struktur kolom data Kaprog terisi secara tepat.
                                </p>
                                <a href="{{ route('pokja.import.template', 'kaprog') }}" class="inline-flex items-center gap-1.5 mt-2.5 text-[11px] font-bold text-blue-500 hover:text-blue-400 transition-colors bg-blue-500/10 px-3 py-1.5 rounded-lg border border-blue-500/20">
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
                                    Lihat panduan penulisan kolom, ejaan nama jurusan, dan validasi data Kaprog.
                                </p>
                                <button type="button" @click="guideModalOpen = true" class="inline-flex items-center gap-1.5 mt-2.5 text-[11px] font-bold text-emerald-500 hover:text-emerald-400 transition-colors bg-emerald-500/10 px-3 py-1.5 rounded-lg border border-emerald-500/20 cursor-pointer">
                                    <i data-lucide="help-circle" class="w-3.5 h-3.5"></i>
                                    Buka Panduan Impor
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Upload Input -->
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

        <!-- Guide Modal Popup -->
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
                              Panduan Impor Data Akun Kaprog
                          </h3>
                          <button @click="guideModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                              <i data-lucide="x" class="w-5 h-5"></i>
                          </button>
                      </div>

                      <!-- Modal Body -->
                      <div class="p-6 overflow-y-auto space-y-6 text-sm text-slate-600 dark:text-slate-400">
                          <div class="space-y-2">
                              <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                                  <i data-lucide="info" class="w-4 h-4 text-blue-500"></i>
                                  Ketentuan Umum Impor Excel:
                              </h4>
                              <ul class="list-disc pl-5 space-y-1 text-xs leading-relaxed">
                                  <li>Jika ada <strong>satu baris data saja yang salah/gagal</strong>, seluruh proses impor dibatalkan (rollback) demi integritas data.</li>
                                  <li>Wajib menggunakan format berkas spreadsheet Excel asli (<strong>.xlsx</strong> / <strong>.xls</strong>).</li>
                              </ul>
                          </div>

                          <div class="space-y-3">
                              <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                                  <i data-lucide="table" class="w-4 h-4 text-blue-500"></i>
                                  Struktur Kolom Data Kaprog:
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
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Nama lengkap kaprog beserta gelar.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">Drs. Ahmad Yusuf, M.T.</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">username</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Username unik tanpa spasi untuk login.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">ahmadyusuf</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">email</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Email unik dan aktif.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">ahmad@example.com</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">password</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Sandi minimal 6 karakter.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">kaprog123</td>
                                          </tr>
                                          <tr>
                                              <td class="px-4 py-2.5 font-semibold text-slate-900 dark:text-slate-100">program_keahlian</td>
                                              <td class="px-4 py-2.5"><strong>Wajib.</strong> Harus cocok persis dengan salah satu nama Program Keahlian di database sekolah.</td>
                                              <td class="px-4 py-2.5 text-right font-mono text-blue-500">Pengembangan Perangkat Lunak dan Gim</td>
                                          </tr>
                                      </tbody>
                                  </table>
                              </div>
                          </div>

                          <!-- Reference section -->
                          <div class="space-y-3 p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                              <h4 class="text-xs font-bold text-slate-950 dark:text-white flex items-center gap-1.5">
                                  <i data-lucide="layers" class="w-4.5 h-4.5 text-emerald-500"></i>
                                  Daftar Nama Program Keahlian Resmi di Database (Klik Salin):
                              </h4>
                              <p class="text-[11px] text-slate-500">
                                  Kolom <strong>program_keahlian</strong> pada berkas Excel Anda harus diisi sama persis menggunakan nama di bawah ini:
                              </p>
                              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                  @forelse($programs as $jur)
                                      <div class="flex items-center justify-between p-2 bg-white dark:bg-slate-900 border border-slate-200/40 dark:border-slate-800/60 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200">
                                          <span class="truncate">{{ $jur->nama }}</span>
                                          <button type="button" onclick="navigator.clipboard.writeText('{{ $jur->nama }}'); alert('Berhasil menyalin nama program keahlian!')" class="p-1 text-slate-400 hover:text-emerald-500 rounded transition-colors" title="Salin">
                                              <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                          </button>
                                      </div>
                                  @empty
                                      <span class="text-xs text-amber-500 italic">Belum ada program keahlian yang terdaftar.</span>
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
    </div>

    @if(session('import_errors'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 text-sm">
            <h4 class="font-bold mb-2 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 shrink-0"></i>
                Gagal Mengimpor Data Kaprog. Silakan periksa kesalahan berikut:
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
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Search & Filter Controls -->
    <div class="glass-card p-6 mb-6">
        <form action="{{ route('pokja.kaprog.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Pencarian</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nama, username, email..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all">
                    <div class="absolute left-3.5 top-3.5 text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>
            
            <div>
                <label for="program" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Program Keahlian</label>
                <select name="program" id="program" class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all">
                    <option value="">Semua Program Keahlian</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" {{ request('program') == $prog->id ? 'selected' : '' }}>{{ $prog->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl transition-all text-sm flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Terapkan
                </button>
                @if(request()->anyFilled(['search', 'program']))
                    <a href="{{ route('pokja.kaprog.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-xl transition-all text-sm flex items-center justify-center" title="Reset filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-800/30">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Nama Lengkap</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Username</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Program Keahlian</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($kaprogs as $item)
                        <tr class="hover:bg-white dark:bg-slate-800/20 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-blue-500 font-bold">
                                        {{ substr($item->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100 block truncate">{{ $item->name }}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $item->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 font-mono">
                                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                    {{ $item->username }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-slate-600 dark:text-slate-400">
                                    {{ $item->programKeahlian->nama ?? 'Belum Ditentukan' }}
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
                                        <a href="{{ route('pokja.kaprog.edit', $item) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-500"></i>
                                            Edit
                                        </a>
                                        <form action="{{ route('pokja.kaprog.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Kaprog {{ addslashes($item->name) }} ini?')">
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
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic">
                                Belum ada data akun Kaprog.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kaprogs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $kaprogs->links() }}
            </div>
        @endif
    </div>

    <!-- Trigger icons rendering -->
    <script>
        document.addEventListener('alpine:init', () => {
            lucide.createIcons();
        });
        window.addEventListener('load', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</x-app-layout>
