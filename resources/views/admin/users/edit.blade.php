<x-app-layout>
    <x-slot name="header">Edit Pengguna</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-2xl text-slate-700 dark:text-slate-300">
        <div class="glass-card p-8">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all font-mono italic">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Role / Hak Akses</label>
                        <select name="role" id="role" required
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            @foreach(['siswa', 'pembimbing_sekolah', 'pembimbing_dudi', 'pokja', 'super_admin'] as $role)
                                <option value="{{ $role }}" {{ $user->role == $role ? 'selected' : '' }}>{{ str_replace('_', ' ', strtoupper($role)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4 border-t border-slate-200/50 dark:border-slate-700/50">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Kosongkan password jika tidak ingin mengubah.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password Baru</label>
                                <input type="password" name="password" id="password"
                                       class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all text-sm uppercase tracking-widest">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
