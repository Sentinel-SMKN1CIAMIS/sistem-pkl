# CHANGELOG

## [2026-06-16]
### Fixed
- Memperbaiki test case `ExampleTest` agar memvalidasi redirect ke `/login` (302) alih-alih status 200 sukses karena rute `/` di-redirect ke `/login`.
- Memperbaiki penanganan error logging pada `ChangePasswordController` dengan memindahkan `$request->validate()` ke dalam blok `try` utama dan menangkap `ValidationException` secara eksplisit untuk memastikan kegagalan validasi password dicatat sebagai aktivitas `Password Change Failed` di database log.
- Memperbaiki test setup pada `KaprogAccessControlTest` dengan menambahkan properti `program_keahlian_id` saat pembuatan user Kaprog bimbingan agar sinkron dengan logika filter relasi program keahlian pada controller `KaprogController` dan `PengajuanPklController`.
- Memperbaiki rute `/siswa/jurnal/portofolio`, `/siswa/jurnal/export`, dan `/siswa/jurnal/sertifikat` yang sebelumnya salah dipetakan ke wildcard `Route::resource('jurnal', ...)` (menyebabkan error `Call to undefined method JurnalController::show()`) dengan memindahkan definisi rute-rute tersebut ke sebelum rute resource.
- Memperbaiki bug layout shift yang menyebabkan sidebar menciut/bergeser saat modal dialog SweetAlert2 muncul dengan memindahkan kelas flex dari tag `<body>` ke div wrapper penampung konten utama.



### Added
- Integrasi library **SweetAlert2** secara global pada layout aplikasi (`app.blade.php` dan `guest.blade.php`).
- Implementasi global override untuk `window.alert(...)` agar menampilkan dialog modal info yang modern dan premium dengan tema warna yang sesuai.
- Implementasi interceptor dinamis untuk `confirm(...)` pada form `onsubmit` dan tombol/tautan `onclick` guna menggantikan dialog konfirmasi bawaan browser dengan modal SweetAlert2 interaktif.
- Implementasi rendering otomatis untuk notifikasi Laravel (`session('success')`, `session('error')`, `$errors->any()`) menjadi toast notification melayang di pojok kanan atas layar.
- Penambahan konfirmasi keluar sistem (logout) menggunakan SweetAlert2 saat user menekan tombol Logout.
- Penambahan mekanisme anti-BFCache global via event listener 'pageshow' pada layout utama dan guest untuk mendeteksi navigasi tombol back/forward browser, memaksa reload halaman, dan memicu proteksi rute middleware Laravel (auth & guest) secara dinamis.
- Implementasi fitur pengurutan data secara drag-and-drop pada halaman kelola **Program Keahlian** dan **Konsentrasi Keahlian** di bawah hak akses Admin/Pokja menggunakan library **Sortable.js** dan penyimpanan urutan via request AJAX ke route baru.
- Penambahan kolom `sort_order` pada tabel database `program_keahlians` dan `konsentrasi_keahlians` beserta berkas migrasinya.
- Implementasi fitur **Select All + Bulk Delete** pada halaman **Kelola Pengguna Sistem** (Users) untuk memudahkan administrator menghapus banyak akun secara massal dengan proteksi keamanan (mencegah penghapusan akun diri sendiri).

### Changed
- Mengubah tema default website menjadi **Light Mode**. Jika pengguna belum pernah memilih tema secara manual (tidak ada preferensi di `localStorage`), maka website akan memuat tema terang secara default (alih-alih mengikuti settingan dark mode bawaan OS/system). Pilihan tema manual ("Light", "Dark", dan "System") tetap dipertahankan untuk fleksibilitas pengguna.
- Menghapus pembatasan visual ukuran maksimum file 4MB pada semua fitur impor data Excel (Siswa, DUDI, Pembimbing Sekolah, Pembimbing DUDI, dan Kaprog) serta memperbarui teks petunjuk format menjadi "Format file yang didukung: .xlsx, .xls saja".
- Menambahkan fitur kompresi foto jurnal harian otomatis di sisi klien (CropperJS canvas diekspor sebagai JPEG 85%) dan di sisi server (PHP GD extension mengompresi gambar base64 ke JPEG 85% sebelum disimpan) untuk menghemat ruang penyimpanan server secara drastis tanpa mengurangi ketajaman gambar secara kasat mata.



