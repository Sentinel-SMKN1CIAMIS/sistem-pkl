<x-app-layout>
    <x-slot name="header">Panduan Interaktif & Tutorial Penggunaan</x-slot>

    <!-- UI/UX Styling & Hotspot Animations -->
    <style>
        .hotspot-pulse {
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 40;
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .hotspot-pulse:hover {
            transform: scale(1.4);
        }
        .hotspot-pulse::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #3b82f6;
            border-radius: 50%;
            animation: pulse-ring 2s cubic-bezier(0.215, 0.610, 0.355, 1) infinite;
        }
        .hotspot-pulse::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 5px;
            width: 10px;
            height: 10px;
            background-color: #2563eb;
            border-radius: 50%;
            box-shadow: 0 0 12px rgba(37, 99, 235, 1);
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.6); opacity: 1; }
            100% { transform: scale(2.8); opacity: 0; }
        }
        [x-cloak] { display: none !important; }
        
        /* Dashboard Mockup Aesthetics */
        .mockup-grid {
            background-image: radial-gradient(rgba(148, 163, 184, 0.08) 1.5px, transparent 0);
            background-size: 20px 20px;
        }
        .dark .mockup-grid {
            background-image: radial-gradient(rgba(51, 65, 85, 0.25) 1.5px, transparent 0);
        }

        .glass-card-mockup {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dark .glass-card-mockup {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(30, 41, 59, 0.8);
        }
        .glass-card-mockup:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -10px rgba(0, 0, 0, 0.05);
        }
        .dark .glass-card-mockup:hover {
            box-shadow: 0 12px 20px -10px rgba(0, 0, 0, 0.3);
        }

        /* Ambient Glow effects */
        .glow-blue {
            box-shadow: 0 0 40px -10px rgba(59, 130, 246, 0.15);
        }
        .glow-emerald {
            box-shadow: 0 0 40px -10px rgba(16, 185, 129, 0.15);
        }
        .glow-purple {
            box-shadow: 0 0 40px -10px rgba(139, 92, 246, 0.15);
        }

        /* Custom scrollbar for mockup */
        .mockup-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .mockup-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .mockup-scroll::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 3px;
        }
    </style>

    <div x-data="{ 
        activeTab: 'stepper', 
        currentStep: 1, 
        activeHotspot: null,
        siswaClockedIn: false,
        pokjaMapped: false,
        kaprogApproved: false,
        guruAcc: false,
        dudiValidated: false,
        adminSaved: false
    }" @keydown.window.escape="activeHotspot = null" class="max-w-7xl mx-auto text-slate-700 dark:text-slate-300">
        
        <!-- Tab Selector (Premium Pill buttons) -->
        <div class="flex justify-center mb-8">
            <div class="inline-flex p-1.5 bg-slate-200/50 dark:bg-slate-900/60 backdrop-blur-md border border-slate-300/30 dark:border-slate-800/30 rounded-2xl shadow-inner">
                <button @click="activeTab = 'stepper'; activeHotspot = null"
                        :class="activeTab === 'stepper' 
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/25' 
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
                        class="px-6 py-2.5 text-sm font-semibold rounded-xl transition-all duration-300 flex items-center gap-2 cursor-pointer border-0 outline-none">
                    <i data-lucide="compass" class="w-4 h-4"></i>
                    Alur Kerja & Langkah Panduan
                </button>
                <button @click="activeTab = 'hotspot'; currentStep = 1"
                        :class="activeTab === 'hotspot' 
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/25' 
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
                        class="px-6 py-2.5 text-sm font-semibold rounded-xl transition-all duration-300 flex items-center gap-2 cursor-pointer border-0 outline-none">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    Eksplorasi Dashboard (Hotspot)
                </button>
            </div>
        </div>

        <!-- Role Banner / Header Info -->
        <div class="glass-card p-6 mb-8 flex flex-col md:flex-row items-center gap-5 bg-gradient-to-r from-blue-500/5 to-indigo-500/5 border border-slate-200/50 dark:border-slate-800/50 rounded-2xl shadow-sm">
            <div class="p-3.5 bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-2xl">
                <i data-lucide="info" class="w-8 h-8"></i>
            </div>
            <div class="text-center md:text-left flex-1">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Panduan Mode: {{ str_replace('_', ' ', strtoupper($role)) }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Sistem menyajikan panduan alur dan simulasi playground interaktif yang disesuaikan secara khusus dengan wewenang peran Anda.</p>
            </div>
        </div>

        <!-- Mode 1: Stepper (Langkah Kerja Berurutan) -->
        <div x-show="activeTab === 'stepper'" class="space-y-6">
            @if($role === 'siswa')
                <!-- Stepper Siswa -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Stepper Controls & Steps -->
                    <div class="lg:col-span-1 space-y-4">
                        <div class="glass-card p-6 border border-slate-200/50 dark:border-slate-800/50 rounded-2xl shadow-sm">
                            <h4 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6">Langkah Panduan</h4>
                            <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-800 space-y-8">
                                <button @click="currentStep = 1" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 1 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 1 ? 'text-blue-500' : 'text-slate-400'">Langkah 1</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 1 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Pengenalan Aplikasi</span>
                                    </div>
                                </button>
                                <button @click="currentStep = 2" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 2 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 2 ? 'text-blue-500' : 'text-slate-400'">Langkah 2</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 2 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Absensi Kehadiran</span>
                                    </div>
                                </button>
                                <button @click="currentStep = 3" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 3 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 3 ? 'text-blue-500' : 'text-slate-400'">Langkah 3</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 3 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Pengisian Jurnal Harian</span>
                                    </div>
                                </button>
                                <button @click="currentStep = 4" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 4 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 4 ? 'text-blue-500' : 'text-slate-400'">Langkah 4</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 4 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Pengajuan Mandiri & Laporan</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Step Content Cards -->
                    <div class="lg:col-span-2">
                        <div x-show="currentStep === 1" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-blue-500">
                                <i data-lucide="layout-dashboard" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Pengenalan Aplikasi MAS-PKL</h3>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Selamat datang di Aplikasi **MAS-PKL** (Monitoring & Aplikasi Sistem Praktik Kerja Lapangan). Sebagai seorang **Siswa**, sistem ini akan menjadi alat bantu utama Anda dalam mendokumentasikan kegiatan belajar Anda di industri (DUDI) dan mencatat kehadiran Anda setiap harinya.
                            </p>
                            <div class="p-5 bg-blue-500/5 border border-blue-500/10 rounded-2xl">
                                <h4 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2.5">Tujuan Penggunaan Sistem</h4>
                                <ul class="list-disc pl-5 space-y-2 text-xs text-slate-655 dark:text-slate-400">
                                    <li>Mencatat kehadiran secara akurat berbasis geolokasi (GPS) agar terverifikasi oleh industri.</li>
                                    <li>Mencatat aktivitas kompetensi harian di Jurnal Kegiatan untuk diajukan ke pembimbing sekolah dan DUDI.</li>
                                    <li>Mengekspor laporan jurnal dan portofolio PKL di akhir periode kegiatan.</li>
                                </ul>
                            </div>
                        </div>

                        <div x-show="currentStep === 2" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-emerald-500">
                                <i data-lucide="calendar-check" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Melakukan Absensi Kehadiran</h3>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Presensi kehadiran harus dicatat dua kali sehari. Pastikan Anda memberikan izin akses lokasi (GPS) pada browser Anda sebelum menekan tombol.
                            </p>
                            <div class="space-y-4">
                                <div class="flex gap-4 items-start">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex-shrink-0 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-300">1</div>
                                    <div class="text-xs">
                                        <strong class="text-slate-900 dark:text-white block mb-1">Clock-In (Masuk)</strong>
                                        Lakukan Clock-In saat Anda mulai bekerja di industri di pagi hari. Sistem akan mencocokkan koordinat GPS Anda dengan koordinat radius DUDI tempat Anda di-plot.
                                    </div>
                                </div>
                                <div class="flex gap-4 items-start">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex-shrink-0 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-300">2</div>
                                    <div class="text-xs">
                                        <strong class="text-slate-900 dark:text-white block mb-1">Clock-Out (Pulang)</strong>
                                        Lakukan Clock-Out setelah jam kerja selesai di sore hari untuk mencatat kepulangan Anda secara sah.
                                    </div>
                                </div>
                                <div class="flex gap-4 items-start">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex-shrink-0 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-300">3</div>
                                    <div class="text-xs">
                                        <strong class="text-slate-900 dark:text-white block mb-1">Pengajuan Izin / Sakit</strong>
                                        Jika Anda berhalangan hadir, klik tombol pengajuan izin, pilih keterangan (Sakit/Izin), tulis alasannya, dan unggah foto surat keterangan dokter atau surat izin dari orang tua.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="currentStep === 3" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-amber-500">
                                <i data-lucide="book-open" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Pengisian Jurnal Harian</h3>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Jurnal Harian wajib diisi setiap hari kerja. Tulis apa yang Anda kerjakan secara detail dan pilih kompetensi yang dilatih.
                            </p>
                            <div class="p-5 bg-amber-500/5 border border-amber-500/10 rounded-2xl text-xs space-y-3">
                                <div>
                                    <strong class="text-slate-955 dark:text-white block mb-1">Pilih Kompetensi yang Sesuai</strong>
                                    Anda harus memilih Kompetensi Dasar (TP/CP) yang sesuai dengan tugas yang Anda kerjakan agar sinkron dengan kurikulum sekolah.
                                </div>
                                <div>
                                    <strong class="text-slate-955 dark:text-white block mb-1">Unggah Foto Kegiatan</strong>
                                    Unggah satu foto sebagai bukti visual aktivitas Anda di tempat PKL (misal: saat memprogram, mendesain, melayani konsumen, dll).
                                </div>
                                <div>
                                    <strong class="text-slate-955 dark:text-white block mb-1">Persetujuan Jurnal</strong>
                                    Setelah disimpan, jurnal akan dikirim ke guru pembimbing untuk diulas dan disetujui (ACC). Jurnal yang ditolak harus direvisi sesuai catatan pembimbing.
                                </div>
                            </div>
                        </div>

                        <div x-show="currentStep === 4" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-purple-500">
                                <i data-lucide="file-plus-2" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Pengajuan Mandiri & Laporan Akhi</h3>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Jika sekolah memperbolehkan mencari tempat PKL secara mandiri, Anda dapat menggunakan menu **Pengajuan PKL** di sidebar.
                            </p>
                            <div class="p-5 bg-purple-500/5 border border-purple-500/10 rounded-2xl text-xs space-y-3">
                                <div>
                                    <strong class="text-slate-955 dark:text-white block mb-1">1. Isi Form Pengajuan</strong>
                                    Masukkan detail perusahaan DUDI, pimpinan, nomor telepon, alamat, kota, dan juga pilih Pembimbing DUDI jika sudah mengetahuinya.
                                </div>
                                <div>
                                    <strong class="text-slate-955 dark:text-white block mb-1">2. Tunggu Verifikasi Kaprog</strong>
                                    Kepala Program Keahlian Anda akan meninjau kelayakan DUDI tersebut. Setelah disetujui, Pokja akan memetakan pembimbing Anda secara otomatis.
                                </div>
                                <div>
                                    <strong class="text-slate-955 dark:text-white block mb-1">3. Unduh Sertifikat & Portofolio</strong>
                                    Di akhir periode PKL, Anda bisa mengunduh rekap jurnal dalam format PDF, portofolio kegiatan, serta mencetak sertifikat digital jika sudah dinilai oleh pembimbing DUDI.
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons inside stepper card -->
                        <div class="flex justify-between items-center pt-6 border-t border-slate-200/50 dark:border-slate-800/50 mt-6 font-medium">
                            <button @click="currentStep > 1 ? currentStep-- : null"
                                    :disabled="currentStep === 1"
                                    class="px-5 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-xs hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer bg-transparent transition-all">
                                Kembali
                            </button>
                            <button @click="currentStep < 4 ? currentStep++ : null"
                                    :disabled="currentStep === 4"
                                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-blue-500/25 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer border-0 transition-all">
                                Lanjut
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($role === 'pokja')
                <!-- Stepper Pokja -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Stepper Controls & Steps -->
                    <div class="lg:col-span-1 space-y-4">
                        <div class="glass-card p-6 border border-slate-200/50 dark:border-slate-800/50 rounded-2xl shadow-sm">
                            <h4 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6">Langkah Panduan</h4>
                            <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-800 space-y-8">
                                <button @click="currentStep = 1" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 1 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 1 ? 'text-blue-500' : 'text-slate-400'">Langkah 1</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 1 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Kelola Akun & DUDI</span>
                                    </div>
                                </button>
                                <button @click="currentStep = 2" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 2 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 2 ? 'text-blue-500' : 'text-slate-400'">Langkah 2</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 2 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Data Akademik</span>
                                    </div>
                                </button>
                                <button @click="currentStep = 3" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 3 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 3 ? 'text-blue-500' : 'text-slate-400'">Langkah 3</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 3 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Workspace Pemetaan</span>
                                    </div>
                                </button>
                                <button @click="currentStep = 4" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 4 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 4 ? 'text-blue-500' : 'text-slate-400'">Langkah 4</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 4 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Monitoring & Feedback</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Step Content Cards -->
                    <div class="lg:col-span-2">
                        <div x-show="currentStep === 1" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-blue-500">
                                <i data-lucide="users" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Kelola Akun & Data DUDI</h3>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Kelompok menu **Kelola Akun** di sidebar memuat pendaftaran master data. Anda dapat memasukkan data secara satu-per-satu atau mengunggah data sekaligus dalam jumlah banyak dengan berkas Excel (.xlsx).
                            </p>
                            <div class="p-5 bg-blue-500/5 border border-blue-500/10 rounded-2xl text-xs space-y-2 text-slate-655 dark:text-slate-400">
                                <li><strong>Impor Excel Bawaan</strong>: Setiap sub-menu pendaftaran akun memiliki tombol "Impor" untuk mengunggah file. Gunakan "Unduh Template Excel" terlebih dahulu agar struktur kolom data terisi secara presisi.</li>
                                <li><strong>Status Akun</strong>: Anda dapat menonaktifkan akun yang sudah tidak aktif agar tidak bisa login.</li>
                            </div>
                        </div>

                        <div x-show="currentStep === 2" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-emerald-500">
                                <i data-lucide="book-open" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Mengelola Parameter Akademik</h3>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Sebagai Pokja, Anda memiliki akses penuh ke menu **Akademik & Jurusan** yang memuat:
                            </p>
                            <div class="p-5 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl text-xs space-y-3 text-slate-655 dark:text-slate-400">
                                <div>
                                    <strong class="text-slate-905 dark:text-white block mb-1">1. Program Keahlian</strong>
                                    Mengelola program keahlian utama sekolah (misal: PPLG, MPLB, KLN).
                                </div>
                                <div>
                                    <strong class="text-slate-905 dark:text-white block mb-1">2. Konsentrasi Keahlian</strong>
                                    Mengelola cabang konsentrasi khusus beserta durasi bulan PKL dan batas tanggal periode pelaksanaan PKL.
                                </div>
                                <div>
                                    <strong class="text-slate-905 dark:text-white block mb-1">3. Kelola TP/CP (Kompetensi)</strong>
                                    Merumuskan target pencapaian pembelajaran (Kompetensi Dasar) yang akan diajarkan ke siswa saat magang berdasarkan jurusannya masing-masing.
                                </div>
                            </div>
                        </div>

                        <div x-show="currentStep === 3" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-amber-500">
                                <i data-lucide="network" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Workspace Pemetaan Siswa</h3>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Plotting penempatan siswa adalah inti dari tugas Pokja.
                            </p>
                            <div class="p-5 bg-amber-500/5 border border-amber-500/10 rounded-2xl text-xs space-y-2 text-slate-655 dark:text-slate-400">
                                <li><strong>Plotting Pembimbing & DUDI</strong>: Petakan siswa ke lokasi DUDI industri yang dituju serta tentukan Guru Pembimbing Sekolah dan Pembimbing DUDI yang memandu mereka.</li>
                                <li><strong>Kelola Zona</strong>: Klasterkan perusahaan ke dalam zona geografi wilayah tertentu agar satu orang Guru Pembimbing dapat memonitor sekelompok siswa dalam zona yang sama secara hemat waktu dan jarak tempuh.</li>
                            </div>
                        </div>

                        <div x-show="currentStep === 4" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-purple-500">
                                <i data-lucide="eye" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Monitoring Pembimbingan & Feedback</h3>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Setelah siswa ditempatkan, tugas Pokja bergeser pada pengawasan jalannya program.
                            </p>
                            <div class="p-5 bg-purple-500/5 border border-purple-500/10 rounded-2xl text-xs space-y-3 text-slate-655 dark:text-slate-400">
                                <div>
                                    <strong class="text-slate-905 dark:text-white block mb-1">Monitoring Pembimbing</strong>
                                    Pantau keaktifan guru pembimbing dalam mengunjungi siswa magang dan melihat rekap log kunjungan bimbingan sekolah.
                                </div>
                                <div>
                                    <strong class="text-slate-905 dark:text-white block mb-1">Evaluasi PKL & Feedback DUDI</strong>
                                    Menganalisis masukan dan tingkat kepuasan dari industri (DUDI) untuk perbaikan kurikulum serta melihat akumulasi penilaian akhir siswa.
                                </div>
                            </div>
                        </div>

                        <!-- Stepper navigation -->
                        <div class="flex justify-between items-center pt-6 border-t border-slate-200/50 dark:border-slate-800/50 mt-6 font-medium">
                            <button @click="currentStep > 1 ? currentStep-- : null"
                                    :disabled="currentStep === 1"
                                    class="px-5 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-xs hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer bg-transparent transition-all">
                                Kembali
                            </button>
                            <button @click="currentStep < 4 ? currentStep++ : null"
                                    :disabled="currentStep === 4"
                                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-blue-500/25 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer border-0 transition-all">
                                Lanjut
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Stepper generic roles -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Stepper Controls & Steps -->
                    <div class="lg:col-span-1 space-y-4">
                        <div class="glass-card p-6 border border-slate-200/50 dark:border-slate-800/50 rounded-2xl shadow-sm">
                            <h4 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6">Langkah Panduan</h4>
                            <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-800 space-y-8">
                                <button @click="currentStep = 1" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 1 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 1 ? 'text-blue-500' : 'text-slate-400'">Langkah 1</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 1 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Ringkasan Peran</span>
                                    </div>
                                </button>
                                <button @click="currentStep = 2" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 2 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 2 ? 'text-blue-500' : 'text-slate-400'">Langkah 2</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 2 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Alur Kerja Utama</span>
                                    </div>
                                </button>
                                <button @click="currentStep = 3" class="w-full text-left flex items-start gap-3 relative group focus:outline-none bg-transparent border-0 cursor-pointer">
                                    <div class="absolute -left-[31px] w-4 h-4 rounded-full border-2 transition-all duration-300"
                                         :class="currentStep >= 3 ? 'bg-blue-600 border-blue-600 scale-110 shadow' : 'bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700'"></div>
                                    <div>
                                        <span class="text-xs font-bold uppercase block tracking-wider" :class="currentStep == 3 ? 'text-blue-500' : 'text-slate-400'">Langkah 3</span>
                                        <span class="text-sm font-semibold" :class="currentStep == 3 ? 'text-slate-900 dark:text-white' : 'text-slate-500'">Tips & FAQ</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Step Content Cards -->
                    <div class="lg:col-span-2">
                        <div x-show="currentStep === 1" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-blue-500">
                                <i data-lucide="award" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Deskripsi Tugas Peran Anda</h3>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Berdasarkan otorisasi login, Anda terdaftar sebagai **{{ ucfirst(str_replace('_', ' ', $role)) }}**. MAS-PKL menyajikan modul spesifik agar mempermudah monitoring siswa PKL.
                            </p>
                            <div class="p-5 bg-blue-500/5 border border-blue-500/10 rounded-2xl text-xs space-y-2 text-slate-655 dark:text-slate-400">
                                @if($role === 'kaprog')
                                    <li><strong>Tanggung Jawab Kaprog</strong>: Menyetujui atau menolak usulan penempatan magang mandiri dari siswa di bawah Program Keahlian Anda.</li>
                                    <li><strong>Visualisasi Statistik</strong>: Memantau penyebaran siswa PKL per perusahaan pada Program Keahlian Anda.</li>
                                @elseif($role === 'pembimbing_sekolah')
                                    <li><strong>Tanggung Jawab Pembimbing</strong>: Mengecek data log bimbingan, menyetujui jurnal kegiatan harian siswa, menyetujui dispensasi kehadiran, serta memberi nilai bimbingan.</li>
                                @elseif($role === 'pembimbing_dudi')
                                    <li><strong>Tanggung Jawab Mentor</strong>: Melakukan pengecekan jurnal kerja harian di industri, melakukan absensi konfirmasi kehadiran siswa, dan mengisi kuisioner penilaian akhir.</li>
                                @else
                                    <li><strong>Tanggung Jawab Administrator</strong>: Mengontrol fungsionalitas sistem global, manajemen data akun user secara menyeluruh, dan audit aktivitas server.</li>
                                @endif
                            </div>
                        </div>

                        <div x-show="currentStep === 2" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-emerald-500">
                                <i data-lucide="git-branch" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Alur Operasional Aplikasi</h3>
                            </div>
                            <p class="text-sm leading-relaxed">
                                Panduan langkah operasional utama untuk memperlancar pekerjaan Anda sehari-hari:
                            </p>
                            <div class="p-5 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl text-xs space-y-3 text-slate-655 dark:text-slate-400">
                                @if($role === 'kaprog')
                                    <div>
                                        <strong class="text-slate-900 dark:text-white block mb-1">1. Tinjau Pengajuan</strong>
                                        Buka menu "Pengajuan PKL", tinjau form pengajuan baru, dan ubah status pengajuan.
                                    </div>
                                    <div>
                                        <strong class="text-slate-900 dark:text-white block mb-1">2. Lihat Statistik Siswa</strong>
                                        Gunakan dashboard utama untuk memantau persentase siswa bimbingan yang sudah/belum dipetakan di industri.
                                    </div>
                                @elseif($role === 'pembimbing_sekolah')
                                    <div>
                                        <strong class="text-slate-900 dark:text-white block mb-1">1. Review Jurnal Harian</strong>
                                        Siswa menulis jurnal -> buka menu "Monitoring Jurnal" -> tinjau deskripsi kerja -> Klik tombol "Setujui" atau "Tolak".
                                    </div>
                                    <div>
                                        <strong class="text-slate-900 dark:text-white block mb-1">2. Validasi Absensi & Nilai</strong>
                                        Verifikasi pengajuan ketidakhadiran di menu "Persetujuan Absensi" dan input nilai akhir di menu "Evaluasi Laporan".
                                    </div>
                                @elseif($role === 'pembimbing_dudi')
                                    <div>
                                        <strong class="text-slate-900 dark:text-white block mb-1">1. Validasi Kehadiran</strong>
                                        Verifikasi kehadiran jam clock-in/out siswa magang setiap hari di menu "Validasi Kehadiran".
                                    </div>
                                    <div>
                                        <strong class="text-slate-900 dark:text-white block mb-1">2. Penilaian Akhir & Feedback</strong>
                                        Tulis evaluasi kompetensi siswa dan isilah instrumen feedback industri di menu "Feedback Sekolah".
                                    </div>
                                @else
                                    <div>
                                        <strong class="text-slate-900 dark:text-white block mb-1">1. Konfigurasi Awal</strong>
                                        Set data sekolah, batasan radius presensi, dan data Pokja aktif di menu "Konfigurasi Sistem".
                                    </div>
                                    <div>
                                        <strong class="text-slate-900 dark:text-white block mb-1">2. Audit Log & Pemeliharaan</strong>
                                        Pantau anomali data di menu "Log Sistem" dan kelola status user di menu "Kelola Pengguna".
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div x-show="currentStep === 3" class="glass-card p-8 space-y-6" x-cloak>
                            <div class="flex items-center gap-3 text-amber-500">
                                <i data-lucide="help-circle" class="w-8 h-8"></i>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Tips & Pertanyaan Umum (FAQ)</h3>
                            </div>
                            <div class="space-y-4 text-xs text-slate-655 dark:text-slate-400">
                                <div>
                                    <strong class="text-slate-955 dark:text-white block mb-1">Bagaimana jika ada kesalahan data input?</strong>
                                    Anda dapat berkoordinasi dengan Tim Pokja untuk melakukan koreksi atau pemetaan ulang data jika terdapat kekeliruan plotting DUDI/Pembimbing.
                                </div>
                                <div>
                                    <strong class="text-slate-955 dark:text-white block mb-1">Di mana siswa dapat berkonsultasi?</strong>
                                    MAS-PKL menyediakan fitur "Pesan" (Chatting) internal di sidebar yang memungkinkan komunikasi langsung antar peran (Siswa-Guru-Mentor) secara privat.
                                </div>
                            </div>
                        </div>

                        <!-- Stepper navigation -->
                        <div class="flex justify-between items-center pt-6 border-t border-slate-200/50 dark:border-slate-800/50 mt-6 font-medium">
                            <button @click="currentStep > 1 ? currentStep-- : null"
                                    :disabled="currentStep === 1"
                                    class="px-5 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-xs hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer bg-transparent transition-all">
                                Kembali
                            </button>
                            <button @click="currentStep < 3 ? currentStep++ : null"
                                    :disabled="currentStep === 3"
                                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-blue-500/25 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer border-0 transition-all">
                                Lanjut
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Mode 2: Hotspot (Eksplorasi Dashboard dengan Mockup Hi-Fi & Otoritas Tour) -->
        <div x-show="activeTab === 'hotspot'" class="space-y-6" x-cloak>
            <p class="text-sm text-slate-500 dark:text-slate-450 text-center mb-6">Arahkan kursor atau klik titik biru pulsing (hotspot) pada mockup antarmuka di bawah ini untuk melihat rincian fungsinya.</p>
            
            <!-- Main Mockup Browser Window Container -->
            <div class="relative w-full border border-slate-200 dark:border-slate-800/80 bg-slate-50 dark:bg-slate-955 rounded-2xl overflow-hidden shadow-2xl mockup-grid transition-all duration-300 flex flex-col min-h-[600px] glow-blue">
                
                <!-- Mockup Coach Mark Overlay Mask -->
                <div x-show="activeHotspot !== null" 
                     class="absolute inset-0 bg-slate-950/40 dark:bg-slate-950/65 z-30 transition-opacity duration-350"
                     @click="activeHotspot = null"></div>

                <!-- Mockup Browser Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/40 backdrop-blur-md relative z-10">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500/90 shadow-sm"></span>
                        <span class="w-3 h-3 rounded-full bg-yellow-500/90 shadow-sm"></span>
                        <span class="w-3 h-3 rounded-full bg-green-500/90 shadow-sm"></span>
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 ml-3 flex items-center gap-1.5 font-mono select-none">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-blue-500"></i>
                            simulasi.mas-pkl.sch.id
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-0.5 bg-blue-500/10 text-blue-500 rounded-lg text-[9px] font-black tracking-wider uppercase select-none border border-blue-500/20">DEMO LIVE PLAYGROUND</span>
                    </div>
                </div>

                <!-- Mockup Page Body -->
                <div class="p-8 flex-1 overflow-y-auto mockup-scroll max-h-[750px] relative z-10">
                    @if($role === 'siswa')
                        <!-- Mockup Siswa Dashboard -->
                        <div class="space-y-8 text-left">
                            <div class="flex items-center justify-between border-b border-slate-200/50 dark:border-slate-800/50 pb-4">
                                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Dashboard Siswa</h2>
                                <span class="text-xs bg-slate-100 dark:bg-slate-900 px-3 py-1 rounded-xl text-slate-500 dark:text-slate-400 border border-slate-200/30 dark:border-slate-800/30 font-medium">Siswa Active Playground</span>
                            </div>

                            <!-- Stats Cards Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                                <!-- Status Hari Ini Card -->
                                <div class="p-6 glass-card-mockup border-l-4 border-blue-500 rounded-2xl relative overflow-hidden group">
                                    <div class="absolute -right-6 -top-6 w-20 h-20 bg-blue-500/10 rounded-full"></div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-450 text-[10px] font-bold uppercase tracking-wider mb-1">Status Hari Ini</p>
                                            <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tighter">SEDANG PKL</h3>
                                        </div>
                                        <div class="w-10 h-10 bg-blue-500/15 rounded-xl flex items-center justify-center relative z-10 shadow-sm border border-blue-500/20">
                                            <i data-lucide="activity" class="w-5 h-5 text-blue-500"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 font-medium">
                                        <i data-lucide="building-2" class="w-4 h-4 text-slate-400"></i>
                                        PT Teknologi Nusantara
                                    </div>
                                </div>

                                <!-- Jurnal Terisi Card -->
                                <div class="p-6 glass-card-mockup border-l-4 border-emerald-500 rounded-2xl relative overflow-hidden group">
                                    <div class="absolute -right-6 -top-6 w-20 h-20 bg-emerald-500/10 rounded-full"></div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-slate-505 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Jurnal Terisi</p>
                                            <h3 class="text-lg font-black text-slate-800 dark:text-white">12 <span class="text-xs font-normal text-slate-450 dark:text-slate-505">Harian</span></h3>
                                        </div>
                                        <div class="w-10 h-10 bg-emerald-500/15 rounded-xl flex items-center justify-center relative z-10 shadow-sm border border-emerald-500/20">
                                            <i data-lucide="book-open" class="w-5 h-5 text-emerald-500"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                                        <div class="flex items-center justify-between text-xs mb-1.5 font-bold text-emerald-500">
                                            <span>Valid: 10</span>
                                        </div>
                                        <div class="overflow-hidden h-1.5 rounded-full bg-slate-100 dark:bg-slate-850">
                                            <div class="h-1.5 rounded-full bg-emerald-500" style="width: 83%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kehadiran Card -->
                                <div class="p-6 glass-card-mockup border-l-4 border-purple-500 rounded-2xl relative overflow-hidden group">
                                    <div class="absolute -right-6 -top-6 w-20 h-20 bg-purple-500/10 rounded-full"></div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Kehadiran</p>
                                            <h3 class="text-lg font-black text-slate-800 dark:text-white">10 <span class="text-xs font-normal text-slate-450 dark:text-slate-505">Hari</span></h3>
                                        </div>
                                        <div class="w-10 h-10 bg-purple-500/15 rounded-xl flex items-center justify-center relative z-10 shadow-sm border border-purple-500/20">
                                            <i data-lucide="check-circle" class="w-5 h-5 text-purple-500"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                        Absensi harian aktif dan terverifikasi
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions / Interaksi Presensi -->
                            <div class="p-6 glass-card-mockup rounded-2xl relative shadow-md transition-all border border-slate-200/70 dark:border-slate-800/70 overflow-hidden"
                                 :class="activeHotspot === 1 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                <div class="absolute -left-6 -bottom-6 w-20 h-20 bg-blue-500/5 dark:bg-blue-500/10 rounded-full"></div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center relative flex-shrink-0">
                                            <span class="absolute inset-0 rounded-full border border-blue-500/30 animate-ping" style="animation-duration: 2s;"></span>
                                            <i data-lucide="map-pin" class="w-6 h-6 text-blue-500"></i>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-bold text-slate-800 dark:text-white">Presensi Kehadiran Geofencing</h5>
                                            <p class="text-xs text-slate-500 mt-0.5" x-text="siswaClockedIn ? 'Presensi masuk berhasil direkam pada 07:28 WIB' : 'Pastikan GPS aktif & Anda berada dalam radius magang'"></p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 flex-shrink-0 self-end sm:self-auto relative">
                                        <button type="button" @click="siswaClockedIn = !siswaClockedIn"
                                                :class="siswaClockedIn ? 'bg-emerald-500 hover:bg-emerald-600 text-white' : 'bg-blue-600 hover:bg-blue-500 text-white'"
                                                class="px-5 py-2.5 text-xs font-bold rounded-xl shadow-md transition-all border-0 cursor-pointer outline-none flex items-center gap-1.5">
                                            <i data-lucide="check" class="w-4 h-4" x-show="siswaClockedIn"></i>
                                            <span x-text="siswaClockedIn ? 'Valid (Clocked-In)' : 'Lakukan Clock-In'"></span>
                                        </button>
                                        <!-- Hotspot 1 -->
                                        <div class="hotspot-pulse -right-2.5 -top-2.5" @click="activeHotspot = 1"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2 Column Layout for details -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Left Column: Jurnal Harian -->
                                <div class="p-6 glass-card-mockup rounded-2xl shadow-md relative flex flex-col justify-between"
                                     :class="activeHotspot === 2 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                    <div>
                                        <div class="flex items-center justify-between mb-5">
                                            <h5 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                                <i data-lucide="edit-3" class="w-5 h-5 text-blue-500"></i>
                                                Jurnal Kegiatan Harian
                                            </h5>
                                            <span class="text-[9px] bg-slate-100 dark:bg-slate-900 px-2.5 py-0.5 rounded-lg text-slate-500 font-semibold border border-slate-200/40 dark:border-slate-800/40">Siswa Log</span>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/50 dark:border-slate-800/60 flex justify-between items-center text-xs">
                                                <span class="truncate pr-2 font-medium text-slate-700 dark:text-slate-350">Membuat UI/UX layout di Figma...</span>
                                                <span class="text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 px-2 py-0.5 rounded-lg font-bold border border-emerald-500/20">ACC</span>
                                            </div>
                                            <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/50 dark:border-slate-800/60 flex justify-between items-center text-xs">
                                                <span class="truncate pr-2 font-medium text-slate-700 dark:text-slate-350">Menghubungkan controller absensi...</span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-lg font-bold border"
                                                      :class="guruAcc ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-500/20' : 'bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-400 border-amber-500/20'"
                                                      x-text="guruAcc ? 'ACC' : 'PENDING'"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex justify-end items-center relative">
                                        <button type="button" @click="guruAcc = !guruAcc"
                                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl border-0 cursor-pointer transition-colors outline-none flex items-center gap-1.5">
                                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                            Simulasikan Guru ACC
                                        </button>
                                        <!-- Hotspot 2 -->
                                        <div class="hotspot-pulse -right-2 top-3" @click="activeHotspot = 2"></div>
                                    </div>
                                </div>

                                <!-- Right Column: Usulan & Chart -->
                                <div class="space-y-6">
                                    <!-- Usulan Card -->
                                    <div class="p-6 glass-card-mockup rounded-2xl shadow-md relative"
                                         :class="activeHotspot === 3 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                        <h5 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                                            <i data-lucide="file-plus-2" class="w-5 h-5 text-blue-500"></i>
                                            Pengajuan PKL Mandiri
                                        </h5>
                                        <div class="p-4 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/60 rounded-xl flex justify-between items-center relative">
                                            <div class="min-w-0 pr-2">
                                                <span class="text-xs font-bold text-slate-800 dark:text-white block truncate">PT Teknologi Nusantara</span>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 block mt-0.5 truncate">Oleh: Rizky Pratama (PPLG)</span>
                                            </div>
                                            <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-400 rounded-lg text-[10px] font-extrabold uppercase border border-amber-500/20">Menunggu</span>
                                            <!-- Hotspot 3 -->
                                            <div class="hotspot-pulse -right-2.5 -top-2.5" @click="activeHotspot = 3"></div>
                                        </div>
                                    </div>

                                    <!-- Mini Chart -->
                                    <div class="p-6 glass-card-mockup rounded-2xl shadow-md flex flex-col justify-between h-[155px] relative">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider block">Grafik Absensi Harian</span>
                                            <span class="text-xs text-blue-500 font-bold">Minggu Ini</span>
                                        </div>
                                        <div class="flex-1 w-full relative flex items-end">
                                            <svg viewBox="0 0 350 100" class="w-full h-18 text-blue-500 fill-current opacity-85">
                                                <defs>
                                                    <linearGradient id="siswa-chart-glow" x1="0" y1="0" x2="0" y2="1">
                                                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25"/>
                                                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"/>
                                                    </linearGradient>
                                                </defs>
                                                <path d="M 0 80 Q 50 30 100 50 T 200 20 T 300 40 L 350 50 L 350 100 L 0 100 Z" fill="url(#siswa-chart-glow)"></path>
                                                <path d="M 0 80 Q 50 30 100 50 T 200 20 T 300 40 L 350 50" fill="none" stroke="currentColor" stroke-width="3"></path>
                                                <circle cx="100" cy="50" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="1.5"></circle>
                                                <circle cx="200" cy="20" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="1.5"></circle>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($role === 'pokja')
                        <!-- Mockup Pokja Dashboard -->
                        <div class="space-y-8 text-left">
                            <div class="flex items-center justify-between border-b border-slate-200/50 dark:border-slate-800/50 pb-4">
                                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Dashboard Pokja</h2>
                                <span class="text-xs bg-slate-100 dark:bg-slate-900 px-3 py-1 rounded-xl text-slate-500 dark:text-slate-400 border border-slate-200/30 dark:border-slate-800/30 font-medium">Pokja Active Playground</span>
                            </div>

                            <!-- Stats Cards Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative"
                                 :class="activeHotspot === 1 ? 'relative z-40 ring-2 ring-blue-500 border-transparent rounded-2xl bg-white dark:bg-slate-900 shadow-2xl p-2' : ''">
                                <!-- Siswa Aktif Card -->
                                <div class="p-6 glass-card-mockup border-t-4 border-blue-500 rounded-2xl relative overflow-hidden group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-blue-500/15 rounded-xl flex items-center justify-center flex-shrink-0 border border-blue-500/20">
                                            <i data-lucide="graduation-cap" class="w-5 h-5 text-blue-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-450 text-[10px] font-bold uppercase tracking-wider">Total Siswa PKL</p>
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">120</h3>
                                        </div>
                                    </div>
                                    <!-- Hotspot 1 -->
                                    <div class="hotspot-pulse -right-2 top-2" @click="activeHotspot = 1" x-show="activeHotspot !== 1"></div>
                                </div>

                                <!-- DUDI Mitra Card -->
                                <div class="p-6 glass-card-mockup border-t-4 border-amber-500 rounded-2xl relative overflow-hidden group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-amber-500/15 rounded-xl flex items-center justify-center flex-shrink-0 border border-amber-500/20">
                                            <i data-lucide="building-2" class="w-5 h-5 text-amber-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-450 text-[10px] font-bold uppercase tracking-wider">Total DUDI Mitra</p>
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">34</h3>
                                        </div>
                                    </div>
                                </div>

                                <!-- Guru Pembimbing Card -->
                                <div class="p-6 glass-card-mockup border-t-4 border-purple-500 rounded-2xl relative overflow-hidden group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-purple-500/15 rounded-xl flex items-center justify-center flex-shrink-0 border border-purple-500/20">
                                            <i data-lucide="users" class="w-5 h-5 text-purple-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-450 text-[10px] font-bold uppercase tracking-wider">Guru Pembimbing</p>
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">15</h3>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sistem Aktif Card -->
                                <div class="p-6 glass-card-mockup border-t-4 border-emerald-500 rounded-2xl relative overflow-hidden group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-emerald-500/15 rounded-xl flex items-center justify-center flex-shrink-0 border border-emerald-500/20">
                                            <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-450 text-[10px] font-bold uppercase tracking-wider">Sistem Aktif</p>
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white mt-0.5">100%</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2 Column Layout (Workspace Pemetaan vs Kelola Zona) -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Interactive Mapping Area Mockup -->
                                <div class="p-6 glass-card-mockup rounded-2xl shadow-md flex flex-col justify-between min-h-[160px] relative"
                                     :class="activeHotspot === 2 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                    <div>
                                        <div class="flex items-center justify-between mb-4">
                                            <h5 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                                <i data-lucide="network" class="w-5 h-5 text-blue-500"></i>
                                                Workspace Pemetaan Siswa (Plotting)
                                            </h5>
                                            <span class="text-[9px] bg-blue-100 text-blue-800 dark:bg-blue-500/10 dark:text-blue-400 px-2.5 py-0.5 rounded-lg font-bold border border-blue-500/20">Demo</span>
                                        </div>
                                        <div class="space-y-3 text-xs">
                                            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-200/50 dark:border-slate-800/60">
                                                <span class="font-bold text-slate-700 dark:text-slate-350">Rizky Pratama (Siswa PPLG)</span>
                                                <i data-lucide="arrow-right" class="w-4 h-4 text-slate-400"></i>
                                                <span class="font-bold text-blue-500 truncate max-w-[150px]" x-text="pokjaMapped ? 'PT Teknologi Nusantara' : 'Belum Dipetakan'"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex justify-end relative">
                                        <button type="button" @click="pokjaMapped = !pokjaMapped"
                                                :class="pokjaMapped ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-blue-600 hover:bg-blue-500'"
                                                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md transition-all border-0 cursor-pointer outline-none flex items-center gap-1.5">
                                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                            <span x-text="pokjaMapped ? 'Reset Plotting' : 'Simulasikan Plotting'"></span>
                                        </button>
                                        <!-- Hotspot 2 -->
                                        <div class="hotspot-pulse -right-2 top-3" @click="activeHotspot = 2"></div>
                                    </div>
                                </div>

                                <!-- Kelola Zona Card -->
                                <div class="p-6 glass-card-mockup rounded-2xl shadow-md flex flex-col justify-between min-h-[160px] relative"
                                     :class="activeHotspot === 3 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                    <div>
                                        <div class="flex items-center justify-between mb-4">
                                            <h5 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                                <i data-lucide="compass" class="w-5 h-5 text-emerald-500"></i>
                                                Pembagian Zona Wilayah (Geografi)
                                            </h5>
                                        </div>
                                        <div class="space-y-2.5 text-xs">
                                            <div class="flex justify-between items-center p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/50 dark:border-slate-800/60">
                                                <span class="font-bold text-slate-700 dark:text-slate-350">Zona Ciamis Utara</span>
                                                <span class="text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 px-2.5 py-0.5 rounded-lg font-bold border border-emerald-500/20">12 DUDI Mitra</span>
                                            </div>
                                            <div class="flex justify-between items-center p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/50 dark:border-slate-800/60">
                                                <span class="font-bold text-slate-700 dark:text-slate-350">Zona Ciamis Selatan</span>
                                                <span class="text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 px-2.5 py-0.5 rounded-lg font-bold border border-emerald-500/20">8 DUDI Mitra</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Hotspot 3 -->
                                    <div class="hotspot-pulse -right-2 top-3" @click="activeHotspot = 3"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Mockup Other Roles (Generic Mockup Dashboard) -->
                        <div class="space-y-8 text-left">
                            <div class="flex items-center justify-between border-b border-slate-200/50 dark:border-slate-800/50 pb-4">
                                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Dashboard {{ ucfirst(str_replace('_', ' ', $role)) }}</h2>
                                <span class="text-xs bg-slate-100 dark:bg-slate-900 px-3 py-1 rounded-xl text-slate-500 dark:text-slate-400 border border-slate-200/30 dark:border-slate-800/30 font-medium">Playground Simulasi</span>
                            </div>

                            <!-- Stats Cards Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative">
                                @if($role === 'kaprog')
                                    <!-- Kaprog stats -->
                                    <div class="p-6 glass-card-mockup border-l-4 border-blue-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-500 dark:text-slate-450 text-[10px] font-bold uppercase tracking-wider mb-1">Total Siswa PKL</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">120</h3>
                                            <i data-lucide="users" class="w-6 h-6 text-blue-500/20"></i>
                                        </div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-emerald-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-500 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Total DUDI Mitra</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">34</h3>
                                            <i data-lucide="building-2" class="w-6 h-6 text-emerald-500/20"></i>
                                        </div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-purple-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-500 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Guru Pembimbing</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">15</h3>
                                            <i data-lucide="user-check" class="w-6 h-6 text-purple-500/20"></i>
                                        </div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-amber-500 rounded-2xl relative overflow-hidden group"
                                         :class="activeHotspot === 2 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                        <p class="text-slate-500 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Pengajuan Menunggu</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">3</h3>
                                            <i data-lucide="clock" class="w-6 h-6 text-amber-500/20"></i>
                                        </div>
                                        <!-- Hotspot 2 -->
                                        <div class="hotspot-pulse -right-2 top-2" @click="activeHotspot = 2"></div>
                                    </div>
                                @elseif($role === 'pembimbing_sekolah')
                                    <!-- Pembimbing stats -->
                                    <div class="p-6 glass-card-mockup border-l-4 border-blue-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-505 dark:text-slate-450 text-[10px] font-bold uppercase tracking-wider mb-1">Siswa Bimbingan</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">12</h3>
                                            <i data-lucide="users" class="w-6 h-6 text-blue-500/20"></i>
                                        </div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-amber-500 rounded-2xl relative overflow-hidden group"
                                         :class="activeHotspot === 1 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                        <p class="text-slate-505 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Jurnal Belum ACC</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">3</h3>
                                            <i data-lucide="alert-circle" class="w-6 h-6 text-amber-500/20"></i>
                                        </div>
                                        <!-- Hotspot 1 -->
                                        <div class="hotspot-pulse -right-2 top-2" @click="activeHotspot = 1" x-show="activeHotspot !== 1"></div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-purple-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-505 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Absensi Pending</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">1</h3>
                                            <i data-lucide="calendar" class="w-6 h-6 text-purple-500/20"></i>
                                        </div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-emerald-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-505 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Kehadiran Valid</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">98%</h3>
                                            <i data-lucide="shield-check" class="w-6 h-6 text-emerald-500/20"></i>
                                        </div>
                                    </div>
                                @elseif($role === 'pembimbing_dudi')
                                    <!-- Dudi mentor stats -->
                                    <div class="p-6 glass-card-mockup border-l-4 border-blue-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-505 dark:text-slate-450 text-[10px] font-bold uppercase tracking-wider mb-1">Siswa Magang</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">2</h3>
                                            <i data-lucide="users" class="w-6 h-6 text-blue-500/20"></i>
                                        </div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-amber-500 rounded-2xl relative overflow-hidden group"
                                         :class="activeHotspot === 1 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                        <p class="text-slate-505 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Validasi Hari Ini</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">1</h3>
                                            <i data-lucide="calendar-check" class="w-6 h-6 text-amber-500/20"></i>
                                        </div>
                                        <!-- Hotspot 1 -->
                                        <div class="hotspot-pulse -right-2 top-2" @click="activeHotspot = 1" x-show="activeHotspot !== 1"></div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-purple-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-505 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Status Keaktifan</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-base font-black text-slate-800 dark:text-white uppercase tracking-tighter mt-1">AKTIF</h3>
                                            <i data-lucide="activity" class="w-6 h-6 text-purple-500/20"></i>
                                        </div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-emerald-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-505 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Feedback Industri</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-base font-black text-emerald-500 uppercase tracking-tighter mt-1 font-bold">OK</h3>
                                            <i data-lucide="message-square" class="w-6 h-6 text-emerald-500/20"></i>
                                        </div>
                                    </div>
                                @else
                                    <!-- Admin stats -->
                                    <div class="p-6 glass-card-mockup border-l-4 border-blue-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-505 dark:text-slate-450 text-[10px] font-bold uppercase tracking-wider mb-1">Total Users</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">178</h3>
                                            <i data-lucide="users" class="w-6 h-6 text-blue-500/20"></i>
                                        </div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-emerald-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-505 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">System Status</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">100% OK</h3>
                                            <i data-lucide="server" class="w-6 h-6 text-emerald-500/20"></i>
                                        </div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-purple-500 rounded-2xl relative overflow-hidden group"
                                         :class="activeHotspot === 1 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                        <p class="text-slate-505 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Radius Absensi</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-xl font-black text-slate-800 dark:text-white">50m</h3>
                                            <i data-lucide="compass" class="w-6 h-6 text-purple-500/20"></i>
                                        </div>
                                        <!-- Hotspot 1 -->
                                        <div class="hotspot-pulse -right-2 top-2" @click="activeHotspot = 1" x-show="activeHotspot !== 1"></div>
                                    </div>
                                    <div class="p-6 glass-card-mockup border-l-4 border-amber-500 rounded-2xl relative overflow-hidden group">
                                        <p class="text-slate-505 dark:text-slate-455 text-[10px] font-bold uppercase tracking-wider mb-1">Audit Logs</p>
                                        <div class="flex items-end justify-between">
                                            <h3 class="text-base font-black text-slate-800 dark:text-white uppercase tracking-tighter mt-1">ACTIVE</h3>
                                            <i data-lucide="scroll-text" class="w-6 h-6 text-amber-500/20"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Row 2: Live SVG line chart -->
                            <div class="p-6 glass-card-mockup rounded-2xl shadow-md border border-slate-200/50 dark:border-slate-800/80 relative"
                                 :class="activeHotspot === 1 && '{{ $role }}' === 'kaprog' ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                <h5 class="text-xs font-bold text-slate-800 dark:text-white mb-3">Statistik Progres & Kehadiran Sekolah</h5>
                                
                                <div class="h-32 bg-slate-50 dark:bg-slate-900/60 border dark:border-slate-800/80 rounded-xl p-4 flex flex-col justify-between relative overflow-hidden">
                                    <div class="flex justify-between items-center text-[10px] text-slate-400 dark:text-slate-500">
                                        <span>Rata-Rata Keaktifan Mingguan</span>
                                        <span class="text-emerald-500 font-semibold flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span> 94.2%
                                        </span>
                                    </div>
                                    <div class="w-full relative flex items-end">
                                        <svg viewBox="0 0 500 80" class="w-full h-20 text-blue-500 fill-current opacity-85">
                                            <defs>
                                                <linearGradient id="glow-generic-role" x1="0" y1="0" x2="0" y2="1">
                                                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.2"/>
                                                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"/>
                                                </linearGradient>
                                            </defs>
                                            <path d="M 0 60 Q 75 10 150 40 T 300 20 T 450 10 L 500 20 L 500 80 L 0 80 Z" fill="url(#glow-generic-role)"></path>
                                            <path d="M 0 60 Q 75 10 150 40 T 300 20 T 450 10 L 500 20" fill="none" stroke="currentColor" stroke-width="3.5"></path>
                                            <circle cx="150" cy="40" r="4.5" fill="#3b82f6" stroke="#ffffff" stroke-width="2"></circle>
                                            <circle cx="300" cy="20" r="4.5" fill="#3b82f6" stroke="#ffffff" stroke-width="2"></circle>
                                        </svg>
                                    </div>
                                    @if($role === 'kaprog')
                                        <!-- Hotspot 1 -->
                                        <div class="hotspot-pulse -right-2 top-2" @click="activeHotspot = 1"></div>
                                    @endif
                                </div>
                            </div>

                            <!-- Row 3: Symmetrical Bottom Widgets -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Left column role-specific interactive widget -->
                                <div>
                                    @if($role === 'kaprog')
                                        <!-- Tinjau Pengajuan Baris Mockup -->
                                        <div class="p-6 glass-card-mockup rounded-2xl shadow-md flex flex-col justify-between min-h-[145px]">
                                            <h6 class="text-xs font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                                                <i data-lucide="file-plus-2" class="w-5 h-5 text-blue-500"></i>
                                                Validasi Usulan Magang Mandiri
                                            </h6>
                                            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-200/50 dark:border-slate-800/60">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center font-bold text-xs text-blue-550 flex-shrink-0">R</div>
                                                    <div class="min-w-0">
                                                        <h6 class="text-xs font-bold truncate text-slate-800 dark:text-white">Rizky Pratama</h6>
                                                        <p class="text-[10px] text-slate-500 mt-0.5 truncate">DUDI: PT Teknologi Nusantara</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 flex-shrink-0">
                                                    <button type="button" @click="kaprogApproved = !kaprogApproved"
                                                            :class="kaprogApproved ? 'bg-emerald-500 text-white' : 'bg-blue-600 text-white hover:bg-blue-500'"
                                                            class="px-3.5 py-2 rounded-xl text-xs font-bold shadow transition-all border-0 cursor-pointer outline-none">
                                                        <span x-text="kaprogApproved ? 'Disetujui' : 'Setujui'"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($role === 'pembimbing_sekolah')
                                        <!-- Persetujuan Jurnal Baris Mockup -->
                                        <div class="p-6 glass-card-mockup rounded-2xl shadow-md flex flex-col justify-between min-h-[145px]"
                                             :class="activeHotspot === 2 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                            <h6 class="text-xs font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                                                <i data-lucide="check-square" class="w-5 h-5 text-blue-500"></i>
                                                Persetujuan Jurnal Harian Siswa
                                            </h6>
                                            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-200/50 dark:border-slate-800/60">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center font-bold text-xs text-blue-550 flex-shrink-0">R</div>
                                                    <div class="min-w-0">
                                                        <h6 class="text-xs font-bold truncate text-slate-800 dark:text-white">Rizky - Jurnal Web Dev</h6>
                                                        <p class="text-[10px] text-slate-505 mt-0.5 truncate" x-text="guruAcc ? 'Status: Terverifikasi (ACC)' : 'Status: Menunggu Ulasan'"></p>
                                                    </div>
                                                </div>
                                                <button type="button" @click="guruAcc = !guruAcc"
                                                        :class="guruAcc ? 'bg-emerald-500 text-white' : 'bg-blue-600 text-white hover:bg-blue-500'"
                                                        class="px-3.5 py-2 rounded-xl text-xs font-bold shadow transition-all border-0 cursor-pointer outline-none flex-shrink-0">
                                                    <span x-text="guruAcc ? 'Di-ACC' : 'ACC Jurnal'"></span>
                                                </button>
                                            </div>
                                            <!-- Hotspot 2 -->
                                            <div class="hotspot-pulse -right-2 top-3" @click="activeHotspot = 2"></div>
                                        </div>
                                    @elseif($role === 'pembimbing_dudi')
                                        <!-- Validasi Kehadiran Baris Mockup -->
                                        <div class="p-6 glass-card-mockup rounded-2xl shadow-md flex flex-col justify-between min-h-[145px]"
                                             :class="activeHotspot === 2 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                            <h6 class="text-xs font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                                                <i data-lucide="clipboard-check" class="w-5 h-5 text-blue-500"></i>
                                                Konfirmasi Validasi Kehadiran DUDI
                                            </h6>
                                            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-200/50 dark:border-slate-800/60">
                                                <div class="min-w-0 pr-2">
                                                    <h6 class="text-xs font-bold truncate text-slate-800 dark:text-white">Presensi: Rizky Pratama</h6>
                                                    <span class="text-[10px] text-slate-500 dark:text-slate-450 block mt-0.5 truncate" x-text="dudiValidated ? 'Terverifikasi Masuk (07:28)' : 'Menunggu Validasi Masuk'"></span>
                                                </div>
                                                <button type="button" @click="dudiValidated = !dudiValidated"
                                                        :class="dudiValidated ? 'bg-emerald-500 text-white' : 'bg-blue-600 text-white hover:bg-blue-500'"
                                                        class="px-3.5 py-2 rounded-xl text-xs font-bold shadow transition-all border-0 cursor-pointer outline-none flex-shrink-0">
                                                    <span x-text="dudiValidated ? 'Tervalidasi' : 'Validasi'"></span>
                                                </button>
                                            </div>
                                            <!-- Hotspot 2 -->
                                            <div class="hotspot-pulse -right-2 top-3" @click="activeHotspot = 2"></div>
                                        </div>
                                    @elseif($role === 'super_admin')
                                        <!-- Configuration Panel Mockup -->
                                        <div class="p-6 glass-card-mockup rounded-2xl shadow-md flex flex-col justify-between min-h-[145px]"
                                             :class="activeHotspot === 2 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                            <h6 class="text-xs font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                                                <i data-lucide="settings" class="w-5 h-5 text-blue-500"></i>
                                                Penyetelan Konfigurasi Geofencing
                                            </h6>
                                            <div class="space-y-3">
                                                <div class="flex justify-between items-center text-xs">
                                                    <label class="font-semibold text-slate-500">Radius Batas Absensi (Meter)</label>
                                                    <input type="text" value="50m" disabled class="w-12 px-2 py-0.5 bg-slate-50 dark:bg-slate-900 border dark:border-slate-800 rounded-lg text-center font-mono text-xs font-bold">
                                                </div>
                                            </div>
                                            <!-- Hotspot 2 -->
                                            <div class="hotspot-pulse -right-2 top-3" @click="activeHotspot = 2"></div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Right column role-specific details/logs/charts -->
                                <div>
                                    @if($role === 'kaprog')
                                        <!-- Laporan Kaprog Card -->
                                        <div class="p-6 glass-card-mockup rounded-2xl shadow-md min-h-[145px] relative"
                                             :class="activeHotspot === 3 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                            <h5 class="text-xs font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                                                <i data-lucide="bar-chart-2" class="w-5 h-5 text-emerald-500"></i>
                                                Rekapitulasi Laporan Jurusan
                                            </h5>
                                            <div class="space-y-2 text-xs">
                                                <div class="flex justify-between items-center p-2 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/50 dark:border-slate-800/60">
                                                    <span>Total Siswa Terplot DUDI:</span>
                                                    <span class="font-extrabold text-emerald-500">32 Siswa</span>
                                                </div>
                                                <div class="flex justify-between items-center p-2 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/50 dark:border-slate-800/60">
                                                    <span>Total Belum Terplot:</span>
                                                    <span class="font-extrabold text-slate-500">4 Siswa</span>
                                                </div>
                                            </div>
                                            <!-- Hotspot 3 -->
                                            <div class="hotspot-pulse -right-2 top-3" @click="activeHotspot = 3"></div>
                                        </div>
                                    @elseif($role === 'pembimbing_sekolah')
                                        <!-- Persetujuan Absensi / Dispensasi Card -->
                                        <div class="p-6 glass-card-mockup rounded-2xl shadow-md min-h-[145px] space-y-4 relative"
                                             :class="activeHotspot === 3 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                            <div>
                                                <h6 class="text-xs font-bold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                                                    <i data-lucide="info" class="w-4.5 h-4.5 text-amber-500"></i>
                                                    Persetujuan Absensi Pending
                                                </h6>
                                                <div class="p-2.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/50 dark:border-slate-800/60 flex justify-between items-center text-xs">
                                                    <span class="truncate pr-1 font-semibold text-slate-700 dark:text-slate-305">Ahmad Faisal (Izin Sakit)</span>
                                                    <span class="text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-400 px-2 py-0.5 rounded-lg font-extrabold border border-amber-500/20">TINJAU</span>
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center text-xs pt-2.5 border-t dark:border-slate-800/60">
                                                <span class="font-bold text-slate-800 dark:text-white">Nilai Sidang PKL:</span>
                                                <span class="font-extrabold text-blue-600 dark:text-blue-400">Rizky Pratama: 92</span>
                                            </div>
                                            <!-- Hotspot 3 -->
                                            <div class="hotspot-pulse -right-2 top-3" @click="activeHotspot = 3"></div>
                                        </div>
                                    @elseif($role === 'pembimbing_dudi')
                                        <!-- Feedback Sekolah Card -->
                                        <div class="p-6 glass-card-mockup rounded-2xl shadow-md min-h-[145px] relative"
                                             :class="activeHotspot === 3 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                            <h5 class="text-xs font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                                                <i data-lucide="message-square" class="w-5 h-5 text-blue-500"></i>
                                                Penilaian & Feedback Mentor DUDI
                                            </h5>
                                            <div class="space-y-2 text-xs">
                                                <div class="flex justify-between items-center p-2 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/50 dark:border-slate-800/60">
                                                    <span>Nilai Sikap:</span>
                                                    <span class="font-extrabold text-emerald-500">Sangat Baik (95)</span>
                                                </div>
                                                <div class="flex justify-between items-center p-2 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/50 dark:border-slate-800/60">
                                                    <span>Nilai Keahlian:</span>
                                                    <span class="font-extrabold text-emerald-500">Sangat Baik (92)</span>
                                                </div>
                                            </div>
                                            <!-- Hotspot 3 -->
                                            <div class="hotspot-pulse -right-2 top-3" @click="activeHotspot = 3"></div>
                                        </div>
                                    @elseif($role === 'super_admin')
                                        <!-- Audit Logs Widget -->
                                        <div class="p-6 glass-card-mockup rounded-2xl shadow-md min-h-[145px] relative"
                                             :class="activeHotspot === 3 ? 'relative z-40 ring-2 ring-blue-500 border-transparent bg-white dark:bg-slate-900 shadow-2xl' : ''">
                                            <h5 class="text-xs font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                                                <i data-lucide="scroll-text" class="w-5 h-5 text-red-500"></i>
                                                System Security Logs
                                            </h5>
                                            <div class="space-y-2 font-mono text-[10px] text-slate-500">
                                                <div class="truncate p-2 bg-slate-50 dark:bg-slate-900/60 rounded-xl border dark:border-slate-800/60"><span class="text-blue-500 font-bold">[INFO]</span> 12:08 - geofence radius set to 50m</div>
                                                <div class="truncate p-2 bg-slate-50 dark:bg-slate-900/60 rounded-xl border dark:border-slate-800/60"><span class="text-emerald-500 font-bold">[AUTH]</span> 12:05 - kaprog logged in from IP ::1</div>
                                            </div>
                                            <!-- Hotspot 3 -->
                                            <div class="hotspot-pulse -right-2 top-3" @click="activeHotspot = 3"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mode 2: Center Modal Overlay Penjelasan Detail (Teleported to Body) -->
        <template x-teleport="body">
            <div x-show="activeHotspot !== null" 
                 @click="activeHotspot = null"
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-955/70 backdrop-blur-md"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 x-cloak>
                 
                <!-- Click-away backdrop area -->
                <div class="absolute inset-0 cursor-pointer"></div>

                <!-- Modal Window Container (Radix UI Aligned) -->
                <div class="w-full max-w-xl rounded-3xl overflow-hidden shadow-2xl border border-slate-200/60 dark:border-slate-800/80 bg-white dark:bg-slate-900 text-left p-8 relative z-10 max-h-[85vh] flex flex-col glow-blue"
                     @click.stop
                     role="dialog"
                     aria-modal="true"
                     aria-labelledby="modal-title"
                     aria-describedby="modal-description">
                    
                    <!-- Close Button -->
                    <button @click="activeHotspot = null" 
                            class="absolute top-5 right-5 p-2 rounded-2xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all border-0 bg-transparent cursor-pointer z-20 flex items-center justify-center outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="Tutup">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>

                    <!-- Modal Header -->
                    <div class="mb-5 pr-8">
                        <span id="modal-title" class="text-xs font-black uppercase tracking-widest text-blue-600 dark:text-blue-400 flex items-center gap-2 select-none">
                            <i data-lucide="sparkles" class="w-4 h-4 text-blue-600 dark:text-blue-400 animate-pulse"></i>
                            PANDUAN FITUR DASHBOARD
                        </span>
                    </div>

                    <!-- Modal Body Content -->
                    <div id="modal-description" class="overflow-y-auto pr-1 text-sm text-slate-655 dark:text-slate-400 space-y-4">
                        @if($role === 'siswa')
                            <div x-show="activeHotspot === 1" class="space-y-4" x-cloak>
                                <h4 class="text-lg font-black text-slate-900 dark:text-white">Presensi Harian Berbasis Lokasi (Geofencing)</h4>
                                <p class="leading-relaxed">
                                    Tombol **Clock-In** (Masuk) dan **Clock-Out** (Pulang) digunakan untuk mencatat kehadiran harian Anda di industri.
                                </p>
                                <div class="p-5 bg-blue-500/5 dark:bg-blue-500/10 border border-blue-500/20 rounded-2xl text-xs space-y-2.5">
                                    <p><strong>Cara Kerja Radius GPS</strong>: Browser akan meminta izin akses lokasi. Sistem akan memeriksa apakah koordinat GPS Anda berada di dalam radius toleransi aman (misal 50 meter) dari titik lokasi DUDI magang.</p>
                                    <p class="text-amber-605 dark:text-amber-400 font-bold flex items-center gap-1.5">
                                        <i data-lucide="help-circle" class="w-4 h-4"></i>
                                        Tip: Pastikan fitur GPS / Lokasi pada ponsel Anda menyala dan diizinkan oleh browser agar tidak muncul error "Diluar Radius".
                                    </p>
                                </div>
                            </div>
                            <div x-show="activeHotspot === 2" class="space-y-4" x-cloak>
                                <h4 class="text-lg font-black text-slate-900 dark:text-white">Menulis Jurnal Harian & Kompetensi</h4>
                                <p class="leading-relaxed">
                                    Menu **Jurnal Kegiatan** berfungsi mendokumentasikan hasil belajar dan pekerjaan Anda di industri setiap hari.
                                </p>
                                <div class="p-5 bg-blue-500/5 dark:bg-blue-500/10 border border-blue-500/20 rounded-2xl text-xs space-y-2.5">
                                    <li>Tulis ringkasan aktivitas nyata yang dikerjakan beserta dokumentasi foto pendukung.</li>
                                    <li>Pilihlah Tujuan Pembelajaran (TP) / Capaian Pembelajaran (CP) yang sesuai dengan program keahlian Anda agar terverifikasi secara kurikulum.</li>
                                    <li class="font-bold text-emerald-600 dark:text-emerald-400">Penting: Jurnal Anda harus disetujui (ACC) oleh guru pembimbing agar masuk rekap penilaian. Jurnal yang ditolak harus direvisi sesuai ulasan pembimbing.</li>
                                </div>
                            </div>
                            <div x-show="activeHotspot === 3" class="space-y-4" x-cloak>
                                <h4 class="text-lg font-black text-slate-900 dark:text-white">Pengajuan Tempat PKL Mandiri</h4>
                                <p class="leading-relaxed">
                                    Modul ini menampilkan status pendaftaran tempat PKL secara mandiri yang diajukan oleh siswa di luar daftar DUDI utama.
                                </p>
                                <div class="p-5 bg-blue-500/5 dark:bg-blue-500/10 border border-blue-500/20 rounded-2xl text-xs space-y-2.5">
                                    <p><strong>Alur Persetujuan</strong>: Pengajuan Siswa -> Tinjauan Kelayakan oleh Kaprog -> Status Disetujui/Ditolak.</p>
                                    <p>Jika disetujui, guru pembimbing sekolah dan pembimbing industri akan otomatis ditugaskan oleh tim Pokja melalui sistem pemetaan.</p>
                                </div>
                            </div>
                        @elseif($role === 'pokja')
                            <div x-show="activeHotspot === 1" class="space-y-4" x-cloak>
                                <h4 class="text-lg font-black text-slate-900 dark:text-white">Kelola Akun & Master Data DUDI</h4>
                                <p class="leading-relaxed">
                                    Menu utama Pokja untuk mendaftarkan akun Siswa, Guru Pembimbing, Mentor DUDI, Perusahaan DUDI, dan Kaprog.
                                </p>
                                <div class="p-5 bg-blue-500/5 dark:bg-blue-500/10 border border-blue-500/20 rounded-2xl text-xs space-y-2.5">
                                    <p><strong>Fitur Impor Excel</strong>: Anda dapat menyalin data akun ke template Excel bawaan dan mengunggahnya secara massal (.xlsx) untuk menghemat waktu pendaftaran manual.</p>
                                </div>
                            </div>
                            <div x-show="activeHotspot === 2" class="space-y-4" x-cloak>
                                <h4 class="text-lg font-black text-slate-900 dark:text-white">Workspace Pemetaan Siswa (Plotting)</h4>
                                <p class="leading-relaxed">
                                    Inti dari tugas Pokja: menempatkan siswa PKL ke perusahaan DUDI serta menetapkan pembimbing sekolah & industri.
                                </p>
                                <div class="p-5 bg-blue-500/5 dark:bg-blue-500/10 border border-blue-500/20 rounded-2xl text-xs space-y-2.5">
                                    <p><strong>Penting</strong>: Sebelum pemetaan diselesaikan oleh Pokja, siswa belum dapat melakukan absensi geofencing dan menulis jurnal harian di sistem.</p>
                                </div>
                            </div>
                            <div x-show="activeHotspot === 3" class="space-y-4" x-cloak>
                                <h4 class="text-lg font-black text-slate-900 dark:text-white">Membagi Wilayah & Kelola Zona</h4>
                                <p class="leading-relaxed">
                                    Zona wilayah digunakan untuk mengelompokkan lokasi geografis perusahaan DUDI mitra (contoh: Zona Ciamis Utara, Zona Tasikmalaya, dll).
                                </p>
                                <div class="p-5 bg-blue-500/5 dark:bg-blue-500/10 border border-blue-500/20 rounded-2xl text-xs space-y-2.5">
                                    <p>Pembagian zona ini membantu Pokja dalam menetapkan guru pembimbing agar satu guru dapat mendampingi kelompok siswa di wilayah yang berdekatan.</p>
                                </div>
                            </div>
                        @else
                            <!-- Custom Explanations for Kaprog, Teachers, Mentors, Admin -->
                            @if($role === 'kaprog')
                                <div x-show="activeHotspot === 1" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Statistik Keaktifan & Progres Jurusan</h4>
                                    <p class="leading-relaxed">Grafik visualisasi sebaran dan status penempatan siswa khusus untuk Program Keahlian Anda (misalnya Program Keahlian PPLG).</p>
                                </div>
                                <div x-show="activeHotspot === 2" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Persetujuan Penempatan Mandiri</h4>
                                    <p class="leading-relaxed">Di halaman ini, Anda memvalidasi detail usulan tempat magang mandiri dari siswa. Jika dinilai layak dan memenuhi standar industri, berikan persetujuan agar tim Pokja dapat memproses guru pembimbingnya.</p>
                                </div>
                                <div x-show="activeHotspot === 3" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Laporan Rekapitulasi Kaprog</h4>
                                    <p class="leading-relaxed">Menampilkan ringkasan status magang siswa beserta plotting guru pendamping dan industri untuk evaluasi kurikulum program.</p>
                                </div>
                            @elseif($role === 'pembimbing_sekolah')
                                <div x-show="activeHotspot === 1" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Verifikasi Jurnal Harian (ACC)</h4>
                                    <p class="leading-relaxed">Meninjau dan menyetujui isian jurnal siswa bimbingan Anda. Jika isiannya asal-asalan atau tidak sinkron dengan kompetensi magang, Anda dapat menolak jurnal dengan menyertakan catatan penolakan.</p>
                                </div>
                                <div x-show="activeHotspot === 2" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Persetujuan Ketidakhadiran (Absensi)</h4>
                                    <p class="leading-relaxed">Meninjau pengajuan ketidakhadiran siswa karena sakit atau izin penting. Verifikasi berkas surat keterangan yang diunggah siswa sebelum memberikan ACC dispensasi.</p>
                                </div>
                                <div x-show="activeHotspot === 3" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Evaluasi & Input Nilai Laporan</h4>
                                    <p class="leading-relaxed">Mengisi nilai akhir PKL siswa berdasarkan ulasan laporan magang tertulis serta hasil presentasi sidang magang siswa di sekolah.</p>
                                </div>
                            @elseif($role === 'pembimbing_dudi')
                                <div x-show="activeHotspot === 1" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Validasi Absensi Industri</h4>
                                    <p class="leading-relaxed">Memeriksa rekap jam clock-in dan clock-out harian siswa di industri Anda. Validasi data kehadiran ini agar rekap akhir absensi bulanan siswa dinyatakan sah.</p>
                                </div>
                                <div x-show="activeHotspot === 2" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Konfirmasi Validasi Kehadiran</h4>
                                    <p class="leading-relaxed">Melakukan checklist absensi harian secara manual bila siswa mengalami kendala perangkat atau GPS saat presensi geofencing.</p>
                                </div>
                                <div x-show="activeHotspot === 3" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Feedback Kepuasan Industri & Penilaian</h4>
                                    <p class="leading-relaxed">Mengisi instrumen kuisioner kepuasan DUDI terhadap performa siswa magang dan menilai aspek kompetensi teknis serta soft-skill (kedisiplinan, kerjasama, etos kerja) siswa di akhir program.</p>
                                </div>
                            @else
                                <div x-show="activeHotspot === 1" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Penyetelan Parameter Global (Konfigurasi)</h4>
                                    <p class="leading-relaxed">Mengatur konfigurasi global web seperti nama sekolah, tahun ajaran aktif, toleransi keterlambatan presensi, dan mengaktifkan/menonaktifkan pemeliharaan sistem.</p>
                                </div>
                                <div x-show="activeHotspot === 2" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Audit Security Logging</h4>
                                    <p class="leading-relaxed">Halaman rekap log server yang mencatat setiap aktivitas write/delete data krusial di sistem. Berguna untuk mendeteksi tindakan mencurigakan atau memulihkan data bermasalah.</p>
                                </div>
                                <div x-show="activeHotspot === 3" class="space-y-4" x-cloak>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Pengaturan Radius Geofencing</h4>
                                    <p class="leading-relaxed">Mengonfigurasi radius jangkauan presensi geofencing absensi siswa (contoh: 50m, 100m) agar sesuai dengan cakupan jaringan dan area fisik industri magang.</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-8 pt-5 border-t border-slate-200/60 dark:border-slate-800/80 flex justify-end">
                        <button type="button" @click="activeHotspot = null" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer border-0 outline-none">
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>
        </template>

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
