<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\Dudi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembimbingSekolahMapTest extends TestCase
{
    use RefreshDatabase;

    private $pembimbing1;
    private $pembimbing2;
    private $dudi1;
    private $dudi2;
    private $siswa1;
    private $siswa2;
    private $pembimbingUser1;
    private $pembimbingUser2;

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

        // Create Dudis
        $this->dudi1 = Dudi::create([
            'nama' => 'DUDI 1 Guided',
            'alamat' => 'Alamat DUDI 1',
            'kota' => 'Kota DUDI 1',
            'latitude' => -7.123456,
            'longitude' => 108.123456,
            'is_active' => true,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
        ]);

        $this->dudi2 = Dudi::create([
            'nama' => 'DUDI 2 Not Guided',
            'alamat' => 'Alamat DUDI 2',
            'kota' => 'Kota DUDI 2',
            'latitude' => -7.654321,
            'longitude' => 108.654321,
            'is_active' => true,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
        ]);

        // Create Advisors
        $this->pembimbingUser1 = User::create([
            'name' => 'Advisor 1',
            'username' => 'adv1',
            'email' => 'adv1@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);
        $this->pembimbing1 = PembimbingSekolah::create([
            'user_id' => $this->pembimbingUser1->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nip' => '11111111',
            'nama_lengkap' => 'Advisor 1',
            'tipe' => 'kejuruan',
        ]);

        $this->pembimbingUser2 = User::create([
            'name' => 'Advisor 2',
            'username' => 'adv2',
            'email' => 'adv2@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);
        $this->pembimbing2 = PembimbingSekolah::create([
            'user_id' => $this->pembimbingUser2->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nip' => '22222222',
            'nama_lengkap' => 'Advisor 2',
            'tipe' => 'umum',
        ]);

        // Create Students
        $siswaUser1 = User::create([
            'name' => 'Siswa 1',
            'username' => 'sis1',
            'email' => 'sis1@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $this->siswa1 = Siswa::create([
            'user_id' => $siswaUser1->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nis' => 'sis1',
            'nama_lengkap' => 'Siswa 1',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'dudi_id' => $this->dudi1->id,
            'pembimbing_sekolah_id' => $this->pembimbing1->id,
        ]);

        $siswaUser2 = User::create([
            'name' => 'Siswa 2',
            'username' => 'sis2',
            'email' => 'sis2@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $this->siswa2 = Siswa::create([
            'user_id' => $siswaUser2->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nis' => 'sis2',
            'nama_lengkap' => 'Siswa 2',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'P',
            'tahun_ajaran' => '2026/2027',
            'dudi_id' => $this->dudi2->id,
            'pembimbing_sekolah_umum_id' => $this->pembimbing2->id,
        ]);
    }

    public function test_school_advisor_maps_scoped_to_guided_students_only()
    {
        // 1. Authenticate as Advisor 1 (Vocational)
        $this->actingAs($this->pembimbingUser1);

        $response = $this->get(route('shared.pemetaan.maps'));
        $response->assertStatus(200);
        $response->assertViewHas('totalDudi', 1);
        $response->assertViewHas('totalSiswa', 1);

        // Check map JSON data
        $responseJson = $this->get(route('shared.pemetaan.maps.data'));
        $responseJson->assertStatus(200);
        
        $data = $responseJson->json();
        $markers = $data['markers'];

        // Should only contain Dudi 1
        $this->assertCount(1, $markers);
        $this->assertEquals('DUDI 1 Guided', $markers[0]['nama']);
    }

    public function test_general_advisor_maps_scoped_to_guided_students_only()
    {
        // 2. Authenticate as Advisor 2 (General / Umum)
        $this->actingAs($this->pembimbingUser2);

        $response = $this->get(route('shared.pemetaan.maps'));
        $response->assertStatus(200);
        $response->assertViewHas('totalDudi', 1);
        $response->assertViewHas('totalSiswa', 1);

        // Check map JSON data
        $responseJson = $this->get(route('shared.pemetaan.maps.data'));
        $responseJson->assertStatus(200);
        
        $data = $responseJson->json();
        $markers = $data['markers'];

        // Should only contain Dudi 2
        $this->assertCount(1, $markers);
        $this->assertEquals('DUDI 2 Not Guided', $markers[0]['nama']);
    }
}
