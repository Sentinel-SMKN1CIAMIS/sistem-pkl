<x-app-layout>
    <x-slot name="header">Profil Saya</x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-slate-700 dark:text-slate-300">
        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-8 text-center">
                <div class="relative inline-flex mb-6">
                    <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white dark:border-slate-800 shadow-xl bg-slate-100">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($pembimbing->nama_lengkap) }}&background=f59e0b&color=fff&size=200" 
                             alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full flex items-center justify-center shadow-lg transform translate-x-1/4 translate-y-1/4">
                        <i data-lucide="check" class="w-4 h-4 text-white"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ $pembimbing->nama_lengkap }}</h3>
                <p class="text-xs text-amber-500 font-bold uppercase tracking-wider mt-1">Pembimbing DUDI / Industri</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">{{ $pembimbing->jabatan ?: 'Jabatan: -' }}</p>
                
                <div class="mt-6 pt-6 border-t border-slate-200/50 dark:border-slate-700/50 flex flex-col gap-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Perusahaan / DUDI</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300 text-right">{{ $pembimbing->dudi->nama ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Kota Perusahaan</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $pembimbing->dudi->kota ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="glass-card">
                <div class="p-6 border-b border-slate-200/50 dark:border-slate-700/50">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="user-cog" class="w-5 h-5 text-blue-400"></i>
                        Pengaturan Profil
                    </h3>
                </div>
                
                <form action="{{ route('pembimbing_dudi.profile.update') }}" method="POST" class="p-8 space-y-8">
                    @csrf
                    @method('PATCH')

                    <!-- Data Personal -->
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-blue-400"></i>
                            Informasi Pribadi & Kontak
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama_lengkap" class="block text-xs font-bold text-slate-500/80 dark:text-slate-400 uppercase mb-2">Nama Lengkap</label>
                                <div class="relative">
                                    <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $pembimbing->nama_lengkap) }}" required
                                           class="w-full pl-10 pr-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="jabatan" class="block text-xs font-bold text-slate-500/80 dark:text-slate-400 uppercase mb-2">Jabatan</label>
                                <div class="relative">
                                    <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan', $pembimbing->jabatan) }}"
                                           placeholder="Contoh: Supervisor, Mentor, HRD"
                                           class="w-full pl-10 pr-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="briefcase" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-500/80 dark:text-slate-400 uppercase mb-2">Email</label>
                                <div class="relative">
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                           class="w-full pl-10 pr-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="no_hp" class="block text-xs font-bold text-slate-500/80 dark:text-slate-400 uppercase mb-2">Nomor HP / WhatsApp</label>
                                <div class="relative">
                                    <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $pembimbing->no_hp) }}"
                                           placeholder="Contoh: 081234567890"
                                           class="w-full pl-10 pr-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="username" class="block text-xs font-bold text-slate-500/80 dark:text-slate-400 uppercase mb-2">Username</label>
                                <div class="relative">
                                    <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required
                                           class="w-full pl-10 pr-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="user-check" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ganti Password -->
                    <div class="pt-6 border-t border-slate-200/50 dark:border-slate-700/50">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-2 flex items-center gap-2">
                            <i data-lucide="lock" class="w-4 h-4 text-amber-400"></i>
                            Ubah Password (Opsional)
                        </h4>
                        <p class="text-xs text-slate-500 mb-6">Kosongkan jika tidak ingin mengubah password akun Anda.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-500/80 dark:text-slate-400 uppercase mb-2">Password Baru</label>
                                <input type="password" name="password" id="password"
                                       class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-500/80 dark:text-slate-400 uppercase mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                            <i data-lucide="save" class="w-5 h-5"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
