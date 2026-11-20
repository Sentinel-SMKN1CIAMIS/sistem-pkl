<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingDudi;
use App\Models\PokjaGroup;
use App\Models\ProgramKeahlian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PembimbingDudiPasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $pokja;
    private User $siswaUser;
    private User $mentorUser;
    private PembimbingDudi $pembimbingDudi;

    protected function setUp(): void
    {
        parent::setUp();

        $program = ProgramKeahlian::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
        ]);

        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 4,
        ]);

        $dudi = Dudi::create([
            'nama' => 'PT Nusantara Teknologi',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Merdeka No. 10',
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'admin@test.com',
            'password' => Hash::make('AdminPass123'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->pokja = User::create([
            'name' => 'Petugas Pokja',
            'username' => 'pokja_user',
            'email' => 'pokja@test.com',
            'password' => Hash::make('PokjaPass123'),
            'role' => 'pokja',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $pokjaGroup = PokjaGroup::create([
            'name' => 'Grup Pokja Utama',
            'is_active' => true,
        ]);
        $pokjaGroup->users()->attach($this->pokja->id);

        $this->siswaUser = User::create([
            'name' => 'Siswa Biasa',
            'username' => '242500999',
            'email' => 'siswa@test.com',
            'password' => Hash::make('SiswaPass123'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->mentorUser = User::create([
            'name' => 'Budi Santoso',
            'username' => 'budi.dudi',
            'email' => 'budi@dudi.test',
            'password' => Hash::make('OldPassword123'),
            'role' => 'pembimbing_dudi',
            'is_active' => true,
            'force_password_change' => true,
        ]);

        $this->pembimbingDudi = PembimbingDudi::create([
            'user_id' => $this->mentorUser->id,
            'dudi_id' => $dudi->id,
            'nama_lengkap' => 'Budi Santoso',
            'jabatan' => 'HRD Manager',
            'no_hp' => '081234567890',
        ]);
    }

    public function test_super_admin_can_change_pembimbing_dudi_password(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->from(route('pokja.pembimbing_dudi.index'))
            ->put(route('admin.pembimbing_dudi.password.update', $this->pembimbingDudi), [
                'password' => 'SandiBaru123',
                'password_confirmation' => 'SandiBaru123',
            ]);

        $response->assertRedirect(route('pokja.pembimbing_dudi.index'))
            ->assertSessionHas('success');

        $freshUser = $this->mentorUser->fresh();
        $this->assertTrue(Hash::check('SandiBaru123', $freshUser->password));
        $this->assertFalse(Hash::check('OldPassword123', $freshUser->password));
        $this->assertFalse($freshUser->force_password_change);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->superAdmin->id,
            'action' => 'Ganti Password Pembimbing DUDI',
        ]);
    }

    public function test_super_admin_can_require_force_password_change_on_next_login(): void
    {
        $this->actingAs($this->superAdmin)
            ->put(route('admin.pembimbing_dudi.password.update', $this->pembimbingDudi), [
                'password' => 'SandiWajibGanti1',
                'password_confirmation' => 'SandiWajibGanti1',
                'force_password_change' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $freshUser = $this->mentorUser->fresh();
        $this->assertTrue(Hash::check('SandiWajibGanti1', $freshUser->password));
        $this->assertTrue($freshUser->force_password_change);
    }

    public function test_password_change_validates_minimum_length_and_confirmation(): void
    {
        // Too short (< 8 chars)
        $this->actingAs($this->superAdmin)
            ->put(route('admin.pembimbing_dudi.password.update', $this->pembimbingDudi), [
                'password' => 'short1',
                'password_confirmation' => 'short1',
            ])
            ->assertSessionHasErrors('password');

        // Mismatched confirmation
        $this->actingAs($this->superAdmin)
            ->put(route('admin.pembimbing_dudi.password.update', $this->pembimbingDudi), [
                'password' => 'ValidPass123',
                'password_confirmation' => 'DifferentPass123',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('OldPassword123', $this->mentorUser->fresh()->password));
    }

    public function test_non_super_admin_cannot_change_pembimbing_dudi_password(): void
    {
        $this->mentorUser->update(['force_password_change' => false]);

        foreach ([$this->pokja, $this->mentorUser, $this->siswaUser] as $unauthorizedUser) {
            $this->actingAs($unauthorizedUser)
                ->put(route('admin.pembimbing_dudi.password.update', $this->pembimbingDudi), [
                    'password' => 'HackedPass123',
                    'password_confirmation' => 'HackedPass123',
                ])
                ->assertStatus(403);
        }

        $this->assertTrue(Hash::check('OldPassword123', $this->mentorUser->fresh()->password));
    }

    public function test_ganti_sandi_ui_is_only_visible_to_super_admin(): void
    {
        // Super Admin sees controls on both index and edit pages
        $this->actingAs($this->superAdmin)
            ->get(route('pokja.pembimbing_dudi.index'))
            ->assertOk()
            ->assertSee('Ganti Sandi')
            ->assertSee('Ganti Kata Sandi Pembimbing DUDI');

        $this->actingAs($this->superAdmin)
            ->get(route('pokja.pembimbing_dudi.edit', $this->pembimbingDudi))
            ->assertOk()
            ->assertSee('Ganti Kata Sandi Akun (Super Admin)');

        // Pokja does NOT see Ganti Sandi controls on index or edit pages
        $this->actingAs($this->pokja)
            ->get(route('pokja.pembimbing_dudi.index'))
            ->assertOk()
            ->assertDontSee('Ganti Sandi')
            ->assertDontSee('Ganti Kata Sandi Pembimbing DUDI');

        $this->actingAs($this->pokja)
            ->get(route('pokja.pembimbing_dudi.edit', $this->pembimbingDudi))
            ->assertOk()
            ->assertDontSee('Ganti Kata Sandi Akun (Super Admin)');
    }
}
