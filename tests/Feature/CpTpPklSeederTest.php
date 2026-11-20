<?php

namespace Tests\Feature;

use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Database\Seeders\CpTpPklSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CpTpPklSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_workbook_using_the_requested_concentration_mapping(): void
    {
        $this->createConcentrations();

        $this->seed(CpTpPklSeeder::class);

        $expectedCounts = ['AKL' => 15, 'PBS' => 15, 'PBR' => 28, 'PBD' => 28, 'MPLB' => 19, 'HTL' => 27, 'KLN' => 19, 'DKV' => 10, 'PPLG' => 16];
        foreach ($expectedCounts as $code => $count) {
            $concentration = KonsentrasiKeahlian::where('kode', $code)->firstOrFail();
            $this->assertSame($count, $concentration->kompetensi()->count(), $code);
        }
        $this->assertDatabaseCount('kompetensis', 177);

        $pplgId = KonsentrasiKeahlian::where('kode', 'PPLG')->value('id');
        $this->assertSame(2, Kompetensi::where('konsentrasi_keahlian_id', $pplgId)->where('kategori', 'pengembangan')->count());
        $this->assertSame(3, Kompetensi::where('konsentrasi_keahlian_id', $pplgId)->where('kategori', 'wirausaha')->count());
        $this->assertDatabaseHas('kompetensis', [
            'konsentrasi_keahlian_id' => $pplgId,
            'nama' => 'Penyiapan Kemandirian Berwirausaha',
            'tp' => '1. Memahami alur bisnis pada lingkungan kerja',
            'cp' => 'Pada akhir fase F, peserta didik mampu melakukan analisis usaha secara mandiri.',
        ]);

        $softSkills = Kompetensi::where('kategori', 'soft-skill')->pluck('cp')->unique();
        $this->assertCount(1, $softSkills);
        $this->assertStringContainsString('dan/atau bekerja di dalam tim', $softSkills->first());

        $ids = Kompetensi::orderBy('id')->pluck('id')->all();
        $this->seed(CpTpPklSeeder::class);
        $this->assertSame($ids, Kompetensi::orderBy('id')->pluck('id')->all());
        $this->assertDatabaseCount('kompetensis', 177);
    }

    public function test_it_corrects_legacy_pplg_rows_without_replacing_their_ids(): void
    {
        $this->createConcentrations();
        $pplgId = KonsentrasiKeahlian::where('kode', 'PPLG')->value('id');
        $legacyRows = [
            ['Peningkatan dan Pengembangan Hard Skills', '1. Memahami alur bisnis pada lingkungan kerja', '1. Memahami alur bisnis pada lingkungan kerja', 'wirausaha', 1],
            ['Peningkatan dan Pengembangan Hard Skills', '2. Melaksanakan konfigurasi perangkat lunak sesuai environment Development, Staging, Production', '1. Melaksanakan Konfigurasi Perangkat Lunak Sesuai Environment Development, Staging, Production', 'pengembangan', 1],
            ['Peningkatan dan Pengembangan Hard Skills', '3. Melakukan logging aplikasi', '2. Melakukan Logging Aplikasi', 'pengembangan', 2],
            ['Penyiapan Kemandirian Berwirausaha', '1. Menganalisis peluang wirausaha pada lingkungan kerja', '2. Menganalisis peluang wirausaha pada lingkungan kerja', 'wirausaha', 2],
            ['Penyiapan Kemandirian Berwirausaha', '2. Menetapkan jenis usaha pada lingkungan kerja', '3. Menetapkan jenis usaha pada lingkungan kerja', 'wirausaha', 3],
        ];
        $existing = [];
        foreach ($legacyRows as [$element, $oldTp, $newTp, $category, $order]) {
            $row = Kompetensi::create(['konsentrasi_keahlian_id' => $pplgId, 'nama' => $element, 'kategori' => 'pengembangan', 'tp' => $oldTp, 'cp' => 'CP lama']);
            $existing[] = [$row->id, $newTp, $category, $order];
        }
        $custom = Kompetensi::create(['konsentrasi_keahlian_id' => $pplgId, 'nama' => 'Kompetensi tambahan', 'tp' => 'TP khusus tempat PKL']);

        $this->seed(CpTpPklSeeder::class);

        foreach ($existing as [$id, $tp, $category, $order]) {
            $this->assertDatabaseHas('kompetensis', ['id' => $id, 'tp' => $tp, 'kategori' => $category, 'sort_order' => $order]);
        }
        $this->assertDatabaseHas('kompetensis', ['id' => $custom->id, 'tp' => 'TP khusus tempat PKL']);
        $this->assertDatabaseCount('kompetensis', 178);
    }

    private function createConcentrations(): void
    {
        $program = ProgramKeahlian::create(['kode' => 'TEST', 'nama' => 'Program pengujian']);
        foreach (['AKL', 'PBR', 'MPLB', 'HTL', 'KLN', 'DKV', 'PPLG', 'PBS', 'PBD'] as $code) {
            KonsentrasiKeahlian::create(['program_keahlian_id' => $program->id, 'kode' => $code, 'nama' => $code]);
        }
    }
}
