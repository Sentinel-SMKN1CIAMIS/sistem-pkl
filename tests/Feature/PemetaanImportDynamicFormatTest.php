<?php

namespace Tests\Feature;

use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use App\Services\PemetaanImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemetaanImportDynamicFormatTest extends TestCase
{
    use RefreshDatabase;

    private ProgramKeahlian $progAkl;
    private KonsentrasiKeahlian $konAkl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->progAkl = ProgramKeahlian::create(['kode' => 'AKL', 'nama' => 'Akuntansi dan Keuangan Lembaga']);
        $this->konAkl = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->progAkl->id,
            'kode' => 'AKL',
            'nama' => 'Akuntansi',
            'durasi_pkl_bulan' => 6,
        ]);
    }

    public function test_dynamic_header_parsing_with_durasi_dudi_and_dual_mentors(): void
    {
        $userSiswa = User::create([
            'name' => 'Fidela Egalita Adliyah',
            'username' => '242510099',
            'password' => 'password',
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $siswa = Siswa::create([
            'user_id' => $userSiswa->id,
            'konsentrasi_keahlian_id' => $this->konAkl->id,
            'nis' => '242510099',
            'nama_lengkap' => 'Fidela Egalita Adliyah',
            'kelas' => 'XII AKL 2',
            'jenis_kelamin' => 'P',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'belum_mulai',
        ]);

        $csvData = <<<CSV
No,Durasi PKL,Nama DUDI,Nama Siswa,Kelas,Penyerahan,Monitoring 1,Penarikan
1,6 BULAN,Ibis Bandung Pasteur,FIDELA EGALITA ADLIYAH,XII AKL 2,Iqbal Fauzi Lisyanto,Hj. Atin Herawati,Iqbal Fauzi Lisyanto
CSV;

        $service = new PemetaanImportService();
        $parsed = $service->parseCsv($csvData);

        $this->assertCount(1, $parsed['items']);
        $this->assertEquals(0, $parsed['skipped_count']);

        $item = $parsed['items'][0];
        $this->assertEquals('Ibis Bandung Pasteur', $item['dudi_name']);
        $this->assertEquals('FIDELA EGALITA ADLIYAH', $item['siswa_nama']);
        $this->assertEquals('XII AKL 2', $item['kelas']);
        $this->assertEquals('Iqbal Fauzi Lisyanto', $item['pembimbing_canonical']);
        $this->assertEquals('Atin Herawati', $item['pembimbing_umum_canonical']);

        $stats = $service->executeImport($parsed['items'], ['default_password' => 'smkn1ciamis']);

        $this->assertEquals(1, $stats['siswa_mapped']);
        $this->assertEquals(1, $stats['dudi_created']);
        $this->assertEquals(2, $stats['pembimbing_created']); // Kejuruan & Umum

        $siswa->refresh();
        $this->assertNotNull($siswa->dudi_id);
        $this->assertEquals('Ibis Bandung Pasteur', $siswa->dudi->nama);
        $this->assertEquals('sedang_pkl', $siswa->status_pkl);

        $this->assertNotNull($siswa->pembimbing_sekolah_id);
        $this->assertNotNull($siswa->pembimbing_sekolah_umum_id);

        $this->assertDatabaseHas('pembimbing_sekolahs', [
            'id' => $siswa->pembimbing_sekolah_id,
            'tipe' => 'kejuruan',
        ]);

        $this->assertDatabaseHas('pembimbing_sekolahs', [
            'id' => $siswa->pembimbing_sekolah_umum_id,
            'tipe' => 'umum',
        ]);
    }

    public function test_import_can_map_dudi_only_when_mentor_column_is_empty(): void
    {
        $userSiswa = User::create([
            'name' => 'Budi Santoso',
            'username' => '242510100',
            'password' => 'password',
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $siswa = Siswa::create([
            'user_id' => $userSiswa->id,
            'konsentrasi_keahlian_id' => $this->konAkl->id,
            'nis' => '242510100',
            'nama_lengkap' => 'Budi Santoso',
            'kelas' => 'XII AKL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'belum_mulai',
        ]);

        $csvData = <<<CSV
No,Nama DUDI,Nama Siswa,Kelas
1,Bank BJB Ciamis,Budi Santoso,XII AKL 1
CSV;

        $service = new PemetaanImportService();
        $parsed = $service->parseCsv($csvData);

        $this->assertCount(1, $parsed['items']);
        $stats = $service->executeImport($parsed['items'], ['default_password' => 'smkn1ciamis']);

        $this->assertEquals(1, $stats['siswa_mapped']);
        $this->assertEquals(1, $stats['dudi_created']);

        $siswa->refresh();
        $this->assertNotNull($siswa->dudi_id);
        $this->assertEquals('Bank BJB Ciamis', $siswa->dudi->nama);
        $this->assertNull($siswa->pembimbing_sekolah_id);
    }
}
