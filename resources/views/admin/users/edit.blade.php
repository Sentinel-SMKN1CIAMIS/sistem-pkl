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
                        @if($user->role === 'super_admin')
                            <div class="px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl flex items-center justify-between">
                                <span class="font-bold text-red-500 font-mono text-sm">SUPER ADMIN</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 italic">Hak akses mutlak (tidak dapat diubah)</span>
                            </div>
                            <input type="hidden" name="role" value="super_admin">
                        @else
                            <select name="role" id="role" required onchange="togglePokjaCheckbox()"
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                @foreach(['siswa', 'pembimbing_sekolah', 'pembimbing_dudi', 'pokja', 'kaprog', 'super_admin', 'kepala_sekolah'] as $roleVal)
                                    @php
                                        $disabled = ($roleVal === 'super_admin' && \App\Models\User::where('role', 'super_admin')->exists())
                                            || ($roleVal === 'kepala_sekolah' && $user->role !== 'kepala_sekolah' && \App\Models\User::where('role', 'kepala_sekolah')->exists());
                                    @endphp
                                    <option value="{{ $roleVal }}" {{ $user->role == $roleVal ? 'selected' : '' }} @if($disabled) disabled title="Role {{ str_replace('_', ' ', strtoupper($roleVal)) }} sudah dibuat (Maksimal 1 akun)" @endif>{{ str_replace('_', ' ', strtoupper($roleVal)) }}</option>
                                @endforeach
                            </select>

                            <div id="is-pokja-wrapper" class="mt-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/60 {{ old('role', $user->role) === 'pembimbing_sekolah' ? '' : 'hidden' }}">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_pokja" value="1" {{ old('is_pokja', $user->is_pokja) ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <span class="block text-sm font-bold text-slate-800 dark:text-slate-100">Akses Tambahan: Sekaligus Anggota Pokja</span>
                                        <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            Jika dicentang, guru pembimbing ini akan melihat halaman pilih 2 workspace (Workspace Pokja bersama &amp; Pembimbing Sekolah) saat login.
                                        </span>
                                    </div>
                                </label>
                            </div>
                        @endif
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
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all text-sm uppercase tracking-widest">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePokjaCheckbox() {
            const roleSelect = document.getElementById('role');
            const wrapper = document.getElementById('is-pokja-wrapper');
            if (!roleSelect || !wrapper) return;
            wrapper.classList.toggle('hidden', roleSelect.value !== 'pembimbing_sekolah');
        }
    </script>
</x-app-layout>
