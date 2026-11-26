<x-app-layout>
    <x-slot name="header">Tambah Pengguna Baru</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-2xl text-slate-700 dark:text-slate-300">
        <div class="glass-card p-8 shadow-2xl shadow-blue-500/10">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required
                               class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all placeholder-slate-600"
                               placeholder="Contoh: pkl_admin_01">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Role / Hak Akses</label>
                        <select name="role" id="role" required onchange="togglePokjaCheckboxCreate()"
                                class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all group">
                            <option value="siswa">SISWA</option>
                            <option value="pembimbing_sekolah">PEMBIMBING SEKOLAH</option>
                            <option value="pembimbing_dudi">PEMBIMBING DUDI</option>
                            <option value="pokja">POKJA</option>
                            <option value="kaprog">KAPROG</option>
                            <option value="super_admin" @if(\App\Models\User::where('role', 'super_admin')->exists()) disabled title="Role Super Admin sudah dibuat (Maksimal 1 akun)" @endif>SUPER ADMIN</option>
                            <option value="kepala_sekolah" @if(\App\Models\User::where('role', 'kepala_sekolah')->exists()) disabled title="Role Kepala Sekolah sudah dibuat (Maksimal 1 akun)" @endif>KEPALA SEKOLAH</option>
                        </select>

                        <div id="is-pokja-wrapper-create" class="mt-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/60 hidden">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="is_pokja" value="1" {{ old('is_pokja') ? 'checked' : '' }}
                                       class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="block text-sm font-bold text-slate-800 dark:text-slate-100">Akses Tambahan: Sekaligus Anggota Pokja</span>
                                    <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Berikan akses ke Shared Workspace Pokja tanpa membuat akun terpisah.
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password</label>
                            <input type="password" name="password" id="password" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <x-button icon="user-plus" class="px-8 py-3 !text-sm uppercase tracking-widest">
                        Buat Akun Sekarang
                    </x-button>
                </div>
            </form>
        </div>
        
        <div class="mt-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20">
            <p class="text-[11px] text-blue-300 leading-relaxed uppercase font-black tracking-widest mb-1">Catatan Admin</p>
            <p class="text-xs text-slate-600 dark:text-slate-400">Pastikan username unik dan instruksikan pengguna untuk segera mengubah password bawaan demi keamanan.</p>
        </div>
    </div>

    <script>
        function togglePokjaCheckboxCreate() {
            const roleSelect = document.getElementById('role');
            const wrapper = document.getElementById('is-pokja-wrapper-create');
            if (!roleSelect || !wrapper) return;
            wrapper.classList.toggle('hidden', roleSelect.value !== 'pembimbing_sekolah');
        }
        document.addEventListener('DOMContentLoaded', togglePokjaCheckboxCreate);
    </script>
</x-app-layout>
