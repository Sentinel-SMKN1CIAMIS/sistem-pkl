<x-app-layout>
    <x-slot name="header">Profil Saya</x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-8 text-center">
                <div class="relative inline-block mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=3b82f6&color=fff&size=128" 
                         alt="Avatar" class="w-32 h-32 rounded-3xl object-cover border-4 border-white dark:border-slate-800 shadow-xl">
                    <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 border-4 border-white dark:border-slate-900 rounded-full flex items-center justify-center">
                        <i data-lucide="check" class="w-5 h-5 text-white"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ $siswa->nama_lengkap }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ $siswa->nis }}</p>
                
                <div class="mt-6 pt-6 border-t border-slate-200/50 dark:border-slate-700/50 flex flex-col gap-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Kelas</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $siswa->kelas }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Program</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300 text-right">{{ $siswa->konsentrasiKeahlian->nama }}</span>
                    </div>
                </div>
            </div>

            <div class="glass-card p-6">
                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-blue-400"></i>
                    Informasi PKL
                </h4>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">DUDI / Industri</p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $siswa->dudi->nama ?? 'Belum Penempatan' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">Pembimbing Sekolah</p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $siswa->pembimbingSekolah->nama_lengkap ?? 'Belum Ditugaskan' }}</p>
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
                
                <form action="{{ route('siswa.profile.update') }}" method="POST" class="p-8 space-y-8">
                    @csrf
                    @method('PATCH')

                    <!-- Manual DUDI Input Section -->
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                            <i data-lucide="building-2" class="w-4 h-4 text-purple-400"></i>
                            Pembimbing Industri (Manual)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="pembimbing_dudi_nama" class="block text-xs font-bold text-slate-500 uppercase mb-2">Nama Pembimbing Industri</label>
                                <input type="text" name="pembimbing_dudi_nama" id="pembimbing_dudi_nama" 
                                       value="{{ old('pembimbing_dudi_nama', $siswa->pembimbing_dudi_nama) }}"
                                       placeholder="Nama Pembimbing di Industri"
                                       class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            </div>
                            <div>
                                <label for="pembimbing_dudi_jabatan" class="block text-xs font-bold text-slate-500 uppercase mb-2">Jabatan Pembimbing</label>
                                <input type="text" name="pembimbing_dudi_jabatan" id="pembimbing_dudi_jabatan" 
                                       value="{{ old('pembimbing_dudi_jabatan', $siswa->pembimbing_dudi_jabatan) }}"
                                       placeholder="Contoh: HRD / Mentor / Supervisor"
                                       class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            </div>
                        </div>
                        <p class="mt-3 text-[11px] text-slate-500 italic">Isi kolom di atas jika pembimbing industri belum terdaftar di sistem.</p>
                    </div>

                    <div class="pt-6 border-t border-slate-200/50 dark:border-slate-700/50">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                            <i data-lucide="contact" class="w-4 h-4 text-emerald-400"></i>
                            Kontak & Alamat
                        </h4>
                        <div class="space-y-6">
                            <div>
                                <label for="no_hp" class="block text-xs font-bold text-slate-500 uppercase mb-2">Nomor WhatsApp</label>
                                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $siswa->no_hp) }}"
                                       class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            </div>
                            <div>
                                <label for="alamat" class="block text-xs font-bold text-slate-500 uppercase mb-2">Alamat Lengkap</label>
                                <textarea name="alamat" id="alamat" rows="3"
                                          class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">{{ old('alamat', $siswa->alamat) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-slate-900 dark:text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                            <i data-lucide="save" class="w-5 h-5"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
