# CHANGELOG

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
