<?php

namespace Database\Seeders;

use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CpTpPklSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Elemen dan CP-nya (sama untuk semua jurusan).
     *
     * @var array<string, string>
     */
    protected array $elemenCp = [
        'Internalisasi dan Penerapan Soft Skills' => 'Pada akhir fase F, peserta didik mampu menerapkan etika berkomunikasi secara lisan dan tulisan, integritas (antara lain jujur, disiplin, komitmen, dan tanggung jawab), etos kerja, bekerja secara mandiri dan/atau bekerja di dalam tim, kepedulian sosial dan lingkungan, serta ketaatan terhadap norma, K3LH, dan POS yang berlaku di dunia kerja.',
        'Penerapan Hard Skills' => 'Pada akhir fase F, peserta didik mampu menerapkan kompetensi teknis pada pekerjaan sesuai POS yang berlaku di dunia kerja.',
        'Peningkatan dan Pengembangan Hard Skills' => 'Pada akhir fase F, peserta didik mampu menerapkan kompetensi teknis baru dan/atau kompetensi teknis yang belum tuntas dipelajari sesuai konsentrasi keahlian.',
        'Penyiapan Kemandirian Berwirausaha' => 'Pada akhir fase F, peserta didik mampu melakukan analisis usaha secara mandiri.',
    ];

    /**
     * Pemetaan konsentrasi sesuai arahan pengguna, termasuk AKL/PBS dan PBR/PBD.
     *
     * @var array<string, list<string>>
     */
    protected array $jurusanKonsentrasi = [
        'Akuntansi dan Keuangan Lembaga (Akuntansi)' => ['AKL', 'PBS'],
        'Pemasaran' => ['PBR', 'PBD'],
        'Manajemen Perkantoran dan Layanan Bisnis' => ['MPLB'],
        'Perhotelan' => ['HTL'],
        'Kuliner' => ['KLN'],
        'Desain Komunikasi Visual (DKV)' => ['DKV'],
        'Rekayasa Perangkat Lunak (RPL)' => ['RPL'],
    ];

    /**
     * Sumber: Data_Elemen_CP_TP_PKL_Per_Jurusan.xlsx, sheet Master_Import_Aplikasi.
     * Format: [jurusan, elemen, nomor TP dalam elemen, teks TP].
     *
     * @var list<array{0: string, 1: string, 2: int, 3: string}>
     */
    protected array $tps = [
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Internalisasi dan Penerapan Soft Skills', 1, 'Menerapkan etika berkomunikasi secara lisan dan tulisan di bidang Akuntansi'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Internalisasi dan Penerapan Soft Skills', 2, 'Menerapkan integritas (antara lain jujur, disiplin, komitmen, dan tanggung jawab), etos kerja di lingkungan kerja di bidang akuntansi'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Internalisasi dan Penerapan Soft Skills', 3, 'Bekerja secara mandiri dan/atau bekerja di dalam tim'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Internalisasi dan Penerapan Soft Skills', 4, 'Menerapkan kepedulian sosial dan lingkungan'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Internalisasi dan Penerapan Soft Skills', 5, 'Menerapkan ketaatan terhadap norma, K3LH, dan POS yang berlaku di dunia kerja di bidang akuntansi baik di pemerintahan maupun industri'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Penerapan Hard Skills', 1, 'Menganalisis bukti-bukti transaksi'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Penerapan Hard Skills', 2, 'Mengerjakan siklus akuntansi perusahaan jasa, dagang dan manufaktur'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Penerapan Hard Skills', 3, 'Menyusun laporan keuangan'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Penerapan Hard Skills', 4, 'Mengoperasikan paket program pengolah angka (spreadsheet)'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Penerapan Hard Skills', 5, 'Menerapkan standar akuntansi yang digunakan lembaga atau instansi pemerintah'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Penerapan Hard Skills', 6, 'Mengoperasikan program komputer akuntansi'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Penerapan Hard Skills', 7, 'Menyusun laporan pajak serta memahami ketentuan umum dan tata cara perpajakan (KUP)'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Peningkatan dan Pengembangan Hard Skills', 1, 'Menerapkan aplikasi komputer Akuntansi'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Peningkatan dan Pengembangan Hard Skills', 2, 'Menerapkan SOP pajak'],
        ['Akuntansi dan Keuangan Lembaga (Akuntansi)', 'Penyiapan Kemandirian Berwirausaha', 1, 'Menganalisis peluang wirausaha di bidang akuntansi pada lingkungan kerja'],
        ['Pemasaran', 'Internalisasi dan Penerapan Soft Skills', 1, 'Menerapkan etika berkomunikasi secara lisan dan tulisan dalam bidang pemasaran'],
        ['Pemasaran', 'Internalisasi dan Penerapan Soft Skills', 2, 'Menerapkan integritas (antara lain jujur, disiplin, komitmen, dan tanggung jawab), etos kerja di lingkungan kerja dalam bidang pemasaran'],
        ['Pemasaran', 'Internalisasi dan Penerapan Soft Skills', 3, 'Bekerja secara mandiri dan/atau bekerja di dalam tim dalam bidang pemasaran'],
        ['Pemasaran', 'Internalisasi dan Penerapan Soft Skills', 4, 'Menerapkan kepedulian sosial dan lingkungan serta ketaatan terhadap norma, dalam bidang pemasaran'],
        ['Pemasaran', 'Internalisasi dan Penerapan Soft Skills', 5, 'Menerapkan K3LH, dan POS yang berlaku di dunia kerja dalam bidang pemasaran'],
        ['Pemasaran', 'Penerapan Hard Skills', 1, 'Melakukan strategi marketing'],
        ['Pemasaran', 'Penerapan Hard Skills', 2, 'Melaksanakan standar pelayanan, service excellent'],
        ['Pemasaran', 'Penerapan Hard Skills', 3, 'Melaksanakan komunikasi dalam aktivitas bisnis'],
        ['Pemasaran', 'Penerapan Hard Skills', 4, 'Menerapkan proses bisnis ritel (ordering, receiving, warehousing, displaying, selling)'],
        ['Pemasaran', 'Penerapan Hard Skills', 5, 'Melakukan transaksi ritel, alat dan sistem transaksi'],
        ['Pemasaran', 'Penerapan Hard Skills', 6, 'Melakukan stock opname'],
        ['Pemasaran', 'Penerapan Hard Skills', 7, 'Melakukan pelabelan/barcode produk'],
        ['Pemasaran', 'Penerapan Hard Skills', 8, 'Menganalisis layout/planogram'],
        ['Pemasaran', 'Penerapan Hard Skills', 9, 'Melakukan proses pemajangan (facing up, filling in, quality control, sorter, floor display)'],
        ['Pemasaran', 'Penerapan Hard Skills', 10, 'Melakukan perawatan produk secara berkala'],
        ['Pemasaran', 'Penerapan Hard Skills', 11, 'Menganalisis stock floor'],
        ['Pemasaran', 'Penerapan Hard Skills', 12, 'Melakukan keamanan toko'],
        ['Pemasaran', 'Penerapan Hard Skills', 13, 'Mengevaluasi berbagai kemasan dan teknik pengemasan produk pelanggan'],
        ['Pemasaran', 'Penerapan Hard Skills', 14, 'Menggunakan dokumen pada proses ordering, receiving dan warehousing'],
        ['Pemasaran', 'Penerapan Hard Skills', 15, 'Mengidentifikasi prosedur penerimaan dan pengiriman barang'],
        ['Pemasaran', 'Penerapan Hard Skills', 16, 'Mengoperasikan alat ukur'],
        ['Pemasaran', 'Penerapan Hard Skills', 17, 'Melakukan kegiatan proses administrasi transaksi'],
        ['Pemasaran', 'Penerapan Hard Skills', 18, 'Merekap laporan transaksi penjualan'],
        ['Pemasaran', 'Peningkatan dan Pengembangan Hard Skills', 1, 'Menerapkan alur kerja pengelolaan bisnis ritel'],
        ['Pemasaran', 'Peningkatan dan Pengembangan Hard Skills', 2, 'Menerapkan SOP Kasir yang berlaku di tempat kerja'],
        ['Pemasaran', 'Peningkatan dan Pengembangan Hard Skills', 3, 'Menerapkan SOP Pramuniaga yang berlaku di tempat kerja'],
        ['Pemasaran', 'Peningkatan dan Pengembangan Hard Skills', 4, 'Melakukan aktivitas pemasaran digital'],
        ['Pemasaran', 'Penyiapan Kemandirian Berwirausaha', 1, 'Menganalisis peluang wirausaha pada lingkungan kerja dalam bidang pemasaran'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Internalisasi dan Penerapan Soft Skills', 1, 'Memahami soft skills pada lingkungan kerja di bidang manajemen perkantoran'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Internalisasi dan Penerapan Soft Skills', 2, 'Menerapkan soft skills pada lingkungan kerja di bidang manajemen perkantoran'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Internalisasi dan Penerapan Soft Skills', 3, 'Memahami norma, POS dan K3LH yang ada pada lingkungan kerja di bidang manajemen perkantoran'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Internalisasi dan Penerapan Soft Skills', 4, 'Menerapkan norma, POS dan K3LH yang ada pada lingkungan kerja di bidang manajemen perkantoran'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Penerapan Hard Skills', 1, 'Menerapkan kolaborasi dalam tim untuk menerapkan pekerjaan administrasi perkantoran'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Penerapan Hard Skills', 2, 'Menerapkan korespondensi Bahasa Indonesia dan Bahasa Inggris'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Penerapan Hard Skills', 3, 'Mengatur perjalanan dinas'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Penerapan Hard Skills', 4, 'Melakukan pengelolaan jadwal kegiatan pimpinan'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Penerapan Hard Skills', 5, 'Menerapkan prosedur berkomunikasi lisan dalam Bahasa Indonesia dan Bahasa Inggris baik secara langsung maupun menggunakan media telepon'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Penerapan Hard Skills', 6, 'Menerapkan pengelolaan arsip secara konvensional dan elektronik/digital'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Penerapan Hard Skills', 7, 'Mengelola kas kecil'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Peningkatan dan Pengembangan Hard Skills', 1, 'Memahami alur bisnis pada lingkungan kerja'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Peningkatan dan Pengembangan Hard Skills', 2, 'Memahami peraturan ketenagakerjaan'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Peningkatan dan Pengembangan Hard Skills', 3, 'Memahami peraturan sarana dan prasarana kantor'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Peningkatan dan Pengembangan Hard Skills', 4, 'Menerapkan prosedur administrasi pengadaan'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Peningkatan dan Pengembangan Hard Skills', 5, 'Menerapkan kegiatan kehumasan'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Peningkatan dan Pengembangan Hard Skills', 6, 'Menerapkan kegiatan keprotokolan'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Peningkatan dan Pengembangan Hard Skills', 7, 'Menerapkan Manajemen Pergudangan'],
        ['Manajemen Perkantoran dan Layanan Bisnis', 'Penyiapan Kemandirian Berwirausaha', 1, 'Menganalisis peluang wirausaha pada lingkungan kerja di bidang manajemen perkantoran'],
        ['Perhotelan', 'Internalisasi dan Penerapan Soft Skills', 1, 'Menginternalisasi dan menerapkan etika berkomunikasi secara lisan dan tulisan'],
        ['Perhotelan', 'Internalisasi dan Penerapan Soft Skills', 2, 'Menerapkan integritas (antara lain jujur, disiplin, komitmen, dan tanggung jawab) dan etos kerja di lingkungan kerja'],
        ['Perhotelan', 'Internalisasi dan Penerapan Soft Skills', 3, 'Bekerja secara mandiri dan/atau bekerja di dalam tim'],
        ['Perhotelan', 'Internalisasi dan Penerapan Soft Skills', 4, 'Menerapkan kepedulian sosial dan lingkungan, ketaatan terhadap norma'],
        ['Perhotelan', 'Internalisasi dan Penerapan Soft Skills', 5, 'Menerapkan K3LH dan POS yang berlaku di dunia kerja'],
        ['Perhotelan', 'Penerapan Hard Skills', 1, 'Melaksanakan tugas reservasi'],
        ['Perhotelan', 'Penerapan Hard Skills', 2, 'Menyediakan jasa porter'],
        ['Perhotelan', 'Penerapan Hard Skills', 3, 'Menyediakan layanan akomodasi reception'],
        ['Perhotelan', 'Penerapan Hard Skills', 4, 'Memproses transaksi keuangan'],
        ['Perhotelan', 'Penerapan Hard Skills', 5, 'Menjelaskan fasilitas dan pelayanan hotel'],
        ['Perhotelan', 'Penerapan Hard Skills', 6, 'Menyajikan informasi yang dibutuhkan tamu'],
        ['Perhotelan', 'Penerapan Hard Skills', 7, 'Melakukan komunikasi melalui telepon'],
        ['Perhotelan', 'Penerapan Hard Skills', 8, 'Melaksanakan tugas menyediakan layanan housekeeping untuk tamu'],
        ['Perhotelan', 'Penerapan Hard Skills', 9, 'Membersihkan lokasi/area dan peralatan'],
        ['Perhotelan', 'Penerapan Hard Skills', 10, 'Merangkai bunga sesuai pedoman dasar'],
        ['Perhotelan', 'Penerapan Hard Skills', 11, 'Menyiapkan kamar untuk tamu'],
        ['Perhotelan', 'Penerapan Hard Skills', 12, 'Menangani kehilangan dan penemuan barang'],
        ['Perhotelan', 'Penerapan Hard Skills', 13, 'Menangani linen, pakaian seragam karyawan dan tamu'],
        ['Perhotelan', 'Penerapan Hard Skills', 14, 'Melaksanakan proses laundry'],
        ['Perhotelan', 'Penerapan Hard Skills', 15, 'Melaksanakan layanan laundry valet'],
        ['Perhotelan', 'Penerapan Hard Skills', 16, 'Melaksanakan tugas menyediakan layanan makanan dan minuman'],
        ['Perhotelan', 'Penerapan Hard Skills', 17, 'Menyediakan room service'],
        ['Perhotelan', 'Penerapan Hard Skills', 18, 'Menerima dan menyimpan persediaan barang'],
        ['Perhotelan', 'Peningkatan dan Pengembangan Hard Skills', 1, 'Menerapkan alur kerja Departemen Front Office sesuai POS yang berlaku di hotel tempat bekerja'],
        ['Perhotelan', 'Peningkatan dan Pengembangan Hard Skills', 2, 'Menerapkan alur kerja Departemen Housekeeping dan Laundry sesuai POS yang berlaku di hotel tempat bekerja'],
        ['Perhotelan', 'Peningkatan dan Pengembangan Hard Skills', 3, 'Menerapkan alur kerja Departemen Food and Beverage Service sesuai POS yang berlaku di hotel tempat bekerja'],
        ['Perhotelan', 'Penyiapan Kemandirian Berwirausaha', 1, 'Menganalisis peluang wirausaha bidang perhotelan'],
        ['Kuliner', 'Internalisasi dan Penerapan Soft Skills', 1, 'Menerapkan soft skills pada lingkungan kerja di bidang kuliner'],
        ['Kuliner', 'Internalisasi dan Penerapan Soft Skills', 2, 'Menerapkan norma pada lingkungan kerja di bidang kuliner'],
        ['Kuliner', 'Internalisasi dan Penerapan Soft Skills', 3, 'Menerapkan POS dan K3LH yang ada pada lingkungan kerja di bidang kuliner'],
        ['Kuliner', 'Internalisasi dan Penerapan Soft Skills', 4, 'Menerapkan pelayanan prima di bidang kuliner'],
        ['Kuliner', 'Penerapan Hard Skills', 1, 'Menerapkan mise en place and mise en scene di lingkungan kerja'],
        ['Kuliner', 'Penerapan Hard Skills', 2, 'Menyusun standar menu'],
        ['Kuliner', 'Penerapan Hard Skills', 3, 'Menata dan memporsi makanan'],
        ['Kuliner', 'Penerapan Hard Skills', 4, 'Membuat hiasan makanan (garnish)'],
        ['Kuliner', 'Penerapan Hard Skills', 5, 'Membuat hidangan Kontinental'],
        ['Kuliner', 'Penerapan Hard Skills', 6, 'Membuat hidangan Oriental'],
        ['Kuliner', 'Penerapan Hard Skills', 7, 'Membuat hidangan Indonesia'],
        ['Kuliner', 'Penerapan Hard Skills', 8, 'Membuat kue tradisional Indonesia'],
        ['Kuliner', 'Penerapan Hard Skills', 9, 'Membuat produk pastry dan bakery'],
        ['Kuliner', 'Penerapan Hard Skills', 10, 'Melakukan pelayanan makanan dan minuman sesuai dengan prosedur'],
        ['Kuliner', 'Peningkatan dan Pengembangan Hard Skills', 1, 'Membuat minuman panas dan dingin'],
        ['Kuliner', 'Peningkatan dan Pengembangan Hard Skills', 2, 'Membuat produk confectionery'],
        ['Kuliner', 'Peningkatan dan Pengembangan Hard Skills', 3, 'Menerapkan molecular gastronomy'],
        ['Kuliner', 'Penyiapan Kemandirian Berwirausaha', 1, 'Menganalisis peluang wirausaha pada industri kuliner'],
        ['Kuliner', 'Penyiapan Kemandirian Berwirausaha', 2, 'Melakukan wirausaha pada industri kuliner'],
        ['Desain Komunikasi Visual (DKV)', 'Internalisasi dan Penerapan Soft Skills', 1, 'Menerapkan soft skills pada lingkungan kerja pada bidang desain komunikasi visual'],
        ['Desain Komunikasi Visual (DKV)', 'Internalisasi dan Penerapan Soft Skills', 2, 'Menerapkan norma, POS dan K3LH yang ada pada lingkungan kerja pada bidang desain komunikasi visual'],
        ['Desain Komunikasi Visual (DKV)', 'Penerapan Hard Skills', 1, 'Menerapkan kolaborasi dalam tim untuk menentukan desain produk komunikasi visual'],
        ['Desain Komunikasi Visual (DKV)', 'Penerapan Hard Skills', 2, 'Menerapkan fotografi dalam produk desain komunikasi visual'],
        ['Desain Komunikasi Visual (DKV)', 'Penerapan Hard Skills', 3, 'Menerapkan tipografi dalam produk desain komunikasi visual'],
        ['Desain Komunikasi Visual (DKV)', 'Penerapan Hard Skills', 4, 'Menerapkan videografi dalam produk desain komunikasi visual'],
        ['Desain Komunikasi Visual (DKV)', 'Peningkatan dan Pengembangan Hard Skills', 1, 'Menerapkan animasi 2D dan 3D dalam produk desain komunikasi visual'],
        ['Desain Komunikasi Visual (DKV)', 'Peningkatan dan Pengembangan Hard Skills', 2, 'Menerapkan AI pada produk desain komunikasi visual'],
        ['Desain Komunikasi Visual (DKV)', 'Penyiapan Kemandirian Berwirausaha', 1, 'Memahami alur bisnis pada lingkungan kerja'],
        ['Desain Komunikasi Visual (DKV)', 'Penyiapan Kemandirian Berwirausaha', 2, 'Menganalisis peluang wirausaha pada lingkungan kerja pada bidang desain komunikasi visual'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Internalisasi dan Penerapan Soft Skills', 1, 'Memahami soft skills pada lingkungan kerja'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Internalisasi dan Penerapan Soft Skills', 2, 'Menerapkan soft skills pada lingkungan kerja'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Internalisasi dan Penerapan Soft Skills', 3, 'Memahami norma, POS dan K3LH yang ada pada lingkungan kerja'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Internalisasi dan Penerapan Soft Skills', 4, 'Menerapkan norma, POS dan K3LH yang ada pada lingkungan kerja'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Internalisasi dan Penerapan Soft Skills', 5, 'Mengidentifikasi aspek kode etik dan HAKI di bidang TIK'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Penerapan Hard Skills', 1, 'Menerapkan kolaborasi dalam tim untuk pengembangan perangkat lunak'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Penerapan Hard Skills', 2, 'Mengimplementasikan User Interface'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Penerapan Hard Skills', 3, 'Menerapkan basis data dalam pengembangan perangkat lunak'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Penerapan Hard Skills', 4, 'Menerapkan perintah eksekusi pemrograman berbasis teks, grafik, dan multimedia'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Penerapan Hard Skills', 5, 'Menerapkan perintah eksekusi pemrograman berbasis web dan mobile'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Penerapan Hard Skills', 6, 'Melakukan pengujian perangkat lunak yang dikembangkan'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Peningkatan dan Pengembangan Hard Skills', 1, 'Melaksanakan Konfigurasi Perangkat Lunak Sesuai Environment Development, Staging, Production'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Peningkatan dan Pengembangan Hard Skills', 2, 'Melakukan Logging Aplikasi'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Penyiapan Kemandirian Berwirausaha', 1, 'Memahami alur bisnis pada lingkungan kerja'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Penyiapan Kemandirian Berwirausaha', 2, 'Menganalisis peluang wirausaha pada lingkungan kerja'],
        ['Rekayasa Perangkat Lunak (RPL)', 'Penyiapan Kemandirian Berwirausaha', 3, 'Menetapkan jenis usaha pada lingkungan kerja'],
    ];

    public function run(): void
    {
        // Kode konsentrasi -> id dari tabel konsentrasi_keahlians
        $kodeIds = [];
        $kodeDibutuhkan = array_unique(array_merge(...array_values($this->jurusanKonsentrasi)));
        foreach ($kodeDibutuhkan as $kode) {
            $konsentrasi = KonsentrasiKeahlian::where('kode', $kode)->first();
            if (! $konsentrasi) {
                $this->command?->warn("CpTpPklSeeder: konsentrasi kode {$kode} tidak ditemukan — barisnya dilewati.");

                continue;
            }
            $kodeIds[$kode] = $konsentrasi->id;
        }

        $jumlah = 0;
        $existing = Kompetensi::whereIn('konsentrasi_keahlian_id', array_values($kodeIds))
            ->orderBy('id')
            ->get()
            ->groupBy('konsentrasi_keahlian_id');

        foreach ($this->tps as [$jurusan, $elemen, $noTp, $tp]) {
            $cp = $this->elemenCp[$elemen] ?? null;
            if ($cp === null) {
                $this->command?->warn("CpTpPklSeeder: CP elemen {$elemen} tidak ditemukan — barisnya dilewati.");

                continue;
            }

            foreach ($this->jurusanKonsentrasi[$jurusan] ?? [] as $kode) {
                if (! isset($kodeIds[$kode])) {
                    continue;
                }

                // Cocokkan isi TP agar koreksi elemen/nomor tetap mempertahankan ID yang dirujuk jurnal.
                $kompetensi = $existing->get($kodeIds[$kode], collect())->first(
                    fn (Kompetensi $row) => $this->normalizeTp($row->tp) === $this->normalizeTp($tp)
                ) ?? new Kompetensi;
                $kompetensi->fill([
                    'konsentrasi_keahlian_id' => $kodeIds[$kode],
                    'nama' => $elemen,
                    'tp' => $noTp.'. '.$tp,
                    'kategori' => $this->kategori($elemen),
                    'cp' => $cp,
                    'deskripsi' => $cp,
                ]);

                // sort_order tidak fillable di model, jadi diisi lewat forceFill.
                $kompetensi->forceFill(['sort_order' => $noTp])->save();
                $jumlah++;
            }
        }

        $this->command?->info("CpTpPklSeeder: {$jumlah} baris CP/TP PKL diproses (upsert, aman dijalankan ulang).");
    }

    protected function normalizeTp(?string $tp): string
    {
        return mb_strtolower(trim(preg_replace('/^\s*\d+\.\s*/u', '', $tp ?? '')));
    }

    /**
     * Kategori kompetensi dari nama elemen (aturan yang sama dengan import CP/TP di Pokja).
     */
    protected function kategori(string $elemen): string
    {
        $elemenLower = strtolower($elemen);

        return match (true) {
            str_contains($elemenLower, 'soft') => 'soft-skill',
            str_contains($elemenLower, 'wirausaha') => 'wirausaha',
            str_contains($elemenLower, 'peningkatan') => 'pengembangan',
            default => 'teknis',
        };
    }
}
