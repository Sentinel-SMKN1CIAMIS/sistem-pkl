<x-app-layout>
    <x-slot name="header">Pengaturan Format Surat Pengantar PKL</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Info Card for Placeholders -->
        <div class="glass-card p-6 border-l-4 border-blue-500 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-2 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-blue-500"></i> Panduan Penggunaan Placeholder (Variabel Dinamis)
            </h3>
            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-3">
                Anda dapat menggunakan variabel/placeholder berikut di dalam teks isi surat atau nomor surat. Sistem akan menggantinya secara otomatis saat surat dicetak:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 bg-slate-100 dark:bg-slate-800/40 p-4 rounded-2xl border border-slate-200/50 dark:border-slate-700/50 text-xs font-mono text-slate-700 dark:text-slate-300">
                <div>
                    <span class="font-bold text-blue-600 dark:text-blue-400 block">[NAMA_SISWA]</span>
                    <span class="text-slate-500">Nama Lengkap Siswa</span>
                </div>
                <div>
                    <span class="font-bold text-blue-600 dark:text-blue-400 block">[NIS]</span>
                    <span class="text-slate-500">NIS Siswa</span>
                </div>
                <div>
                    <span class="font-bold text-blue-600 dark:text-blue-400 block">[KELAS]</span>
                    <span class="text-slate-500">Kelas Siswa</span>
                </div>
                <div>
                    <span class="font-bold text-blue-600 dark:text-blue-400 block">[JURUSAN]</span>
                    <span class="text-slate-500">Jurusan/Konsentrasi</span>
                </div>
                <div>
                    <span class="font-bold text-blue-600 dark:text-blue-400 block">[NAMA_PERUSAHAAN]</span>
                    <span class="text-slate-500">Nama Perusahaan/DUDI</span>
                </div>
                <div>
                    <span class="font-bold text-blue-600 dark:text-blue-400 block">[TAHUN_AJARAN]</span>
                    <span class="text-slate-500">Tahun Ajaran Aktif</span>
                </div>
                <div>
                    <span class="font-bold text-blue-600 dark:text-blue-400 block">[TAHUN_SEKARANG]</span>
                    <span class="text-slate-500">Tahun Saat Ini (2026)</span>
                </div>
                <div>
                    <span class="font-bold text-blue-600 dark:text-blue-400 block">[TANGGAL_SEKARANG]</span>
                    <span class="text-slate-500">Tanggal Hari Ini</span>
                </div>
            </div>
        </div>

        <form action="{{ route('pokja.pengaturan.surat_pengantar.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Kop Surat -->
            <div class="glass-card p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 border-b border-slate-200/60 dark:border-slate-800 pb-3">
                    <i data-lucide="building" class="w-5 h-5 text-indigo-500"></i> Kepala / Kop Surat
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="surat_kop_baris_1" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kop Baris 1 (Instansi Induk)</label>
                        <input type="text" name="surat_kop_baris_1" id="surat_kop_baris_1" value="{{ old('surat_kop_baris_1', $surat_kop_baris_1) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    </div>
                    <div>
                        <label for="surat_kop_baris_2" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kop Baris 2 (Dinas Terkait)</label>
                        <input type="text" name="surat_kop_baris_2" id="surat_kop_baris_2" value="{{ old('surat_kop_baris_2', $surat_kop_baris_2) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    </div>
                    <div>
                        <label for="surat_kop_baris_3" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kop Baris 3 (Cabang Dinas/Wilayah)</label>
                        <input type="text" name="surat_kop_baris_3" id="surat_kop_baris_3" value="{{ old('surat_kop_baris_3', $surat_kop_baris_3) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    </div>
                    <div>
                        <label for="surat_kop_baris_4" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kop Baris 4 (Nama Sekolah)</label>
                        <input type="text" name="surat_kop_baris_4" id="surat_kop_baris_4" value="{{ old('surat_kop_baris_4', $surat_kop_baris_4) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-bold">
                    </div>
                    <div class="md:col-span-2">
                        <label for="surat_kop_baris_5" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kop Baris 5 (Alamat Jalan & Telepon)</label>
                        <input type="text" name="surat_kop_baris_5" id="surat_kop_baris_5" value="{{ old('surat_kop_baris_5', $surat_kop_baris_5) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label for="surat_kop_baris_6" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kop Baris 6 (Website, Faks, & Email)</label>
                        <input type="text" name="surat_kop_baris_6" id="surat_kop_baris_6" value="{{ old('surat_kop_baris_6', $surat_kop_baris_6) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    </div>
                    <div>
                        <label for="surat_kop_baris_7" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kop Baris 7 (Kota & Kode Pos)</label>
                        <input type="text" name="surat_kop_baris_7" id="surat_kop_baris_7" value="{{ old('surat_kop_baris_7', $surat_kop_baris_7) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    </div>
                </div>
            </div>

            <!-- Section 2: Nomor & Detail Surat -->
            <div class="glass-card p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 border-b border-slate-200/60 dark:border-slate-800 pb-3">
                    <i data-lucide="hash" class="w-5 h-5 text-indigo-500"></i> Format Nomor Surat
                </h3>
                <div>
                    <label for="surat_nomor_format" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Format Nomor</label>
                    <input type="text" name="surat_nomor_format" id="surat_nomor_format" value="{{ old('surat_nomor_format', $surat_nomor_format) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-mono">
                    <p class="mt-1.5 text-xs text-slate-500">Contoh default: <code class="bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded font-mono">421.5 / ............ / SMKN1.CMS / PKL / [TAHUN_SEKARANG]</code></p>
                </div>
            </div>

            <!-- Section 3: Isi Surat -->
            <div class="glass-card p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 border-b border-slate-200/60 dark:border-slate-800 pb-3">
                    <i data-lucide="file-text" class="w-5 h-5 text-indigo-500"></i> Tubuh & Isi Surat
                </h3>
                <div class="space-y-4">
                    <div>
                        <label for="surat_isi_pembuka" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Paragraf 1: Pembuka / Latar Belakang</label>
                        <textarea name="surat_isi_pembuka" id="surat_isi_pembuka" rows="3" required
                                  class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm resize-y">{{ old('surat_isi_pembuka', $surat_isi_pembuka) }}</textarea>
                    </div>
                    <div>
                        <label for="surat_isi_tengah" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Paragraf 2: Pengantar Data Siswa</label>
                        <textarea name="surat_isi_tengah" id="surat_isi_tengah" rows="2" required
                                  class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm resize-y">{{ old('surat_isi_tengah', $surat_isi_tengah) }}</textarea>
                    </div>
                    <div>
                        <label for="surat_isi_penutup" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Paragraf 3: Keterangan Waktu PKL & Aturan</label>
                        <textarea name="surat_isi_penutup" id="surat_isi_penutup" rows="2" required
                                  class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm resize-y">{{ old('surat_isi_penutup', $surat_isi_penutup) }}</textarea>
                    </div>
                    <div>
                        <label for="surat_isi_salam" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Paragraf 4: Harapan & Penutup Terima Kasih</label>
                        <textarea name="surat_isi_salam" id="surat_isi_salam" rows="2" required
                                  class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm resize-y">{{ old('surat_isi_salam', $surat_isi_salam) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 4: Penanda Tangan -->
            <div class="glass-card p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 border-b border-slate-200/60 dark:border-slate-800 pb-3">
                    <i data-lucide="signature" class="w-5 h-5 text-indigo-500"></i> Blok Penandatangan Surat
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="surat_ttd_jabatan" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jabatan Penanda Tangan</label>
                        <input type="text" name="surat_ttd_jabatan" id="surat_ttd_jabatan" value="{{ old('surat_ttd_jabatan', $surat_ttd_jabatan) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    </div>
                    <div>
                        <label for="surat_ttd_nama" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nama Lengkap Penanda Tangan</label>
                        <input type="text" name="surat_ttd_nama" id="surat_ttd_nama" value="{{ old('surat_ttd_nama', $surat_ttd_nama) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    </div>
                    <div>
                        <label for="surat_ttd_nip" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">NIP Penanda Tangan</label>
                        <input type="text" name="surat_ttd_nip" id="surat_ttd_nip" value="{{ old('surat_ttd_nip', $surat_ttd_nip) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3">
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 cursor-pointer">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Format Surat
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
