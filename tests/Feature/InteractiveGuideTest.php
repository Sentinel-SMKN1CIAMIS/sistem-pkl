<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractiveGuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_interactive_guide()
    {
        $user = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa_test',
            'email' => 'siswa_test@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('panduan.interaktif'));

        $response->assertStatus(200);
        $response->assertSee('Panduan Interaktif');
    }

    public function test_guest_cannot_access_interactive_guide()
    {
        $response = $this->get(route('panduan.interaktif'));
        $response->assertRedirect('/login');
    }
}
