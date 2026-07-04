<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PembimbingSekolah;
use App\Models\PembimbingDudi;
use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvisorProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $konsentrasi;
    protected $dudi;

    protected function setUp(): void
    {
        parent::setUp();

        $prog = ProgramKeahlian::create([
            'kode' => 'RPL-P',
            'nama' => 'Program RPL',
        ]);
        $this->konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $prog->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);
        $this->dudi = Dudi::create([
            'nama' => 'PT Test DUDI',
            'alamat' => 'Alamat DUDI',
            'kota' => 'Kota DUDI',
            'is_active' => true,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
        ]);
    }

    public function test_school_advisor_can_access_and_update_profile()
    {
        $user = User::create([
            'name' => 'Guru A',
            'username' => 'gurua',
            'email' => 'gurua@example.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
        ]);

        $pembimbing = PembimbingSekolah::create([
            'user_id' => $user->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nip' => '12345',
            'nama_lengkap' => 'Guru A',
            'tipe' => 'kejuruan',
            'no_hp' => '0812',
            'kapasitas' => 10,
        ]);

        // 1. Can access profile page
        $response = $this->actingAs($user)
            ->get(route('pembimbing_sekolah.profile.index'));
        $response->assertStatus(200);
        $response->assertSee('Guru A');
        $response->assertSee('12345');

        // 2. Can update profile fields
        $response = $this->actingAs($user)
            ->patch(route('pembimbing_sekolah.profile.update'), [
                'nama_lengkap' => 'Guru A Baru',
                'username' => 'gurua_baru',
                'email' => 'gurua_baru@example.com',
                'nip' => '123456',
                'no_hp' => '089999',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify changes in database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Guru A Baru',
            'username' => 'gurua_baru',
            'email' => 'gurua_baru@example.com',
        ]);

        $this->assertDatabaseHas('pembimbing_sekolahs', [
            'id' => $pembimbing->id,
            'nama_lengkap' => 'Guru A Baru',
            'nip' => '123456',
            'no_hp' => '089999',
        ]);
    }

    public function test_dudi_advisor_can_access_and_update_profile()
    {
        $user = User::create([
            'name' => 'Mentor Dudi',
            'username' => 'mentordudi',
            'email' => 'mentordudi@example.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_dudi',
        ]);

        $pembimbing = PembimbingDudi::create([
            'user_id' => $user->id,
            'dudi_id' => $this->dudi->id,
            'nama_lengkap' => 'Mentor Dudi',
            'jabatan' => 'HRD',
            'no_hp' => '0822',
        ]);

        // 1. Can access profile page
        $response = $this->actingAs($user)
            ->get(route('pembimbing_dudi.profile.index'));
        $response->assertStatus(200);
        $response->assertSee('Mentor Dudi');
        $response->assertSee('HRD');

        // 2. Can update profile fields
        $response = $this->actingAs($user)
            ->patch(route('pembimbing_dudi.profile.update'), [
                'nama_lengkap' => 'Mentor Dudi Baru',
                'username' => 'mentordudi_baru',
                'email' => 'mentordudi_baru@example.com',
                'jabatan' => 'Supervisor',
                'no_hp' => '0899991',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify changes in database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Mentor Dudi Baru',
            'username' => 'mentordudi_baru',
            'email' => 'mentordudi_baru@example.com',
        ]);

        $this->assertDatabaseHas('pembimbing_dudis', [
            'id' => $pembimbing->id,
            'nama_lengkap' => 'Mentor Dudi Baru',
            'jabatan' => 'Supervisor',
            'no_hp' => '0899991',
        ]);
    }
}
