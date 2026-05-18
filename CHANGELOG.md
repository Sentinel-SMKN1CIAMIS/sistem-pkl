# CHANGELOG

## [2026-05-18]
### Added
- Implementasi fitur **Multi-select Konsentrasi Keahlian** pada form tambah/edit DUDI di role Pokja menggunakan tampilan daftar *checkbox* yang responsif.
- Pembuatan tabel pivot `dudi_konsentrasi_keahlian` untuk memetakan hubungan *Many-to-Many* antara DUDI dengan Konsentrasi Keahlian.
- Penambahan fitur **Feedback Pembimbing DUDI** yang memungkinkan Pembimbing DUDI memberikan masukan/evaluasi umum (bersifat opsional) per periode rekap (mingguan/bulanan) kepada sekolah.
- Halaman **Feedback DUDI** di role Pokja untuk memantau masukan/evaluasi yang masuk dari mitra industri.

### Fixed
- Perbaikan layout halaman secara menyeluruh (*responsive design*) untuk dukungan optimal sebagai *Progressive Web App* (PWA) di perangkat *mobile*.
- Penambahan class `whitespace-nowrap` pada seluruh sel tabel (`<th>` dan `<td>`) di semua halaman untuk mencegah teks turun (*wrap*) dan merusak struktur kontainer saat diakses dari layar kecil.
- Memastikan seluruh tabel data dibungkus dalam kontainer `overflow-x-auto` yang memungkinkan pengguliran horizontal (*scrollable*) di perangkat *mobile*.
## [2026-05-11]
### Added
- Implementasi sistem **Tipe Pembimbing Sekolah** (Enum: Normatif, Adaptif, Produktif) pada tabel `pembimbing_sekolahs` untuk kategorisasi guru bimbingan.
- Pembuatan fitur **Profil Siswa** (`Siswa\ProfileController`) yang memungkinkan siswa menginput secara manual nama dan jabatan **Pembimbing Industri (DUDI)** jika belum terdaftar di sistem.
- Penambahan fitur **Filter Pencarian** (Nama/NIS) dan **Filter Konsentrasi Keahlian** pada halaman daftar siswa dan DUDI di role Pokja.
- Implementasi fitur **Rekapitulasi Kehadiran** pada role Pembimbing Sekolah dengan fitur filter (rentang tanggal & nama siswa) serta fitur **Ekspor PDF** menggunakan DomPDF.
- Penambahan menu "Lihat Siswa" pada daftar pembimbing sekolah untuk memudahkan Pokja memantau ploting siswa bimbingan.
- Integrasi **Buku Pedoman PKL 2025-2026** langsung ke dalam dashboard siswa menggunakan `<iframe>` dengan proteksi toolbar.

### Changed
- Field **Nama Pimpinan/HRD** pada data DUDI kini bersifat opsional (`nullable`) untuk fleksibilitas input data.
- Relokasi hak akses **Validasi Jurnal** harian siswa: kini hanya dapat dilakukan oleh **Pembimbing Sekolah** (Guru), sementara Pembimbing DUDI hanya diberikan akses *view-only* (Review).
- Pemindahan rute dan logika notifikasi validasi jurnal dari controller Pembimbing DUDI ke `PembimbingSekolah\JurnalController`.

### Fixed
- Sinkronisasi status kehadiran harian ("Masuk Kerja", "Pulang Kerja") agar muncul secara real-time di seluruh tabel monitoring siswa berdasarkan aktivitas absensi terbaru.
- Perbaikan query pagination pada tabel-tabel utama agar tetap mempertahankan parameter filter saat berpindah halaman (`withQueryString`).

## [2026-04-22]
### Changed
- Perubahan nama aplikasi dari **Simbiosis** menjadi **MAS-PKL** di seluruh sistem (Konfigurasi, Halaman Login, dan Layout Utama).
- Penyesuaian `APP_NAME` pada file `.env` untuk sinkronisasi identitas aplikasi.
- Perubahan layout form **Tambah Jurnal Harian**: field "Foto Bukti Kegiatan" dan "Catatan Detail / Kendala" kini ditampilkan secara *grid* berdampingan (foto kiri, catatan kanan).

### Added
- Penambahan `@stack('scripts')` pada layout utama (`app.blade.php`) agar seluruh skrip `@push('scripts')` di halaman Blade dapat dieksekusi dengan benar.
- Inisialisasi ulang **Lucide Icons** via `DOMContentLoaded` di layout utama untuk memastikan ikon selalu ter-render.
- Pembuatan *storage symlink* (`php artisan storage:link`) dan direktori `signatures/` untuk penyimpanan tanda tangan digital.
- Implementasi fitur **Crop Foto 1:1** pada form Tambah Jurnal menggunakan **Cropper.js** dengan popup modal preview, tombol rotasi, serta pratinjau hasil crop sebelum submit.
- Penyimpanan foto jurnal hasil crop sebagai **Base64** yang di-decode di server dan disimpan sebagai PNG.

