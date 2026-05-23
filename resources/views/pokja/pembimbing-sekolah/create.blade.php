<x-app-layout>
    <x-slot name="header">Tambah Pembimbing Sekolah</x-slot>

    <div class="mb-6">
        <a href="{{ route('pokja.pembimbing_sekolah.index') }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="max-w-4xl text-slate-700 dark:text-slate-300">
        <form action="{{ route('pokja.pembimbing_sekolah.store') }}" method="POST" class="space-y-6">
            @csrf
            
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
                            <input type="text" name="username" id="username" value="{{ old('username') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all font-mono">
                            @error('username') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password</label>
                            <input type="password" name="password" id="password" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Data Profil -->
                <div class="glass-card p-6 md:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <i data-lucide="user-check" class="w-5 h-5 text-emerald-400"></i>
                        Profil Pengajar
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap (Gelar)</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                        <div>
                            <label for="nip" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">NIP (Opsional)</label>
                            <input type="text" name="nip" id="nip" value="{{ old('nip') }}"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all font-mono">
                        </div>
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}"
                                   class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Penugasan -->
                <div class="glass-card p-6 md:col-span-2">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-5 h-5 text-purple-400"></i>
                        Penugasan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="konsentrasi_keahlian_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tugas di Konsentrasi Keahlian</label>
                            <select name="konsentrasi_keahlian_id" id="konsentrasi_keahlian_id" required
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                <option value="" disabled selected>Pilih Konsentrasi Keahlian</option>
                                @foreach($concentrations as $item)
                                    <option value="{{ $item->id }}" {{ old('konsentrasi_keahlian_id') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tipe" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tipe Pembimbing</label>
                            <select name="tipe" id="tipe" required onchange="toggleAdaptifFields()"
                                    class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all">
                                <option value="produktif" {{ old('tipe') == 'produktif' ? 'selected' : '' }}>Guru Produktif</option>
                                <option value="normatif" {{ old('tipe') == 'normatif' ? 'selected' : '' }}>Guru Normatif</option>
                                <option value="adaptif" {{ old('tipe') == 'adaptif' ? 'selected' : '' }}>Guru Adaptif (Umum)</option>
                            </select>
                        </div>

                        <!-- Adaptif/Normatif specific fields -->
                        <div id="adaptif-fields" class="md:col-span-2 hidden space-y-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <div>
                                <label for="mapel_cp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mata Pelajaran (Mapel)</label>
                                <input type="text" name="mapel_cp" id="mapel_cp" value="{{ old('mapel_cp') }}"
                                       class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-all"
                                       placeholder="Contoh: Matematika, Bahasa Inggris, dll">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kelas yang Diajar</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200/50 dark:border-slate-700/50 max-h-48 overflow-y-auto">
                                    @foreach($existingClasses as $kelas)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="kelas[]" value="{{ $kelas }}" class="rounded text-blue-600 focus:ring-blue-500" {{ is_array(old('kelas')) && in_array($kelas, old('kelas')) ? 'checked' : '' }}>
                                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $kelas }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                    <p class="mt-4 text-xs text-slate-500 dark:text-slate-400 italic">Pembimbing akan mengelola siswa dari konsentrasi keahlian dan kategori yang dipilih.</p>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Pembimbing
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleAdaptifFields() {
            const tipe = document.getElementById('tipe').value;
            const adaptifFields = document.getElementById('adaptif-fields');
            if(tipe === 'normatif' || tipe === 'adaptif') {
                adaptifFields.classList.remove('hidden');
            } else {
                adaptifFields.classList.add('hidden');
            }
        }
        document.addEventListener('DOMContentLoaded', toggleAdaptifFields);
    </script>
</x-app-layout>
