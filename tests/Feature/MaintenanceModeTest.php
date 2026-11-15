<?php

namespace Tests\Feature;

use App\Models\PokjaGroup;
use App\Models\User;
use App\Services\MaintenanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $pokjaUser;
    protected User $siswaUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $this->pokjaUser = User::factory()->create([
            'role' => 'pokja',
        ]);

        $group = PokjaGroup::create(['name' => 'Pokja Maintenance Test', 'is_active' => true]);
        $group->users()->attach($this->pokjaUser->id);

        $this->siswaUser = User::factory()->create([
            'role' => 'siswa',
        ]);

        // Pastikan awal selalu nonaktif
        MaintenanceService::disable();
    }

    public function test_super_admin_can_access_maintenance_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.maintenance.index'));
        $response->assertStatus(200);
        $response->assertSee('Mode Pemeliharaan (Maintenance)');
    }

    public function test_non_super_admin_cannot_access_maintenance_dashboard(): void
    {
        $response = $this->actingAs($this->pokjaUser)->get(route('admin.maintenance.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_enable_maintenance_mode_with_allowed_roles(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.maintenance.update'), [
            'is_active' => '1',
            'allowed_roles' => ['super_admin', 'pokja'],
            'message' => 'Sedang dilakukan pemeliharaan database oleh tim IT.',
        ]);

        $response->assertRedirect();
        $this->assertTrue(MaintenanceService::isMaintenanceActive());
        $this->assertTrue(MaintenanceService::isRoleAllowed('super_admin'));
        $this->assertTrue(MaintenanceService::isRoleAllowed('pokja'));
        $this->assertFalse(MaintenanceService::isRoleAllowed('siswa'));
        $this->assertEquals('Sedang dilakukan pemeliharaan database oleh tim IT.', MaintenanceService::getMessage());
    }

    public function test_super_admin_can_always_browse_system_during_maintenance(): void
    {
        MaintenanceService::enable(allowedRoles: ['super_admin'], message: 'Maintenance test');

        $response = $this->actingAs($this->superAdmin)->get(route('admin.config.index'));
        $response->assertStatus(200);
    }

    public function test_allowed_role_can_browse_system_during_maintenance(): void
    {
        // Izinkan pokja
        MaintenanceService::enable(allowedRoles: ['super_admin', 'pokja'], message: 'Maintenance for pokja only');

        $response = $this->actingAs($this->pokjaUser)->get(route('pokja.siswa.index'));
        $response->assertStatus(200);
    }

    public function test_non_allowed_role_is_redirected_to_maintenance_page(): void
    {
        // Hanya super_admin dan pokja yang boleh, siswa tidak boleh
        MaintenanceService::enable(allowedRoles: ['super_admin', 'pokja'], message: 'Khusus staf');

        $response = $this->actingAs($this->siswaUser)->get('/dashboard');
        $response->assertRedirect(route('maintenance'));
    }

    public function test_non_allowed_role_login_is_rejected_during_maintenance(): void
    {
        MaintenanceService::enable(allowedRoles: ['super_admin', 'pokja'], message: 'Khusus staf');

        $response = $this->post(route('login'), [
            'username' => $this->siswaUser->username,
            'password' => 'password', // default factory password
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_allowed_role_can_login_during_maintenance(): void
    {
        MaintenanceService::enable(allowedRoles: ['super_admin', 'pokja'], message: 'Khusus staf');

        $response = $this->post(route('login'), [
            'username' => $this->pokjaUser->username,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->pokjaUser);
    }

    public function test_guest_is_redirected_to_maintenance_page_but_can_view_login(): void
    {
        MaintenanceService::enable(allowedRoles: ['super_admin'], message: 'Sistem sedang dipelihara');

        // Tamu mencoba akses halaman publik selain login
        $response = $this->get('/register-pembimbing-dudi');
        $response->assertRedirect(route('maintenance'));

        // Tamu tetap bisa membuka form login
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertStatus(200);

        // Halaman maintenance mengembalikan respons 503
        $maintenanceResponse = $this->get(route('maintenance'));
        $maintenanceResponse->assertStatus(503);
        $maintenanceResponse->assertSee('Sistem sedang dipelihara');
    }

    public function test_super_admin_can_disable_maintenance_mode(): void
    {
        MaintenanceService::enable(allowedRoles: ['super_admin']);
        $this->assertTrue(MaintenanceService::isMaintenanceActive());

        $response = $this->actingAs($this->superAdmin)->post(route('admin.maintenance.update'), [
            'is_active' => '0',
            'allowed_roles' => ['super_admin'],
        ]);

        $response->assertRedirect();
        $this->assertFalse(MaintenanceService::isMaintenanceActive());

        // Setelah maintenance dimatikan, halaman dapat diakses kembali oleh pengguna
        $pokjaResponse = $this->actingAs($this->pokjaUser)->get(route('pokja.siswa.index'));
        $pokjaResponse->assertStatus(200);
    }
}
