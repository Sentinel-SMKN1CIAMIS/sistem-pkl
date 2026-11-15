<x-app-layout>
    <x-slot name="header">Mode Pemeliharaan (Maintenance)</x-slot>

    <div class="max-w-5xl space-y-8 text-slate-700 dark:text-slate-300">
        <!-- Header Info -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2.5">
                    <i data-lucide="shield-alert" class="w-6 h-6 text-amber-500"></i>
                    Kontrol Mode Pemeliharaan Sistem
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Atur status pemeliharaan aplikasi langsung dari panel Super Admin tanpa perlu menjalankan perintah terminal di server.
                </p>
            </div>
            
            <a href="{{ route('maintenance') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-all self-start sm:self-center shadow-xs">
                <i data-lucide="external-link" class="w-4 h-4 text-slate-400"></i>
                Pratinjau Layar Pemeliharaan
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 dark:text-emerald-400 text-xs font-semibold flex items-center gap-3 shadow-xs">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-semibold space-y-1 shadow-xs">
                <div class="flex items-center gap-2 font-bold">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span>Terjadi kesalahan input:</span>
                </div>
                <ul class="list-disc pl-6 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Status Live Banner -->
        @if($summary['is_active'])
            <div class="p-6 rounded-2xl bg-gradient-to-r from-amber-500/15 via-red-500/10 to-amber-500/5 border border-amber-500/30 shadow-lg shadow-amber-500/5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="p-3.5 rounded-2xl bg-amber-500 text-white shadow-md shadow-amber-500/25 shrink-0">
                        <i data-lucide="wrench" class="w-7 h-7 animate-spin-slow"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500 text-white">
                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                Mode Maintenance Aktif
                            </span>
                            @if(!empty($summary['activated_at']))
                                <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Sejak: {{ \Carbon\Carbon::parse($summary['activated_at'])->translatedFormat('d F Y, H:i') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-700 dark:text-slate-300 mt-2 font-medium">
                            Akses pengguna dibatasi. Hanya <strong>Super Administrator</strong> dan peran terpilih yang dapat login dan menggunakan sistem saat ini.
                        </p>
                        @if(!empty($summary['activated_by']))
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                Diaktifkan oleh: <span class="font-bold text-slate-600 dark:text-slate-300">{{ $summary['activated_by'] }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="p-6 rounded-2xl bg-gradient-to-r from-emerald-500/15 via-teal-500/10 to-emerald-500/5 border border-emerald-500/30 shadow-lg shadow-emerald-500/5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="p-3.5 rounded-2xl bg-emerald-500 text-white shadow-md shadow-emerald-500/25 shrink-0">
                        <i data-lucide="check-check" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500 text-white">
                            <span class="w-2 h-2 rounded-full bg-white"></span>
                            Sistem Berjalan Normal (Live)
                        </span>
                        <p class="text-xs text-slate-700 dark:text-slate-300 mt-2 font-medium">
                            Seluruh pengguna (Siswa, Pembimbing, Pokja, Kaprog, dan Kepala Sekolah) dapat masuk dan menggunakan seluruh fitur sistem tanpa batasan.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulir Pengaturan -->
        <form action="{{ route('admin.maintenance.update') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Sakelar Status Utama -->
            <div class="glass-card p-6 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-blue-500/10 text-blue-500">
                        <i data-lucide="power" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Status Operasional Aplikasi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pilih mode operasional yang ingin diterapkan pada server sistem.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <!-- Opsi Normal -->
                    <label class="relative flex items-start gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-slate-800/40 {{ !$summary['is_active'] ? 'border-emerald-500 bg-emerald-500/5 dark:bg-emerald-500/10' : 'border-slate-200 dark:border-slate-800' }}">
                        <input type="radio" name="is_active" value="0" {{ !$summary['is_active'] ? 'checked' : '' }} class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 block">Sistem Normal (Semua Role Terbuka)</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block leading-relaxed">
                                Operasional reguler berjalan. Seluruh siswa, guru, pembimbing, dan staf dapat mengakses akun masing-masing.
                            </span>
                        </div>
                    </label>

                    <!-- Opsi Maintenance -->
                    <label class="relative flex items-start gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-slate-800/40 {{ $summary['is_active'] ? 'border-amber-500 bg-amber-500/5 dark:bg-amber-500/10' : 'border-slate-200 dark:border-slate-800' }}">
                        <input type="radio" name="is_active" value="1" {{ $summary['is_active'] ? 'checked' : '' }} class="mt-0.5 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 block">Mode Pemeliharaan (Maintenance Mode)</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block leading-relaxed">
                                Batasi akses publik. Hanya peran yang dicentang di bawah yang tetap diizinkan menggunakan sistem.
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Pemilihan Hak Akses Peran Saat Maintenance (Checkboxes) -->
            <div class="glass-card p-6 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-purple-500/10 text-purple-500">
                            <i data-lucide="user-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Peran yang Tetap Diberi Izin Akses (Bypass Mode)</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Centang peran yang masih boleh login dan memakai sistem saat pemeliharaan aktif.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-2">
                    @foreach($allRoles as $roleKey => $roleData)
                        @php
                            $isSuperAdmin = $roleKey === 'super_admin';
                            $isChecked = $isSuperAdmin || in_array($roleKey, $summary['allowed_roles'], true);
                        @endphp
                        <label class="relative flex items-start gap-3 p-3.5 rounded-xl border transition-all {{ $isSuperAdmin ? 'bg-slate-100/60 dark:bg-slate-900/40 border-slate-300/80 dark:border-slate-700/80 cursor-default' : 'hover:bg-slate-50 dark:hover:bg-slate-800/40 border-slate-200/80 dark:border-slate-800 cursor-pointer' }}">
                            @if($isSuperAdmin)
                                <input type="hidden" name="allowed_roles[]" value="super_admin">
                                <input type="checkbox" checked disabled class="mt-0.5 rounded text-blue-600 opacity-60">
                            @else
                                <input type="checkbox" name="allowed_roles[]" value="{{ $roleKey }}" {{ $isChecked ? 'checked' : '' }} class="mt-0.5 rounded text-blue-600 focus:ring-blue-500 dark:bg-slate-900 border-slate-300 dark:border-slate-700">
                            @endif

                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ $roleData['label'] }}</span>
                                    @if($isSuperAdmin)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-blue-500/10 text-blue-500 border border-blue-500/20 uppercase">Wajib Aktif</span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-normal">{{ $roleData['desc'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                
                <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-xs flex items-center gap-2.5">
                    <i data-lucide="info" class="w-4 h-4 shrink-0"></i>
                    <span><strong>Contoh Praktis:</strong> Jika Anda perlu memperbaiki data bersama tim Pokja, cukup centang <strong>Pokja PKL</strong>. Tim Pokja dapat tetap login tanpa mengganggu siswa atau pembimbing lain.</span>
                </div>
            </div>

            <!-- Pesan Pemeliharaan Kustom -->
            <div class="glass-card p-6 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-amber-500/10 text-amber-500">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Pesan Pengumuman untuk Pengguna</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pesan ini akan ditampilkan kepada pengunjung atau pengguna yang ditolak saat mengakses halaman pemeliharaan.</p>
                    </div>
                </div>

                <div class="pt-2">
                    <textarea name="message" rows="3" class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs text-slate-800 dark:text-slate-200 transition-all leading-relaxed" placeholder="Tuliskan pesan penjelasan pemeliharaan...">{{ old('message', $summary['message']) }}</textarea>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Kosongkan untuk menggunakan pesan standar aplikasi.</p>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all text-xs flex items-center gap-2 cursor-pointer transform hover:scale-[1.01] active:scale-[0.99]">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan & Terapkan Pengaturan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

