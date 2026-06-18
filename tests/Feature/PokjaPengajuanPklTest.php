<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\PengajuanPkl;
use App\Models\PokjaGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PokjaPengajuanPklTest extends TestCase
{
    use RefreshDatabase;

    private $pokja;
    private $siswa;
    private $konsentrasi;

    protected function setUp(): void
    {
        parent::setUp();

        // Create program and concentration
        $prog = ProgramKeahlian::create(['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak']);
        $this->konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $prog->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        // Create Pokja
        $this->pokja = User::create([
            'name' => 'Pokja User',
            'username' => 'pokja1',
            'email' => 'pokja@test.com',
            'password' => bcrypt('password'),
            'role' => 'pokja',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        // Create Pokja Group and attach Pokja User
        $group = PokjaGroup::create([
            'name' => 'Grup Pokja Utama',
            'is_active' => true,
        ]);
        $group->users()->attach($this->pokja->id);

        // Create Student
        $studentUser = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa1',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'belum_mulai',
        ]);
    }

    /**
     * Test single submission delete by Pokja.
     */
    public function test_pokja_can_delete_single_submission_with_file_cleanup()
    {
        Storage::fake('public');

        $filePath = 'bukti_balasan/test_bukti_single.pdf';
        Storage::disk('public')->put($filePath, 'fake content');

        $pengajuan = PengajuanPkl::create([
            'siswa_id' => $this->siswa->id,
            'nama_perusahaan' => 'PT Test Single',
            'pimpinan' => 'Pimpinan Test',
            'alamat' => 'Alamat Test',
            'kota' => 'Kota Test',
            'status' => 'menunggu',
            'bukti_balasan' => $filePath,
        ]);

        $this->actingAs($this->pokja);

        $response = $this->delete(route('pokja.pengajuan_pkl.destroy', $pengajuan->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('pengajuan_pkls', ['id' => $pengajuan->id]);
        $this->assertFalse(Storage::disk('public')->exists($filePath));
    }

    /**
     * Test bulk delete submissions by Pokja.
     */
    public function test_pokja_can_bulk_delete_submissions_with_file_cleanup()
    {
        Storage::fake('public');

        $filePath1 = 'bukti_balasan/test_bukti1.pdf';
        $filePath2 = 'bukti_balasan/test_bukti2.pdf';
        $filePath3 = 'bukti_balasan/test_bukti3.pdf';

        Storage::disk('public')->put($filePath1, 'fake content 1');
        Storage::disk('public')->put($filePath2, 'fake content 2');
        Storage::disk('public')->put($filePath3, 'fake content 3');

        $p1 = PengajuanPkl::create([
            'siswa_id' => $this->siswa->id,
            'nama_perusahaan' => 'PT Test 1',
            'status' => 'menunggu',
            'bukti_balasan' => $filePath1,
        ]);

        $p2 = PengajuanPkl::create([
            'siswa_id' => $this->siswa->id,
            'nama_perusahaan' => 'PT Test 2',
            'status' => 'menunggu',
            'bukti_balasan' => $filePath2,
        ]);

        $p3 = PengajuanPkl::create([
            'siswa_id' => $this->siswa->id,
            'nama_perusahaan' => 'PT Test 3',
            'status' => 'menunggu',
            'bukti_balasan' => $filePath3,
        ]);

        $this->actingAs($this->pokja);

        // Bulk delete only first two
        $response = $this->delete(route('pokja.pengajuan_pkl.bulk_destroy'), [
            'ids' => [$p1->id, $p2->id]
        ]);

        $response->assertRedirect();
        
        // Assert first two deleted, third remains
        $this->assertDatabaseMissing('pengajuan_pkls', ['id' => $p1->id]);
        $this->assertDatabaseMissing('pengajuan_pkls', ['id' => $p2->id]);
        $this->assertDatabaseHas('pengajuan_pkls', ['id' => $p3->id]);

        // Assert files for deleted ones are gone, remaining one is kept
        $this->assertFalse(Storage::disk('public')->exists($filePath1));
        $this->assertFalse(Storage::disk('public')->exists($filePath2));
        $this->assertTrue(Storage::disk('public')->exists($filePath3));
    }

    /**
     * Test unauthorized users cannot delete submissions.
     */
    public function test_unauthorized_users_cannot_delete_submissions()
    {
        $pengajuan = PengajuanPkl::create([
            'siswa_id' => $this->siswa->id,
            'nama_perusahaan' => 'PT Test Unauthorized',
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->siswa->user);

        $response = $this->delete(route('pokja.pengajuan_pkl.destroy', $pengajuan->id));
        $response->assertStatus(403);

        $responseBulk = $this->delete(route('pokja.pengajuan_pkl.bulk_destroy'), [
            'ids' => [$pengajuan->id]
        ]);
        $responseBulk->assertStatus(403);
    }
}
