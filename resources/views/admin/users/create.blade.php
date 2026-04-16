<x-app-layout>
    <x-slot name="header">Tambah Pengguna Baru</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 text-sm text-slate-400 hover:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-2xl text-slate-300">
        <div class="glass-card p-8 shadow-2xl shadow-blue-500/10">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-300 mb-2">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required
                               class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all placeholder-slate-600"
                               placeholder="Contoh: pkl_admin_01">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-300 mb-2">Role / Hak Akses</label>
                        <select name="role" id="role" required
                                class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all group">
                            <option value="siswa">SISWA</option>
                            <option value="pembimbing_sekolah">PEMBIMBING SEKOLAH</option>
                            <option value="pembimbing_dudi">PEMBIMBING DUDI</option>
                            <option value="pokja">POKJA</option>
                            <option value="super_admin">SUPER ADMIN</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                            <input type="password" name="password" id="password" required
                                   class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-200 transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all text-sm uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        Buat Akun Sekarang
                    </button>
                </div>
            </form>
        </div>
        
        <div class="mt-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20">
            <p class="text-[11px] text-blue-300 leading-relaxed uppercase font-black tracking-widest mb-1">Catatan Admin</p>
            <p class="text-xs text-slate-400">Pastikan username unik dan instruksikan pengguna untuk segera mengubah password bawaan demi keamanan.</p>
        </div>
    </div>
</x-app-layout>
