<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\PembimbingSekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DualWorkspacePokjaTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $dualTeacherUser;
    private PembimbingSekolah $dualTeacher;
    private User $regularTeacherUser;
    private PembimbingSekolah $regularTeacher;
    private User $otherPokjaUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin_test',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->dualTeacherUser = User::create([
            'name' => 'Pak Budi Pokja',
            'username' => '198001012005011011',
            'password' => bcrypt('password123'),
            'role' => 'pembimbing_sekolah',
            'is_pokja' => true,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->dualTeacher = PembimbingSekolah::create([
            'user_id' => $this->dualTeacherUser->id,
            'nama_lengkap' => 'Pak Budi Santoso',
        ]);

        $this->regularTeacherUser = User::create([
            'name' => 'Bu Siti Pembimbing',
            'username' => '198101012005011022',
            'password' => bcrypt('password123'),
            'role' => 'pembimbing_sekolah',
            'is_pokja' => false,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->regularTeacher = PembimbingSekolah::create([
            'user_id' => $this->regularTeacherUser->id,
            'nama_lengkap' => 'Bu Siti Aminah',
        ]);

        $this->otherPokjaUser = User::create([
            'name' => 'Bu Rina Pokja',
            'username' => '198201012005011033',
            'password' => bcrypt('password123'),
            'role' => 'pembimbing_sekolah',
            'is_pokja' => true,
            'is_active' => true,
            'force_password_change' => false,
        ]);
    }

    public function test_super_admin_can_toggle_is_pokja_for_pembimbing_sekolah(): void
    {
        // Grant is_pokja to regular teacher
        $res = $this->actingAs($this->superAdmin)->put(route('admin.users.update', $this->regularTeacherUser), [
            'username' => $this->regularTeacherUser->username,
            'role' => 'pembimbing_sekolah',
            'is_pokja' => '1',
        ]);

        $res->assertRedirect(route('admin.users.index'));
        $this->regularTeacherUser->refresh();
        $this->assertTrue($this->regularTeacherUser->is_pokja);
        $this->assertTrue($this->regularTeacherUser->hasDualWorkspace());
        $this->assertTrue($this->regularTeacherUser->canAccessPokja());

        // Badge + POKJA appears on user list
        $indexRes = $this->actingAs($this->superAdmin)->get(route('admin.users.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('+ POKJA', false);

        // Revoke is_pokja
        $this->actingAs($this->superAdmin)->put(route('admin.users.update', $this->regularTeacherUser), [
            'username' => $this->regularTeacherUser->username,
            'role' => 'pembimbing_sekolah',
        ]);

        $this->regularTeacherUser->refresh();
        $this->assertFalse($this->regularTeacherUser->is_pokja);
        $this->assertFalse($this->regularTeacherUser->hasDualWorkspace());
    }

    public function test_dual_role_user_sees_workspace_selector_with_two_cards(): void
    {
        // Visiting dashboard without active_workspace redirects to workspace picker
        $res = $this->actingAs($this->dualTeacherUser)->get(route('dashboard'));
        $res->assertRedirect(route('workspace.select'));

        // Workspace picker displays both cards
        $selectRes = $this->actingAs($this->dualTeacherUser)->get(route('workspace.select'));
        $selectRes->assertStatus(200);
        $selectRes->assertSee('Workspace Pokja', false);
        $selectRes->assertSee('Pembimbing Sekolah', false);
        $selectRes->assertSee('Shared Workspace', false);
    }

    public function test_dual_role_user_can_switch_between_pokja_and_pembimbing_sekolah_workspaces(): void
    {
        // Switch to Pokja workspace
        $switchPokja = $this->actingAs($this->dualTeacherUser)->post(route('workspace.switch'), [
            'workspace' => 'pokja',
        ]);
        $switchPokja->assertRedirect(route('dashboard'));
        $this->assertEquals('pokja', session('active_workspace'));

        // Dashboard renders Pokja workspace + switcher to Pembimbing Sekolah + live toast container
        $dashPokja = $this->actingAs($this->dualTeacherUser)->get(route('dashboard'));
        $dashPokja->assertStatus(200);
        $dashPokja->assertSee('Ke Pembimbing Sekolah', false);
        $dashPokja->assertSee('pokja-activity-toast-container', false);

        // Can access Pokja routes without 403
        $dudiRes = $this->actingAs($this->dualTeacherUser)->get(route('pokja.dudi.index'));
        $dudiRes->assertStatus(200);

        // Switch to Pembimbing Sekolah workspace
        $switchGuru = $this->actingAs($this->dualTeacherUser)->post(route('workspace.switch'), [
            'workspace' => 'pembimbing_sekolah',
        ]);
        $switchGuru->assertRedirect(route('dashboard'));
        $this->assertEquals('pembimbing_sekolah', session('active_workspace'));

        // Dashboard renders Pembimbing Sekolah workspace + switcher to Pokja
        $dashGuru = $this->actingAs($this->dualTeacherUser)->get(route('dashboard'));
        $dashGuru->assertStatus(200);
        $dashGuru->assertSee('Ke Workspace Pokja', false);
    }

    public function test_regular_teacher_cannot_access_workspace_switch_or_pokja_routes(): void
    {
        $this->actingAs($this->regularTeacherUser)
            ->get(route('workspace.select'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($this->regularTeacherUser)
            ->post(route('workspace.switch'), ['workspace' => 'pokja'])
            ->assertStatus(403);

        $this->actingAs($this->regularTeacherUser)
            ->get(route('pokja.dudi.index'))
            ->assertStatus(403);

        $this->actingAs($this->regularTeacherUser)
            ->getJson(route('pokja.activity-feed'))
            ->assertStatus(403);
    }

    public function test_pokja_activity_feed_returns_changes_made_by_other_pokja_members(): void
    {
        // Initial poll with after_id=0 returns latest_id and empty events
        $initRes = $this->actingAs($this->dualTeacherUser)
            ->getJson(route('pokja.activity-feed', ['after_id' => 0]));
        $initRes->assertStatus(200);
        $afterId = $initRes->json('latest_id');
        $this->assertEmpty($initRes->json('events'));

        // Another Pokja member updates DUDI #5
        ActivityLog::create([
            'user_id' => $this->otherPokjaUser->id,
            'action' => 'UPDATED',
            'description' => 'Data dudis dengan ID #5 telah diperbarui.',
            'ip_address' => '127.0.0.1',
            'location' => 'Localhost',
        ]);

        // Own action should NOT be returned back to self
        ActivityLog::create([
            'user_id' => $this->dualTeacherUser->id,
            'action' => 'CREATED',
            'description' => 'Data siswas dengan ID #99 berhasil dibuat.',
            'ip_address' => '127.0.0.1',
            'location' => 'Localhost',
        ]);

        // Poll again with after_id
        $pollRes = $this->actingAs($this->dualTeacherUser)
            ->getJson(route('pokja.activity-feed', ['after_id' => $afterId]));

        $pollRes->assertStatus(200);
        $events = $pollRes->json('events');
        $this->assertCount(1, $events);
        $this->assertEquals('Bu Rina Pokja', $events[0]['actor']);
        $this->assertEquals('UPDATED', $events[0]['action']);
        $this->assertStringContainsString('Data DUDI (#5)', $events[0]['summary']);
    }
}
