<x-app-layout>
    <x-slot name="header">Panduan Lengkap Impor Data Massal</x-slot>

    <div class="h-full overflow-y-auto px-4 py-6 md:px-8 space-y-6 max-w-7xl mx-auto animate-fade-in-up" x-data="{ activeTab: 'general', searchQuery: '' }">
        <!-- Premium Hero Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 p-6 md:p-8 text-white shadow-xl shadow-blue-500/10">
            <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 opacity-10 blur-sm pointer-events-none">
                <i data-lucide="file-spreadsheet" class="w-72 h-72"></i>
            </div>
            <div class="relative z-10 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold border border-white/20">
                    <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                    Buku Panduan Administrator
                </div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Pendaftaran Data Massal (Excel Asli)</h1>
                <p class="text-sm md:text-base text-blue-100 max-w-2xl leading-relaxed">
                    Sistem kini mendukung penuh berkas **Microsoft Excel asli (`.xlsx`)**. Panduan ini dirancang khusus untuk memandu Anda menyusun data secara profesional tanpa kendala separator ataupun karakter berantakan.
                </p>
            </div>
        </div>

        <!-- Interactive Layout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
            <!-- Sidebar Navigation (Glassmorphic) -->
            <div class="lg:col-span-1 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl p-4 shadow-sm space-y-1 sticky top-6">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-3 mb-2">Navigasi Panduan</p>
                
                <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs rounded-xl transition-all text-left">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    Ketentuan Umum
                </button>
                <button @click="activeTab = 'siswa'" :class="activeTab === 'siswa' ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs rounded-xl transition-all text-left">
                    <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    Panduan Data Siswa
                </button>
                <button @click="activeTab = 'dudi'" :class="activeTab === 'dudi' ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs rounded-xl transition-all text-left">
                    <i data-lucide="building-2" class="w-4 h-4"></i>
                    Panduan Data DUDI
                </button>
                <button @click="activeTab = 'pembimbing_sekolah'" :class="activeTab === 'pembimbing_sekolah' ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs rounded-xl transition-all text-left">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                    Pembimbing Sekolah
                </button>
                <button @click="activeTab = 'pembimbing_dudi'" :class="activeTab === 'pembimbing_dudi' ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs rounded-xl transition-all text-left">
                    <i data-lucide="user-cog" class="w-4 h-4"></i>
                    Pembimbing DUDI
                </button>
                <button @click="activeTab = 'troubleshoot'" :class="activeTab === 'troubleshoot' ? 'bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs rounded-xl transition-all text-left">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    Pemecahan Masalah
                </button>
            </div>

            <!-- Content Area (Main Guide Body) -->
            <div class="lg:col-span-3 space-y-6">
                <!-- 1. Ketentuan Umum -->
                <div x-show="activeTab === 'general'" class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-500">
                            <i data-lucide="info" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Ketentuan Umum & Karakteristik Impor</h2>
                            <p class="text-xs text-slate-500">Prinsip dasar pengolahan data masal sistem</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl space-y-2 border border-slate-100 dark:border-slate-800">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                                Penjaminan Transaksi (Atomic)
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                Seluruh proses impor berjalan di dalam satu transaksi database. Apabila ada <strong>satu saja baris data yang gagal divalidasi</strong>, maka seluruh operasi akan dibatalkan otomatis. Ini menjaga sistem tetap bersih tanpa data duplikat atau setengah tersimpan.
                            </p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl space-y-2 border border-slate-100 dark:border-slate-800">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-blue-500"></i>
                                Format Excel Asli (.xlsx)
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                Template resmi diunduh langsung dalam format **Microsoft Excel asli (`.xlsx`)**. Tampilan template dirancang premium (berwarna biru, auto-fit kolom) agar administrator dapat langsung mengisinya dan menyimpannya kembali tanpa kebingungan delimiters atau konversi encoding CSV.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Tahapan Penggunaan Fitur Impor:</h4>
                        <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
                            <div class="relative flex gap-3 items-start">
                                <span class="absolute -left-6 flex items-center justify-center w-4 h-4 rounded-full bg-blue-600 text-white text-[9px] font-bold">1</span>
                                <div class="text-xs text-slate-700 dark:text-slate-300">
                                    <strong>Unduh Template:</strong> Pergi ke menu pendaftaran yang dituju (Kelola Siswa/DUDI/Pembimbing) dan klik unduh template Excel asli.
                                </div>
                            </div>
                            <div class="relative flex gap-3 items-start">
                                <span class="absolute -left-6 flex items-center justify-center w-4 h-4 rounded-full bg-blue-600 text-white text-[9px] font-bold">2</span>
                                <div class="text-xs text-slate-700 dark:text-slate-300">
                                    <strong>Isi Data:</strong> Buka file menggunakan Microsoft Excel atau Google Sheets, timpa baris contoh ke-2 (baris miring abu-abu), lalu isi data Anda.
                                </div>
                            </div>
                            <div class="relative flex gap-3 items-start">
                                <span class="absolute -left-6 flex items-center justify-center w-4 h-4 rounded-full bg-blue-600 text-white text-[9px] font-bold">3</span>
                                <div class="text-xs text-slate-700 dark:text-slate-300">
                                    <strong>Unggah:</strong> Unggah file `.xlsx` Anda kembali ke modal impor di halaman web. Sistem secara dinamis akan mem-parse dan memvalidasi seluruh baris data.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Panduan Siswa -->
                <div x-show="activeTab === 'siswa'" class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-500">
                            <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Panduan Struktur Kolom Siswa</h2>
                            <p class="text-xs text-slate-500">Aturan kolom untuk data Siswa (`template_siswa.xlsx`)</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-600 dark:text-slate-400">
                            <thead class="text-[10px] text-slate-400 dark:text-slate-500 uppercase bg-slate-50 dark:bg-slate-800/40">
                                <tr>
                                    <th class="px-4 py-2">Nama Kolom</th>
                                    <th class="px-4 py-2">Validasi & Keterangan</th>
                                    <th class="px-4 py-2">Contoh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">nis</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Berupa angka unik. Digunakan juga sebagai username login siswa.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">12345678</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">nama_lengkap</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Nama lengkap siswa sesuai pendaftaran resmi sekolah.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">Ahmad Fauzi</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">email</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Alamat email unik siswa yang aktif.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">ahmad@mail.com</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">password</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Kata sandi awal masuk. Minimal 6 karakter.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">password123</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">kelas</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Nama kelas siswa saat ini.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">XII RPL 1</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">jenis_kelamin</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Hanya diisi huruf tunggal <strong class="text-blue-500">L</strong> (Laki-laki) atau <strong class="text-rose-500">P</strong> (Perempuan).</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">L</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">tahun_ajaran</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Tahun ajaran siswa aktif.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">2025/2026</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">konsentrasi_keahlian</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Harus diisi nama jurusan yang terdaftar di database (lihat daftar jurusan di bawah).</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">Rekayasa Perangkat Lunak</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Dynamic Concentrations Reference Section -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800 rounded-2xl space-y-3">
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                            <i data-lucide="layers" class="w-4.5 h-4.5 text-blue-500"></i>
                            Daftar Konsentrasi Keahlian Terdaftar (Gunakan Teks Ini Persis):
                        </h4>
                        <p class="text-[11px] text-slate-500 leading-normal">
                            Pastikan pengisian kolom <strong>konsentrasi_keahlian</strong> pada file Excel menggunakan nama di bawah ini secara tepat (tanpa salah eja atau spasi berlebih):
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-1">
                            @forelse($jurusans as $jurusan)
                                <div class="flex items-center justify-between p-2.5 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800/80 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200">
                                    <span>{{ $jurusan }}</span>
                                    <button onclick="navigator.clipboard.writeText('{{ $jurusan }}'); alert('Berhasil menyalin nama jurusan!')" class="p-1 text-slate-400 hover:text-blue-500 rounded transition-colors" title="Salin nama jurusan">
                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            @empty
                                <p class="text-xs text-amber-500 italic col-span-2">Belum ada Konsentrasi Keahlian yang terdaftar di database.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 3. Panduan DUDI -->
                <div x-show="activeTab === 'dudi'" class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-500">
                            <i data-lucide="building-2" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Panduan Struktur Kolom DUDI</h2>
                            <p class="text-xs text-slate-500">Aturan kolom untuk data Instansi Industri (`template_dudi.xlsx`)</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-600 dark:text-slate-400">
                            <thead class="text-[10px] text-slate-400 dark:text-slate-500 uppercase bg-slate-50 dark:bg-slate-800/40">
                                <tr>
                                    <th class="px-4 py-2">Nama Kolom</th>
                                    <th class="px-4 py-2">Validasi & Keterangan</th>
                                    <th class="px-4 py-2">Contoh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">nama</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Nama resmi perusahaan/DUDI. Harus unik di sistem.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">PT Solusi Digital</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">bidang_usaha</td>
                                    <td class="px-4 py-3 leading-relaxed">Opsional. Bidang komersial atau usaha DUDI.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">Teknologi Informasi</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">alamat</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Alamat lengkap jalan lokasi perusahaan.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">Jl. Asia Afrika No. 45</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">kota</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Kota/Kabupaten domisili perusahaan.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">Bandung</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">konsentrasi_keahlian</td>
                                    <td class="px-4 py-3 leading-relaxed">
                                        <strong>Wajib.</strong> Nama jurusan yang diterima. Jika DUDI menerima multi-jurusan, gabungkan dan pisahkan dengan tanda koma (`,`) di dalam sel Excel bersangkutan.
                                    </td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">Rekayasa Perangkat Lunak, Teknik Jaringan</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Panduan Pembimbing Sekolah -->
                <div x-show="activeTab === 'pembimbing_sekolah'" class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-500">
                            <i data-lucide="user-check" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Panduan Struktur Kolom Pembimbing Sekolah</h2>
                            <p class="text-xs text-slate-500">Aturan kolom untuk guru pembimbing (`template_pembimbing_sekolah.xlsx`)</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-600 dark:text-slate-400">
                            <thead class="text-[10px] text-slate-400 dark:text-slate-500 uppercase bg-slate-50 dark:bg-slate-800/40">
                                <tr>
                                    <th class="px-4 py-2">Nama Kolom</th>
                                    <th class="px-4 py-2">Validasi & Keterangan</th>
                                    <th class="px-4 py-2">Contoh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">nip</td>
                                    <td class="px-4 py-3 leading-relaxed">Opsional. NIP resmi guru. Apabila diisi wajib angka unik.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">198501012010...</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">username</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Nama pendek tanpa spasi (boleh kombinasi strip/garis bawah). Harus unik.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">hendrawijaya</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">tipe</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Kategori guru pembimbing. Harus ditulis salah satu dari: <code class="text-blue-500">kejuruan</code> atau <code class="text-blue-500">umum</code>.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">kejuruan</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">kelas_diajar</td>
                                    <td class="px-4 py-3 leading-relaxed">
                                        Opsional. Daftar kelas binaan yang dibimbing guru. Dipisah menggunakan koma (`,`) jika membimbing lebih dari satu kelas.
                                    </td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">XII RPL 1, XII RPL 2</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. Panduan Pembimbing DUDI -->
                <div x-show="activeTab === 'pembimbing_dudi'" class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-500">
                            <i data-lucide="user-cog" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Panduan Struktur Kolom Pembimbing DUDI</h2>
                            <p class="text-xs text-slate-500">Aturan kolom untuk mentor perusahaan (`template_pembimbing_dudi.xlsx`)</p>
                        </div>
                    </div>

                    <div class="p-4.5 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex gap-3 items-start">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5 animate-pulse"></i>
                        <div class="text-xs leading-relaxed text-amber-700 dark:text-amber-300">
                            <strong>Prasyarat Wajib:</strong> Perusahaan/DUDI mentor bersangkutan **wajib sudah terdaftar terlebih dahulu** di sistem. Proses impor pembimbing DUDI akan mencocokkan teks kolom `nama_perusahaan` ke database secara realtime.
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-600 dark:text-slate-400">
                            <thead class="text-[10px] text-slate-400 dark:text-slate-500 uppercase bg-slate-50 dark:bg-slate-800/40">
                                <tr>
                                    <th class="px-4 py-2">Nama Kolom</th>
                                    <th class="px-4 py-2">Validasi & Keterangan</th>
                                    <th class="px-4 py-2">Contoh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">nama_lengkap</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Nama lengkap mentor industri.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">Eko Prasetyo</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">username</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Username unik tanpa spasi untuk akses login mentor.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">ekoprasetyo</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">jabatan</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Nama jabatan/posisi kerja mentor di DUDI.</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">Senior Developer</code></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200">nama_perusahaan</td>
                                    <td class="px-4 py-3 leading-relaxed"><strong>Wajib.</strong> Harus ditulis sama persis dengan Nama DUDI yang terdaftar (lihat referensi pencarian di bawah).</td>
                                    <td class="px-4 py-3"><code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">PT Solusi Digital</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Searchable DUDIs Reference List -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800 rounded-2xl space-y-3">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <i data-lucide="search" class="w-4.5 h-4.5 text-blue-500"></i>
                                Cari Nama DUDI Terdaftar (Gunakan Teks Ini Persis):
                            </h4>
                            <div class="relative w-full md:w-64">
                                <input type="text" x-model="searchQuery" placeholder="Cari nama perusahaan..." class="w-full text-xs px-3 py-1.5 pl-8 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:outline-none focus:border-blue-500 text-slate-800 dark:text-slate-200">
                                <i data-lucide="search" class="absolute left-2.5 top-2.5 w-3.5 h-3.5 text-slate-400"></i>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-normal">
                            Mentor hanya dapat terdaftar ke dalam salah satu dari daftar perusahaan aktif di bawah ini:
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-1 max-h-48 overflow-y-auto pr-1">
                            @forelse($dudis as $dudi)
                                <div x-show="searchQuery === '' || '{{ strtolower($dudi) }}'.includes(searchQuery.toLowerCase())" class="flex items-center justify-between p-2.5 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800/80 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200">
                                    <span class="truncate">{{ $dudi }}</span>
                                    <button onclick="navigator.clipboard.writeText('{{ $dudi }}'); alert('Berhasil menyalin nama DUDI!')" class="p-1 text-slate-400 hover:text-blue-500 rounded transition-colors flex-shrink-0" title="Salin nama DUDI">
                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            @empty
                                <p class="text-xs text-amber-500 italic col-span-2">Belum ada DUDI/Perusahaan yang terdaftar di database.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 6. Troubleshooting CSV -->
                <div x-show="activeTab === 'troubleshoot'" class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-500">
                            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Pemecahan Masalah & Solusi Error</h2>
                            <p class="text-xs text-slate-500">Mengatasi kendala umum saat mempersiapkan berkas Excel</p>
                        </div>
                    </div>

                    <div class="space-y-4" x-data="{ openAccordion: null }">
                        <!-- Accordion 1 -->
                        <div class="border border-slate-200/60 dark:border-slate-800/80 rounded-xl overflow-hidden">
                            <button @click="openAccordion = openAccordion === 1 ? null : 1" class="w-full flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/30 text-left text-xs font-bold text-slate-800 dark:text-slate-200">
                                <span>1. Apakah saya bisa menggunakan Google Sheets atau WPS Office untuk mengisi data?</span>
                                <i data-lucide="chevron-down" :class="openAccordion === 1 ? 'rotate-185' : ''" class="w-4 h-4 transition-transform text-slate-400"></i>
                            </button>
                            <div x-show="openAccordion === 1" class="p-4 text-xs text-slate-600 dark:text-slate-400 space-y-2 border-t border-slate-200/60 dark:border-slate-800/80 leading-relaxed">
                                <p>Ya, tentu saja. Anda dapat membuka template `.xlsx` yang diunduh di Google Sheets, LibreOffice, maupun WPS Office secara langsung. Cukup isi datanya, lalu pilih **Save As** atau **Download** dengan format **Microsoft Excel (.xlsx)** asli.</p>
                            </div>
                        </div>

                        <!-- Accordion 2 -->
                        <div class="border border-slate-200/60 dark:border-slate-800/80 rounded-xl overflow-hidden">
                            <button @click="openAccordion = openAccordion === 2 ? null : 2" class="w-full flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/30 text-left text-xs font-bold text-slate-800 dark:text-slate-200">
                                <span>2. Mengapa muncul error "Email/NIS/Username sudah terdaftar"?</span>
                                <i data-lucide="chevron-down" :class="openAccordion === 2 ? 'rotate-185' : ''" class="w-4 h-4 transition-transform text-slate-400"></i>
                            </button>
                            <div x-show="openAccordion === 2" class="p-4 text-xs text-slate-600 dark:text-slate-400 space-y-1 border-t border-slate-200/60 dark:border-slate-800/80 leading-relaxed">
                                <p>Sistem memproteksi database agar tidak terjadi tabrakan login atau data ganda.</p>
                                <p><strong>Solusi:</strong> Cari baris data yang dilaporkan error di Excel Anda. Pastikan NIS/Email yang digunakan belum pernah terdaftar sama sekali di sekolah. Jika nama siswa tersebut sudah ada, pendaftaran baris ini bisa dilewati.</p>
                            </div>
                        </div>

                        <!-- Accordion 3 -->
                        <div class="border border-slate-200/60 dark:border-slate-800/80 rounded-xl overflow-hidden">
                            <button @click="openAccordion = openAccordion === 3 ? null : 3" class="w-full flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/30 text-left text-xs font-bold text-slate-800 dark:text-slate-200">
                                <span>3. Muncul error "DUDI/Perusahaan belum terdaftar" saat impor Pembimbing DUDI</span>
                                <i data-lucide="chevron-down" :class="openAccordion === 3 ? 'rotate-185' : ''" class="w-4 h-4 transition-transform text-slate-400"></i>
                            </button>
                            <div x-show="openAccordion === 3" class="p-4 text-xs text-slate-600 dark:text-slate-400 space-y-1 border-t border-slate-200/60 dark:border-slate-800/80 leading-relaxed">
                                <p>Kolom <strong>nama_perusahaan</strong> pada mentor/Pembimbing DUDI bertindak sebagai kunci relasi. Sistem tidak dapat menugaskan mentor ke perusahaan yang belum terdaftar.</p>
                                <p><strong>Solusi:</strong> Daftarkan perusahaan tempat mentor tersebut bekerja terlebih dahulu (melalui menu Kelola DUDI). Pastikan ejaan nama perusahaan di data mentor sama persis dengan yang ada di sistem kelola DUDI.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ensure lucide icons are updated when state switches -->
    <script>
        document.addEventListener('alpine:init', () => {
            lucide.createIcons();
        });
        window.addEventListener('load', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</x-app-layout>