### Fixed
- Perbaikan fitur **Tanda Tangan Digital** pada halaman Absensi Siswa — skrip `SignaturePad` dan logika JavaScript tidak pernah dieksekusi karena `@stack('scripts')` hilang dari layout utama.
- Perbaikan JavaScript signature pad: inisialisasi di dalam `DOMContentLoaded`, *debounced resize*, preserve data saat resize, `touch-action: none` untuk mobile, dan error handling geolokasi.
- Perbaikan bug foto jurnal **tidak tersimpan** — field form `kegiatan` tidak dipetakan ke kolom database `deskripsi_pekerjaan` pada controller `JurnalController@store`.
- Perbaikan bug data kegiatan tidak muncul pada daftar jurnal dan hasil export PDF dengan mengganti properti lama `$item->kegiatan` menjadi `$item->deskripsi_pekerjaan`.
- Perbaikan bug foto jurnal **tidak tersimpan** pada halaman Tambah Jurnal Harian — foto hasil crop tidak terkirim ke server karena field form `foto_bukti` tidak memiliki atribut `name`.
- Perubahan format angka "Rata-rata Jurnal Valid" pada halaman Evaluasi Progres Siswa agar ditampilkan sebagai angka bulat (integer).
- Perubahan tampilan kolom "Progress Jurnal" dari menggunakan format pecah (contoh: 1/10) menjadi menggunakan kata hubung (contoh: 1 dari 10).

### Added
- Penambahan fitur input form **Link Media Sosial** dinamis (seperti link YouTube atau TikTok) pada Laporan Akhir PKL Siswa. Pengguna dapat menambahkan hingga 5 link dengan menekan tombol "Tambah Link" menggunakan Alpine.js.
- Pembuatan migrasi database untuk menambahkan field `link_media_sosial` berformat `json` ke dalam tabel `laporan_pkls` guna mendukung multi-link.
- Pembuatan halaman **Evaluasi Laporan Akhir** (`PembimbingSekolah\LaporanController`) beserta *routes* dan menu navigasinya, sehingga Pembimbing Sekolah (Guru) dapat melihat dan meninjau (*Approve/Reject*) laporan akhir beserta tautan eksternal yang diunggah siswa bimbingannya.
- Pembuatan fitur penanda "Status Hari Ini" yang dinamis berdasarkan aktivitas **Absensi** harian. Status "Sedang PKL" pada _dashboard_ siswa dan tabel daftar siswa (baik bagi Pembimbing DUDI, Pembimbing Sekolah, maupun Pokja) kini secara otomatis berubah menjadi "Masuk Kerja", "Pulang Kerja", atau "Belum Absen" mengikuti rekam jejak absensi hari tersebut.

### Changed
- Pengisian Laporan Akhir PKL kini sepenuhnya bergantung pada tautan eksternal dan ringkasan teks.

### Removed
- Menghapus fitur unggah dokumen (PDF/Docx) beserta tombol "Lihat File" dari halaman Laporan Akhir PKL, karena laporan kini difokuskan menggunakan link media sosial atau platform eksternal. Kolom `file_path` pada tabel `laporan_pkls` juga dihapus via migrasi.


## [2026-04-16]
### Added
- Implementasi sistem beralih tema (Light, Dark, dan System) secara menyeluruh menggunakan Alpine.js.
- Refaktor antarmuka besar-besaran dengan menghapus seluruh efek "glassmorphism" dan "blur" sesuai preferensi desain solid.
- Sinkronisasi warna teks global di 52+ file Blade agar mendukung keterbacaan di Mode Terang dan Gelap.
- Implementasi fitur Daftar Siswa Bimbingan untuk Pembimbing Sekolah dan Pembimbing DUDI.
- Perbaikan berbagai peringatan (*linting*) Tailwind CSS v4 untuk optimalisasi performa dan standar kode.
- Pembersihan skrip refaktor sementara dari direktori utama.
- Inisialisasi CHANGELOG.md sesuai dengan aturan proyek.
- Implementasi status "Memproses" dan validasi otomatis pada tombol submit menggunakan Alpine.js.
- Komponen Blade `<x-button>` untuk standarisasi tombol di seluruh aplikasi.
- Logika global pada `app.js` untuk menangani status loading form secara otomatis.
- Fitur pesan validasi real-time pada komponen `<x-button>` jika form tidak valid.
- Penggunaan `<x-button>` pada halaman login, logout, create user admin, dan absensi siswa.
- Penambahan varian warna 'emerald' dan 'orange' pada komponen `<x-button>`.
- Relokasi pesan validasi form dari bawah tombol ke teks di dalam tombol itu sendiri pada komponen `<x-button>`.
- Implementasi logika pesan validasi dinamis pada halaman login (Isi username/NIS/NIP, Isi Password, atau keduanya) yang ditampilkan langsung pada tombol.
- Penambahan prop `errorText` pada komponen `<x-button>` untuk kustomisasi pesan error inline.
- Implementasi fitur show/hide password pada halaman login untuk meningkatkan UX.
- Perbaikan fatal error "namespace already in use" pada `NotifikasiController`.
- Perbaikan missing PHP tag dan namespace pada `AbsensiController` (Siswa) dan `BukuPanduanController` (Admin).
- Pembersihan duplikasi import (Request & Controller) pada `LaporanController` dan `PanduanController`.
- Implementasi sistem **Audit Log (Log Aktivitas)** kustom untuk melacak aktivitas pengguna.
- Pembuatan Trait `Auditable` untuk pemantauan otomatis pada model `User`, `Jurnal`, dan `Kompetensi`.
- Implementasi Event Listener untuk pencatatan otomatis aktivitas **Login** dan **Logout**.
- Pembuatan halaman **Log Sistem** di dashboard admin dengan antarmuka premium dan fitur pagination.
- Penambahan `ActivityLogSeeder` untuk inisialisasi data log sistem.


### Fixed
- Perbaikan fatal error pada `UserController` (Admin) akibat hilangnya tag pembuka PHP `<?php` dan namespace declaration.
- Perbaikan navigasi "Kelola Pengguna" pada sidebar admin agar mengarah ke route yang valid.
- Perbaikan `RelationNotFoundException` pada halaman Kelola Kompetensi dengan mendefinisikan relasi `konsentrasiKeahlian` pada model `Kompetensi`.
