<?php

namespace Tests\Feature;

use App\Models\KonsentrasiKeahlian;
use App\Models\PokjaGroup;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SiswaPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private User $pokja;
    private User $superAdmin;
    private User $kepalaSekolah;
    private Siswa $siswa1;
    private Siswa $siswa2;

    protected function setUp(): void
    {
        parent::setUp();

        $prog = ProgramKeahlian::create(['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak']);
        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $prog->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        $this->pokja = User::create([
            'name' => 'Pokja User',
            'username' => 'pokja1',
            'email' => 'pokja@test.com',
            'password' => bcrypt('password'),
            'role' => 'pokja',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $group = PokjaGroup::create([
            'name' => 'Grup Pokja Utama',
            'is_active' => true,
        ]);
        $group->users()->attach($this->pokja->id);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->kepalaSekolah = User::create([
            'name' => 'Kepala Sekolah',
            'username' => 'kepsek',
            'email' => 'kepsek@test.com',
            'password' => bcrypt('password'),
            'role' => 'kepala_sekolah',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        // Siswa 1
        $userSiswa1 = User::create([
            'name' => 'Siswa Pertama',
            'username' => '12345678',
            'email' => 'siswa1@test.com',
            'password' => Hash::make('CustomPass123!'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => true,
        ]);
        $this->siswa1 = Siswa::create([
            'user_id' => $userSiswa1->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nis' => '12345678',
            'nama_lengkap' => 'Siswa Pertama',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2025/2026',
        ]);

        // Siswa 2
        $userSiswa2 = User::create([
            'name' => 'Siswa Kedua',
            'username' => '87654321',
            'email' => 'siswa2@test.com',
            'password' => Hash::make('AnotherCustom456!'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => true,
        ]);
        $this->siswa2 = Siswa::create([
            'user_id' => $userSiswa2->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nis' => '87654321',
            'nama_lengkap' => 'Siswa Kedua',
            'kelas' => 'XII RPL 2',
            'jenis_kelamin' => 'P',
            'tahun_ajaran' => '2025/2026',
        ]);
    }

    public function test_pokja_can_reset_all_student_passwords_to_default_nis(): void
    {
        $response = $this->actingAs($this->pokja)
            ->post(route('pokja.siswa.reset-passwords'));

        $response->assertRedirect(route('pokja.siswa.index'));
        $response->assertSessionHas('success');

        $this->siswa1->refresh();
        $this->siswa2->refresh();

        // Passwords must match NIS
        $this->assertTrue(Hash::check('12345678', $this->siswa1->user->password));
        $this->assertTrue(Hash::check('87654321', $this->siswa2->user->password));

        // force_password_change must be false
        $this->assertFalse((bool) $this->siswa1->user->force_password_change);
        $this->assertFalse((bool) $this->siswa2->user->force_password_change);
    }

    public function test_pokja_can_reset_single_student_password_to_nis(): void
    {
        $response = $this->actingAs($this->pokja)
            ->post(route('pokja.siswa.reset-password', $this->siswa1));

        $response->assertRedirect(route('pokja.siswa.index'));
        $response->assertSessionHas('success');

        $this->siswa1->refresh();
        $this->siswa2->refresh();

        // Siswa 1 password is now NIS
        $this->assertTrue(Hash::check('12345678', $this->siswa1->user->password));
        $this->assertFalse((bool) $this->siswa1->user->force_password_change);

        // Siswa 2 password remains untouched
        $this->assertTrue(Hash::check('AnotherCustom456!', $this->siswa2->user->password));
    }

    public function test_pokja_can_reset_passwords_in_chunks_via_ajax(): void
    {
        $response1 = $this->actingAs($this->pokja)
            ->postJson(route('pokja.siswa.reset-passwords'), [
                'offset' => 0,
                'batch_size' => 1,
            ]);

        $response1->assertOk()
            ->assertJson([
                'status' => 'success',
                'batch_processed' => 1,
                'offset' => 1,
                'total' => 2,
                'done' => false,
            ]);

        $response2 = $this->actingAs($this->pokja)
            ->postJson(route('pokja.siswa.reset-passwords'), [
                'offset' => 1,
                'batch_size' => 1,
            ]);

        $response2->assertOk()
            ->assertJson([
                'status' => 'success',
                'batch_processed' => 1,
                'offset' => 2,
                'total' => 2,
                'done' => true,
            ]);

        $this->siswa1->refresh();
        $this->siswa2->refresh();

        $this->assertTrue(Hash::check('12345678', $this->siswa1->user->password));
        $this->assertTrue(Hash::check('87654321', $this->siswa2->user->password));
    }

    public function test_kepala_sekolah_cannot_reset_passwords_view_only(): void
    {
        $response = $this->actingAs($this->kepalaSekolah)
            ->post(route('pokja.siswa.reset-passwords'));

        $response->assertStatus(403);
    }
}

