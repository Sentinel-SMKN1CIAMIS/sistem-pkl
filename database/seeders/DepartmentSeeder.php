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
        // Truncate existing data to avoid duplicates
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Kompetensi::truncate();
        KonsentrasiKeahlian::truncate();
        ProgramKeahlian::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 7 Program Keahlian
        $programs = [
            ['kode' => 'AKL', 'nama' => 'Akuntansi'],
            ['kode' => 'MPLB', 'nama' => 'Managemen Bisnis'],
            ['kode' => 'PBR', 'nama' => 'Pemasaran Ritel'],
            ['kode' => 'HOTEL', 'nama' => 'Perhotelan'],
            ['kode' => 'DKV', 'nama' => 'Desain Komunikasi Visual'],
            ['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak dan Gim'],
            ['kode' => 'KLN', 'nama' => 'Kuliner'],
        ];

        foreach ($programs as $prog) {
            $p = ProgramKeahlian::create($prog);

            // Mapping Concentrations
            $concentrations = [];
            if ($prog['kode'] == 'AKL') {
                $concentrations = [
                    ['kode' => 'AKL', 'nama' => 'Akuntansi'],
                    ['kode' => 'PBR', 'nama' => 'Perbankan Syariah'],
                ];
            } elseif ($prog['kode'] == 'PR') {
                $concentrations = [
                    ['kode' => 'PR', 'nama' => 'Pemasaran Ritel'],
                    ['kode' => 'PD', 'nama' => 'Pemasaran Digital'],
                ];
            } else {
                $concentrations = [
                    ['kode' => $prog['kode'], 'nama' => $prog['nama']],
                ];
            }

            foreach ($concentrations as $con) {
                $k = KonsentrasiKeahlian::create([
                    'program_keahlian_id' => $p->id,
                    'kode' => $con['kode'],
                    'nama' => $con['nama'],
                    'durasi_pkl_bulan' => ($prog['kode'] == 'PH' || $prog['kode'] == 'KLN') ? 5 : 4,
                ]);

                // Dummy Competencies
                Kompetensi::create([
                    'konsentrasi_keahlian_id' => $k->id,
                    'nama' => 'Kompetensi Dasar ' . $con['nama'],
                    'kategori' => 'Umum'
                ]);
            }
        }
    }
}
