<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\Dudi;
use App\Models\PokjaGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemetaanMapFilterTest extends TestCase
{
    use RefreshDatabase;

    private $pokjaUser;
    private $kaprogUser;
    private $kaprogUserOther;
    private $progRPL;
    private $progTKJ;
    private $konRPL1;
    private $konRPL2;
    private $konTKJ;
    private $dudiRPL;
    private $dudiTKJ;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Program & Konsentrasi Keahlian
        $this->progRPL = ProgramKeahlian::create(['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak']);
        $this->progTKJ = ProgramKeahlian::create(['kode' => 'TKJ', 'nama' => 'Teknik Komputer Jaringan']);

        $this->konRPL1 = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->progRPL->id,
            'kode' => 'RPL1',
            'nama' => 'RPL Kelas 1',
            'durasi_pkl_bulan' => 6,
        ]);

        $this->konRPL2 = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->progRPL->id,
            'kode' => 'RPL2',
            'nama' => 'RPL Kelas 2',
            'durasi_pkl_bulan' => 6,
        ]);

        $this->konTKJ = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->progTKJ->id,
            'kode' => 'TKJ1',
            'nama' => 'TKJ Kelas 1',
            'durasi_pkl_bulan' => 6,
        ]);

        // Create DUDIs with coordinates and different jenis_industri
        $this->dudiRPL = Dudi::create([
            'nama' => 'DUDI Teknologi RPL',
            'alamat' => 'Alamat RPL',
            'kota' => 'Kota RPL',
            'latitude' => -7.123456,
            'longitude' => 108.123456,
            'is_active' => true,
            'konsentrasi_keahlian_id' => $this->konRPL1->id,
            'jenis_industri' => 'teknologi',
        ]);
        $this->dudiRPL->konsentrasiKeahlians()->sync([$this->konRPL1->id]);

        $this->dudiTKJ = Dudi::create([
            'nama' => 'DUDI Industri TKJ',
            'alamat' => 'Alamat TKJ',
            'kota' => 'Kota TKJ',
            'latitude' => -7.654321,
            'longitude' => 108.654321,
            'is_active' => true,
            'konsentrasi_keahlian_id' => $this->konTKJ->id,
            'jenis_industri' => 'industri',
        ]);
        $this->dudiTKJ->konsentrasiKeahlians()->sync([$this->konTKJ->id]);

        // Create Pokja User
        $this->pokjaUser = User::create([
            'name' => 'Pokja User',
            'username' => 'pokja1',
            'email' => 'pokja1@test.com',
            'password' => bcrypt('password'),
            'role' => 'pokja',
            'is_active' => true,
        ]);

        // Create active Pokja group and attach the user
        $group = PokjaGroup::create([
            'name' => 'Grup Pokja Utama',
            'is_active' => true,
        ]);
        $group->users()->attach($this->pokjaUser->id);

        // Create Kaprog Users
        $this->kaprogUser = User::create([
            'name' => 'Kaprog RPL',
            'username' => 'kaprog_rpl',
            'email' => 'kaprog_rpl@test.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'program_keahlian_id' => $this->progRPL->id,
            'is_active' => true,
        ]);

        $this->kaprogUserOther = User::create([
            'name' => 'Kaprog TKJ',
            'username' => 'kaprog_tkj',
            'email' => 'kaprog_tkj@test.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'program_keahlian_id' => $this->progTKJ->id,
            'is_active' => true,
        ]);
    }

    public function test_pokja_can_create_dudi_with_jenis_industri()
    {
        $this->actingAs($this->pokjaUser);

        $response = $this->post(route('pokja.dudi.store'), [
            'konsentrasi_keahlian_ids' => [$this->konRPL1->id],
            'nama' => 'Dudi Baru Pemerintahan',
            'alamat' => 'Alamat Dudi Baru',
            'kota' => 'Ciamis',
            'jenis_industri' => 'pemerintahan',
        ]);

        $response->assertRedirect(route('pokja.dudi.index'));
        $this->assertDatabaseHas('dudis', [
            'nama' => 'Dudi Baru Pemerintahan',
            'jenis_industri' => 'pemerintahan',
        ]);
    }

    public function test_pokja_can_view_all_dudi_on_map_and_filter()
    {
        $this->actingAs($this->pokjaUser);

        $response = $this->get(route('shared.pemetaan.maps'));
        $response->assertStatus(200);
        $response->assertViewHas('programKeahlians');
        $response->assertViewHas('konsentrasiKeahlians');

        // Check data without filters
        $responseJson = $this->get(route('shared.pemetaan.maps.data'));
        $responseJson->assertStatus(200);
        $data = $responseJson->json();
        $this->assertCount(2, $data['markers']);

        // Check filter by program RPL
        $responseFiltered = $this->get(route('shared.pemetaan.maps.data', [
            'program_keahlian_id' => $this->progRPL->id
        ]));
        $responseFiltered->assertStatus(200);
        $dataFiltered = $responseFiltered->json();
        $this->assertCount(1, $dataFiltered['markers']);
        $this->assertEquals('DUDI Teknologi RPL', $dataFiltered['markers'][0]['nama']);
    }

    public function test_kaprog_can_only_view_dudi_in_their_major()
    {
        // Login as Kaprog RPL
        $this->actingAs($this->kaprogUser);

        $response = $this->get(route('shared.pemetaan.maps'));
        $response->assertStatus(200);
        
        // Kaprog should not get ProgramKeahlians in view, only KonsentrasiKeahlians of their major
        $this->assertEmpty($response->viewData('programKeahlians'));
        $this->assertCount(2, $response->viewData('konsentrasiKeahlians')); // RPL1 and RPL2

        // Check JSON data
        $responseJson = $this->get(route('shared.pemetaan.maps.data'));
        $responseJson->assertStatus(200);
        $data = $responseJson->json();

        // Should only see DUDI Teknologi RPL
        $this->assertCount(1, $data['markers']);
        $this->assertEquals('DUDI Teknologi RPL', $data['markers'][0]['nama']);
    }
}
