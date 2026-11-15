<?php

namespace Tests\Feature;

use App\Models\BukuPanduan;
use App\Models\Dudi;
use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingDudi;
use App\Models\PembimbingSekolah;
use App\Models\PokjaGroup;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $pokjaUser;
    protected ProgramKeahlian $program;
    protected KonsentrasiKeahlian $konsentrasi;
    protected PokjaGroup $pokjaGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $this->pokjaUser = User::factory()->create([
            'role' => 'pokja',
        ]);

        $this->pokjaGroup = PokjaGroup::create([
            'name' => 'Pokja Unit Test',
            'is_active' => true,
        ]);
        $this->pokjaGroup->users()->attach($this->pokjaUser->id);

        $this->program = ProgramKeahlian::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
        ]);

        $this->konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->program->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
        ]);
    }

    public function test_pokja_can_bulk_delete_siswa_and_associated_users(): void
    {
        $u1 = User::factory()->create(['role' => 'siswa']);
        $s1 = Siswa::create([
            'user_id' => $u1->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => '11111111',
            'nama_lengkap' => 'Siswa Satu',
            'jenis_kelamin' => 'L',
            'kelas' => 'XII RPL 1',
            'tahun_ajaran' => '2026/2027',
        ]);

        $u2 = User::factory()->create(['role' => 'siswa']);
        $s2 = Siswa::create([
            'user_id' => $u2->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => '22222222',
            'nama_lengkap' => 'Siswa Dua',
            'jenis_kelamin' => 'P',
            'kelas' => 'XII RPL 2',
            'tahun_ajaran' => '2026/2027',
        ]);

        $response = $this->actingAs($this->pokjaUser)
            ->post(route('pokja.siswa.bulk_destroy'), [
                'ids' => [$s1->id, $s2->id],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('siswas', ['id' => $s1->id]);
        $this->assertDatabaseMissing('siswas', ['id' => $s2->id]);
        $this->assertDatabaseMissing('users', ['id' => $u1->id]);
        $this->assertDatabaseMissing('users', ['id' => $u2->id]);
    }

    public function test_pokja_can_bulk_delete_pembimbing_sekolah_and_users(): void
    {
        $u1 = User::factory()->create(['role' => 'pembimbing_sekolah']);
        $p1 = PembimbingSekolah::create([
            'user_id' => $u1->id,
            'nama_lengkap' => 'Guru Satu, S.Pd.',
            'nip' => '199001012020011001',
            'tipe' => 'kejuruan',
            'kapasitas' => 10,
        ]);

        $u2 = User::factory()->create(['role' => 'pembimbing_sekolah']);
        $p2 = PembimbingSekolah::create([
            'user_id' => $u2->id,
            'nama_lengkap' => 'Guru Dua, S.Pd.',
            'nip' => '199002022020012002',
            'tipe' => 'umum',
            'kapasitas' => 10,
        ]);

        $response = $this->actingAs($this->pokjaUser)
            ->post(route('pokja.pembimbing_sekolah.bulk_destroy'), [
                'ids' => [$p1->id, $p2->id],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('pembimbing_sekolahs', ['id' => $p1->id]);
        $this->assertDatabaseMissing('pembimbing_sekolahs', ['id' => $p2->id]);
        $this->assertDatabaseMissing('users', ['id' => $u1->id]);
        $this->assertDatabaseMissing('users', ['id' => $u2->id]);
    }

    public function test_pokja_can_bulk_delete_dudi(): void
    {
        $d1 = Dudi::create([
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nama' => 'PT Maju Terus',
            'alamat' => 'Jl. Merdeka No 1',
            'kota' => 'Ciamis',
            'kuota' => 5,
        ]);

        $d2 = Dudi::create([
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nama' => 'CV Sukses Selalu',
            'alamat' => 'Jl. Sudirman No 2',
            'kota' => 'Ciamis',
            'kuota' => 3,
        ]);

        $response = $this->actingAs($this->pokjaUser)
            ->post(route('pokja.dudi.bulk_destroy'), [
                'ids' => [$d1->id, $d2->id],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('dudis', ['id' => $d1->id]);
        $this->assertDatabaseMissing('dudis', ['id' => $d2->id]);
    }

    public function test_pokja_can_bulk_delete_zona(): void
    {
        $z1 = Zona::create(['nama' => 'Zona Test A', 'koordinat_geojson' => '{}']);
        $z2 = Zona::create(['nama' => 'Zona Test B', 'koordinat_geojson' => '{}']);

        $response = $this->actingAs($this->pokjaUser)
            ->post(route('pokja.zona.bulk_destroy'), [
                'ids' => [$z1->id, $z2->id],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('zonas', ['id' => $z1->id]);
        $this->assertDatabaseMissing('zonas', ['id' => $z2->id]);
    }

    public function test_admin_can_bulk_delete_program_keahlian(): void
    {
        $p1 = ProgramKeahlian::create(['kode' => 'PK1', 'nama' => 'Prog 1']);
        $p2 = ProgramKeahlian::create(['kode' => 'PK2', 'nama' => 'Prog 2']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.program_keahlian.bulk-destroy'), [
                'ids' => [$p1->id, $p2->id],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('program_keahlians', ['id' => $p1->id]);
        $this->assertDatabaseMissing('program_keahlians', ['id' => $p2->id]);
    }

    public function test_admin_can_bulk_delete_konsentrasi_keahlian(): void
    {
        $k1 = KonsentrasiKeahlian::create(['program_keahlian_id' => $this->program->id, 'kode' => 'K1', 'nama' => 'Konsentrasi 1']);
        $k2 = KonsentrasiKeahlian::create(['program_keahlian_id' => $this->program->id, 'kode' => 'K2', 'nama' => 'Konsentrasi 2']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.konsentrasi_keahlian.bulk-destroy'), [
                'ids' => [$k1->id, $k2->id],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('konsentrasi_keahlians', ['id' => $k1->id]);
        $this->assertDatabaseMissing('konsentrasi_keahlians', ['id' => $k2->id]);
    }

    public function test_admin_can_bulk_delete_pokja_groups(): void
    {
        $g1 = PokjaGroup::create(['name' => 'Group A', 'is_active' => true]);
        $g2 = PokjaGroup::create(['name' => 'Group B', 'is_active' => true]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.pokja-groups.bulk-destroy'), [
                'ids' => [$g1->id, $g2->id],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('pokja_groups', ['id' => $g1->id]);
        $this->assertDatabaseMissing('pokja_groups', ['id' => $g2->id]);
    }

    public function test_pokja_can_bulk_delete_kompetensi(): void
    {
        $c1 = Kompetensi::create(['konsentrasi_keahlian_id' => $this->konsentrasi->id, 'kode' => 'CP1', 'nama' => 'Capaian 1']);
        $c2 = Kompetensi::create(['konsentrasi_keahlian_id' => $this->konsentrasi->id, 'kode' => 'CP2', 'nama' => 'Capaian 2']);

        $response = $this->actingAs($this->pokjaUser)
            ->post(route('pokja.kompetensi.bulk_destroy'), [
                'ids' => [$c1->id, $c2->id],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('kompetensis', ['id' => $c1->id]);
        $this->assertDatabaseMissing('kompetensis', ['id' => $c2->id]);
    }

    public function test_unauthorized_user_cannot_bulk_delete(): void
    {
        $d1 = Dudi::create([
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nama' => 'PT Maju Terus',
            'alamat' => 'Jl. Merdeka No 1',
            'kota' => 'Ciamis',
            'kuota' => 5,
        ]);

        $siswaUser = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($siswaUser)
            ->post(route('pokja.dudi.bulk_destroy'), [
                'ids' => [$d1->id],
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('dudis', ['id' => $d1->id]);
    }
}
