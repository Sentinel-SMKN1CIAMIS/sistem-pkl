<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;

class KompetensiCPTPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all konsentrasi keahlian
        $konsentrasis = KonsentrasiKeahlian::all();

        foreach ($konsentrasis as $konsentrasi) {
            // Add sample CP/TP data for each konsentrasi keahlian
            $sampleData = [
                [
                    'nama' => 'Persiapan dan Perencanaan',
                    'kategori' => 'teknis',
                    'tp' => 'TP-001: Memahami prosedur perencanaan kerja',
                    'cp' => 'Dapat menyusun rencana kerja harian dengan benar',
                    'deskripsi' => 'Memahami dan mampu menyusun rencana kerja serta melakukan persiapan untuk menjalankan pekerjaan sehari-hari di industri.'
                ],
                [
                    'nama' => 'Pelaksanaan Pekerjaan',
                    'kategori' => 'teknis',
                    'tp' => 'TP-002: Melaksanakan pekerjaan sesuai standar',
                    'cp' => 'Dapat melaksanakan pekerjaan sesuai dengan SOP dan standar industri',
                    'deskripsi' => 'Mampu menjalankan tugas dan pekerjaan sesuai dengan standar operasional prosedur (SOP) yang berlaku di industri.'
                ],
                [
                    'nama' => 'Komunikasi Kerja',
                    'kategori' => 'soft-skill',
                    'tp' => 'TP-003: Berkomunikasi efektif dengan tim',
                    'cp' => 'Dapat berkomunikasi dengan baik dengan atasan dan rekan kerja',
                    'deskripsi' => 'Mampu melakukan komunikasi yang efektif dan profesional dengan atasan, rekan kerja, dan pihak lain dalam lingkungan kerja.'
                ],
                [
                    'nama' => 'Keselamatan Kerja',
                    'kategori' => 'keselamatan',
                    'tp' => 'TP-004: Menerapkan prosedur keselamatan kerja',
                    'cp' => 'Dapat menerapkan protokol kesehatan dan keselamatan kerja',
                    'deskripsi' => 'Memahami dan menerapkan semua prosedur keselamatan kerja, penggunaan APD (Alat Pelindung Diri), dan protokol kesehatan.'
                ],
                [
                    'nama' => 'Evaluasi dan Perbaikan',
                    'kategori' => 'pengembangan',
                    'tp' => 'TP-005: Melakukan evaluasi dan perbaikan',
                    'cp' => 'Dapat mengevaluasi hasil pekerjaan dan melakukan perbaikan',
                    'deskripsi' => 'Mampu mengevaluasi hasil pekerjaan yang telah dilakukan dan melakukan perbaikan untuk peningkatan kualitas.'
                ]
            ];

            foreach ($sampleData as $data) {
                Kompetensi::updateOrCreate(
                    [
                        'konsentrasi_keahlian_id' => $konsentrasi->id,
                        'nama' => $data['nama']
                    ],
                    [
                        'kategori' => $data['kategori'],
                        'tp' => $data['tp'],
                        'cp' => $data['cp'],
                        'deskripsi' => $data['deskripsi']
                    ]
                );
            }
        }
    }
}
