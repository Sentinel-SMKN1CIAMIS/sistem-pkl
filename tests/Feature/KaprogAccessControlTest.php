<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\PengajuanPkl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaprogAccessControlTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Setup test data
     */
    private function setupTestData()
    {
        // Create program keahlian
        $prog1 = ProgramKeahlian::create(['kode' => 'AKL', 'nama' => 'Akuntansi']);
        $prog2 = ProgramKeahlian::create(['kode' => 'MPLB', 'nama' => 'Manajemen Bisnis']);

        // Create konsentrasi keahlian
        $konsentrasi1 = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $prog1->id,
            'kode' => 'AKL',
            'nama' => 'Akuntansi',
            'durasi_pkl_bulan' => 4,
        ]);

        $konsentrasi2 = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $prog2->id,
            'kode' => 'MPLB',
            'nama' => 'Manajemen Bisnis',
            'durasi_pkl_bulan' => 4,
        ]);

        // Create Kaprog users
        $kaprog1 = User::create([
            'name' => 'Kaprog Akuntansi',
            'username' => 'kaprog_akl',
            'email' => 'kaprog_akl@test.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'konsentrasi_keahlian_id' => $konsentrasi1->id,
            'program_keahlian_id' => $prog1->id,
            'is_active' => true,
        ]);

        $kaprog2 = User::create([
            'name' => 'Kaprog Bisnis',
            'username' => 'kaprog_mplb',
            'email' => 'kaprog_mplb@test.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'konsentrasi_keahlian_id' => $konsentrasi2->id,
            'program_keahlian_id' => $prog2->id,
            'is_active' => true,
        ]);

        // Create student users
        $student1 = User::create([
            'name' => 'Siswa AKL 1',
            'username' => 'siswa_akl1',
            'email' => 'siswa_akl1@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $student2 = User::create([
            'name' => 'Siswa AKL 2',
            'username' => 'siswa_akl2',
            'email' => 'siswa_akl2@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $student3 = User::create([
            'name' => 'Siswa MPLB 1',
            'username' => 'siswa_mplb1',
            'email' => 'siswa_mplb1@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        // Create siswas
        $siswa1 = Siswa::create([
            'user_id' => $student1->id,
            'konsentrasi_keahlian_id' => $konsentrasi1->id,
            'nis' => '001',
            'nama_lengkap' => 'Siswa AKL 1',
            'kelas' => '12-AKL',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2025-2026',
        ]);

        $siswa2 = Siswa::create([
            'user_id' => $student2->id,
            'konsentrasi_keahlian_id' => $konsentrasi1->id,
            'nis' => '002',
            'nama_lengkap' => 'Siswa AKL 2',
            'kelas' => '12-AKL',
            'jenis_kelamin' => 'P',
            'tahun_ajaran' => '2025-2026',
        ]);

        $siswa3 = Siswa::create([
            'user_id' => $student3->id,
            'konsentrasi_keahlian_id' => $konsentrasi2->id,
            'nis' => '003',
            'nama_lengkap' => 'Siswa MPLB 1',
            'kelas' => '12-MPLB',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2025-2026',
        ]);

        // Create pengajuan PKL
        PengajuanPkl::create([
            'siswa_id' => $siswa1->id,
            'nama_perusahaan' => 'Perusahaan 1',
            'alamat' => 'Alamat 1',
            'kota' => 'Bandung',
            'no_telp' => '081234567890',
            'status' => 'menunggu',
        ]);

        PengajuanPkl::create([
            'siswa_id' => $siswa2->id,
            'nama_perusahaan' => 'Perusahaan 2',
            'alamat' => 'Alamat 2',
            'kota' => 'Jakarta',
            'no_telp' => '082345678901',
            'status' => 'menunggu',
        ]);

        PengajuanPkl::create([
            'siswa_id' => $siswa3->id,
            'nama_perusahaan' => 'Perusahaan 3',
            'alamat' => 'Alamat 3',
            'kota' => 'Surabaya',
            'no_telp' => '083456789012',
            'status' => 'menunggu',
        ]);

        return [
            'kaprog1' => $kaprog1,
            'kaprog2' => $kaprog2,
            'siswa1' => $siswa1,
            'siswa2' => $siswa2,
            'siswa3' => $siswa3,
            'konsentrasi1' => $konsentrasi1,
            'konsentrasi2' => $konsentrasi2,
        ];
    }

    /**
     * Test Kaprog can see only their class students
     */
    public function test_kaprog_can_see_only_their_class_students()
    {
        $data = $this->setupTestData();

        $response = $this->actingAs($data['kaprog1'])
            ->get(route('kaprog.laporan.index'));

        $response->assertStatus(200);
        $response->assertViewHas('siswas');

        // Get the siswas from the response
        $siswas = $response->viewData('siswas')->items();

        // Verify only Kaprog 1's students are shown
        foreach ($siswas as $siswa) {
            $this->assertEquals($data['konsentrasi1']->id, $siswa->konsentrasi_keahlian_id);
        }
    }

    /**
     * Test Kaprog sees different students based on assigned class
     */
    public function test_different_kaprog_sees_different_students()
    {
        $data = $this->setupTestData();

        // Kaprog 1 should see students from Akuntansi class
        $response1 = $this->actingAs($data['kaprog1'])
            ->get(route('kaprog.laporan.index'));

        $response1->assertStatus(200);
        $siswas1 = $response1->viewData('siswas')->items();
        $this->assertCount(2, $siswas1); // 2 students in Akuntansi

        // Kaprog 2 should see students from Bisnis class
        $response2 = $this->actingAs($data['kaprog2'])
            ->get(route('kaprog.laporan.index'));

        $response2->assertStatus(200);
        $siswas2 = $response2->viewData('siswas')->items();
        $this->assertCount(1, $siswas2); // 1 student in Bisnis
    }

    /**
     * Test Kaprog can only see pengajuan from their class students
     */
    public function test_kaprog_can_see_only_their_class_pengajuan()
    {
        $data = $this->setupTestData();

        $response = $this->actingAs($data['kaprog1'])
            ->get(route('kaprog.pengajuan_pkl.index'));

        $response->assertStatus(200);
        $response->assertViewHas('pengajuans');

        // Verify only Kaprog 1's pengajuans are shown
        $pengajuans = $response->viewData('pengajuans')->items();
        foreach ($pengajuans as $pengajuan) {
            $this->assertEquals($data['konsentrasi1']->id, $pengajuan->siswa->konsentrasi_keahlian_id);
        }
    }

    /**
     * Test Kaprog cannot update pengajuan from other class
     */
    public function test_kaprog_cannot_update_other_class_pengajuan()
    {
        $data = $this->setupTestData();

        // Get pengajuan from Siswa MPLB (different from Kaprog 1's class)
        $pengajuan = PengajuanPkl::where('siswa_id', $data['siswa3']->id)->first();

        $response = $this->actingAs($data['kaprog1'])
            ->patch(route('kaprog.pengajuan_pkl.update', $pengajuan->id), [
                'status' => 'disetujui',
                'catatan' => 'Disetujui',
            ]);

        // Should be forbidden
        $response->assertStatus(403);
    }

    /**
     * Test Kaprog can update pengajuan from their class
     */
    public function test_kaprog_can_update_their_class_pengajuan()
    {
        $data = $this->setupTestData();

        // Get pengajuan from Siswa AKL (same as Kaprog 1's class)
        $pengajuan = PengajuanPkl::where('siswa_id', $data['siswa1']->id)->first();

        $response = $this->actingAs($data['kaprog1'])
            ->patch(route('kaprog.pengajuan_pkl.update', $pengajuan->id), [
                'status' => 'disetujui',
                'catatan' => 'Disetujui',
            ]);

        // Should succeed
        $response->assertStatus(302);

        // Verify the pengajuan was updated
        $this->assertEquals('disetujui', $pengajuan->fresh()->status);
    }

    /**
     * Test Kaprog stats show only their class data
     */
    public function test_kaprog_stats_show_only_their_class_data()
    {
        $data = $this->setupTestData();

        $response = $this->actingAs($data['kaprog1'])
            ->get(route('kaprog.laporan.index'));

        $response->assertStatus(200);

        // Check stats
        $this->assertEquals(2, $response->viewData('totalSiswa')); // 2 students in Akuntansi
        $this->assertEquals(0, $response->viewData('siswaPkl')); // None are in PKL yet
        $this->assertEquals(2, $response->viewData('siswaBelumPkl')); // All haven't started PKL
    }

    /**
     * Test Kaprog without assigned class sees no data
     */
    public function test_kaprog_without_assigned_class_sees_no_data()
    {
        // Create a Kaprog without assigned class
        $kaprogNoClass = User::create([
            'name' => 'Kaprog No Class',
            'username' => 'kaprog_noclass',
            'email' => 'kaprog_noclass@test.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'konsentrasi_keahlian_id' => null, // No assigned class
            'is_active' => true,
        ]);

        $response = $this->actingAs($kaprogNoClass)
            ->get(route('kaprog.laporan.index'));

        $response->assertStatus(200);
        $this->assertCount(0, $response->viewData('siswas')->items());
        $this->assertEquals(0, $response->viewData('totalSiswa'));
    }
}
