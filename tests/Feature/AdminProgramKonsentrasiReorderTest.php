<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ProgramKeahlian;
use App\Models\KonsentrasiKeahlian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProgramKonsentrasiReorderTest extends TestCase
{
    use RefreshDatabase;

    private $pokjaUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->pokjaUser = User::create([
            'name' => 'Ketua Pokja',
            'username' => 'pokja',
            'email' => 'pokja@example.com',
            'password' => bcrypt('password'),
            'role' => 'pokja',
            'is_active' => true,
            'force_password_change' => false,
        ]);
    }

    public function test_can_reorder_program_keahlians()
    {
        $p1 = ProgramKeahlian::create(['kode' => 'P1', 'nama' => 'Program 1', 'sort_order' => 0]);
        $p2 = ProgramKeahlian::create(['kode' => 'P2', 'nama' => 'Program 2', 'sort_order' => 1]);
        $p3 = ProgramKeahlian::create(['kode' => 'P3', 'nama' => 'Program 3', 'sort_order' => 2]);

        // Reorder to: P3, P1, P2
        $response = $this->actingAs($this->pokjaUser)
            ->postJson(route('admin.program_keahlian.reorder'), [
                'ids' => [$p3->id, $p1->id, $p2->id]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Urutan program keahlian berhasil diperbarui.',
        ]);

        $this->assertEquals(0, $p3->refresh()->sort_order);
        $this->assertEquals(1, $p1->refresh()->sort_order);
        $this->assertEquals(2, $p2->refresh()->sort_order);
    }

    public function test_can_reorder_konsentrasi_keahlians()
    {
        $program = ProgramKeahlian::create(['kode' => 'PPLG', 'nama' => 'PPLG']);

        $c1 = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'C1',
            'nama' => 'Concentration 1',
            'sort_order' => 0,
            'durasi_pkl_bulan' => 4
        ]);
        $c2 = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'C2',
            'nama' => 'Concentration 2',
            'sort_order' => 1,
            'durasi_pkl_bulan' => 4
        ]);

        // Reorder to: c2, c1
        $response = $this->actingAs($this->pokjaUser)
            ->postJson(route('admin.konsentrasi_keahlian.reorder'), [
                'ids' => [$c2->id, $c1->id]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Urutan konsentrasi keahlian berhasil diperbarui.',
        ]);

        $this->assertEquals(0, $c2->refresh()->sort_order);
        $this->assertEquals(1, $c1->refresh()->sort_order);
    }
}
