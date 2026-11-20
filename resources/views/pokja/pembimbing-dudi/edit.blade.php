<x-app-layout>
    <x-slot name="header">Edit Pembimbing DUDI</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.pembimbing_dudi.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 dark:text-red-400 text-sm flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('pokja.pembimbing_dudi.update', $pembimbing_dudi) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Akun (Limited Edit) -->
                <div class="glass-card p-6 md:col-span-1 border border-blue-500/10">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="key" class="w-5 h-5 text-blue-400"></i>
                        Akses Login
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">Username</label>
                            <input type="text" value="{{ $pembimbing_dudi->user->username }}" disabled
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/30 border border-slate-800 rounded-xl text-slate-500 dark:text-slate-400 font-mono italic">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">Email</label>
                            <input type="text" value="{{ $pembimbing_dudi->user->email }}" disabled
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/30 border border-slate-800 rounded-xl text-slate-500 dark:text-slate-400 italic">
                        </div>
                    </div>
                </div>

                <!-- Profil Mentor -->
                <div class="glass-card p-6 md:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="user-check" class="w-5 h-5 text-purple-400"></i>
                         Profil Mentor
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $pembimbing_dudi->nama_lengkap) }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200">
                        </div>
                        <div>
                            <label for="jabatan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jabatan</label>
                            <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan', $pembimbing_dudi->jabatan) }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200">
                        </div>
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $pembimbing_dudi->no_hp) }}"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200">
                        </div>
                    </div>
                </div>

                <!-- Perusahaan -->
                <div class="glass-card p-6 md:col-span-2">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                        <i data-lucide="building-2" class="w-5 h-5 text-amber-400"></i>
                        Afiliasi Perusahaan (DUDI)
                    </h3>
                    <div>
                        <label for="dudi_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pilih Industri</label>
                        <select name="dudi_id" id="dudi_id" required
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            @foreach($dudis as $item)
                                <option value="{{ $item->id }}" {{ (old('dudi_id', $pembimbing_dudi->dudi_id) == $item->id) ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Update Data Pembimbing
                </button>
            </div>
        </form>

        @if(auth()->user()->role === 'super_admin' && $pembimbing_dudi->user)
            <div class="glass-card p-6 border border-amber-500/20 mt-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2 flex items-center gap-2">
                    <i data-lucide="key-round" class="w-5 h-5 text-amber-500"></i>
                    Ganti Kata Sandi Akun (Super Admin)
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">
                    Ubah kata sandi login untuk akun <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $pembimbing_dudi->user->username }}</span> secara langsung.
                </p>

                <form action="{{ route('admin.pembimbing_dudi.password.update', $pembimbing_dudi) }}" method="POST" x-data="{ showNewPassword: false, showConfirmPassword: false }" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kata Sandi Baru (minimal 8 karakter)</label>
                            <div class="relative">
                                <input :type="showNewPassword ? 'text' : 'password'" name="password" id="new_password" required minlength="8" autocomplete="new-password"
                                       placeholder="Masukkan kata sandi baru..."
                                       class="w-full px-4 py-2.5 pr-16 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-slate-800 dark:text-slate-200">
                                <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-semibold">
                                    <span x-text="showNewPassword ? 'Sembunyi' : 'Lihat'"></span>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Kata Sandi Baru</label>
                            <div class="relative">
                                <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" id="new_password_confirmation" required minlength="8" autocomplete="new-password"
                                       placeholder="Ulangi kata sandi baru..."
                                       class="w-full px-4 py-2.5 pr-16 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-slate-800 dark:text-slate-200">
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-semibold">
                                    <span x-text="showConfirmPassword ? 'Sembunyi' : 'Lihat'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <label class="flex items-center gap-2.5 pt-1 cursor-pointer">
                        <input type="checkbox" name="force_password_change" value="1"
                               class="rounded border-slate-300 dark:border-slate-700 text-amber-600 focus:ring-amber-500 dark:bg-slate-900 w-4 h-4">
                        <span class="text-xs text-slate-600 dark:text-slate-400">Wajibkan ganti sandi saat login berikutnya</span>
                    </label>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-lg shadow-amber-500/25 transition-all flex items-center gap-2 cursor-pointer">
                            <i data-lucide="key-round" class="w-4 h-4"></i>
                            Simpan Sandi Baru
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
