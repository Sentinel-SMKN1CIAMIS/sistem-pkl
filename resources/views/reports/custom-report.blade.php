<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20">
                    <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        Custom Dynamic Report & Automated Excel/Pivot Builder
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Rancang dan ekspor laporan kredensial akun, data siswa, guru, serta rekap penempatan PKL berformat Excel/CSV multi-sheet.
                    </p>
                </div>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Fast Stream Engine (&lt; 20ms)
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6 max-w-7xl mx-auto sm:px-6 lg:px-8" 
         x-data="customReportBuilder()" 
         x-init="initBuilder()">

        <!-- Stat Counter Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass-card p-4 rounded-2xl border border-slate-200/50 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 shadow-sm flex items-center gap-3.5">
                <div class="p-3 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Master Siswa</span>
                    <span class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ number_format($stats['total_siswa']) }}</span>
                    <span class="text-[10px] text-slate-400 block">Peserta Didik</span>
                </div>
            </div>

            <div class="glass-card p-4 rounded-2xl border border-slate-200/50 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 shadow-sm flex items-center gap-3.5">
                <div class="p-3 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <i data-lucide="user-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Master Guru</span>
                    <span class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ number_format($stats['total_guru']) }}</span>
                    <span class="text-[10px] text-slate-400 block">Guru Pembimbing</span>
                </div>
            </div>

            <div class="glass-card p-4 rounded-2xl border border-slate-200/50 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 shadow-sm flex items-center gap-3.5">
                <div class="p-3 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <i data-lucide="network" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Terplot PKL</span>
                    <span class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ number_format($stats['siswa_mapped']) }}</span>
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 block font-medium">Siswa Terpetakan</span>
                </div>
            </div>

            <div class="glass-card p-4 rounded-2xl border border-slate-200/50 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 shadow-sm flex items-center gap-3.5">
                <div class="p-3 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400">
                    <i data-lucide="building-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Mitra DUDI</span>
                    <span class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ number_format($stats['total_dudi']) }}</span>
                    <span class="text-[10px] text-slate-400 block">Perusahaan / Instansi</span>
                </div>
            </div>
        </div>

        <!-- Form Ekspor Utama -->
        @php
            $exportRoute = auth()->user()->role === 'super_admin' ? route('admin.custom_report.export') : route('pokja.custom_report.export');
        @endphp
        <form method="POST" action="{{ $exportRoute }}" @submit="isDownloading = true; setTimeout(() => { isDownloading = false; }, 3500)" class="space-y-6">
            @csrf

            <!-- LANGKAH 1: PILIH MODUL LAPORAN UTAMA -->
            <div class="glass-card p-6 rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold">1</span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Pilih Kategori & Modul Laporan Utama</h3>
                            <p class="text-[11px] text-slate-500">Pilih modul data yang ingin disusun dan diekspor ke berkas Excel.</p>
                        </div>
                    </div>

                    <!-- Category Pills -->
                    <div class="flex items-center gap-1.5 overflow-x-auto text-xs pb-1 sm:pb-0">
                        <button type="button" @click="categoryFilter = 'all'" 
                                :class="categoryFilter === 'all' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-lg transition-all shrink-0">
                            Semua Modul (4)
                        </button>
                        <button type="button" @click="categoryFilter = 'siswa'" 
                                :class="categoryFilter === 'siswa' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-lg transition-all shrink-0 flex items-center gap-1.5">
                            <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                            Kategori Siswa
                        </button>
                        <button type="button" @click="categoryFilter = 'guru'" 
                                :class="categoryFilter === 'guru' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-lg transition-all shrink-0 flex items-center gap-1.5">
                            <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                            Kategori Guru
                        </button>
                    </div>
                </div>

                <!-- Modul Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Modul 1: Kredensial Akun Siswa -->
                    <label x-show="categoryFilter === 'all' || categoryFilter === 'siswa'"
                           class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all"
                           :class="selectedModule === 'kredensial_siswa' ? 'border-blue-500 bg-blue-500/5 dark:bg-blue-500/10 shadow-md shadow-blue-500/5' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-800/40'">
                        <input type="radio" name="module" value="kredensial_siswa" x-model="selectedModule" @change="onModuleChange()" class="sr-only">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                    Kategori: Kredensial & Akun Siswa
                                </span>
                            </div>
                            <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                  :class="selectedModule === 'kredensial_siswa' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 dark:border-slate-600'">
                                <i data-lucide="check" class="w-3 h-3" x-show="selectedModule === 'kredensial_siswa'"></i>
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-2.5">
                            Data Master Siswa & Kredensial Login
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Ekspor NIS, Username, Password Awal (NIS), status keamanan password, penempatan DUDI, dan guru pembimbing. Cocok langsung dibagikan ke grup angkatan.
                        </p>
                    </label>

                    <!-- Modul 2: Kredensial Akun Pembimbing Sekolah -->
                    <label x-show="categoryFilter === 'all' || categoryFilter === 'guru'"
                           class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all"
                           :class="selectedModule === 'kredensial_guru' ? 'border-indigo-500 bg-indigo-500/5 dark:bg-indigo-500/10 shadow-md shadow-indigo-500/5' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-800/40'">
                        <input type="radio" name="module" value="kredensial_guru" x-model="selectedModule" @change="onModuleChange()" class="sr-only">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                                    Kategori: Kredensial & Akun Guru
                                </span>
                            </div>
                            <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                  :class="selectedModule === 'kredensial_guru' ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-300 dark:border-slate-600'">
                                <i data-lucide="check" class="w-3 h-3" x-show="selectedModule === 'kredensial_guru'"></i>
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-2.5">
                            Data Akun Pembimbing Sekolah (Guru)
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Ekspor Username, Password Default (smkn1ciamis), NIP, email, total siswa bimbingan, dan daftar tempat DUDI yang dibimbing.
                        </p>
                    </label>

                    <!-- Modul 3: Rekap Penempatan PKL -->
                    <label x-show="categoryFilter === 'all' || categoryFilter === 'siswa'"
                           class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all"
                           :class="selectedModule === 'penempatan_pkl' ? 'border-emerald-500 bg-emerald-500/5 dark:bg-emerald-500/10 shadow-md shadow-emerald-500/5' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-800/40'">
                        <input type="radio" name="module" value="penempatan_pkl" x-model="selectedModule" @change="onModuleChange()" class="sr-only">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                    Kategori: Pemetaan & Penempatan
                                </span>
                            </div>
                            <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                  :class="selectedModule === 'penempatan_pkl' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 dark:border-slate-600'">
                                <i data-lucide="check" class="w-3 h-3" x-show="selectedModule === 'penempatan_pkl'"></i>
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-2.5">
                            Rekap Pemetaan Siswa & Tempat DU/DI
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Data komprehensif penempatan siswa di DU/DI mitra, wilayah/kota, pembimbing sekolah, dan mentor lapangan DU/DI.
                        </p>
                    </label>

                    <!-- Modul 4: Rekap Kehadiran Siswa -->
                    <label x-show="categoryFilter === 'all' || categoryFilter === 'siswa'"
                           class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all"
                           :class="selectedModule === 'kehadiran_siswa' ? 'border-purple-500 bg-purple-500/5 dark:bg-purple-500/10 shadow-md shadow-purple-500/5' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-800/40'">
                        <input type="radio" name="module" value="kehadiran_siswa" x-model="selectedModule" @change="onModuleChange()" class="sr-only">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400">
                                    Kategori: Presensi & Jurnal PKL
                                </span>
                            </div>
                            <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                  :class="selectedModule === 'kehadiran_siswa' ? 'border-purple-600 bg-purple-600 text-white' : 'border-slate-300 dark:border-slate-600'">
                                <i data-lucide="check" class="w-3 h-3" x-show="selectedModule === 'kehadiran_siswa'"></i>
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-2.5">
                            Rekap Kehadiran & Jurnal Harian Siswa
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Statistik absensi kehadiran (Hadir, Sakit, Izin, Alpa), persentase kehadiran %, serta total jurnal kegiatan tervalidasi.
                        </p>
                    </label>
                </div>
            </div>

            <!-- LANGKAH 2: TENTUKAN FILTER DATA & RENTANG -->
            <div class="glass-card p-6 rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 shadow-sm space-y-6">
                <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold">2</span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tentukan Filter Data & Rentang Periode</h3>
                        <p class="text-[11px] text-slate-500">Saring data sesuai jurusan yang ingin dibawa/dikecualikan, kelas, dan status akun.</p>
                    </div>
                </div>

                <!-- Filter Jurusan / Konsentrasi (Checkbox Smart Filter Grid) -->
                <div x-show="selectedModule !== 'kredensial_guru'" class="p-4 rounded-xl bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-800/60 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <i data-lucide="layers" class="w-4 h-4 text-blue-500"></i>
                                Filter Jurusan / Konsentrasi yang Disertakan ke File Ekspor
                            </label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                <strong class="text-emerald-600 dark:text-emerald-400">☑ Dicentang</strong> = Data jurusan <u>dibawa</u> ke ekspor. <strong class="text-slate-500">☐ Dikosongkan</strong> = Data jurusan <u>dikecualikan</u> (tidak diekspor).
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 flex-wrap">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                <span x-text="selectedJurusan.length"></span> / <span x-text="allJurusanCodes.length"></span> Jurusan Dipilih
                            </span>
                            <button type="button" @click="selectAllJurusan()" class="px-2.5 py-1 text-xs font-medium rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700 shadow-sm cursor-pointer">
                                Pilih Semua
                            </button>
                            <button type="button" @click="excludeDkvOnly()" class="px-2.5 py-1 text-xs font-medium rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30 shadow-sm cursor-pointer" title="DKV sudah beres PKL">
                                Kecualikan DKV
                            </button>
                            <button type="button" @click="clearAllJurusan()" class="px-2.5 py-1 text-xs font-medium rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700 shadow-sm cursor-pointer">
                                Kosongkan
                            </button>
                        </div>
                    </div>

                    <!-- Checkbox Cards Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 pt-1">
                        @foreach($konsentrasis as $kon)
                            <label class="flex items-start gap-2.5 p-2.5 rounded-xl border transition-all cursor-pointer text-xs"
                                   :class="selectedJurusan.includes('{{ $kon->kode }}') 
                                       ? 'bg-blue-50/80 dark:bg-blue-900/20 border-blue-500/50 shadow-sm' 
                                       : 'bg-white dark:bg-slate-800/40 border-slate-200/60 dark:border-slate-700/60 opacity-60 hover:opacity-100'">
                                <input type="checkbox" 
                                       name="filter_jurusan[]" 
                                       value="{{ $kon->kode }}" 
                                       x-model="selectedJurusan" 
                                       class="mt-0.5 rounded text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $kon->kode }}</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded"
                                              :class="selectedJurusan.includes('{{ $kon->kode }}') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-500/10 text-slate-500'">
                                            <span x-text="selectedJurusan.includes('{{ $kon->kode }}') ? 'Disertakan' : 'Dikecualikan'"></span>
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ $kon->nama }}</p>
                                    @if($kon->kode === 'DKV')
                                        <span class="inline-block mt-1 text-[9px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded">
                                            Beres PKL
                                        </span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- Legend Info -->
                    <div class="flex flex-wrap items-center gap-4 pt-2 text-[11px] text-slate-500 dark:text-slate-400 border-t border-slate-200/50 dark:border-slate-800/50">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                            <span><strong>Dicentang:</strong> Data siswa dari jurusan ini <u>akan dibawa</u> ke file ekspor.</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400 inline-block"></span>
                            <span><strong>Tidak Dicentang:</strong> Data siswa jurusan ini <u>dikecualikan</u> (disaring keluar).</span>
                        </span>
                    </div>
                </div>

                <!-- Filter Tambahan: Kelas, Status Password, Status PKL, Tahun Ajaran -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <!-- Filter Kelas -->
                    <div x-show="selectedModule !== 'kredensial_guru'" class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Filter Kelas Spesifik</label>
                        <select name="filter_kelas" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Kelas (Seluruh Sekolah)</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas }}">{{ $kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status Password -->
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Status Keamanan Password</label>
                        <select name="filter_password_status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Status Password</option>
                            <option value="default">Hanya Password Default (Wajib Ubah)</option>
                            <option value="changed">Hanya yang Sudah Ubah Password</option>
                        </select>
                    </div>

                    <!-- Filter Status PKL -->
                    <div x-show="selectedModule !== 'kredensial_guru'" class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Status PKL Siswa</label>
                        <select name="filter_status_pkl" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Status Penempatan</option>
                            <option value="sedang_pkl">Sedang PKL</option>
                            <option value="belum_mulai">Belum Mulai</option>
                            <option value="selesai">Selesai PKL</option>
                        </select>
                    </div>

                    <!-- Filter Tahun Ajaran -->
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Tahun Ajaran</label>
                        <select name="filter_tahun_ajaran" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Tahun Ajaran</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta }}">{{ $ta }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- LANGKAH 3: PILIH KOLOM DATA YANG INGIN DISERTAKAN -->
            <div class="glass-card p-6 rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold">3</span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Pilih Kolom Data yang Ingin Disertakan</h3>
                            <p class="text-[11px] text-slate-500">Centang bidang data yang akan menjadi kolom header tabel pada Sheet Data Mentah.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold px-3 py-1 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400">
                            <span x-text="selectedColumns.length"></span> / <span x-text="availableColumns.length"></span> Kolom Terpilih
                        </span>
                        <button type="button" @click="selectAllColumns()" class="px-2.5 py-1 text-xs font-medium rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300">
                            Pilih Semua
                        </button>
                        <button type="button" @click="clearAllColumns()" class="px-2.5 py-1 text-xs font-medium rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300">
                            Hapus Semua
                        </button>
                    </div>
                </div>

                <!-- Checkbox Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    <template x-for="col in availableColumns" :key="col.key">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/60 hover:border-blue-500 dark:hover:border-blue-500 cursor-pointer transition-all">
                            <input type="checkbox" name="columns[]" :value="col.key" x-model="selectedColumns" class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="col.label"></span>
                                <span class="text-[10px] text-slate-400 font-mono" x-text="col.key"></span>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <!-- LANGKAH 4: OPSI EKSPOR & UNDUH FILE -->
            <div class="glass-card p-6 rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold">4</span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Opsi Ekspor & Unduh File</h3>
                        <p class="text-[11px] text-slate-500">Konfigurasi sheet tambahan dan format dokumen.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Option: Sertakan Sheet Rekapitulasi / Pivot Otomatis -->
                    <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 cursor-pointer hover:border-emerald-500 transition-all">
                        <input type="checkbox" name="include_pivot" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 mt-0.5">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Sertakan Sheet Rekapitulasi / Pivot Otomatis (Tab ke-2 dalam file Excel)</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Rekomendasi</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                File Excel yang diunduh akan otomatis memiliki Tab kedua (Sheet "Rekapitulasi & Pivot") yang memuat data agregasi ringkas (rekap per jurusan / status / pembimbing) sehingga siap ditinjau pimpinan tanpa perlu membuat tabel pivot manual di Microsoft Excel.
                            </p>
                        </div>
                    </label>

                    <!-- Format & Trigger Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                        <div class="flex items-center gap-4 text-xs">
                            <span class="font-bold text-slate-700 dark:text-slate-300">Format Berkas:</span>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="format" value="xlsx" checked class="text-blue-600 focus:ring-blue-500">
                                <span class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                                    Microsoft Excel (.xlsx)
                                </span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="format" value="csv" class="text-blue-600 focus:ring-blue-500">
                                <span class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                    <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
                                    Comma Separated (.csv)
                                </span>
                            </label>
                        </div>

                        <button type="submit" 
                                :disabled="selectedColumns.length === 0 || isDownloading"
                                class="inline-flex items-center justify-center gap-2.5 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-lg shadow-blue-500/25 transition-all cursor-pointer">
                            <span x-show="!isDownloading" class="inline-flex items-center gap-2">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                Generate & Unduh Laporan Ekspor
                            </span>
                            <span x-show="isDownloading" class="inline-flex items-center gap-2" style="display: none;">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                                Menyiapkan & Mengunduh Berkas...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function customReportBuilder() {
            return {
                isDownloading: false,
                selectedModule: 'kredensial_siswa',
                categoryFilter: 'all',
                availableColumns: [],
                selectedColumns: [],
                allJurusanCodes: @json($konsentrasis->pluck('kode')->values()),
                selectedJurusan: @json($konsentrasis->pluck('kode')->filter(fn($k) => $k !== 'DKV')->values()),

                selectAllJurusan() {
                    this.selectedJurusan = [...this.allJurusanCodes];
                },

                excludeDkvOnly() {
                    this.selectedJurusan = this.allJurusanCodes.filter(k => k !== 'DKV');
                },

                clearAllJurusan() {
                    this.selectedJurusan = [];
                },

                columnConfigs: {
                    kredensial_siswa: [
                        { key: 'nama_siswa', label: 'Nama Lengkap Siswa' },
                        { key: 'nis', label: 'NIS Siswa' },
                        { key: 'nisn', label: 'NISN Siswa' },
                        { key: 'kelas', label: 'Kelas' },
                        { key: 'jurusan', label: 'Program / Konsentrasi' },
                        { key: 'username', label: 'Username Login Akun' },
                        { key: 'password_default', label: 'Password Awal Default (NIS)' },
                        { key: 'status_password', label: 'Status Keamanan Password' },
                        { key: 'nama_dudi', label: 'Tempat PKL / DU/DI' },
                        { key: 'pembimbing_sekolah', label: 'Guru Pembimbing Sekolah' },
                        { key: 'status_pkl', label: 'Status PKL' },
                        { key: 'no_hp', label: 'No. HP Siswa' }
                    ],
                    kredensial_guru: [
                        { key: 'nama_guru', label: 'Nama Guru Pembimbing' },
                        { key: 'nip', label: 'NIP Guru' },
                        { key: 'username', label: 'Username Login Akun' },
                        { key: 'password_default', label: 'Password Awal Default (smkn1ciamis)' },
                        { key: 'status_password', label: 'Status Keamanan Password' },
                        { key: 'email', label: 'Alamat Email' },
                        { key: 'tipe_guru', label: 'Tipe Pembimbing' },
                        { key: 'jumlah_bimbingan', label: 'Jumlah Siswa Bimbingan' },
                        { key: 'daftar_dudi', label: 'Tempat DUDI Bimbingan' },
                        { key: 'no_hp', label: 'No. HP / Kontak Guru' }
                    ],
                    penempatan_pkl: [
                        { key: 'nama_siswa', label: 'Nama Lengkap Siswa' },
                        { key: 'nis', label: 'NIS Siswa' },
                        { key: 'kelas', label: 'Kelas' },
                        { key: 'jurusan', label: 'Program / Konsentrasi' },
                        { key: 'nama_dudi', label: 'Nama Tempat DU/DI' },
                        { key: 'kota_dudi', label: 'Wilayah / Kota DU/DI' },
                        { key: 'alamat_dudi', label: 'Alamat Lengkap DU/DI' },
                        { key: 'nama_pembimbing', label: 'Guru Pembimbing Sekolah' },
                        { key: 'mentor_dudi', label: 'Pembimbing Lapangan DU/DI' },
                        { key: 'status_penempatan', label: 'Status Penempatan' }
                    ],
                    kehadiran_siswa: [
                        { key: 'nama_siswa', label: 'Nama Lengkap Siswa' },
                        { key: 'nis', label: 'NIS Siswa' },
                        { key: 'kelas', label: 'Kelas' },
                        { key: 'jurusan', label: 'Jurusan' },
                        { key: 'nama_dudi', label: 'Tempat DU/DI' },
                        { key: 'total_hadir', label: 'Total Hadir' },
                        { key: 'sakit', label: 'Sakit (S)' },
                        { key: 'izin', label: 'Izin (I)' },
                        { key: 'alpa', label: 'Alpa / Tanpa Keterangan' },
                        { key: 'persentase_kehadiran', label: 'Persentase Kehadiran (%)' },
                        { key: 'total_jurnal_valid', label: 'Total Jurnal Valid' }
                    ]
                },

                initBuilder() {
                    this.onModuleChange();
                    this.$nextTick(() => window.lucide?.createIcons());
                },

                onModuleChange() {
                    this.availableColumns = this.columnConfigs[this.selectedModule] || [];
                    this.selectedColumns = this.availableColumns.map(c => c.key);
                    this.$nextTick(() => window.lucide?.createIcons());
                },

                selectAllColumns() {
                    this.selectedColumns = this.availableColumns.map(c => c.key);
                },

                clearAllColumns() {
                    this.selectedColumns = [];
                }
            };
        }
    </script>
</x-app-layout>

