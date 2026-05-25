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
            ['kode' => 'AKL',   'nama' => 'Akuntansi'],
            ['kode' => 'MPLB',  'nama' => 'Managemen Bisnis'],
            ['kode' => 'PBR',   'nama' => 'Pemasaran Ritel'],
            ['kode' => 'HOTEL', 'nama' => 'Perhotelan'],
            ['kode' => 'DKV',   'nama' => 'Desain Komunikasi Visual'],
            ['kode' => 'PPLG',  'nama' => 'Pengembangan Perangkat Lunak dan Gim'],
            ['kode' => 'KLN',   'nama' => 'Kuliner'],
        ];

        foreach ($programs as $prog) {
            $p = ProgramKeahlian::firstOrCreate(
                ['kode' => $prog['kode']],
                ['nama' => $prog['nama']]
            );

            // Mapping Concentrations
            $concentrations = [];
            if ($prog['kode'] == 'AKL') {
                $concentrations = [
                    ['kode' => 'AKL', 'nama' => 'Akuntansi'],
                    ['kode' => 'PBS', 'nama' => 'Perbankan Syariah'],
                ];
            } elseif ($prog['kode'] == 'PBR') {
                $concentrations = [
                    ['kode' => 'PBR', 'nama' => 'Pemasaran Ritel'],
                    ['kode' => 'PDG', 'nama' => 'Pemasaran Digital'],
                ];
            } else {
                $concentrations = [
                    ['kode' => $prog['kode'], 'nama' => $prog['nama']],
                ];
            }

            foreach ($concentrations as $con) {
                $k = KonsentrasiKeahlian::firstOrCreate(
                    ['kode' => $con['kode']],
                    [
                        'program_keahlian_id' => $p->id,
                        'nama'                => $con['nama'],
                        'durasi_pkl_bulan'    => in_array($prog['kode'], ['HOTEL', 'KLN']) ? 5 : 4,
                    ]
                );

                // Dummy Competencies
                Kompetensi::firstOrCreate(
                    ['konsentrasi_keahlian_id' => $k->id, 'nama' => 'Kompetensi Dasar ' . $con['nama']],
                    ['kategori' => 'Umum']
                );
            }
        }
    }
}
