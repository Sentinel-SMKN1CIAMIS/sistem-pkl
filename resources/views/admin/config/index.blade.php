<x-app-layout>
    <x-slot name="header">Konfigurasi Sistem</x-slot>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-slate-600 dark:text-slate-400">Kelola pengaturan dasar aplikasi seperti Nama Sekolah, Tahun Ajaran, logo, dan ekspor data.</p>
            </div>
            <a href="{{ route('admin.custom_report.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-lg shadow-blue-500/20 transition-all shrink-0">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Buka Custom Report & Ekspor Data
            </a>
        </div>

        <!-- Banner Shortcut ke Custom Dynamic Report & Ekspor -->
        <div class="glass-card p-5 rounded-2xl border border-blue-500/30 bg-gradient-to-r from-blue-500/10 via-indigo-500/5 to-purple-500/10 mb-6 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="p-3 rounded-2xl bg-blue-600 text-white shadow-md shadow-blue-500/25 shrink-0">
                    <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        Custom Dynamic Report & Automated Excel/Pivot Builder
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-600 dark:text-blue-400">Fitur Baru</span>
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                        Ekspor username & password akun siswa (grup angkatan), akun pembimbing sekolah, dan penempatan PKL ke format Excel (.xlsx) dengan Sheet Pivot otomatis.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.custom_report.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/25 transition-all shrink-0">
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                Buka Menu Ekspor
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 text-sm flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="glass-card p-8 shadow-2xl shadow-blue-500/10">
            <form action="{{ route('admin.config.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @php
                        $defaultConfigs = [
                            ['key' => 'app_name', 'label' => 'Nama Aplikasi', 'placeholder' => 'Contoh: Sistem Informasi PKL SMKN 1 Ciamis'],
                            ['key' => 'tahun_ajaran', 'label' => 'Tahun Ajaran Aktif', 'placeholder' => 'Contoh: 2023/2024'],
                            ['key' => 'kontak_admin', 'label' => 'Kontak Pokja (WA)', 'placeholder' => '0812XXXXXXXX'],
                        ];
                    @endphp

                    @foreach($defaultConfigs as $item)
                        @php
                            $value = $configs->where('key', $item['key'])->first()?->value;
                        @endphp
                        <div>
                            <label for="{{ $item['key'] }}" class="block text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">{{ $item['label'] }}</label>
                            <input type="text" name="{{ $item['key'] }}" id="{{ $item['key'] }}" value="{{ $value }}" placeholder="{{ $item['placeholder'] }}"
                                   class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                    @endforeach

                    <!-- Logo Upload Field with Preview -->
                    @php
                        $logoUrl = $configs->where('key', 'app_logo_url')->first()?->value;
                    @endphp
                    <div>
                        <label class="block text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Logo Aplikasi</label>
                        <div class="flex items-center gap-4 p-3 bg-slate-100/50 dark:bg-slate-900/30 border border-slate-200/30 dark:border-slate-700/30 rounded-2xl">
                            <div class="relative w-16 h-16 rounded-xl bg-slate-200 dark:bg-slate-800 border border-slate-300/50 dark:border-slate-700/50 overflow-hidden flex items-center justify-center shrink-0">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="Logo Aplikasi" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="image" class="w-6 h-6 text-slate-400"></i>
                                @endif
                            </div>
                            <div class="space-y-1.5 w-full">
                                <input type="file" name="app_logo" id="app_logo" accept="image/png, image/jpeg, image/jpg"
                                       class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-black file:uppercase file:tracking-wider file:bg-blue-500/10 file:text-blue-400 hover:file:bg-blue-500/20 transition-all cursor-pointer">
                                <p class="text-[9px] text-slate-400 dark:text-slate-500">Mendukung format PNG, JPG, JPEG (Maks. 2MB)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-xl shadow-lg shadow-blue-500/25 transition-all text-xs uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Semua Pengaturan
                    </button>
                </div>
            </form>
        </div>
        
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Backup Card -->
            <div class="p-6 rounded-2xl bg-linear-to-br from-blue-500/5 to-indigo-500/5 border border-blue-500/10 dark:border-blue-500/5 shadow-xl relative overflow-hidden group">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all duration-500"></div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 rounded-lg bg-blue-500/10 text-blue-400">
                        <i data-lucide="database" class="w-5 h-5"></i>
                    </div>
                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">Backup Database</h4>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">Lakukan pencadangan database secara rutin. Berkas cadangan akan disimpan di server lokal dan dapat diunduh untuk keamanan data fisik.</p>
                <a href="{{ route('admin.config.backup') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition-all shadow-md shadow-blue-500/10">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    Buat Cadangan di Server
                </a>

                @if(session('last_backup_file'))
                    <div class="mt-4 pt-4 border-t border-slate-200/50 dark:border-slate-700/50 space-y-2">
                        <span class="flex items-center gap-1.5 text-[10px] text-emerald-500 dark:text-emerald-400 font-bold uppercase tracking-wider">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-400"></i>
                            Cadangan Sukses Ditulis ke Server!
                        </span>
                        <p class="text-[10px] text-slate-500 leading-normal mb-1">Nama berkas: <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-900 font-mono text-[9px]">{{ session('last_backup_file') }}</code></p>
                        <a href="{{ route('admin.config.download-backup', session('last_backup_file')) }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 border border-emerald-500/20 hover:border-emerald-500/55 rounded-lg transition-all">
                            <i data-lucide="download" class="w-3 h-3"></i>
                            Download Berkas SQL Cadangan
                        </a>
                    </div>
                @endif
            </div>

            <!-- Wipe Card -->
            <div class="p-6 rounded-2xl bg-linear-to-br from-red-500/5 to-amber-500/5 border border-red-500/10 dark:border-red-500/5 shadow-xl relative overflow-hidden group">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-red-500/10 rounded-full blur-2xl group-hover:bg-red-500/20 transition-all duration-500"></div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 rounded-lg bg-red-500/10 text-red-400">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </div>
                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">Bersihkan Sistem (Wipe)</h4>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">Menghapus seluruh data transaksi (absensi, jurnal, laporan, dll.) untuk memulai tahun ajaran baru.</p>
                
                @if(session('database_backed_up'))
                    <form action="{{ route('admin.config.wipe') }}" method="POST" onsubmit="return confirm('PENTING: Apakah Anda yakin ingin menghapus seluruh data transaksi? Tindakan ini bersifat permanen dan tidak dapat dibatalkan!')" class="space-y-3">
                        @csrf
                        <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[11px] flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                            <span>Database sudah dicadangkan di server. Silakan konfirmasi sandi Anda untuk melanjutkan.</span>
                        </div>
                        <div class="space-y-1.5">
                            <label for="password" class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Konfirmasi Password Admin</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                                </span>
                                <input type="password" name="password" id="password" required placeholder="Masukkan password Anda"
                                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-slate-800 dark:text-slate-200 transition-all">
                            </div>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-all shadow-md shadow-red-500/10 cursor-pointer w-full justify-center">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                            Mulai Bersihkan Data Transaksi
                        </button>
                    </form>
                @else
                    <div class="space-y-3">
                        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-500 dark:text-amber-400 text-[11px] flex items-start gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                            <span>Fitur terkunci. Anda wajib mencadangkan **Backup** di server terlebih dahulu pada menu di samping sebelum dapat melakukan wipe data.</span>
                        </div>
                        <button disabled class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-400 dark:text-slate-600 bg-slate-200 dark:bg-slate-800/50 border border-slate-300/10 rounded-xl w-full justify-center opacity-60 cursor-not-allowed">
                            <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                            Wipe Data Terkunci
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pemetaan Pembimbing & DUDI Import Card -->
        <div class="mt-8 glass-card p-6 shadow-xl relative overflow-hidden" x-data="pemetaanImporter()">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-500">
                        <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">Impor Pemetaan Pembimbing & DUDI PKL</h4>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">Smart Filter & Disambiguasi</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-normal mt-0.5">
                            Otomatis memetakan siswa ke tempat PKL, membuat akun pembimbing sekolah (disambiguasi kata 1 & 2), mendaftarkan master DUDI, serta mengecualikan siswa yang sudah selesai (DKV).
                        </p>
                    </div>
                </div>
            </div>

            <!-- Smart Filter Jurusan -->
            <div class="p-4 rounded-xl bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200/50 dark:border-slate-800/50 space-y-3 mb-6">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="filter" class="w-3.5 h-3.5 text-blue-500"></i>
                        Pilih Program Keahlian / Jurusan yang Diimpor (Smart Filter):
                    </label>
                    <span class="text-[10px] text-slate-400 italic">* DKV dinonaktifkan secara default karena sudah selesai PKL</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                        <input type="checkbox" value="AKL" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="font-medium text-slate-700 dark:text-slate-300">AKL (Akuntansi)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                        <input type="checkbox" value="PBS" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="font-medium text-slate-700 dark:text-slate-300">PBS (Perbankan Syariah)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                        <input type="checkbox" value="MPLB" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="font-medium text-slate-700 dark:text-slate-300">MPLB (Perkantoran)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                        <input type="checkbox" value="PM" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="font-medium text-slate-700 dark:text-slate-300">PM (Bisnis Ritel)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                        <input type="checkbox" value="PBD" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="font-medium text-slate-700 dark:text-slate-300">PBD (Bisnis Digital)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                        <input type="checkbox" value="KLN" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Kuliner (Tata Boga)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                        <input type="checkbox" value="HTL" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Perhotelan</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 hover:border-blue-500 transition-all">
                        <input type="checkbox" value="PPLG" x-model="selectedJurusan" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="font-medium text-slate-700 dark:text-slate-300">PPLG (RPL)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-400 hover:border-amber-500 transition-all col-span-2 sm:col-span-4">
                        <input type="checkbox" value="DKV" x-model="selectedJurusan" class="rounded text-amber-600 focus:ring-amber-500">
                        <span class="font-medium">DKV (Beres PKL - dinonaktifkan secara default)</span>
                    </label>
                </div>
            </div>

            <!-- Options & Source -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-xs">
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Password Awal Akun Guru Baru</label>
                    <input type="text" x-model="defaultPassword" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-mono text-xs focus:ring-2 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-400">Password default yang otomatis diset saat membuat akun pembimbing sekolah baru.</p>
                </div>

                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Pilih Berkas Pemetaan (Opsional)</label>
                    <input type="file" @change="fileToUpload = $event.target.files[0]" accept=".csv,.xlsx,.txt" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-amber-500/10 file:text-amber-500 hover:file:bg-amber-500/20 cursor-pointer">
                    <p class="text-[10px] text-slate-400">Biarkan kosong untuk otomatis memakai master berkas pemetaan 2026/2027 yang tersimpan di server.</p>
                </div>
            </div>

            <!-- Action Trigger -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-200/50 dark:border-slate-800/50">
                <div class="text-[11px] text-slate-500">
                    <span class="font-bold text-slate-700 dark:text-slate-300" x-text="selectedJurusan.length"></span> jurusan dipilih untuk dipetakan.
                </div>
                <button type="button" @click="startImportProcess()" :disabled="selectedJurusan.length === 0" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-lg shadow-amber-500/25 transition-all cursor-pointer">
                    <i data-lucide="play" class="w-4 h-4"></i>
                    Mulai Impor & Petakan Data Siswa
                </button>
            </div>

            <!-- Progress & Summary Modal -->
            <template x-teleport="body">
                <div x-show="modalOpen" 
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
                     x-transition.opacity.duration.300ms x-cloak>
                     
                     <div @click.away="closeModal()" 
                          class="glass-card w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl border border-slate-200/50 dark:border-slate-700/50 bg-white dark:bg-slate-900 animate-fade-in-up text-left flex flex-col">
                          
                          <!-- Modal Header -->
                          <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center bg-amber-500/5 dark:bg-amber-500/10 shrink-0">
                              <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                  <i data-lucide="file-spreadsheet" class="text-amber-500 w-5 h-5"></i>
                                  Proses Impor Pemetaan PKL
                              </h3>
                              <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                  <i data-lucide="x" class="w-4 h-4"></i>
                              </button>
                          </div>

                          <!-- Modal Body -->
                          <div class="p-6 space-y-4 text-xs text-slate-600 dark:text-slate-400">
                              <!-- Running State -->
                              <div x-show="isRunning" class="space-y-4 text-center py-2">
                                  <div class="inline-flex p-3 rounded-2xl bg-amber-500/10 text-amber-500 animate-pulse">
                                      <i data-lucide="refresh-cw" class="w-7 h-7 animate-spin"></i>
                                  </div>
                                  <div>
                                      <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Memproses Data Pemetaan...</h4>
                                      <p class="text-[11px] text-slate-500">Memetakan siswa, guru pembimbing, dan DUDI secara bertahap.</p>
                                  </div>

                                  <!-- Progress Bar -->
                                  <div class="space-y-1.5 text-left p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/50 dark:border-slate-700/50">
                                      <div class="flex justify-between items-center text-xs font-semibold">
                                          <span><strong class="text-amber-600 dark:text-amber-400" x-text="processed"></strong> dari <span x-text="total"></span> Data</span>
                                          <span class="font-bold text-amber-600 dark:text-amber-400" x-text="percent + '%'">0%</span>
                                      </div>
                                      <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden shadow-inner">
                                          <div class="bg-gradient-to-r from-amber-500 to-emerald-500 h-3 rounded-full transition-all duration-300" :style="'width: ' + percent + '%'"></div>
                                      </div>
                                  </div>
                              </div>

                              <!-- Finished State -->
                              <div x-show="isFinished" class="space-y-4 py-2" style="display: none;">
                                  <div class="text-center space-y-2">
                                      <div class="inline-flex p-3 rounded-2xl bg-emerald-500/10 text-emerald-500">
                                          <i data-lucide="check-circle-2" class="w-8 h-8"></i>
                                      </div>
                                      <h4 class="text-base font-bold text-emerald-600 dark:text-emerald-400">Impor Pemetaan Berhasil Selesai!</h4>
                                  </div>

                                  <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/50 dark:border-slate-700/50 text-[11px]">
                                      <div class="p-2.5 rounded-lg bg-blue-500/10">
                                          <span class="text-blue-500 font-bold block">Pembimbing Sekolah:</span>
                                          <span class="font-bold text-sm text-slate-800 dark:text-slate-200" x-text="summary.pembimbing_created + ' Baru / ' + summary.pembimbing_matched + ' Cocok'"></span>
                                      </div>
                                      <div class="p-2.5 rounded-lg bg-indigo-500/10">
                                          <span class="text-indigo-500 font-bold block">Tempat DUDI:</span>
                                          <span class="font-bold text-sm text-slate-800 dark:text-slate-200" x-text="summary.dudi_created + ' Baru / ' + summary.dudi_matched + ' Cocok'"></span>
                                      </div>
                                      <div class="p-2.5 rounded-lg bg-emerald-500/10 col-span-2">
                                          <span class="text-emerald-500 font-bold block">Siswa Berhasil Dipetakan (PKL):</span>
                                          <span class="font-bold text-sm text-slate-800 dark:text-slate-200" x-text="summary.siswa_mapped + ' Siswa'"></span>
                                      </div>
                                  </div>
                              </div>

                              <!-- Error State -->
                              <div x-show="errorMessage" class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 space-y-1" style="display: none;">
                                  <p class="font-bold">Terjadi Kesalahan</p>
                                  <p x-text="errorMessage"></p>
                              </div>
                          </div>

                          <!-- Modal Footer -->
                          <div class="px-6 py-4 border-t border-slate-200/50 dark:border-slate-700/50 flex justify-end gap-2 bg-slate-50/50 dark:bg-slate-800/30">
                              <template x-if="isRunning">
                                  <button type="button" @click="cancelImport()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-md transition-all">
                                      Hentikan / Batalkan
                                  </button>
                              </template>
                              <template x-if="isFinished">
                                  <button type="button" @click="finishAndReload()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition-all">
                                      Selesai & Segarkan Halaman
                                  </button>
                              </template>
                              <template x-if="!isRunning && !isFinished">
                                  <button type="button" @click="closeModal()" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl">
                                      Tutup
                                  </button>
                              </template>
                          </div>
                     </div>
                </div>
            </template>
        </div>

        <script>
            function pemetaanImporter() {
                return {
                    selectedJurusan: ['AKL', 'PBS', 'MPLB', 'PM', 'PBD', 'KLN', 'HTL', 'PPLG'],
                    defaultPassword: 'smkn1ciamis',
                    fileToUpload: null,
                    modalOpen: false,
                    isRunning: false,
                    isCancelled: false,
                    isFinished: false,
                    errorMessage: '',
                    processed: 0,
                    total: 0,
                    percent: 0,
                    offset: 0,
                    batchSize: 50,
                    summary: {
                        pembimbing_created: 0,
                        pembimbing_matched: 0,
                        dudi_created: 0,
                        dudi_matched: 0,
                        siswa_mapped: 0
                    },

                    closeModal() {
                        if (this.isRunning) {
                            if (!confirm('Impor sedang berjalan. Yakin ingin membatalkan?')) return;
                            this.cancelImport();
                        }
                        this.modalOpen = false;
                    },

                    cancelImport() {
                        this.isCancelled = true;
                        this.isRunning = false;
                        this.$nextTick(() => window.lucide?.createIcons());
                    },

                    async startImportProcess() {
                        this.modalOpen = true;
                        this.isRunning = true;
                        this.isCancelled = false;
                        this.isFinished = false;
                        this.errorMessage = '';
                        this.processed = 0;
                        this.total = 0;
                        this.percent = 0;
                        this.offset = 0;
                        this.summary = {
                            pembimbing_created: 0,
                            pembimbing_matched: 0,
                            dudi_created: 0,
                            dudi_matched: 0,
                            siswa_mapped: 0
                        };
                        this.$nextTick(() => window.lucide?.createIcons());

                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        let cacheId = null;

                        try {
                            while (this.isRunning && !this.isCancelled) {
                                const formData = new FormData();
                                if (cacheId) {
                                    formData.append('cache_id', cacheId);
                                } else if (this.fileToUpload) {
                                    formData.append('file', this.fileToUpload);
                                }
                                formData.append('offset', this.offset);
                                formData.append('batch_size', this.batchSize);
                                formData.append('default_password', this.defaultPassword);
                                this.selectedJurusan.forEach(j => formData.append('jurusan_filter[]', j));

                                const res = await fetch("{{ route('admin.config.import-pemetaan') }}", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': token,
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: formData
                                });

                                if (!res.ok) {
                                    const err = await res.json().catch(() => ({}));
                                    throw new Error(err.message || ('Server error HTTP ' + res.status));
                                }

                                const data = await res.json();
                                if (data.status !== 'success') {
                                    throw new Error(data.message || 'Gagal memproses batch impor pemetaan.');
                                }

                                if (data.cache_id) {
                                    cacheId = data.cache_id;
                                }

                                this.total = data.total;
                                this.processed = data.processed_total;
                                this.percent = data.percent;
                                this.offset = data.offset;

                                if (data.stats) {
                                    this.summary.pembimbing_created += (data.stats.pembimbing_created || 0);
                                    this.summary.pembimbing_matched += (data.stats.pembimbing_matched || 0);
                                    this.summary.dudi_created += (data.stats.dudi_created || 0);
                                    this.summary.dudi_matched += (data.stats.dudi_matched || 0);
                                    this.summary.siswa_mapped += (data.stats.siswa_mapped || 0);
                                }

                                if (data.done || this.offset >= data.total) {
                                    this.isFinished = true;
                                    this.isRunning = false;
                                    this.percent = 100;
                                    this.$nextTick(() => window.lucide?.createIcons());
                                    break;
                                }

                                await new Promise(r => setTimeout(r, 60));
                            }
                        } catch (err) {
                            this.errorMessage = err.message || 'Terjadi kesalahan saat mengimpor pemetaan.';
                            this.isRunning = false;
                        } finally {
                            this.$nextTick(() => window.lucide?.createIcons());
                        }
                    },

                    finishAndReload() {
                        window.location.reload();
                    }
                };
            }
        </script>

        <!-- Backup History Section -->
        <div class="mt-8 glass-card p-6 shadow-xl relative overflow-hidden">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-lg bg-slate-500/10 text-slate-400">
                    <i data-lucide="history" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">Riwayat Cadangan Database di Server</h4>
                    <p class="text-[11px] text-slate-500 leading-normal">Daftar berkas SQL cadangan yang tersimpan secara fisik di server local.</p>
                </div>
            </div>

            @if(count($backupFiles) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/50 dark:border-slate-800/50 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                                <th class="pb-3 pr-4">Nama Berkas</th>
                                <th class="pb-3 px-4">Tanggal Pembuatan</th>
                                <th class="pb-3 px-4">Ukuran</th>
                                <th class="pb-3 pl-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            @foreach($backupFiles as $file)
                                <tr class="text-slate-600 dark:text-slate-300 hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-all">
                                    <td class="py-3 pr-4 font-mono text-[11px] font-bold tracking-tight">{{ $file['filename'] }}</td>
                                    <td class="py-3 px-4 text-slate-500">{{ $file['created_at']->translatedFormat('d F Y, H:i') }}</td>
                                    <td class="py-3 px-4 text-slate-500">{{ number_format($file['size'] / 1024, 1) }} KB</td>
                                    <td class="py-3 pl-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.config.download-backup', $file['filename']) }}" 
                                               class="p-2 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 transition-colors"
                                               title="Unduh Berkas">
                                                <i data-lucide="download" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('admin.config.delete-backup', $file['filename']) }}" method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus file cadangan ini dari server?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 transition-colors cursor-pointer"
                                                        title="Hapus Berkas">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-8 text-center text-slate-500 border border-dashed border-slate-200/50 dark:border-slate-800/50 rounded-2xl">
                    <i data-lucide="archive-x" class="w-8 h-8 text-slate-400 mx-auto mb-2 opacity-50"></i>
                    <p class="text-xs">Belum ada riwayat cadangan database di server.</p>
                </div>
            @endif
        </div>

        <!-- System Error Log Section -->
        <div class="mt-8 glass-card p-6 shadow-xl relative overflow-hidden">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-lg bg-red-500/10 text-red-400">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">Log Error Sistem (laravel.log)</h4>
                    <p class="text-[11px] text-slate-500 leading-normal">Menampilkan 10 error terakhir yang tercatat oleh sistem.</p>
                </div>
            </div>

            @if(count($errorLogs) > 0)
                <div class="overflow-x-auto">
                    <div class="max-h-96 overflow-y-auto pr-2 rounded-xl border border-slate-200/50 dark:border-slate-800/50 bg-slate-50/50 dark:bg-slate-900/20">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200/50 dark:border-slate-800/50 text-[10px] font-black text-slate-400 uppercase tracking-wider sticky top-0 bg-slate-50/90 dark:bg-slate-900/90 backdrop-blur-sm z-10">
                                    <th class="py-3 px-4 w-40 whitespace-nowrap">Waktu</th>
                                    <th class="py-3 px-4">Pesan Error</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/50 dark:divide-slate-800/50">
                                @foreach($errorLogs as $log)
                                    <tr class="text-slate-600 dark:text-slate-300 hover:bg-slate-100/50 dark:hover:bg-slate-800/30 transition-all group">
                                        <td class="py-3 px-4 font-mono text-[10px] font-bold tracking-tight align-top whitespace-nowrap text-slate-500">
                                            {{ $log['timestamp'] }}
                                        </td>
                                        <td class="py-3 px-4 align-top">
                                            <div class="font-mono text-[10px] text-red-500/80 dark:text-red-400/80 break-words whitespace-pre-wrap max-w-2xl leading-relaxed">{{ $log['message'] }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="py-8 text-center text-slate-500 border border-dashed border-slate-200/50 dark:border-slate-800/50 rounded-2xl">
                    <i data-lucide="check-circle" class="w-8 h-8 text-emerald-400 mx-auto mb-2 opacity-50"></i>
                    <p class="text-xs">Sistem berjalan dengan baik. Tidak ada error log yang ditemukan.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
