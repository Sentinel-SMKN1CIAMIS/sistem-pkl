<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    private $superAdminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->superAdminUser = User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => false,
        ]);
    }

    public function test_super_admin_can_bulk_delete_users()
    {
        $u1 = User::create([
            'name' => 'User 1',
            'username' => 'user1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);
        $u2 = User::create([
            'name' => 'User 2',
            'username' => 'user2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
        ]);

        $response = $this->actingAs($this->superAdminUser)
            ->post(route('admin.users.bulk-destroy'), [
                'ids' => [$u1->id, $u2->id]
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', '2 akun berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $u1->id]);
        $this->assertDatabaseMissing('users', ['id' => $u2->id]);
    }

    public function test_super_admin_cannot_bulk_delete_themselves()
    {
        $u1 = User::create([
            'name' => 'User 1',
            'username' => 'user1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        $response = $this->actingAs($this->superAdminUser)
            ->post(route('admin.users.bulk-destroy'), [
                'ids' => [$this->superAdminUser->id, $u1->id]
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', '1 akun berhasil dihapus.');

        // Current user should still exist
        $this->assertDatabaseHas('users', ['id' => $this->superAdminUser->id]);
        // User 1 should be deleted
        $this->assertDatabaseMissing('users', ['id' => $u1->id]);
    }
}
