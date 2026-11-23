<?php

namespace Tests\Feature;

use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Siswa $siswa;
    private Dudi $dudi;

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

        $this->dudi = Dudi::create([
            'nama' => 'PT Test DUDI',
            'alamat' => 'Jl. Asli No. 123',
            'kota' => 'Ciamis',
            'is_active' => true,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
        ]);

        $this->user = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa123',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $this->user->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $this->dudi->id,
            'nis' => '12345678',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2025/2026',
            'status_pkl' => 'sedang_pkl',
        ]);
    }

    public function test_updating_profile_without_alamat_does_not_nullify_dudi_alamat(): void
    {
        $response = $this->actingAs($this->user)
            ->patch(route('siswa.profile.update'), [
                'no_hp' => '081234567890',
                'alamat' => '', // Empty / null string
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // DUDI address must remain intact, not set to null
        $this->dudi->refresh();
        $this->assertEquals('Jl. Asli No. 123', $this->dudi->alamat);

        // Siswa no_hp is updated
        $this->siswa->refresh();
        $this->assertEquals('081234567890', $this->siswa->no_hp);
    }

    public function test_updating_profile_with_new_alamat_updates_dudi_alamat(): void
    {
        $response = $this->actingAs($this->user)
            ->patch(route('siswa.profile.update'), [
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Baru No. 456 Ciamis',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->dudi->refresh();
        $this->assertEquals('Jl. Baru No. 456 Ciamis', $this->dudi->alamat);
    }
}
