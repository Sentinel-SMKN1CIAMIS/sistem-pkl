<?php

namespace Database\Seeders;

use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // 7 Program Keahlian
        $programs = [
            ['kode' => 'TJKT', 'nama' => 'Teknik Jaringan Komputer dan Telekomunikasi'],
            ['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak dan Gim'],
            ['kode' => 'DKV', 'nama' => 'Desain Komunikasi Visual'],
            ['kode' => 'KUL', 'nama' => 'Kuliner'],
            ['kode' => 'PH', 'nama' => 'Perhotelan'],
            ['kode' => 'TO', 'nama' => 'Teknik Otomotif'],
            ['kode' => 'TE', 'nama' => 'Teknik Elektronika'],
        ];

        foreach ($programs as $prog) {
            $p = ProgramKeahlian::create($prog);

            // 9 Konsentrasi Keahlian (example mapping)
            if ($prog['kode'] == 'PPLG') {
                $k = KonsentrasiKeahlian::create([
                    'program_keahlian_id' => $p->id,
                    'kode' => 'RPL',
                    'nama' => 'Rekayasa Perangkat Lunak',
                    'durasi_pkl_bulan' => 4,
                ]);

                Kompetensi::create(['konsentrasi_keahlian_id' => $k->id, 'nama' => 'Membuat program', 'kategori' => 'Programming']);
                Kompetensi::create(['konsentrasi_keahlian_id' => $k->id, 'nama' => 'Debugging', 'kategori' => 'Programming']);
                Kompetensi::create(['konsentrasi_keahlian_id' => $k->id, 'nama' => 'Testing', 'kategori' => 'Programming']);
            } elseif ($prog['kode'] == 'TJKT') {
                $k = KonsentrasiKeahlian::create([
                    'program_keahlian_id' => $p->id,
                    'kode' => 'TKJ',
                    'nama' => 'Teknik Komputer dan Jaringan',
                    'durasi_pkl_bulan' => 4,
                ]);

                Kompetensi::create(['konsentrasi_keahlian_id' => $k->id, 'nama' => 'Instalasi jaringan', 'kategori' => 'Network']);
                Kompetensi::create(['konsentrasi_keahlian_id' => $k->id, 'nama' => 'Konfigurasi router', 'kategori' => 'Network']);
            } elseif ($prog['kode'] == 'DKV') {
                $k = KonsentrasiKeahlian::create([
                    'program_keahlian_id' => $p->id,
                    'kode' => 'DKV',
                    'nama' => 'Desain Komunikasi Visual',
                    'durasi_pkl_bulan' => 4,
                ]);

                Kompetensi::create(['konsentrasi_keahlian_id' => $k->id, 'nama' => 'Desain poster', 'kategori' => 'Design']);
                Kompetensi::create(['konsentrasi_keahlian_id' => $k->id, 'nama' => 'Editing gambar', 'kategori' => 'Design']);
            } elseif ($prog['kode'] == 'KUL' || $prog['kode'] == 'PH') {
                $k = KonsentrasiKeahlian::create([
                    'program_keahlian_id' => $p->id,
                    'kode' => $prog['kode'],
                    'nama' => $prog['nama'],
                    'durasi_pkl_bulan' => 5, // Hotel dan Kuliner 5 bulan
                ]);
            } else {
                 KonsentrasiKeahlian::create([
                    'program_keahlian_id' => $p->id,
                    'kode' => $prog['kode'],
                    'nama' => $prog['nama'],
                    'durasi_pkl_bulan' => 4,
                ]);
            }
        }
    }
}
