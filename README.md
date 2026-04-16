# SIPKL - Sistem Informasi Praktek Kerja Lapangan

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15.0-4479A1?style=for-the-badge&logo=postgresql&logoColor=white)

**Solusi Digital Terpadu untuk Manajemen Praktek Kerja Lapangan**  
SIPKL SMKN 1 Ciamis - Empowering Vocational Excellence

[Fitur](#-fitur-utama) • [Instalasi](#-instalasi) • [Alur Sistem](#-workflow-sistem) • [Credentials](#-default-credentials) • [Team](#-team-pengembang)

</div>

---

## Tentang Project

**SIPKL (Sistem Informasi Praktek Kerja Lapangan)** adalah platform manajemen PKL yang dirancang untuk mendigitalisasi seluruh ekosistem prakerin (Praktek Kerja Industri). Mulai dari pemetaan penempatan siswa, monitoring harian melalui jurnal digital, hingga evaluasi akhir oleh pembimbing sekolah dan industri.

Aplikasi ini bertujuan untuk menciptakan transparansi, akurasi data, dan efisiensi komunikasi antara pihak Sekolah, Siswa, dan Dunia Usaha/Dunia Industri (DUDI).

### Tujuan Utama

- **Digitalisasi Administrasi**: Menghilangkan penggunaan kertas dalam pengisian jurnal dan absensi.
- **Monitoring Real-time**: Memungkinkan pembimbing sekolah memantau kehadiran dan aktivitas siswa secara langsung.
- **Efisiensi Pemetaan**: Mempermudah Pokja PKL dalam mengelola kuota penempatan di berbagai DUDI.
- **Penilaian Terintegrasi**: Menggabungkan nilai teknis dari industri dan nilai akademis dari sekolah dalam satu dashboard.

---

## Fitur Utama

### Portal Siswa
- **Jurnal Harian**: Pengisian aktivitas PKL dilengkapi dengan upload dokumentasi kegiatan.
- **Digital Attendance**: Absensi berbasis sistem untuk mencatat kehadiran di lokasi DUDI.
- **Status Penempatan**: Melihat informasi detail DUDI dan pembimbing yang ditugaskan.
- **Notifikasi**: Mendapatkan informasi terbaru mengenai jadwal monitoring atau evaluasi.

### Portal Pembimbing Sekolah
- **Monitoring Dashbord**: Pantau grafik kehadiran dan progres jurnal siswa bimbingan.
- **Verifikasi Aktivitas**: Memberikan komentar atau validasi pada jurnal harian siswa.
- **Evaluasi Monitoring**: Input hasil kunjungan monitoring langsung ke sistem.

### Portal Pembimbing DUDI
- **Approval Jurnal/Absensi**: Memverifikasi kebenaran aktivitas siswa di lokasi industri.
- **Penilaian Industri**: Memberikan penilaian terhadap kompetensi dan soft skill siswa selama PKL.

### Manajemen Pokja & Admin
- **Pemetaan Siswa (Mapping)**: Fitur smart-mapping untuk membagi siswa ke tempat PKL yang sesuai.
- **Master Data**: Kelola data Siswa, Guru, DUDI, dan Konsentrasi Keahlian.
- **Audit Log Detail**: Rekam jejak seluruh aktivitas user untuk keamanan dan transparansi.
- **Configurable System**: Pengaturan tahun ajaran, kuota penempatan, dan periode PKL.

---

## Tech Stack

### Backend & Core
- **Framework**: Laravel 13
- **Database**: PostgreSQL 18
- **PDF Engine**: Barryvdh Laravel DomPDF
- **Excel Processor**: Maatwebsite Excel

### Frontend & UI/UX
- **Modern UI**: Tailwind CSS
- **Interactions**: SweetAlert2, FontAwesome 6
- **Typography**: Google Fonts (Inter & Outfit)
- **Asset Manager**: Vite 6.x

---

## Instalasi

### 1. Persiapan Lingkungan
Pastikan Anda memiliki **PHP 8.3+**, **Composer**, **Node.js**, dan database **PostgreSQL** yang sudah terinstall.

### 2. Clone & Setup
```bash
git clone https://github.com/Sentinel-SMKN1CIAMIS/sistem-pkl.git
cd sistem-pkl

# Install dependencies
composer install
npm install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```
Edit file `.env` dan sesuaikan konfigurasi database Anda.

```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_pkl_jurnal
DB_USERNAME=postgres
DB_PASSWORD=
```

### 4. Database & Storage
```bash
# Migrasi dan Seed data awal
php artisan migrate --seed --class=UserSeeder

# Hubungkan folder storage
php artisan storage:link
```

### 5. Compile Assets & Run
```bash
npm run build # Untuk produksi
# atau
npm run dev # Untuk pengembangan

# Jalankan server
php artisan serve
```

---

## Default Credentials

| Akun | Username | Password |
| :--- | :--- | :--- |
| **Super Admin** | `admin` | `password` |
| **Ketua Pokja** | `pokja` | `password` |
| **Pembimbing Sekolah** | `guru` | `password` |
| **Pembimbing DUDI** | `mentor` | `password` |
| **Siswa** | `2223101` | `password` |

> [!WARNING]
> Segera ubah password default Anda pada menu Profil setelah login pertama kali di lingkungan produksi.

---

## Team Pengembang

SIPKL dikembangkan oleh **Tim RPL Sentinel SMKN 1 Ciamis**:

<table align="center">
  <tr>
    <td align="center">
      <a href="https://github.com/PradiptaPPLG" target="_blank">
        <img src="https://github.com/PradiptaPPLG.png" width="100px;" alt="Pradipta"/><br />
        <sub><b>Pradipta Endra Maulana</b></sub>
      </a><br />
      <sub>Lead Developer</sub>
    </td>
    <td align="center">
      <a href="https://github.com/rasyakt" target="_blank">
        <img src="https://github.com/rasyakt.png" width="100px;" alt="Rasya Syahreza"/><br />
        <sub><b>Rasya Syahreza Maulana Zen</b></sub>
      </a><br />
      <sub>Full Stack Developer</sub>
    </td>
    <td align="center">
      <a href="https://github.com/rafliaditya0125" target="_blank">
        <img src="https://github.com/rafliaditya0125.png" width="100px;" alt="Rafli Aditya"/><br />
        <sub><b>Rafli Aditya</b></sub>
      </a><br />
      <sub>Backend Developer</sub>
    </td>
    <td align="center">
      <a href="https://github.com/ZidnyAl-HikamMawarist" target="_blank">
        <img src="https://github.com/ZidnyAl-HikamMawarist.png" width="100px;" alt="Zidny Al-Hikam"/><br />
        <sub><b>Zidny Al-Hikam M.</b></sub>
      </a><br />
      <sub>Busines Analyst & QA Engineer</sub>
    </td>
  </tr>
</table>

### Institusi Penyelenggara

**SMKN 1 Ciamis**  
*Vocational High School - Center of Excellence*  
Jl. Jenderal Sudirman №269, Ciamis, Jawa Barat.

---

<div align="center">

**Developed by Tim RPL Sentinel**  
© 2026 RPL SMKN 1 Ciamis

[Kembali ke Atas](#-sipkl---sistem-informasi-praktek-kerja-lapangan)

</div>
