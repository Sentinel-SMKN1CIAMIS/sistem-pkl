<x-app-layout>
    <x-slot name="header">Tambah Data Siswa PKL</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.siswa.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:text-slate-200 transition-colors inline-flex">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-5xl text-slate-700 dark:text-slate-300">
        <form action="{{ route('pokja.siswa.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informasi Akun -->
                <div class="glass-card p-6 md:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="key" class="w-5 h-5 text-blue-400"></i>
                        Informasi Akun
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="nis" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">NIS (Username)</label>
                            <input type="text" name="nis" id="nis" value="{{ old('nis') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all font-mono">
                            @error('nis') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password Login</label>
                            <input type="password" name="password" id="password" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Profil -->
                <div class="glass-card p-6 md:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-emerald-400"></i>
                        Profil Siswa
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            @error('nama_lengkap') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="kelas" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kelas</label>
                                <input type="text" name="kelas" id="kelas" value="{{ old('kelas') }}" required
                                       class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all" placeholder="Contoh: XII RPL 1">
                            </div>
                            <div>
                                <label for="jenis_kelamin" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="jenis_kelamin" required
                                        class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="tahun_ajaran" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" id="tahun_ajaran" value="{{ old('tahun_ajaran', '2025/2026') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Penempatan & Pembimbing -->
                <div class="glass-card p-6 md:col-span-2">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-purple-400"></i>
                        Penempatan PKL & Pembimbing
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="konsentrasi_keahlian_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jurusan</label>
                            <select name="konsentrasi_keahlian_id" id="konsentrasi_keahlian_id" required
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                <option value="" disabled selected>Pilih Jurusan</option>
                                @foreach($concentrations as $item)
                                    <option value="{{ $item->id }}" {{ old('konsentrasi_keahlian_id') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="dudi_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">DUDI / Industri</label>
                            <select name="dudi_id" id="dudi_id"
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                <option value="">Belum Ditentukan</option>
                                @foreach($dudis as $item)
                                    <option value="{{ $item->id }}" {{ old('dudi_id') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="pembimbing_sekolah_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pembimbing Sekolah</label>
                            <select name="pembimbing_sekolah_id" id="pembimbing_sekolah_id"
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                <option value="">Belum Ditentukan</option>
                                @foreach($pembimbingSekolah as $item)
                                    <option value="{{ $item->id }}" {{ old('pembimbing_sekolah_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="pembimbing_dudi_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pembimbing DUDI</label>
                            <select name="pembimbing_dudi_id" id="pembimbing_dudi_id"
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                <option value="">Belum Ditentukan</option>
                                @foreach($pembimbingDudi as $item)
                                    <option value="{{ $item->id }}" {{ old('pembimbing_dudi_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Seluruh Data
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
