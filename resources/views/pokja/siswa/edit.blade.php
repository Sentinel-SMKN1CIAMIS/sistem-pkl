<x-app-layout>
    <x-slot name="header">Edit Data & Penempatan Siswa PKL</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.siswa.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-5xl text-slate-700 dark:text-slate-300">
        <form action="{{ route('pokja.siswa.update', $siswa->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informasi Akun & Profil -->
                <div class="glass-card p-6 md:col-span-1 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                            <i data-lucide="user" class="w-5 h-5 text-emerald-400"></i>
                            Profil Siswa
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label for="nis" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">NIS (Username)</label>
                                <input type="text" name="nis" id="nis" value="{{ old('nis', $siswa->nis) }}" required
                                       class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all font-mono">
                                @error('nis') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="nama_lengkap" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required
                                       class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all font-sans">
                                @error('nama_lengkap') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="kelas" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kelas</label>
                                    <input type="text" name="kelas" id="kelas" value="{{ old('kelas', $siswa->kelas) }}" required
                                           class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                    @error('kelas') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="jenis_kelamin" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" required
                                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                        <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="tahun_ajaran" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tahun Ajaran</label>
                                <input type="text" name="tahun_ajaran" id="tahun_ajaran" value="{{ old('tahun_ajaran', $siswa->tahun_ajaran) }}" required
                                       class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                            </div>
                            <div>
                                <label for="konsentrasi_keahlian_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konsentrasi Keahlian</label>
                                <select name="konsentrasi_keahlian_id" id="konsentrasi_keahlian_id" required
                                        class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                    @foreach($concentrations as $item)
                                        <option value="{{ $item->id }}" {{ old('konsentrasi_keahlian_id', $siswa->konsentrasi_keahlian_id) == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Penempatan PKL & Pembimbing -->
                <div class="glass-card p-6 md:col-span-1 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-5 h-5 text-blue-400"></i>
                            Penempatan & Pembimbing
                        </h3>
                        <div class="space-y-4">
                            @if($siswa->pengajuanPkl && $siswa->pengajuanPkl->bukti_balasan)
                                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs flex items-center justify-between gap-3">
                                    <div>
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400 block mb-1">Bukti Balasan Perusahaan</span>
                                        <p class="text-slate-600 dark:text-slate-400 leading-normal">Siswa telah mengunggah bukti penerimaan resmi dari perusahaan.</p>
                                    </div>
                                    <a href="{{ asset('storage/' . $siswa->pengajuanPkl->bukti_balasan) }}" target="_blank"
                                       class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Lihat Bukti
                                    </a>
                                </div>
                            @endif

                            <div>
                                <label for="dudi_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tempat PKL (DUDI)</label>
                                <select name="dudi_id" id="dudi_id"
                                        class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                    <option value="">-- Belum Ditempatkan / Mandiri --</option>
                                    @foreach($dudis as $item)
                                        <option value="{{ $item->id }}" {{ old('dudi_id', $siswa->dudi_id) == $item->id ? 'selected' : '' }}>{{ $item->nama }} ({{ $item->kota }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="pembimbing_sekolah_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Guru Pembimbing Sekolah</label>
                                <select name="pembimbing_sekolah_id" id="pembimbing_sekolah_id"
                                        class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                    <option value="">-- Pilih Guru Pembimbing --</option>
                                    @foreach($pembimbingSekolah as $item)
                                        <option value="{{ $item->id }}" {{ old('pembimbing_sekolah_id', $siswa->pembimbing_sekolah_id) == $item->id ? 'selected' : '' }}>{{ $item->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="pembimbing_dudi_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pembimbing Lapangan (DUDI)</label>
                                <select name="pembimbing_dudi_id" id="pembimbing_dudi_id"
                                        class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                    <option value="">-- Pilih Pembimbing DUDI --</option>
                                    @foreach($pembimbingDudi as $item)
                                        <option value="{{ $item->id }}" {{ old('pembimbing_dudi_id', $siswa->pembimbing_dudi_id) == $item->id ? 'selected' : '' }}>{{ $item->nama_lengkap }} ({{ $item->dudi->nama ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="status_pkl" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status PKL</label>
                                <select name="status_pkl" id="status_pkl" required
                                        class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                    <option value="belum_mulai" {{ old('status_pkl', $siswa->status_pkl) == 'belum_mulai' ? 'selected' : '' }}>Belum Mulai</option>
                                    <option value="sedang_pkl" {{ old('status_pkl', $siswa->status_pkl) == 'sedang_pkl' ? 'selected' : '' }}>Sedang PKL</option>
                                    <option value="selesai" {{ old('status_pkl', $siswa->status_pkl) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="dibatalkan" {{ old('status_pkl', $siswa->status_pkl) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