## [2026-06-06] - Modul 4 & 7
### Added
- Implementasi fitur **Cetak Sertifikat PKL** pada halaman Jurnal Siswa yang menghasilkan sertifikat dalam format PDF menggunakan DomPDF.
- Pembuatan halaman **Pengaturan Template Sertifikat** (`PengaturanController`) pada dashboard Pokja yang memungkinkan edit teks paragraf pembuka sertifikat secara dinamis.
- Penambahan widget **Monitoring Kaprog** pada dashboard Kepala Program untuk memantau metrik kehadiran harian, rasio pengisian jurnal, serta daftar siswa yang belum berpartisipasi.
- Penambahan fitur tersembunyi **Bulk ACC** (Rapid Testing) pada dashboard Pembimbing Sekolah (diaktifkan dengan *triple-click* pada judul dashboard) yang menyetujui semua jurnal dan absensi tertunda secara massal.

### Changed
- Perubahan logika pencarian "Siswa PKL" bagi **Pembimbing DUDI**: pencarian siswa kini menggunakan `dudi_id` alih-alih `pembimbing_dudi_id` agar mentor tetap dapat melihat siswa yang belum diplot spesifik ke dirinya namun berada di perusahaan yang sama.
- Pembaruan mekanisme proteksi akses aplikasi Siswa (Jurnal, Absensi, Laporan): siswa kini diwajibkan untuk berada pada status **Sedang PKL** atau **Selesai** (menandakan Surat Pengantar sudah di-ACC Kaprog dan DUDI membalas terima) sebelum dapat menggunakan fitur.

### Added (Modul 4 - Pemetaan)
- Implementasi **Peta Sebaran DUDI** menggunakan **Leaflet.js** dan **OpenStreetMap** dengan tampil di halaman `Peta DUDI` untuk role Pokja, Kaprog, dan Pembimbing Sekolah.
- Integrasi **Leaflet.markercluster** untuk menghindari penumpukan marker saat peta di-zoom out; marker yang berdekatan otomatis menggumpal menjadi satu lingkaran angka.
- Implementasi **Hover Tooltip** pada setiap marker DUDI yang menampilkan ringkasan jurusan siswa (nama jurusan dan jumlah siswa) saat mouse diarahkan di atas marker tanpa klik.
- Implementasi **Click Popup** detail lengkap saat marker diklik: Nama DUDI, Alamat, Jenis Industri, Pimpinan, Telepon, Zona, serta daftar rincian nama seluruh siswa yang sedang PKL di sana.
- Pembedaan warna marker berdasarkan **Jenis Industri** (Pemerintahan=Merah, Industri=Biru, Layanan=Ungu, Perdagangan=Kuning, Pendidikan=Hijau, Kesehatan=Pink, Teknologi=Cyan, Pertanian=Lime, Lainnya=Abu-abu) dilengkapi legenda visual.
- Pembuatan tabel database **`zonas`** untuk menyimpan data zona wilayah (nama, warna isi, warna border, koordinat GeoJSON polygon, nomor zona).
- Implementasi halaman **Kelola Zona** untuk Pokja menggunakan plugin **Leaflet Draw**, memungkinkan Pokja menggambar polygon zona langsung di atas peta, memilih warna, dan menyimpan ke database.
- Rendering **Polygon Zona** di peta sebaran DUDI dengan warna semi-transparan dan garis batas putus-putus (*dashed stroke/border*).
- Implementasi algoritma **Point-in-Polygon (Ray-Casting)** pada model `Zona` untuk mendeteksi secara otomatis zona wilayah sebuah DUDI berdasarkan koordinatnya.
- Auto-deteksi zona berjalan otomatis setiap kali koordinat DUDI diperbarui (baik oleh Siswa maupun Pokja).
- Penambahan fitur **Update Lokasi DUDI via GPS** pada halaman Profil Siswa: siswa cukup memencet tombol "Update Lokasi Saat Ini" dan koordinat GPS ponsel mereka otomatis disimpan sebagai lokasi DUDI tempatnya PKL.
- Penambahan **Mini-map Interaktif (Leaflet)** pada form Edit DUDI di dashboard Pokja sebagai *fallback* untuk menentukan/menggeser koordinat secara manual dengan klik atau drag marker.
- Penambahan kolom `zona_id` (FK) pada tabel `dudis` dengan relasi ke tabel `zonas`.
- Penambahan menu **Peta DUDI** dan **Kelola Zona** pada sidebar Pokja, serta menu **Peta DUDI** pada sidebar Kaprog dan Pembimbing Sekolah.

