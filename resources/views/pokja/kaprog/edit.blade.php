<x-app-layout>
    <x-slot name="header">Edit Akun Kaprog</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.kaprog.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <form action="{{ route('pokja.kaprog.update', $kaprog) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Akun -->
                <div class="glass-card p-6 md:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="key" class="w-5 h-5 text-blue-400"></i>
                        Akses Login
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Username</label>
                            <input type="text" name="username" id="username" value="{{ old('username', $kaprog->username) }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all font-mono">
                            @error('username') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $kaprog->email) }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password Baru (Opsional)</label>
                            <input type="password" name="password" id="password"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                   placeholder="Kosongkan jika tidak ingin diubah">
                            @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Data Profil & Penugasan -->
                <div class="glass-card p-6 md:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="user-check" class="w-5 h-5 text-emerald-400"></i>
                        Profil & Penugasan
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap (Gelar)</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $kaprog->name) }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="program_keahlian_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Program Keahlian</label>
                            <select name="program_keahlian_id" id="program_keahlian_id" required
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                <option value="" disabled>Pilih Program Keahlian</option>
                                @foreach($programs as $item)
                                    <option value="{{ $item->id }}" {{ old('program_keahlian_id', $kaprog->program_keahlian_id) == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            @error('program_keahlian_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Perbarui Akun Kaprog
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
