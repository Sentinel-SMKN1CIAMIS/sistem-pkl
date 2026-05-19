<x-app-layout>
    <x-slot name="header">Pusat Bantuan & FAQ</x-slot>

    <div class="mb-8">
        <p class="text-slate-600 dark:text-slate-400">Temukan panduan langkah-demi-langkah tentang cara menggunakan Sistem PKL.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6" x-data="{ activeAccordion: null }">
            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 mb-4">
                <i data-lucide="help-circle" class="w-6 h-6 text-blue-500"></i>
                Pertanyaan yang Sering Diajukan
            </h3>

            <!-- Jurnal -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 1 ? activeAccordion = null : activeAccordion = 1" 
                        class="w-full flex items-center justify-between p-5 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <i data-lucide="book-open" class="w-4 h-4"></i>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-slate-100">Bagaimana cara mengisi Jurnal Harian?</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300" 
                       :class="activeAccordion === 1 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="activeAccordion === 1" x-collapse>
                    <div class="p-5 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed">
                        <ol class="list-decimal list-inside space-y-2 mt-4">
                            <li>Buka menu <strong>Jurnal Kegiatan</strong> di sidebar.</li>
                            <li>Klik tombol <strong><i data-lucide="plus" class="w-3 h-3 inline"></i> Tulis Jurnal</strong> berwarna biru di pojok kanan atas.</li>
                            <li>Isi form dengan detail kegiatan Anda: tanggal, waktu, kegiatan, dan unggah foto dokumentasi (wajib).</li>
                            <li>Setelah form terisi, klik <strong>Simpan Jurnal</strong>.</li>
                            <li>Tunggu proses validasi oleh Pembimbing DUDI dan Pembimbing Sekolah. Jurnal yang sudah divalidasi akan memiliki status <span class="text-emerald-500 font-bold">Valid</span>.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Absensi -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 2 ? activeAccordion = null : activeAccordion = 2" 
                        class="w-full flex items-center justify-between p-5 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <i data-lucide="calendar-check" class="w-4 h-4"></i>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-slate-100">Cara melakukan Absensi (Clock-in / Clock-out)</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300" 
                       :class="activeAccordion === 2 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="activeAccordion === 2" x-collapse>
                    <div class="p-5 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed">
                        <ol class="list-decimal list-inside space-y-2 mt-4">
                            <li>Akses menu <strong>Daftar Hadir</strong> dari sidebar.</li>
                            <li>Pastikan Anda memberikan izin akses <strong>Lokasi/GPS</strong> pada browser Anda. Sistem akan mendeteksi koordinat Anda.</li>
                            <li>Isi keterangan jika ada, lalu klik tombol hijau <strong>Absen Masuk (Clock-in)</strong> saat tiba di tempat kerja.</li>
                            <li>Saat jam kerja berakhir, kembali ke halaman ini dan klik tombol kuning <strong>Absen Keluar (Clock-out)</strong>.</li>
                            <li>Riwayat absensi dapat Anda lihat di bagian bawah halaman.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Laporan -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 3 ? activeAccordion = null : activeAccordion = 3" 
                        class="w-full flex items-center justify-between p-5 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-slate-100">Syarat Pengumpulan Laporan PKL</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300" 
                       :class="activeAccordion === 3 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="activeAccordion === 3" x-collapse>
                    <div class="p-5 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed">
                        <ol class="list-decimal list-inside space-y-2 mt-4">
                            <li>Masuk ke menu <strong>Laporan PKL</strong>.</li>
                            <li>Laporan hanya dapat diunggah dalam format PDF.</li>
                            <li>Isi judul dokumen dengan jelas, tambahkan deskripsi jika diperlukan.</li>
                            <li>Pilih file PDF laporan Anda (maksimal sesuai ketentuan yang berlaku, biasanya 2MB atau 5MB).</li>
                            <li>Klik <strong>Kirim Laporan</strong>. Laporan akan ditinjau oleh pihak sekolah.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Profil -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 4 ? activeAccordion = null : activeAccordion = 4" 
                        class="w-full flex items-center justify-between p-5 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                            <i data-lucide="user-cog" class="w-4 h-4"></i>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-slate-100">Mengedit Data Pembimbing Industri</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300" 
                       :class="activeAccordion === 4 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="activeAccordion === 4" x-collapse>
                    <div class="p-5 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed">
                        <p class="mt-4">Jika pembimbing industri tempat Anda PKL belum terdaftar di sistem secara otomatis, Anda bisa mendaftarkannya secara manual:</p>
                        <ol class="list-decimal list-inside space-y-2 mt-2">
                            <li>Buka menu <strong>Profil Saya</strong>.</li>
                            <li>Gulir ke bawah ke bagian "Pembimbing Industri (Manual)".</li>
                            <li>Masukkan nama lengkap dan jabatan mentor Anda.</li>
                            <li>Isi kontak tambahan (No. WA/Alamat).</li>
                            <li>Klik <strong>Simpan Perubahan</strong>.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Contact -->
        <div class="lg:col-span-1">
            <div class="glass-card p-6 border-t-4 border-blue-500">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-2xl flex items-center justify-center mb-6">
                    <i data-lucide="message-circle" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Masih Butuh Bantuan?</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                    Jika Anda memiliki pertanyaan khusus, mengalami error, atau butuh bantuan lebih lanjut terkait teknis penggunaan sistem ini.
                </p>
                <div class="space-y-4">
                    <a href="#" class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-all">
                        <i data-lucide="phone" class="w-4 h-4"></i>
                        Hubungi Admin / Pokja
                    </a>
                    <a href="{{ route('siswa.panduan.index') }}" class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Unduh Buku Pedoman
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