## [2026-05-22]
### Changed
- **Relokasi Hak Validasi Jurnal**: Validasi (Valid/Invalid) jurnal harian kini sepenuhnya menjadi tanggung jawab **Pembimbing DUDI (Mentor Industri)**. Pembimbing Sekolah (Guru) **tidak lagi bisa** memvalidasi jurnal.
- **Pembimbing Sekolah → Komentar Saja**: Halaman "Monitoring Jurnal" untuk Pembimbing Sekolah diubah total: tombol Validasi/Tolak dihapus, diganti form "Kirim Saran" yang selalu tampil di setiap kartu jurnal (semua status).
- **Aturan 1 Hari 1 Jurnal**: Siswa kini hanya dapat mengisi 1 jurnal per hari. Jika sudah mengisi, tombol tambah jurnal akan diblokir dengan pesan error yang jelas.
- Penghapusan fitur "Validasi Semua Jurnal" dari panel Pembimbing Sekolah (route dan method `validasiSemua` dihapus).

## [2026-05-20]
### Changed
- Pemindahan menu **Profil Saya** dan tombol **Logout** dari sidebar utama ke dalam *dropdown* (trigger klik pada profil) di bagian bawah sidebar untuk meningkatkan UX dan kerapian navigasi (diimplementasikan untuk semua role).
- Penambahan validasi "Unsaved Changes" global menggunakan event `beforeunload` untuk mencegah hilangnya input form yang belum disimpan saat berpindah halaman secara tidak sengaja.
- Penambahan kolom `unit_pekerjaan` di tabel `siswas` dan penambahan *input field* "Unit / Bagian Pekerjaan" di halaman **Profil Saya**, sehingga penentuan unit kerja kini sepenuhnya diisi mandiri oleh siswa.
- Penambahan section **Contoh Sertifikat PKL** di bagian bawah halaman **Laporan Akhir PKL** (siswa) sebagai referensi visual, lengkap dengan fitur *lightbox* (klik untuk memperbesar) menggunakan Alpine.js.
- Penambahan validasi absensi pada pengisian jurnal harian: Siswa kini wajib melakukan absen datang (clock-in) sebelum dapat membuka form pengisian jurnal, dan tanggal jurnal harus sesuai dengan tanggal absensi yang valid.
- Relokasi hak akses **Validasi Jurnal**: Validasi (Valid/Invalid) kini menjadi tanggung jawab penuh **Pembimbing DUDI**, sementara **Pembimbing Sekolah** hanya diberikan akses untuk memberikan komentar (`catatan_guru`) tanpa dapat merubah status jurnal.
- Penambahan fitur Pembimbing Sekolah dapat memberikan saran/komentar (catatan guru) kepada murid yang dibimbingnya.
- Penambahan aturan "1 HARI 1 JURNAL": Siswa hanya dapat mengisi jurnal 1 kali per hari, dan harus melakukan absen terlebih dahulu.

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
