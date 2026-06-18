<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Siswa;
use App\Models\PembimbingSekolah;
use App\Models\PembimbingDudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\Dudi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // Kaprog
        $pplgProgram = \App\Models\ProgramKeahlian::where('kode', 'PPLG')->first();
        User::create([
            'name' => 'Kepala Program',
            'username' => 'kaprog',
            'email' => 'kaprog@gmail.com',
            'password' => $password,
            'role' => 'kaprog',
            'program_keahlian_id' => $pplgProgram ? $pplgProgram->id : null,
        ]);

        // Pokja
        User::create([
            'name' => 'Ketua Pokja',
            'username' => 'pokja',
            'email' => 'pokja@gmail.com',
            'password' => $password,
            'role' => 'pokja',
        ]);

        // Create a DUDI first
        $pplg = KonsentrasiKeahlian::where('kode', 'PPLG')->first();
        if ($pplg) {
            $dudi = Dudi::create([
                'konsentrasi_keahlian_id' => $pplg->id,
                'nama' => 'PT Teknologi Nusantara',
                'alamat' => 'Jl. Digital No. 101',
                'kota' => 'Bandung',
                'bidang_usaha' => 'IT Services',
            ]);

            // Pembimbing Sekolah
            $guruUser = User::create([
                'name' => 'Budi Santoso, S.Kom',
                'username' => 'guru',
                'email' => 'budi@gmail.com',
                'password' => $password,
                'role' => 'pembimbing_sekolah',
            ]);
            $pembimbingSekolah = PembimbingSekolah::create([
                'user_id' => $guruUser->id,
                'konsentrasi_keahlian_id' => $pplg->id,
                'nip' => '198701012010011001',
                'nama_lengkap' => 'Budi Santoso, S.Kom',
            ]);

            // Pembimbing DUDI
            $mentorUser = User::create([
                'name' => 'Andi Mentor',
                'username' => 'mentor',
                'email' => 'andi@dudi.com',
                'password' => $password,
                'role' => 'pembimbing_dudi',
            ]);
            $pembimbingDudi = PembimbingDudi::create([
                'user_id' => $mentorUser->id,
                'dudi_id' => $dudi->id,
                'nama_lengkap' => 'Andi Mentor',
                'jabatan' => 'Senior Developer',
            ]);

            // Siswa
            $siswaUser = User::create([
                'name' => 'Rizky Pratama',
                'username' => '2223101',
                'email' => 'rizky@gmail.com',
                'password' => $password,
                'role' => 'siswa',
            ]);
            Siswa::create([
                'user_id' => $siswaUser->id,
                'konsentrasi_keahlian_id' => $pplg->id,
                'dudi_id' => $dudi->id,
                'pembimbing_sekolah_id' => $pembimbingSekolah->id,
                'pembimbing_dudi_id' => $pembimbingDudi->id,
                'nis' => '2223101',
                'nama_lengkap' => 'Rizky Pratama',
                'kelas' => 'XII PPLG 1',
                'jenis_kelamin' => 'L',
                'tahun_ajaran' => '2025/2026',
                'status_pkl' => 'sedang_pkl',
            ]);

            // Siswa Demo Flow (Belum PKL / belum mengajukan)
            $siswaDemoUser = User::create([
                'name' => 'Siswa Demo Flow',
                'username' => 'siswa',
                'email' => 'siswa@gmail.com',
                'password' => $password,
                'role' => 'siswa',
            ]);
            Siswa::create([
                'user_id' => $siswaDemoUser->id,
                'konsentrasi_keahlian_id' => $pplg->id,
                'dudi_id' => null,
                'pembimbing_sekolah_id' => null,
                'pembimbing_dudi_id' => null,
                'nis' => 'siswa',
                'nama_lengkap' => 'Siswa Demo Flow',
                'kelas' => 'XII PPLG 1',
                'jenis_kelamin' => 'L',
                'tahun_ajaran' => '2025/2026',
                'status_pkl' => 'belum_mulai',
            ]);
        }

        // Run other developmental seeders
        $this->call([
            ActivityLogSeeder::class,
            PemetaanSeeder::class,
            KompetensiCPTPSeeder::class,
        ]);
    }
}
