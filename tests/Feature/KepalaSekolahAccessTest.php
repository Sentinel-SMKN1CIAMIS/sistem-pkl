<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepalaSekolahAccessTest extends TestCase
{
    use RefreshDatabase;

    private function setupTestData()
    {
        $program = ProgramKeahlian::create(['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak']);
        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6
        ]);

        $kepsek = User::create([
            'name' => 'Kepala Sekolah',
            'username' => 'kepsek',
            'email' => 'kepsek@test.com',
            'password' => bcrypt('password'),
            'role' => 'kepala_sekolah',
            'is_active' => true
        ]);

        $studentUser = User::create([
            'name' => 'Siswa RPL',
            'username' => 'siswa_rpl',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true
        ]);

        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nis' => '12345',
            'nama_lengkap' => 'Siswa RPL 1',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2025/2026'
        ]);

        return compact('kepsek', 'siswa', 'konsentrasi');
    }

    public function test_kepala_sekolah_can_access_dashboard()
    {
        $data = $this->setupTestData();

        $response = $this->actingAs($data['kepsek'])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboards.pokja');
    }

    public function test_kepala_sekolah_can_view_siswa_index()
    {
        $data = $this->setupTestData();

        $response = $this->actingAs($data['kepsek'])
            ->get(route('pokja.siswa.index'));

        $response->assertStatus(200);
        $response->assertSee($data['siswa']->nama_lengkap);
    }

    public function test_kepala_sekolah_cannot_add_siswa()
    {
        $data = $this->setupTestData();

        // Trying to access create form - is allowed to view (GET), but we block modifications (POST)
        $response = $this->actingAs($data['kepsek'])
            ->post(route('pokja.siswa.store'), [
                'nama_lengkap' => 'Siswa Baru',
                'nis' => '99999'
            ]);

        // Should be forbidden by ViewOnlyMiddleware
        $response->assertStatus(403);
    }

    public function test_kepala_sekolah_cannot_delete_siswa()
    {
        $data = $this->setupTestData();

        $response = $this->actingAs($data['kepsek'])
            ->delete(route('pokja.siswa.destroy', $data['siswa']->id));

        $response->assertStatus(403);
    }

    public function test_only_one_kepala_sekolah_allowed_to_be_created()
    {
        $data = $this->setupTestData();
        
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'admin_super',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true
        ]);

        // Creating another kepala_sekolah should fail validation
        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'username' => 'kepsek_two',
                'role' => 'kepala_sekolah',
                'password' => 'password123',
                'password_confirmation' => 'password123'
            ]);

        $response->assertSessionHasErrors('role');
    }
}
