<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zona;
use App\Models\Dudi;
use App\Models\Siswa;
use App\Models\KonsentrasiKeahlian;
use App\Models\User;

class PemetaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Zona (Contoh: Area sekitar Ciamis Kota)
        // Polygon sederhana berbentuk kotak
        $zonaPusat = Zona::create([
            'nama' => 'Zona 1 - Pusat Kota',
            'warna' => '#3b82f6', // Biru
            'warna_border' => '#1e40af',
            'nomor_zona' => 1,
            'koordinat_geojson' => [
                [108.340, -7.320], // Lng, Lat (Kiri Atas)
                [108.360, -7.320], // Kanan Atas
                [108.360, -7.340], // Kanan Bawah
                [108.340, -7.340], // Kiri Bawah
                [108.340, -7.320], // Kembali ke awal
            ]
        ]);

        $zonaTimur = Zona::create([
            'nama' => 'Zona 2 - Ciamis Timur',
            'warna' => '#10b981', // Hijau
            'warna_border' => '#047857',
            'nomor_zona' => 2,
            'koordinat_geojson' => [
                [108.360, -7.320],
                [108.380, -7.320],
                [108.380, -7.340],
                [108.360, -7.340],
                [108.360, -7.320],
            ]
        ]);

        // Pastikan ada Konsentrasi Keahlian
        $rpl = KonsentrasiKeahlian::where('kode', 'RPL')->first() ?? KonsentrasiKeahlian::first();
        $tkj = KonsentrasiKeahlian::where('kode', 'TKJ')->first() ?? KonsentrasiKeahlian::skip(1)->first();

        // 2. Buat DUDI dengan Koordinat di dalam Zona Pusat (-7.330, 108.350)
        $dudiPusat1 = Dudi::create([
            'konsentrasi_keahlian_id' => $rpl->id,
            'nama' => 'PT Teknologi Pusat Ciamis',
            'alamat' => 'Jl. Jend. Sudirman No. 1, Ciamis',
            'kota' => 'Ciamis',
            'latitude' => -7.330500,
            'longitude' => 108.350000,
            'jenis_industri' => 'teknologi',
            'zona_id' => $zonaPusat->id,
            'is_active' => true,
        ]);
        $dudiPusat1->konsentrasiKeahlians()->sync([$rpl->id, $tkj->id]);

        $dudiPusat2 = Dudi::create([
            'konsentrasi_keahlian_id' => $tkj->id,
            'nama' => 'Dinas Pendidikan Ciamis',
            'alamat' => 'Kawasan Perkantoran Kertasari',
            'kota' => 'Ciamis',
            'latitude' => -7.325000,
            'longitude' => 108.345000,
            'jenis_industri' => 'pemerintahan',
            'zona_id' => $zonaPusat->id,
            'is_active' => true,
        ]);
        $dudiPusat2->konsentrasiKeahlians()->sync([$tkj->id]);

        // 3. Buat DUDI di Zona Timur (-7.330, 108.370)
        $dudiTimur = Dudi::create([
            'konsentrasi_keahlian_id' => $rpl->id,
            'nama' => 'CV Software Media',
            'alamat' => 'Jl. Raya Banjar KM 3',
            'kota' => 'Ciamis',
            'latitude' => -7.330000,
            'longitude' => 108.370000,
            'jenis_industri' => 'layanan',
            'zona_id' => $zonaTimur->id,
            'is_active' => true,
        ]);
        $dudiTimur->konsentrasiKeahlians()->sync([$rpl->id]);

        // 4. Buat DUDI Tanpa Koordinat (Untuk tes error handling/belum terplot)
        $dudiKosong = Dudi::create([
            'konsentrasi_keahlian_id' => $rpl->id,
            'nama' => 'PT Belum Ada Lokasi',
            'alamat' => 'Jl. Misterius No. 99',
            'kota' => 'Ciamis',
            'latitude' => null,
            'longitude' => null,
            'jenis_industri' => 'perdagangan',
            'zona_id' => null,
            'is_active' => true,
        ]);

        // 5. Plot Siswa ke DUDI
        // Asumsi ada siswa di database, jika tidak buat dummy
        $siswas = Siswa::limit(10)->get();
        if ($siswas->count() < 10) {
            $this->command->info('Membutuhkan minimal 10 siswa untuk simulasi. Membuat dummy Siswa...');
            $kekurangan = 10 - $siswas->count();
            for ($j = 0; $j < $kekurangan; $j++) {
                $user = User::create([
                    'name' => 'Siswa Dummy ' . $j,
                    'username' => 'dummy' . $j . rand(100,999),
                    'email' => 'dummy' . $j . rand(100,999) . '@gmail.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'siswa',
                ]);
                Siswa::create([
                    'user_id' => $user->id,
                    'konsentrasi_keahlian_id' => $rpl->id,
                    'nis' => 'DUMMY' . $j . rand(100,999),
                    'nama_lengkap' => 'Siswa Dummy ' . $j,
                    'kelas' => 'XII RPL 1',
                    'jenis_kelamin' => 'L',
                    'tahun_ajaran' => '2025/2026',
                    'status_pkl' => 'belum_mulai',
                ]);
            }
            $siswas = Siswa::limit(10)->get();
        }

        // Bagi siswa ke DUDI
        $i = 0;
        foreach ($siswas as $siswa) {
            if ($i < 4) {
                $siswa->update(['dudi_id' => $dudiPusat1->id]);
            } elseif ($i < 7) {
                $siswa->update(['dudi_id' => $dudiPusat2->id]);
            } elseif ($i < 9) {
                $siswa->update(['dudi_id' => $dudiTimur->id]);
            } else {
                $siswa->update(['dudi_id' => $dudiKosong->id]);
            }
            $i++;
        }

        $this->command->info('PemetaanSeeder berhasil dijalankan!');
        $this->command->info('- 2 Zona Dibuat');
        $this->command->info('- 4 DUDI Dibuat (3 Punya Koordinat, 1 Kosong)');
        $this->command->info('- 10 Siswa diplot ke DUDI tersebut');
    }
}
