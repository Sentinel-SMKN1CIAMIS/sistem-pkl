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
            [
                'kode' => 'AKL',
                'nama' => 'Akuntansi dan Keuangan Lembaga',
                'concentrations' => [
                    ['kode' => 'AKL', 'nama' => 'Akuntansi', 'durasi' => 4],
                    ['kode' => 'PBS', 'nama' => 'Perbankan Syariah', 'durasi' => 4],
                ],
            ],
            [
                'kode' => 'MPLB',
                'nama' => 'Manajemen Perkantoran dan Layanan Bisnis',
                'concentrations' => [
                    ['kode' => 'MPLB', 'nama' => 'Manajemen Perkantoran', 'durasi' => 4],
                ],
            ],
            [
                'kode' => 'PM',
                'nama' => 'Pemasaran',
                'concentrations' => [
                    ['kode' => 'PBR', 'nama' => 'Bisnis Ritel', 'durasi' => 4],
                    ['kode' => 'PBD', 'nama' => 'Bisnis Digital', 'durasi' => 4],
                ],
            ],
            [
                'kode' => 'HTL',
                'nama' => 'Perhotelan',
                'concentrations' => [
                    ['kode' => 'HTL', 'nama' => 'Perhotelan', 'durasi' => 5],
                ],
            ],
            [
                'kode' => 'DKV',
                'nama' => 'Desain Komunikasi Visual',
                'concentrations' => [
                    ['kode' => 'DKV', 'nama' => 'Desain Komunikasi Visual', 'durasi' => 4],
                ],
            ],
            [
                'kode' => 'PPLG',
                'nama' => 'Pengembangan Perangkat Lunak dan Gim',
                'concentrations' => [
                    ['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak dan Gim', 'durasi' => 4],
                ],
            ],
            [
                'kode' => 'KLN',
                'nama' => 'Kuliner',
                'concentrations' => [
                    ['kode' => 'KLN', 'nama' => 'Kuliner', 'durasi' => 5],
                ],
            ],
        ];

        foreach ($programs as $prog) {
            $p = ProgramKeahlian::updateOrCreate(
                ['kode' => $prog['kode']],
                ['nama' => $prog['nama']]
            );

            foreach ($prog['concentrations'] as $con) {
                $k = KonsentrasiKeahlian::updateOrCreate(
                    ['kode' => $con['kode']],
                    [
                        'program_keahlian_id' => $p->id,
                        'nama'                => $con['nama'],
                        'durasi_pkl_bulan'    => $con['durasi'],
                    ]
                );

                Kompetensi::firstOrCreate(
                    ['konsentrasi_keahlian_id' => $k->id, 'nama' => 'Kompetensi Dasar ' . $con['nama']],
                    ['kategori' => 'Umum']
                );
            }
        }
    }
}
