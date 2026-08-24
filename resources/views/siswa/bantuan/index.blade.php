<x-app-layout>
    <x-slot name="header">Pusat Bantuan & FAQ</x-slot>

    <div class="mb-8">
        <p class="text-slate-600 dark:text-slate-400">Temukan panduan langkah-demi-langkah, solusi kendala teknis, dan prosedur operasional Sistem PKL SMKN 1 Ciamis.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content (FAQ Accordions) -->
        <div class="lg:col-span-2 space-y-4" x-data="{ activeAccordion: null }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-6 h-6 text-blue-500"></i>
                    Pertanyaan yang Sering Diajukan (FAQ)
                </h3>
            </div>

            <!-- 1. Alur Pengajuan PKL -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 1 ? activeAccordion = null : activeAccordion = 1" 
                        class="w-full flex items-center justify-between py-5 px-6 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                            <i data-lucide="file-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider block mb-0.5">Tahap Awal</span>
                            <span class="text-base md:text-lg font-bold text-slate-900 dark:text-slate-100">Alur Pengajuan Tempat PKL & Surat Pengantar</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" 
                       :class="activeAccordion === 1 ? 'rotate-180' : ''"></i>
                </button>
                <div class="overflow-hidden transition-all duration-300 ease-in-out" 
                     :style="activeAccordion === 1 ? 'max-height: 1200px; opacity: 1;' : 'max-height: 0; opacity: 0;'">
                    <div class="p-6 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed space-y-3">
                        <p class="mt-4">Ikuti alur pengajuan tempat Praktik Kerja Lapangan (PKL) berikut dari awal hingga status aktif:</p>
                        <ol class="list-decimal list-inside space-y-2.5 ml-1">
                            <li><strong>Pilih / Ajukan Tempat PKL:</strong> Buka menu <strong>Pengajuan PKL</strong>. Pilih perusahaan dari daftar mitra DUDI yang sudah ada, atau klik <strong>Ajukan DUDI Baru</strong> jika tempat magang Anda belum terdaftar di sistem.</li>
                            <li><strong>Verifikasi Kepala Program:</strong> Pengajuan Anda akan ditinjau oleh <strong>Kepala Program Keahlian (Kaprog)</strong> untuk memastikan kesesuaian kompetensi keahlian jurusan Anda.</li>
                            <li><strong>Unduh & Cetak Surat Pengantar:</strong> Setelah disetujui Kaprog, tombol <strong>Cetak Surat Pengantar (PDF)</strong> akan aktif. Surat ini sudah memuat KOP resmi sekolah dan siap diserahkan ke pihak instansi/perusahaan.</li>
                            <li><strong>Unggah Bukti Penerimaan (Balasan):</strong> Setelah pihak perusahaan menerima Anda, foto atau scan surat balasan/bukti penerimaan resmi, lalu unggah melalui menu Pengajuan PKL.</li>
                            <li><strong>Aktivasi Status PKL:</strong> Tim Pokja PKL sekolah akan memvalidasi bukti balasan tersebut. Status Anda akan resmi berubah menjadi <span class="text-emerald-500 font-bold">Sedang PKL</span> dan seluruh fitur presensi serta jurnal harian akan otomatis terbuka.</li>
                        </ol>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/40 rounded-xl text-xs text-blue-700 dark:text-blue-300 flex items-start gap-2.5 mt-3">
                            <i data-lucide="info" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                            <span>Jika pengajuan ditolak oleh Kaprog atau DUDI, Anda dapat mengubah data perusahaan atau mengajukan permohonan ke DUDI baru tanpa perlu membuat akun baru.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Absensi Harian -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 2 ? activeAccordion = null : activeAccordion = 2" 
                        class="w-full flex items-center justify-between py-5 px-6 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <i data-lucide="calendar-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block mb-0.5">Aktivitas Harian</span>
                            <span class="text-base md:text-lg font-bold text-slate-900 dark:text-slate-100">Presensi Harian (Clock-in, Clock-out & Izin/Sakit)</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" 
                       :class="activeAccordion === 2 ? 'rotate-180' : ''"></i>
                </button>
                <div class="overflow-hidden transition-all duration-300 ease-in-out" 
                     :style="activeAccordion === 2 ? 'max-height: 1200px; opacity: 1;' : 'max-height: 0; opacity: 0;'">
                    <div class="p-6 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed space-y-3">
                        <p class="mt-4">Tata cara melakukan pencatatan kehadiran setiap hari kerja:</p>
                        <ul class="space-y-2.5 ml-1">
                            <li class="flex items-start gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">1</span>
                                <div><strong>Absen Masuk (Clock-in):</strong> Buka menu <strong>Daftar Hadir</strong> saat tiba di lokasi PKL. Pastikan GPS/Lokasi perangkat aktif. Sistem akan memverifikasi koordinat lokasi kantor, lalu klik tombol hijau <strong>Clock In (Absen Masuk)</strong>.</div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">2</span>
                                <div><strong>Absen Pulang (Clock-out):</strong> Di akhir jam operasional kerja, buka kembali halaman Daftar Hadir dan klik tombol kuning <strong>Clock Out (Absen Keluar)</strong>.</div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">3</span>
                                <div><strong>Pengajuan Izin / Sakit:</strong> Jika berhalangan hadir, buka menu Daftar Hadir &rarr; klik tombol <strong>Ajukan Izin / Sakit</strong>. Pilih jenis dispensasi, tuliskan alasan detail, dan wajib lampirkan foto surat keterangan dokter (jika sakit) atau surat permohonan izin orang tua.</div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">4</span>
                                <div><strong>Izin Pulang Lebih Awal:</strong> Apabila harus meninggalkan tempat PKL lebih cepat karena dinas luar atau urusan darurat, gunakan opsi <strong>Pulang Cepat</strong> dengan menyertakan keterangan izin mentor.</div>
                            </li>
                        </ul>
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40 rounded-xl text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5 mt-3">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                            <span>Pastikan izin akses lokasi (*Location Permission*) pada browser Chrome/Safari Anda disetujui (*Allow*). Jika berada di luar radius kantor, konfirmasi titik lokasi kepada pembimbing sekolah.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Jurnal Harian -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 3 ? activeAccordion = null : activeAccordion = 3" 
                        class="w-full flex items-center justify-between py-5 px-6 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                            <i data-lucide="book-open" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider block mb-0.5">Laporan Aktivitas</span>
                            <span class="text-base md:text-lg font-bold text-slate-900 dark:text-slate-100">Panduan Pengisian Jurnal Harian & Foto Dokumentasi</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" 
                       :class="activeAccordion === 3 ? 'rotate-180' : ''"></i>
                </button>
                <div class="overflow-hidden transition-all duration-300 ease-in-out" 
                     :style="activeAccordion === 3 ? 'max-height: 1200px; opacity: 1;' : 'max-height: 0; opacity: 0;'">
                    <div class="p-6 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed space-y-3">
                        <p class="mt-4">Jurnal harian merupakan bukti fisik kegiatan kerja yang wajib diisi setiap hari PKL:</p>
                        <ol class="list-decimal list-inside space-y-2.5 ml-1">
                            <li>Masuk ke menu <strong>Jurnal Kegiatan</strong> &rarr; klik tombol <strong>+ Tulis Jurnal</strong> di pojok kanan atas.</li>
                            <li>Tentukan <strong>Tanggal Kegiatan</strong>, <strong>Jam Mulai</strong>, dan <strong>Jam Selesai</strong> pelaksanaan pekerjaan.</li>
                            <li>Tuliskan <strong>Deskripsi Pekerjaan</strong> secara detail, jelas, dan profesional (uraikan alat/perangkat yang digunakan, alur pengerjaan, kendala yang dihadapi, serta solusi yang dilakukan).</li>
                            <li>Pilih <strong>Tujuan Pembelajaran (TP) / Elemen Kompetensi</strong> yang relevan dengan pekerjaan Anda (bisa memilih lebih dari 1 capaian).</li>
                            <li>Unggah <strong>Foto Dokumentasi Kerja</strong> (format JPG/PNG). Gunakan fitur *crop* foto bawaan sistem (rasio 1:1) agar foto rapi dan tidak terdistorsi.</li>
                            <li>Klik <strong>Simpan Jurnal</strong>. Jurnal akan masuk ke antrean validasi Pembimbing DUDI dan Guru Pembimbing Sekolah.</li>
                        </ol>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-3">
                            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/40 rounded-xl text-xs">
                                <span class="font-bold text-emerald-700 dark:text-emerald-400 block mb-1">Status Valid:</span>
                                <span class="text-emerald-800 dark:text-emerald-300">Jurnal telah disetujui pembimbing dan siap direkap menjadi nilai portofolio akhir.</span>
                            </div>
                            <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 rounded-xl text-xs">
                                <span class="font-bold text-amber-700 dark:text-amber-400 block mb-1">Status Perlu Revisi:</span>
                                <span class="text-amber-800 dark:text-amber-300">Jika ditolak/diminta revisi, klik tombol edit pada baris jurnal dan perbaiki sesuai catatan pembimbing.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Cetak Rekap, Portofolio & Sertifikat -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 4 ? activeAccordion = null : activeAccordion = 4" 
                        class="w-full flex items-center justify-between py-5 px-6 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            <i data-lucide="printer" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block mb-0.5">Dokumen & Berkas</span>
                            <span class="text-base md:text-lg font-bold text-slate-900 dark:text-slate-100">Cara Cetak Rekap Jurnal, Portofolio TP & Sertifikat PKL</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" 
                       :class="activeAccordion === 4 ? 'rotate-180' : ''"></i>
                </button>
                <div class="overflow-hidden transition-all duration-300 ease-in-out" 
                     :style="activeAccordion === 4 ? 'max-height: 1200px; opacity: 1;' : 'max-height: 0; opacity: 0;'">
                    <div class="p-6 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed space-y-3">
                        <p class="mt-4">Sistem menyediakan berkas cetak PDF otomatis untuk keperluan arsip dan pelaporan:</p>
                        <div class="space-y-3">
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700">
                                <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wide mb-1 flex items-center gap-1.5">
                                    <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
                                    1. Rekapitulasi Jurnal Harian (PDF)
                                </h4>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Buka menu <strong>Jurnal Kegiatan</strong> &rarr; klik tombol <strong>Cetak PDF / Rekap Jurnal</strong> di bagian atas. Dokumen ini merangkum seluruh rincian kegiatan harian beserta foto dokumentasi yang telah diverifikasi.</p>
                            </div>
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700">
                                <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wide mb-1 flex items-center gap-1.5">
                                    <i data-lucide="award" class="w-4 h-4 text-purple-500"></i>
                                    2. Lembar Portofolio Ketercapaian TP (PDF)
                                </h4>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Klik tombol <strong>Cetak Portofolio</strong> pada menu Jurnal untuk mengunduh rekapitulasi ketercapaian Tujuan Pembelajaran Kurikulum Merdeka yang dikelompokkan otomatis.</p>
                            </div>
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700">
                                <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wide mb-1 flex items-center gap-1.5">
                                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                                    3. Sertifikat Resmi PKL
                                </h4>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Tombol unduh Sertifikat PKL akan terbuka secara otomatis di dashboard siswa setelah masa PKL selesai, nilai telah diinput oleh pembimbing, dan status Anda dinyatakan <strong>Selesai PKL</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Laporan Akhir PKL -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 5 ? activeAccordion = null : activeAccordion = 5" 
                        class="w-full flex items-center justify-between py-5 px-6 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider block mb-0.5">Tahap Akhir</span>
                            <span class="text-base md:text-lg font-bold text-slate-900 dark:text-slate-100">Syarat & Prosedur Pengumpulan Laporan Akhir PKL</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" 
                       :class="activeAccordion === 5 ? 'rotate-180' : ''"></i>
                </button>
                <div class="overflow-hidden transition-all duration-300 ease-in-out" 
                     :style="activeAccordion === 5 ? 'max-height: 1200px; opacity: 1;' : 'max-height: 0; opacity: 0;'">
                    <div class="p-6 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed space-y-3">
                        <p class="mt-4">Petunjuk penyusunan dan penyerahan lembar laporan akhir PKL:</p>
                        <ol class="list-decimal list-inside space-y-2.5 ml-1">
                            <li>Buka menu <strong>Laporan PKL</strong> di sidebar.</li>
                            <li>Tuliskan <strong>Judul Laporan</strong> yang spesifik sesuai proyek atau ruang lingkup pekerjaan utama Anda di DUDI.</li>
                            <li>Tuliskan <strong>Deskripsi / Ringkasan Pekerjaan</strong> yang mencakup latar belakang, tugas utama, keahlian baru yang didapat, dan saran untuk DUDI/sekolah.</li>
                            <li>Tambahkan <strong>Tautan Media Sosial / Presentasi / Portofolio</strong> (contoh: Link video YouTube presentasi, video TikTok kegiatan, folder berkas Google Drive, atau GitHub). Anda dapat menambah lebih dari 1 link tautan.</li>
                            <li>Klik <strong>Kirim Laporan</strong> untuk mengajukan laporan kepada Guru Pembimbing Sekolah.</li>
                            <li>Setelah diajukan, klik tombol <strong>Cetak Lembar Laporan (PDF)</strong> untuk mengunduh dokumen resmi format formal monokrom yang siap ditandatangani oleh Pembimbing DUDI, Guru Pembimbing, dan Kaprog.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- 6. Titik Lokasi Kantor Maps -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 6 ? activeAccordion = null : activeAccordion = 6" 
                        class="w-full flex items-center justify-between py-5 px-6 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider block mb-0.5">Konfigurasi GPS</span>
                            <span class="text-base md:text-lg font-bold text-slate-900 dark:text-slate-100">Penentuan Titik Lokasi Kantor / Tempat PKL</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" 
                       :class="activeAccordion === 6 ? 'rotate-180' : ''"></i>
                </button>
                <div class="overflow-hidden transition-all duration-300 ease-in-out" 
                     :style="activeAccordion === 6 ? 'max-height: 1200px; opacity: 1;' : 'max-height: 0; opacity: 0;'">
                    <div class="p-6 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed space-y-3">
                        <p class="mt-4">Jika lokasi absensi belum terdeteksi atau koordinat kantor berpindah, atur titik lokasi GPS melalui langkah berikut:</p>
                        <ol class="list-decimal list-inside space-y-2.5 ml-1">
                            <li>Buka menu <strong>Profil Saya</strong>.</li>
                            <li>Cari bagian <strong>Titik Koordinat Lokasi PKL (Maps)</strong>.</li>
                            <li>Gunakan peta interaktif untuk mengarahkan pin tepat di gedung/kantor tempat Anda bekerja, atau klik tombol <em>Gunakan Lokasi Perangkat Saat Ini</em> saat Anda berada di kantor.</li>
                            <li>Tentukan batas radius toleransi absensi (standar: 50 &ndash; 100 meter).</li>
                            <li>Klik <strong>Simpan Titik Lokasi</strong>. Sekarang sistem absensi akan mencocokkan kehadiran Anda dengan koordinat yang telah ditentukan tersebut.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- 7. Pembimbing Industri -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 7 ? activeAccordion = null : activeAccordion = 7" 
                        class="w-full flex items-center justify-between py-5 px-6 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <i data-lucide="user-cog" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider block mb-0.5">Manajemen Profil</span>
                            <span class="text-base md:text-lg font-bold text-slate-900 dark:text-slate-100">Mengedit Data & Prosedur Pergantian Pembimbing Industri</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" 
                       :class="activeAccordion === 7 ? 'rotate-180' : ''"></i>
                </button>
                <div class="overflow-hidden transition-all duration-300 ease-in-out" 
                     :style="activeAccordion === 7 ? 'max-height: 1200px; opacity: 1;' : 'max-height: 0; opacity: 0;'">
                    <div class="p-6 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed space-y-3">
                        <p class="mt-4">Penjelasan mengenai status pembimbing industri dan langkah jika terjadi mutasi/pergantian:</p>
                        <div class="space-y-3">
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                                <span class="font-bold text-slate-900 dark:text-white block mb-1">A. Mode Input Pembimbing Manual:</span>
                                <span>Jika mentor di tempat PKL belum memiliki akun sistem, masuk ke menu <strong>Profil Saya</strong> &rarr; bagian <em>Pembimbing Industri (Manual)</em> &rarr; isi Nama Lengkap, Jabatan, dan Nomor WhatsApp aktif &rarr; klik Simpan.</span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                                <span class="font-bold text-slate-900 dark:text-white block mb-1">B. Prosedur Jika Pembimbing Industri Diganti:</span>
                                <ol class="list-decimal list-inside space-y-1.5 mt-1 text-slate-600 dark:text-slate-400">
                                    <li>Laporkan pergantian pembimbing kepada <strong>Guru Pembimbing Sekolah</strong> atau tim <strong>Pokja PKL</strong>.</li>
                                    <li>Admin Pokja akan mereset tautan akun pembimbing lama pada profil Anda kembali ke mode manual.</li>
                                    <li>Setelah direset, buka menu <strong>Profil Saya</strong> dan perbarui nama serta nomor HP pembimbing pengganti secara mandiri.</li>
                                    <li>Arahkan pembimbing baru untuk mendaftar akun di portal registrasi DUDI jika ingin mengaktifkan persetujuan digital.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 8. Solusi Kendala Teknis -->
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-200">
                <button @click="activeAccordion === 8 ? activeAccordion = null : activeAccordion = 8" 
                        class="w-full flex items-center justify-between py-5 px-6 text-left bg-white dark:bg-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider block mb-0.5">Troubleshooting</span>
                            <span class="text-base md:text-lg font-bold text-slate-900 dark:text-slate-100">Solusi Kendala Teknis & Error Umum</span>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" 
                       :class="activeAccordion === 8 ? 'rotate-180' : ''"></i>
                </button>
                <div class="overflow-hidden transition-all duration-300 ease-in-out" 
                     :style="activeAccordion === 8 ? 'max-height: 1200px; opacity: 1;' : 'max-height: 0; opacity: 0;'">
                    <div class="p-6 pt-0 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/50 leading-relaxed space-y-3">
                        <p class="mt-4">Panduan perbaikan cepat untuk masalah teknis yang sering ditemui:</p>
                        <div class="space-y-3">
                            <div class="border-l-4 border-amber-500 pl-3">
                                <span class="font-bold text-slate-900 dark:text-white text-xs block">1. Geolocation Error / "Lokasi GPS Tidak Terdeteksi"</span>
                                <span class="text-xs text-slate-600 dark:text-slate-400">Pastikan GPS HP aktif dalam mode *Akurasi Tinggi*. Pada browser Chrome/Safari, klik ikon gembok di sebelah kiri bilah alamat web &rarr; <em>Izin Situs (Permissions)</em> &rarr; ubah <em>Lokasi</em> menjadi <strong>Izinkan (Allow)</strong>, lalu refresh halaman.</span>
                            </div>
                            <div class="border-l-4 border-blue-500 pl-3">
                                <span class="font-bold text-slate-900 dark:text-white text-xs block">2. Menu Jurnal / Absensi Tidak Dapat Diklik</span>
                                <span class="text-xs text-slate-600 dark:text-slate-400">Menu ini terkunci otomatis jika status pengajuan PKL Anda belum disetujui Pokja atau belum mengunggah surat balasan DUDI. Pastikan status Anda di dashboard sudah <strong>Sedang PKL</strong>.</span>
                            </div>
                            <div class="border-l-4 border-purple-500 pl-3">
                                <span class="font-bold text-slate-900 dark:text-white text-xs block">3. Lupa Kata Sandi Akun Siswa</span>
                                <span class="text-xs text-slate-600 dark:text-slate-400">Hubungi <strong>Guru Pembimbing Sekolah</strong> Anda. Guru pembimbing memiliki akses untuk mereset kata sandi siswa bimbingannya ke password bawaan.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar Contact & Download Guide -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-6 border-t-4 border-blue-500">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-2xl flex items-center justify-center mb-6">
                    <i data-lucide="book-marked" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Buku Pedoman & SOP</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                    Pelajari aturan resmi, tata tertib, hak & kewajiban siswa, serta standar operasional prosedur penggunaan aplikasi MAS-PKL SMKN 1 Ciamis dalam format dokumen PDF lengkap.
                </p>
                <div>
                    <a href="{{ asset('SOP & Panduan Aplikasi MAS-PKL.pdf') }}" download="SOP & Panduan Aplikasi MAS-PKL.pdf" class="w-full flex items-center justify-center gap-2.5 px-4 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all text-sm text-center leading-snug">
                        <i data-lucide="download" class="w-4 h-4 shrink-0"></i>
                        <span>Unduh SOP & Panduan Aplikasi MAS-PKL</span>
                    </a>
                </div>
            </div>

            <!-- Card Bantuan Kontak Pokja -->
            <div class="glass-card p-6 border-t-4 border-emerald-500">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 rounded-2xl flex items-center justify-center mb-4">
                    <i data-lucide="headset" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-2">Butuh Bantuan Langsung?</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                    Jika Anda mengalami kendala teknis darurat yang tidak tercantum di atas, silakan hubungi Guru Pembimbing Sekolah atau tim Pokja PKL di kampus SMKN 1 Ciamis.
                </p>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl border border-emerald-200 dark:border-emerald-800/40 text-xs text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400"></i>
                    <span>Layanan Bantuan: Senin &ndash; Jumat, 07.30 &ndash; 15.30 WIB</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
