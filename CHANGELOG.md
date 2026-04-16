# CHANGELOG

## [2026-04-16]
### Added
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
