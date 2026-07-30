<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Dudi;
use App\Models\Zona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterPembimbingDudiTest extends TestCase
{
    use RefreshDatabase;

    protected $dudi;
    protected $konsentrasi;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Program and Konsentrasi
        $prog = \App\Models\ProgramKeahlian::create([
            'kode' => 'RPL-P',
            'nama' => 'Program RPL',
        ]);
        $this->konsentrasi = \App\Models\KonsentrasiKeahlian::create([
            'program_keahlian_id' => $prog->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        // Create a DUDI record
        $this->dudi = Dudi::create([
            'nama' => 'PT Test DUDI',
            'alamat' => 'Alamat DUDI',
            'kota' => 'Kota DUDI',
            'is_active' => true,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
        ]);
    }

    public function test_registration_page_is_accessible()
    {
        $response = $this->get(route('register.pembimbing_dudi.show'));
        $response->assertStatus(200);
        $response->assertSee('Registrasi Pembimbing DUDI');
    }

    public function test_successful_registration()
    {
        $response = $this->post(route('register.pembimbing_dudi.store'), [
            'nama_lengkap' => 'Ahmad Badrudin',
            'username' => 'ahmad_badrudin',
            'email' => 'ahmad@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'jabatan' => 'HRD Manager',
            'no_hp' => '081234567890',
            'dudi_id' => $this->dudi->id,
            'latitude' => '-7.3305',
            'longitude' => '108.3521',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'username' => 'ahmad_badrudin',
            'email' => 'ahmad@example.com',
            'role' => 'pembimbing_dudi',
        ]);

        // Assert pembimbing dudi was created
        $this->assertDatabaseHas('pembimbing_dudis', [
            'nama_lengkap' => 'Ahmad Badrudin',
            'jabatan' => 'HRD Manager',
            'no_hp' => '081234567890',
            'dudi_id' => $this->dudi->id,
        ]);

        // Assert Dudi coordinate was updated
        $this->dudi->refresh();
        $this->assertEquals(-7.3305, $this->dudi->latitude);
        $this->assertEquals(108.3521, $this->dudi->longitude);

        // Assert user is logged in
        $this->assertAuthenticated();
    }

    public function test_validation_errors_for_required_fields()
    {
        $response = $this->post(route('register.pembimbing_dudi.store'), []);

        $response->assertSessionHasErrors([
            'nama_lengkap', 'username', 'email', 'password', 'no_hp', 'dudi_id', 'latitude', 'longitude'
        ]);
    }

    public function test_validation_errors_for_weak_password()
    {
        $response = $this->post(route('register.pembimbing_dudi.store'), [
            'nama_lengkap' => 'Ahmad Badrudin',
            'username' => 'ahmad_badrudin',
            'email' => 'ahmad@example.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'no_hp' => '081234567890',
            'dudi_id' => $this->dudi->id,
            'latitude' => '-7.3305',
            'longitude' => '108.3521',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_validation_errors_for_invalid_phone_number()
    {
        $response = $this->post(route('register.pembimbing_dudi.store'), [
            'nama_lengkap' => 'Ahmad Badrudin',
            'username' => 'ahmad_badrudin',
            'email' => 'ahmad@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'no_hp' => 'notanumber',
            'dudi_id' => $this->dudi->id,
            'latitude' => '-7.3305',
            'longitude' => '108.3521',
        ]);

        $response->assertSessionHasErrors(['no_hp']);
    }
}
