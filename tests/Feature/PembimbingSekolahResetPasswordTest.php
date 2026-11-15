<?php

namespace Tests\Feature;

use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\PokjaGroup;
use App\Models\ProgramKeahlian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PembimbingSekolahResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $pokja;
    private User $pembimbingUser1;
    private PembimbingSekolah $pembimbing1;
    private User $pembimbingUser2;
    private PembimbingSekolah $pembimbing2;

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

        // Teacher 1: Old username slug 'acep.muhammad.soleh', official NIP '199409152022211004'
        $this->pembimbingUser1 = User::create([
            'name' => 'Acep Muhammad Soleh, S.Pd.',
            'username' => 'acep.muhammad.soleh',
            'email' => null,
            'password' => bcrypt('smkn1ciamis'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => true,
        ]);

        $this->pembimbing1 = PembimbingSekolah::create([
            'user_id' => $this->pembimbingUser1->id,
            'nama_lengkap' => 'Acep Muhammad Soleh, S.Pd.',
            'nip' => '199409152022211004',
            'tipe' => 'kejuruan',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'kuota_bimbingan' => 20,
        ]);

        // Teacher 2: Username already NIP
        $this->pembimbingUser2 = User::create([
            'name' => 'Budi Santoso, S.Kom',
            'username' => '198701012010011001',
            'email' => null,
            'password' => bcrypt('198701012010011001'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => true,
        ]);

        $this->pembimbing2 = PembimbingSekolah::create([
            'user_id' => $this->pembimbingUser2->id,
            'nama_lengkap' => 'Budi Santoso, S.Kom',
            'nip' => '198701012010011001',
            'tipe' => 'kejuruan',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'kuota_bimbingan' => 20,
        ]);
    }

    public function test_teacher_can_login_using_nip_even_if_username_is_name_slug()
    {
        $response = $this->post('/login', [
            'username' => '199409152022211004',
            'password' => 'smkn1ciamis',
        ]);

        $response->assertRedirect(route('auth.change-password.show'));
        $this->assertAuthenticatedAs($this->pembimbingUser1);
    }

    public function test_teacher_can_login_using_nip_when_default_password_is_nip()
    {
        $response = $this->post('/login', [
            'username' => '198701012010011001',
            'password' => '198701012010011001',
        ]);

        $response->assertRedirect(route('auth.change-password.show'));
        $this->assertAuthenticatedAs($this->pembimbingUser2);
    }

    public function test_teacher_on_default_password_can_login_with_nip_even_if_password_was_smkn1ciamis()
    {
        // $this->pembimbingUser1 has password 'smkn1ciamis' in DB, but enters NIP as password
        $response = $this->post('/login', [
            'username' => '199409152022211004',
            'password' => '199409152022211004',
        ]);

        $response->assertRedirect(route('auth.change-password.show'));
        $this->assertAuthenticatedAs($this->pembimbingUser1);

        // Verify password auto-migrated to NIP
        $this->pembimbingUser1->refresh();
        $this->assertTrue(Hash::check('199409152022211004', $this->pembimbingUser1->password));
    }

    public function test_inactive_teacher_cannot_login()
    {
        $this->pembimbingUser1->update(['is_active' => false]);

        $response = $this->post('/login', [
            'username' => '199409152022211004',
            'password' => 'smkn1ciamis',
        ]);

        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    public function test_pokja_can_individually_reset_teacher_password()
    {
        $this->actingAs($this->pokja);

        $response = $this->post(route('pokja.pembimbing_sekolah.reset_password', $this->pembimbing1));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->pembimbingUser1->refresh();
        $this->assertEquals('199409152022211004', $this->pembimbingUser1->username);
        $this->assertNull($this->pembimbingUser1->email);
        $this->assertTrue($this->pembimbingUser1->is_active);
        $this->assertTrue($this->pembimbingUser1->force_password_change);
        $this->assertTrue(Hash::check('199409152022211004', $this->pembimbingUser1->password));
    }

    public function test_pokja_can_mass_reset_passwords_via_ajax_batches()
    {
        $this->actingAs($this->pokja);

        $response = $this->postJson(route('pokja.pembimbing_sekolah.reset_password_massal'), [
            'offset' => 0,
            'batch_size' => 10,
        ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'done' => true,
        ]);

        $this->pembimbingUser1->refresh();
        $this->assertEquals('199409152022211004', $this->pembimbingUser1->username);
        $this->assertTrue(Hash::check('199409152022211004', $this->pembimbingUser1->password));
        $this->assertTrue($this->pembimbingUser1->is_active);
        $this->assertTrue($this->pembimbingUser1->force_password_change);
    }

    public function test_unauthorized_user_cannot_mass_reset_passwords()
    {
        $siswaUser = User::create([
            'name' => 'Siswa User',
            'username' => 'siswa1',
            'email' => null,
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->actingAs($siswaUser);

        $response = $this->post(route('pokja.pembimbing_sekolah.reset_password_massal'));
        $response->assertForbidden();
    }
}
