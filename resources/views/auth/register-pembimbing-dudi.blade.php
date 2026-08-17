<x-guest-layout>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            overflow-y: auto !important;
            overflow-x: hidden !important;
            min-height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            padding: 2rem 1rem !important;
        }
    </style>

    <div class="glass-card w-full max-w-2xl mx-auto p-8 my-8 relative z-20">
        <!-- Banner Header (Google Form-like accent top) -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-linear-to-r from-blue-500 to-indigo-600 rounded-t-2xl"></div>

        <!-- Logo & Header -->
        <div class="text-center mb-8 pt-2">
            <img src="{{ $appLogoActive ?: asset('logo.png') }}" alt="Logo" class="w-16 h-16 mx-auto object-contain mb-4 rounded-xl">
            <h1 class="text-3xl font-black tracking-tighter text-transparent bg-clip-text bg-linear-to-r from-blue-500 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 drop-shadow-sm cursor-default">
                Registrasi Pembimbing DUDI
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">Buat akun pembimbing industri dan tandai lokasi koordinat perusahaan</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-500/10 text-red-500 text-sm shadow-sm border border-red-500/20">
                <div class="font-semibold mb-1">Terjadi kesalahan input:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-500/10 text-red-500 text-sm shadow-sm border border-red-500/20">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.pembimbing_dudi.store') }}" class="space-y-6" id="registerForm" x-data="{ password: '', password_confirmation: '', showPassword: false, showConfirmation: false }">
            @csrf

            <!-- Form Section: Data Diri -->
            <div class="bg-slate-100/50 dark:bg-slate-900/30 p-5 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4"></i> Profil Pembimbing Industri
                </h3>
                <hr class="border-slate-200 dark:border-slate-800" />
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Kolom bertanda <span class="text-rose-500 font-bold">*</span> wajib diisi.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div class="md:col-span-2">
                        <label for="nama_lengkap" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap <span class="text-rose-500 font-bold">*</span></label>
                        <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap') }}" required autofocus
                               class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all"
                               placeholder="Nama lengkap Pembimbing Industri">
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label for="jabatan" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jabatan di Perusahaan <span class="text-slate-400 dark:text-slate-500 font-normal">(Opsional)</span></label>
                        <input id="jabatan" name="jabatan" type="text" value="{{ old('jabatan') }}"
                               class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all"
                               placeholder="Contoh: HRD, Manager, Staf Administrasi">
                    </div>

                    <!-- Nomor HP (WhatsApp) -->
                    <div>
                        <label for="no_hp" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">No. HP / WhatsApp <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative flex items-center">
                            <!-- Static Prefix -->
                            <span class="absolute left-4 text-sm font-semibold text-slate-500 pointer-events-none select-none">
                                +62
                            </span>
                            <!-- Input with padding-left to clear the prefix -->
                            <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp') }}" required
                                   class="w-full pl-14 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all font-mono"
                                   placeholder="812-3456-7890">
                        </div>
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Username (Terisi Otomatis) <span class="text-rose-500 font-bold">*</span></label>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all font-mono"
                               placeholder="username_otomatis">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email (Terisi Otomatis) <span class="text-rose-500 font-bold">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all font-mono"
                               placeholder="email@dudi.pkl.id">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Password <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required x-model="password"
                                   class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all"
                                   placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-700 transition-colors">
                                <i data-lucide="eye" x-show="!showPassword" class="h-4 w-4"></i>
                                <i data-lucide="eye-off" x-show="showPassword" class="h-4 w-4" x-cloak></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Konfirmasi Password <span class="text-rose-500 font-bold">*</span></label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" :type="showConfirmation ? 'text' : 'password'" required x-model="password_confirmation"
                                   class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all"
                                   placeholder="••••••••">
                            <button type="button" @click="showConfirmation = !showConfirmation" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-700 transition-colors">
                                <i data-lucide="eye" x-show="!showConfirmation" class="h-4 w-4"></i>
                                <i data-lucide="eye-off" x-show="showConfirmation" class="h-4 w-4" x-cloak></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Requirement Checklist -->
                    <div class="md:col-span-2 p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 shadow-inner">
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Kriteria Password Aman:</p>
                        <ul class="text-[11px] grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                            <li class="flex items-center gap-2 transition-colors duration-200" :class="password.length >= 8 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                                <span class="transition-all duration-200" :class="password.length >= 8 ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                Minimal 8 karakter
                            </li>
                            <li class="flex items-center gap-2 transition-colors duration-200" :class="/[A-Z]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                                <span class="transition-all duration-200" :class="/[A-Z]/.test(password) ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                Mengandung huruf besar (A-Z)
                            </li>
                            <li class="flex items-center gap-2 transition-colors duration-200" :class="/[a-z]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                                <span class="transition-all duration-200" :class="/[a-z]/.test(password) ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                Mengandung huruf kecil (a-z)
                            </li>
                            <li class="flex items-center gap-2 transition-colors duration-200" :class="/[0-9]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                                <span class="transition-all duration-200" :class="/[0-9]/.test(password) ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                Mengandung angka (0-9)
                            </li>
                            <li class="flex items-center gap-2 transition-colors duration-200" :class="/[@$!%*?&#-_]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                                <span class="transition-all duration-200" :class="/[@$!%*?&#-_]/.test(password) ? 'text-emerald-500' : 'text-slate-300 dark:text-slate-600'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                Karakter spesial (contoh: @$!%*?&#-_)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Section: Data Perusahaan & Lokasi -->
            <div class="bg-slate-100/50 dark:bg-slate-900/30 p-5 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4"></i> Lokasi Perusahaan
                </h3>
                <hr class="border-slate-200 dark:border-slate-800" />

                <!-- Searchable Dropdown Pilih DUDI -->
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '{{ old('dudi_id') }}',
                    selectedLabel: '',
                    dudis: {{ json_encode($dudis->map(fn($d) => ['id' => $d->id, 'nama' => $d->nama, 'lat' => $d->latitude, 'lng' => $d->longitude, 'alamat' => $d->alamat])) }},
                    init() {
                        if (this.selectedId) {
                            let found = this.dudis.find(d => d.id == this.selectedId);
                            if (found) {
                                this.selectedLabel = found.nama;
                                this.selectDudi(found);
                            }
                        }
                    },
                    get filteredDudis() {
                        if (this.search === '') return this.dudis;
                        return this.dudis.filter(d => d.nama.toLowerCase().includes(this.search.toLowerCase()));
                    },
                    selectDudi(dudi) {
                        this.selectedId = dudi.id;
                        this.selectedLabel = dudi.nama;
                        this.open = false;
                        this.search = '';
                        
                        // Populate hidden native select so Laravel backend receives it
                        const nativeSelect = document.getElementById('dudi_id');
                        nativeSelect.value = dudi.id;
                        
                        // Trigger native change event for map updating
                        const event = new Event('change');
                        nativeSelect.dispatchEvent(event);
                    }
                }" class="relative">
                    <label for="dudi_search_input" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Perusahaan / Instansi <span class="text-rose-500 font-bold">*</span></label>
                    
                    <!-- Hidden Select for form submission -->
                    <select name="dudi_id" id="dudi_id" class="hidden" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($dudis as $dudi)
                            <option value="{{ $dudi->id }}" data-lat="{{ $dudi->latitude }}" data-lng="{{ $dudi->longitude }}" data-alamat="{{ $dudi->alamat }}" {{ old('dudi_id') == $dudi->id ? 'selected' : '' }}>
                                {{ $dudi->nama }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Search Input Trigger -->
                    <div class="relative">
                        <input id="dudi_search_input" type="text" 
                               class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all cursor-pointer"
                               placeholder="Cari nama perusahaan..."
                               x-model="search"
                               @focus="open = true"
                               @click.away="open = false; if (selectedId && search === '') { search = ''; }"
                               x-bind:value="selectedId ? (open ? search : selectedLabel) : search">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="search" class="h-4 w-4"></i>
                        </div>
                    </div>

                    <!-- Dropdown Options Panel -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl shadow-lg focus:outline-hidden text-sm"
                         x-cloak>
                        <template x-for="dudi in filteredDudis" :key="dudi.id">
                            <div @mousedown="selectDudi(dudi)" 
                                 class="px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer text-slate-700 dark:text-slate-300 flex flex-col transition-colors duration-150 border-b border-slate-100/50 dark:border-slate-800/50 last:border-0">
                                <span class="font-semibold text-slate-800 dark:text-slate-100" x-text="dudi.nama"></span>
                                <span class="text-[10px] text-slate-500 truncate" x-text="dudi.alamat || 'Alamat tidak terdaftar'"></span>
                            </div>
                        </template>
                        <div x-show="filteredDudis.length === 0" class="px-4 py-3 text-slate-500 dark:text-slate-400 text-xs text-center font-medium">
                            Perusahaan tidak ditemukan.
                        </div>
                    </div>
                    
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">Ketik untuk mencari instansi/perusahaan tempat Anda bertugas sebagai pembimbing.</p>
                </div>

                <!-- Info Alamat DUDI terpilih -->
                <div id="dudi-alamat-box" class="hidden p-3.5 bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100/50 dark:border-blue-900/30 rounded-xl text-xs text-slate-600 dark:text-slate-300">
                    <div class="font-bold mb-1 flex items-center gap-1.5 text-blue-600 dark:text-blue-400">
                        <i data-lucide="info" class="w-3.5 h-3.5"></i> Alamat Terdaftar:
                    </div>
                    <span id="dudi-alamat-text">-</span>
                </div>

                <!-- Leaflet Map Integration -->
                <div class="space-y-3 pt-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Tentukan Lokasi Koordinat Kantor <span class="text-rose-500 font-bold">*</span></label>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Klik pada peta atau geser penanda merah di bawah untuk menandai lokasi tepat gerbang/pintu masuk kantor Anda. Siswa akan melakukan presensi kehadiran berdasarkan koordinat ini.
                    </p>

                    <!-- Search Location Input (Google Maps-like Search) -->
                    <div class="relative">
                        <div class="relative">
                            <input id="map_search" type="text" 
                                   class="w-full pl-10 pr-20 py-2.5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200 transition-all font-sans"
                                   placeholder="Cari koordinat, nama jalan, alamat, gedung, atau daerah...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="h-4 w-4 text-slate-400"></i>
                            </div>
                            <button type="button" id="btn_search_map"
                                    class="absolute right-1.5 top-1.5 bottom-1.5 px-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold text-xs rounded-lg transition-colors flex items-center justify-center gap-1 shadow-xs">
                                Cari
                            </button>
                        </div>
                        <div id="search_results_container" class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl shadow-lg hidden max-h-48 overflow-y-auto text-xs">
                        </div>
                    </div>

                    <!-- Gunakan Lokasi GPS Saat Ini Button -->
                    <div class="flex justify-end">
                        <button type="button" id="btn-detect-gps"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors cursor-pointer">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                            Gunakan Lokasi GPS Saat Ini
                        </button>
                    </div>

                    <!-- Coordinates Read-only input -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Latitude</label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" readonly required
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-800 rounded-xl text-xs font-mono text-slate-700 dark:text-slate-300">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Longitude</label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" readonly required
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-800 rounded-xl text-xs font-mono text-slate-700 dark:text-slate-300">
                        </div>
                    </div>

                    <!-- Map container -->
                    <div id="registration-map" style="height:320px; border-radius:.75rem; z-index:1; border: 1px solid rgba(148, 163, 184, 0.2);"></div>
                </div>
            </div>

            <!-- Submit & Links -->
            <div class="space-y-4 pt-4">
                <button type="submit" class="w-full py-3 bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    Buat Akun & Masuk
                </button>

                <div class="text-center pt-2">
                    <a href="{{ route('login') }}" class="text-xs text-slate-500 hover:text-blue-500 dark:text-slate-400 dark:hover:text-blue-400 transition-colors">
                        Sudah memiliki akun pembimbing? <span class="font-bold underline">Masuk di sini</span>
                    </a>
                </div>
            </div>
        </form>

        <div class="mt-8 text-center border-t border-slate-200/50 dark:border-slate-700/50 pt-6">
            <p class="text-xs text-slate-500 dark:text-slate-400">©2026 SMKN 1 CIAMIS. All rights reserved.</p>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- JS Logic for Auto-fill & Maps -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- JS Logic 1: Username & Email Auto fill ---
        const nameInput = document.getElementById('nama_lengkap');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');

        function updateCredentials() {
            let nameVal = nameInput.value;
            
            // 1. Split by comma to remove suffix titles (e.g. , S.T., M.T.)
            let nameClean = nameVal.split(',')[0].trim();
            
            // 2. Remove common Indonesian suffix titles at the end of the name even if no comma (e.g. S.Pd, S.T, M.T, etc.)
            let prevSuffix;
            do {
                prevSuffix = nameClean;
                // Enforce dot after S/M (e.g. S.Pd, S.St. Mos, M.T, M.S.i)
                nameClean = nameClean.replace(/\s+((s|m)\.[a-z]{1,4}(\s*\.?\s*[a-z]{1,4})*\.?)$/i, '').trim();
                // Match exact list of common suffixes without dots (e.g. ST, MT, SST, SPd, SKom, AMd, SE, MM)
                nameClean = nameClean.replace(/\s+(st|mt|sst|skom|mkom|spd|mpd|se|mm|sh|mh|ssi|msi|ssos|spsi|mpsi|amd|phd|bsc|mba|mos)$/i, '').trim();
                // A.Md/A.Ma matching (e.g. A.Md, A.Md.Kom, A.Ma)
                nameClean = nameClean.replace(/\s+(a\.?\s*m(d|a)\.?\s*[a-z]{0,4}(\s*\.?\s*[a-z]{1,4})*\.?)$/i, '').trim();
                // Other exact professional degrees with dots
                nameClean = nameClean.replace(/\s+(ph\.?\s*d|b\.?\s*sc|c\.?\s*a|c\.?\s*p\.?\s*a|c\.?\s*fa|c\.?\s*m\.?\s*a)\.?$/i, '').trim();
            } while (nameClean !== prevSuffix);
            
            // 3. Remove common Indonesian prefix titles (Drs, Dra, Dr, Prof, Ir, H, Hj)
            let prevPrefix;
            do {
                prevPrefix = nameClean;
                nameClean = nameClean.replace(/^(drs\.?|dra\.?|dr\.?|prof\.?|ir\.?|h\.?|hj\.?)\s+/i, '').trim();
            } while (nameClean !== prevPrefix);
            
            // 4. Clean up name: all lowercase, no space, remove non-alphanumeric
            let cleanVal = nameClean.toLowerCase()
                                  .replace(/[^a-z0-9]/g, '');
            
            usernameInput.value = cleanVal;
            if (cleanVal !== '') {
                emailInput.value = cleanVal + '@dudi.pkl.id';
            } else {
                emailInput.value = '';
            }
        }

        nameInput.addEventListener('input', updateCredentials);

        // Allow manual tweak on username but format it
        usernameInput.addEventListener('input', function() {
            let userVal = this.value.toLowerCase().replace(/[^a-z0-9]/g, '');
            this.value = userVal;
            if (userVal !== '') {
                emailInput.value = userVal + '@dudi.pkl.id';
            } else {
                emailInput.value = '';
            }
        });

        // --- JS Logic 1b: Phone Number Formatter ---
        const phoneInput = document.getElementById('no_hp');

        function formatPhoneNumber(val) {
            // Strip everything except digits
            let digits = val.replace(/\D/g, '');
            
            // Strip 62 at the start
            if (digits.startsWith('62')) {
                digits = digits.slice(2);
            }
            
            // Strip 0 at the start
            if (digits.startsWith('0')) {
                digits = digits.slice(1);
            }
            
            // Format digits: 3 digits (operator prefix) followed by dashes every 4 digits
            let formatted = '';
            if (digits.length > 0) {
                let part1 = digits.substring(0, 3);
                formatted = part1;
                
                if (digits.length > 3) {
                    let part2 = digits.substring(3, 7);
                    formatted += '-' + part2;
                }
                
                if (digits.length > 7) {
                    let part3 = digits.substring(7);
                    formatted += '-' + part3;
                }
            }
            
            return formatted;
        }

        phoneInput.addEventListener('input', function() {
            this.value = formatPhoneNumber(this.value);
        });

        // Format initial value if exists
        if (phoneInput.value) {
            phoneInput.value = formatPhoneNumber(phoneInput.value);
        }

        // --- JS Logic 2: Leaflet Map Integration ---
        // Default Center (Center of Ciamis / Alun-alun Ciamis)
        const defaultLat = -7.3305;
        const defaultLng = 108.3521;
        
        let map = L.map('registration-map').setView([defaultLat, defaultLng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;

        function setMarkerLocation(lat, lng) {
            document.getElementById('latitude').value = parseFloat(lat).toFixed(8);
            document.getElementById('longitude').value = parseFloat(lng).toFixed(8);

            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                // Red icon marker
                const redIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                marker = L.marker([lat, lng], { draggable: true, icon: redIcon }).addTo(map);
                marker.on('dragend', function(e) {
                    const pos = e.target.getLatLng();
                    document.getElementById('latitude').value = pos.lat.toFixed(8);
                    document.getElementById('longitude').value = pos.lng.toFixed(8);
                });
            }
            map.setView([lat, lng], 16);
        }

        // Map Click to select location
        map.on('click', function(e) {
            const { lat, lng } = e.latlng;
            setMarkerLocation(lat, lng);
        });

        // --- JS Logic 3: Handle Dropdown DUDI Selection ---
        const dudiSelect = document.getElementById('dudi_id');
        const alamatBox = document.getElementById('dudi-alamat-box');
        const alamatText = document.getElementById('dudi-alamat-text');

        dudiSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const latVal = selectedOption.getAttribute('data-lat');
            const lngVal = selectedOption.getAttribute('data-lng');
            const alamat = selectedOption.getAttribute('data-alamat');

            // Show address
            if (alamat) {
                alamatText.innerText = alamat;
                alamatBox.classList.remove('hidden');
            } else {
                alamatBox.classList.add('hidden');
            }

            // Set marker location on map
            if (latVal && lngVal && latVal !== '' && lngVal !== '') {
                setMarkerLocation(parseFloat(latVal), parseFloat(lngVal));
            } else {
                // If company has no coordinates, center map on default and ask them to click
                map.setView([defaultLat, defaultLng], 12);
                if (marker) {
                    map.removeLayer(marker);
                    marker = null;
                }
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
            }
        });

        // Initialize from old inputs if returning from validation error
        const oldDudiId = "{{ old('dudi_id') }}";
        const oldLat = "{{ old('latitude') }}";
        const oldLng = "{{ old('longitude') }}";

        if (oldDudiId) {
            dudiSelect.value = oldDudiId;
            dudiSelect.dispatchEvent(new Event('change'));
        }
        if (oldLat && oldLng) {
            setMarkerLocation(parseFloat(oldLat), parseFloat(oldLng));
        }

        // --- JS Logic 4: Search Map Location (Nominatim Geocoding) ---
        const mapSearchInput = document.getElementById('map_search');
        const btnSearchMap = document.getElementById('btn_search_map');
        const searchResultsContainer = document.getElementById('search_results_container');

        async function performMapSearch() {
            const query = mapSearchInput.value.trim();
            if (query.length < 3) return;

            // Check if query is coordinates: lat, lng (e.g. -7.3312, 108.2834)
            const coordRegex = /^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/;
            const match = query.match(coordRegex);
            if (match) {
                const lat = parseFloat(match[1]);
                const lon = parseFloat(match[2]);
                if (lat >= -90 && lat <= 90 && lon >= -180 && lon <= 180) {
                    setMarkerLocation(lat, lon);
                    searchResultsContainer.classList.add('hidden');
                    return;
                }
            }

            btnSearchMap.disabled = true;
            btnSearchMap.innerText = 'Mencari...';

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=id`);
                const data = await response.json();

                searchResultsContainer.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer border-b border-slate-100/50 dark:border-slate-800 last:border-b-0 text-slate-700 dark:text-slate-300 transition-colors duration-150';
                        div.innerText = item.display_name;
                        div.addEventListener('click', () => {
                            const lat = parseFloat(item.lat);
                            const lon = parseFloat(item.lon);
                            setMarkerLocation(lat, lon);
                            searchResultsContainer.classList.add('hidden');
                            mapSearchInput.value = item.display_name;
                        });
                        searchResultsContainer.appendChild(div);
                    });
                    searchResultsContainer.classList.remove('hidden');
                } else {
                    searchResultsContainer.innerHTML = '<div class="px-3 py-3 text-center text-slate-500 font-medium">Lokasi tidak ditemukan.</div>';
                    searchResultsContainer.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error fetching geocoding:', error);
            } finally {
                btnSearchMap.disabled = false;
                btnSearchMap.innerText = 'Cari';
            }
        }

        btnSearchMap.addEventListener('click', performMapSearch);
        mapSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performMapSearch();
            }
        });

        // Auto search as user types (with 500ms debounce)
        let searchTimeout = null;
        mapSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            if (query.length < 3) {
                searchResultsContainer.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                performMapSearch();
            }, 500);
        });

        // Hide search results click away
        document.addEventListener('click', function(e) {
            if (!mapSearchInput.contains(e.target) && !searchResultsContainer.contains(e.target) && !btnSearchMap.contains(e.target)) {
                searchResultsContainer.classList.add('hidden');
            }
        });

        // --- JS Logic 5: Geolocation (GPS) ---
        const btnDetectGps = document.getElementById('btn-detect-gps');

        btnDetectGps.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung Geolocation.');
                return;
            }

            const originalText = btnDetectGps.innerHTML;
            btnDetectGps.disabled = true;
            btnDetectGps.innerHTML = `
                <svg class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mendeteksi GPS...
            `;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    setMarkerLocation(lat, lng);
                    
                    btnDetectGps.disabled = false;
                    btnDetectGps.innerHTML = originalText;
                },
                function(error) {
                    let msg = 'Gagal mendapatkan lokasi GPS.';
                    if (error.code === 1) msg = 'Izin akses lokasi (GPS) ditolak. Aktifkan GPS dan izinkan akses lokasi pada browser Anda.';
                    else if (error.code === 2) msg = 'Koneksi GPS tidak tersedia atau tidak akurat.';
                    else if (error.code === 3) msg = 'Waktu deteksi lokasi habis (timeout).';
                    
                    alert(msg);
                    btnDetectGps.disabled = false;
                    btnDetectGps.innerHTML = originalText;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 0
                }
            );
        });

        // Run lucide icons replacement
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
    </script>
</x-guest-layout>
