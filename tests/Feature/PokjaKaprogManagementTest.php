<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\PokjaGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PokjaKaprogManagementTest extends TestCase
{
    use RefreshDatabase;

    private $pokjaUser;
    private $program;
    private $concentration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // Create a program and concentration keahlian
        $this->program = ProgramKeahlian::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak'
        ]);

        $this->concentration = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->program->id,
            'kode' => 'PPLG',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6
        ]);

        // Create a Pokja user with force_password_change set to false
        $this->pokjaUser = User::create([
            'name' => 'Ketua Pokja',
            'username' => 'pokja',
            'email' => 'pokja@example.com',
            'password' => bcrypt('password'),
            'role' => 'pokja',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        // Create active Pokja group and attach the user
        $group = PokjaGroup::create([
            'name' => 'Grup Pokja Utama',
            'is_active' => true,
        ]);
        $group->users()->attach($this->pokjaUser->id);
    }

    /**
     * Test Pokja can view Kaprog list
     */
    public function test_pokja_can_view_kaprog_list()
    {
        // Create a Kaprog user
        User::create([
            'name' => 'Kepala Program RPL',
            'username' => 'kaprog_rpl',
            'email' => 'kaprog_rpl@example.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'program_keahlian_id' => $this->program->id,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $response = $this->actingAs($this->pokjaUser)
            ->get(route('pokja.kaprog.index'));

        $response->assertStatus(200);
        $response->assertSee('Kepala Program RPL');
        $response->assertSee('kaprog_rpl');
    }

    /**
     * Test Pokja can create Kaprog account
     */
    public function test_pokja_can_create_kaprog_account()
    {
        $response = $this->actingAs($this->pokjaUser)
            ->post(route('pokja.kaprog.store'), [
                'name' => 'New Kaprog',
                'username' => 'new_kaprog',
                'email' => 'new_kaprog@example.com',
                'password' => 'password123',
                'program_keahlian_id' => $this->program->id,
            ]);

        $response->assertRedirect(route('pokja.kaprog.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'New Kaprog',
            'username' => 'new_kaprog',
            'email' => 'new_kaprog@example.com',
            'role' => 'kaprog',
            'program_keahlian_id' => $this->program->id,
        ]);
    }

    /**
     * Test Pokja can edit Kaprog account
     */
    public function test_pokja_can_edit_kaprog_account()
    {
        $kaprog = User::create([
            'name' => 'Old Kaprog',
            'username' => 'old_kaprog',
            'email' => 'old_kaprog@example.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'program_keahlian_id' => $this->program->id,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $response = $this->actingAs($this->pokjaUser)
            ->put(route('pokja.kaprog.update', $kaprog), [
                'name' => 'Updated Kaprog',
                'username' => 'updated_kaprog',
                'email' => 'updated_kaprog@example.com',
                'program_keahlian_id' => $this->program->id,
            ]);

        $response->assertRedirect(route('pokja.kaprog.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $kaprog->id,
            'name' => 'Updated Kaprog',
            'username' => 'updated_kaprog',
            'email' => 'updated_kaprog@example.com',
            'program_keahlian_id' => $this->program->id,
        ]);
    }

    /**
     * Test Pokja can delete Kaprog account
     */
    public function test_pokja_can_delete_kaprog_account()
    {
        $kaprog = User::create([
            'name' => 'Delete Me',
            'username' => 'delete_me',
            'email' => 'delete_me@example.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'program_keahlian_id' => $this->program->id,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $response = $this->actingAs($this->pokjaUser)
            ->delete(route('pokja.kaprog.destroy', $kaprog));

        $response->assertRedirect(route('pokja.kaprog.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $kaprog->id,
        ]);
    }

    /**
     * Test Pokja can download Kaprog Excel template
     */
    public function test_pokja_can_download_kaprog_template()
    {
        $response = $this->actingAs($this->pokjaUser)
            ->get(route('pokja.import.template', 'kaprog'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('Content-Disposition', 'attachment; filename="template_kaprog.xlsx"');
    }
}
