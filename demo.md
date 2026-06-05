# 🚀 Panduan Alur Demo Aplikasi MAS-PKL

Gunakan panduan ini untuk hafalan presentasi besok. Alur ini dirancang terstruktur dari awal pendaftaran siswa hingga penempatan aktif.

---

## 🔑 Kredensial Akun (Password semua akun: `password`)

| Peran (Role) | Username / Email | Deskripsi / Kondisi Awal |
| :--- | :--- | :--- |
| **Siswa Baru (Demo)** | `siswa` / `siswa@gmail.com` | Belum memiliki tempat PKL dan pembimbing sekolah |
| **Kaprog** | `kaprog` / `kaprog@gmail.com` | Kepala Program Keahlian (Penyetuju tempat PKL) |
| **Pokja** | `pokja` / `pokja@gmail.com` | Panitia Kelompok Kerja PKL (Pemeta Guru Pembimbing) |

---

## 🔄 Tahapan Alur Demo (Step-by-Step)

### Langkah 1: Siswa Mengajukan Tempat PKL
1. **Login** menggunakan akun Siswa:
   * **Username:** `siswa`
   * **Password:** `password`
2. **Lihat Deteksi Otomatis:**
   * Karena siswa ini belum memiliki tempat PKL, sistem akan otomatis mengarahkan ke halaman **Pengajuan Tempat PKL** (Siswa tidak bisa masuk dashboard utama sebelum mengajukan).
3. **Isi Form Pengajuan:**
   * Di field **Tempat PKL (DUDI)**, pilih opsi `[+] Lainnya / Input Tempat PKL Baru`.
   * Ketik data instansi baru secara manual (misal: *BMTI*, Alamat: *Jl. Bandung*, Kota: *Bandung*).
4. **Kirim Pengajuan:**
   * Klik tombol **Kirim Pengajuan**. Halaman akan berpindah menampilkan status **"Menunggu Persetujuan"**.

---

### Langkah 2: Kaprog Menyetujui Pengajuan
1. **Login** menggunakan akun Kaprog:
   * **Username:** `kaprog`
   * **Password:** `password`
2. **Tinjau Pengajuan:**
   * Masuk ke menu **Pengajuan PKL** di sidebar.
   * Temukan nama siswa **Siswa Demo Flow** yang mengajukan ke instansi *BMTI*.
   * Klik tombol **Tinjau**.
3. **Lakukan Approval:**
   * Pilih opsi **Setujui** dan klik **Simpan**.
   * *Catatan Penting (untuk bahan omongan presentasi):* "Karena siswa menginput instansi secara manual, ketika Kaprog menyetujui, sistem secara otomatis menambahkan instansi baru tersebut ke database DUDI utama."

---

### Langkah 3: Pokja Memetakan Pembimbing
1. **Login** menggunakan akun Pokja:
   * **Username:** `pokja`
   * **Password:** `password`
2. **Periksa Notifikasi Lonceng:**
   * Tunjukkan fitur lonceng notifikasi di pojok kanan atas. Akan muncul pesan: *"Siswa Siswa Demo Flow telah disetujui di BMTI. Silakan lakukan pemetaan pembimbing."*
3. **Lakukan Pemetaan:**
   * Klik notifikasi tersebut atau buka menu **Data Siswa**.
   * Klik tombol **Edit** pada baris data **Siswa Demo Flow**.
   * Di bagian kanan form, pilih **Guru Pembimbing Sekolah** (misal: *Budi Santoso, S.Kom*).
   * Klik **Simpan Perubahan**.
   * *Catatan Penting (Otomatisasi Status):* "Sistem otomatis mengubah status PKL siswa dari 'Belum Mulai' menjadi 'Sedang PKL' karena DUDI dan Guru Pembimbingnya sudah lengkap. Pokja tidak perlu mengubah status secara manual satu per satu."

---

### Langkah 4: Pembuktian di Akun Siswa (PKL Aktif)
1. **Login Kembali** sebagai Siswa:
   * **Username:** `siswa`
   * **Password:** `password`
2. **Lihat Dashboard Baru:**
   * Siswa tidak lagi diarahkan ke halaman pengajuan, melainkan masuk ke **Dashboard Utama**.
   * Tombol **Daftar Hadir (Absensi)** dan menu **Jurnal Kegiatan** sekarang sudah aktif dan siap digunakan siswa untuk mencatat jurnal harian.

---

## 💡 Poin Nilai Tambah Saat Presentasi (Key Selling Points)
* **Keamanan Akun:** Tidak ada tombol registrasi publik untuk siswa; pembuatan akun dikendalikan penuh oleh Pokja/Admin.
* **Otomatisasi DUDI:** DUDI manual yang disetujui Kaprog langsung masuk database agar tidak terjadi duplikasi data.
* **Notifikasi Pintar:** Koordinasi antara Kaprog dan Pokja dijembatani otomatis lewat sistem notifikasi real-time.
* **Efisiensi Kerja Pokja:** Status "Sedang PKL" berubah otomatis saat pembimbing dipetakan, menghemat waktu kelola ratusan siswa.
