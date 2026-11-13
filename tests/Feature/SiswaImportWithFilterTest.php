<?php

namespace Tests\Feature;

use App\Models\KonsentrasiKeahlian;
use App\Models\PokjaGroup;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SiswaImportWithFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $pokja;
    private $concentrations;

    protected function setUp(): void
    {
        parent::setUp();

        // 7 Program Keahlian
        $aklProg = ProgramKeahlian::create(['kode' => 'AKL', 'nama' => 'Akuntansi dan Keuangan Lembaga']);
        $mplbProg = ProgramKeahlian::create(['kode' => 'MPLB', 'nama' => 'Manajemen Perkantoran dan Layanan Bisnis']);
        $pmProg = ProgramKeahlian::create(['kode' => 'PM', 'nama' => 'Pemasaran']);
        $htlProg = ProgramKeahlian::create(['kode' => 'HTL', 'nama' => 'Perhotelan']);
        $dkvProg = ProgramKeahlian::create(['kode' => 'DKV', 'nama' => 'Desain Komunikasi Visual']);
        $pplgProg = ProgramKeahlian::create(['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak dan Gim']);
        $klnProg = ProgramKeahlian::create(['kode' => 'KLN', 'nama' => 'Kuliner']);

        KonsentrasiKeahlian::create(['id' => 1, 'program_keahlian_id' => $aklProg->id, 'kode' => 'AKL', 'nama' => 'Akuntansi', 'durasi_pkl_bulan' => 4]);
        KonsentrasiKeahlian::create(['id' => 2, 'program_keahlian_id' => $aklProg->id, 'kode' => 'PBS', 'nama' => 'Perbankan Syariah', 'durasi_pkl_bulan' => 4]);
        KonsentrasiKeahlian::create(['id' => 3, 'program_keahlian_id' => $mplbProg->id, 'kode' => 'MPLB', 'nama' => 'Manajemen Perkantoran', 'durasi_pkl_bulan' => 4]);
        KonsentrasiKeahlian::create(['id' => 4, 'program_keahlian_id' => $pmProg->id, 'kode' => 'PBR', 'nama' => 'Bisnis Ritel', 'durasi_pkl_bulan' => 4]);
        KonsentrasiKeahlian::create(['id' => 5, 'program_keahlian_id' => $pmProg->id, 'kode' => 'PBD', 'nama' => 'Bisnis Digital', 'durasi_pkl_bulan' => 4]);
        KonsentrasiKeahlian::create(['id' => 6, 'program_keahlian_id' => $htlProg->id, 'kode' => 'HTL', 'nama' => 'Perhotelan', 'durasi_pkl_bulan' => 5]);
        KonsentrasiKeahlian::create(['id' => 7, 'program_keahlian_id' => $dkvProg->id, 'kode' => 'DKV', 'nama' => 'Desain Komunikasi Visual', 'durasi_pkl_bulan' => 4]);
        KonsentrasiKeahlian::create(['id' => 8, 'program_keahlian_id' => $pplgProg->id, 'kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak dan Gim', 'durasi_pkl_bulan' => 4]);
        KonsentrasiKeahlian::create(['id' => 9, 'program_keahlian_id' => $klnProg->id, 'kode' => 'KLN', 'nama' => 'Kuliner', 'durasi_pkl_bulan' => 5]);

        $this->pokja = User::create([
            'name' => 'Pokja Test',
            'username' => 'pokja_test',
            'email' => 'pokja_test@example.com',
            'password' => bcrypt('password'),
            'role' => 'pokja',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $group = PokjaGroup::create(['name' => 'Pokja Group 1', 'is_active' => true]);
        $group->users()->attach($this->pokja->id);
    }

    public function test_can_import_csv_with_jurusan_filter_excluding_dkv(): void
    {
        $csvContent = <<<CSV
No,Nama Lengkap,NIS,Kelas
1,Budi Santoso,242510001,12AK1
2,Siti Aminah,242510325,12MP1
3,Eko Prasetyo,242510181,12PBR1
4,Dewi Sartika,242510577,12DKV
5,Ahmad Fauzi,242510613,12PPLG
CSV;

        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csvContent);

        // Exclude DKV (ID 7)
        $response = $this->actingAs($this->pokja)
            ->post(route('pokja.siswa.import'), [
                'file' => $file,
                'selected_jurusan' => [1, 2, 3, 4, 5, 6, 8, 9], // without 7 (DKV)
                'tahun_ajaran' => '2025/2026',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Budi (AKL), Siti (MPLB), Eko (PBR), Ahmad (PPLG) should be created
        $this->assertDatabaseHas('siswas', ['nis' => '242510001', 'konsentrasi_keahlian_id' => 1]);
        $this->assertDatabaseHas('siswas', ['nis' => '242510325', 'konsentrasi_keahlian_id' => 3]);
        $this->assertDatabaseHas('siswas', ['nis' => '242510181', 'konsentrasi_keahlian_id' => 4]);
        $this->assertDatabaseHas('siswas', ['nis' => '242510613', 'konsentrasi_keahlian_id' => 8]);

        // Dewi Sartika (DKV) must NOT be created because DKV was filtered out
        $this->assertDatabaseMissing('siswas', ['nis' => '242510577']);
    }

    public function test_can_detect_and_correct_nis_typo_for_existing_student(): void
    {
        // Existing student in system with typo NIS
        $user = User::create([
            'name' => 'Vani Haryani',
            'username' => '242518575', // typo NIS in system
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);
        Siswa::create([
            'user_id' => $user->id,
            'konsentrasi_keahlian_id' => 9, // Kuliner
            'nis' => '242518575',
            'nama_lengkap' => 'Vani Haryani',
            'kelas' => '12KLN2',
            'jenis_kelamin' => 'P',
            'tahun_ajaran' => '2025/2026',
        ]);

        // Official file has correct NIS 242510575
        $csvContent = <<<CSV
No,Nama Lengkap,NIS,Kelas
1,Vani Haryani,242510575,12KLN2
CSV;

        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csvContent);

        $response = $this->actingAs($this->pokja)
            ->post(route('pokja.siswa.import'), [
                'file' => $file,
                'selected_jurusan' => [9],
                'sync_mode' => 1,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('nis_corrections');

        // Old NIS should be gone, updated to correct official NIS
        $this->assertDatabaseMissing('siswas', ['nis' => '242518575']);
        $this->assertDatabaseHas('siswas', [
            'nis' => '242510575',
            'nama_lengkap' => 'Vani Haryani',
        ]);
        $this->assertDatabaseHas('users', [
            'username' => '242510575',
        ]);
    }
}
