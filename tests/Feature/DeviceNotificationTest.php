<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Notifikasi;
use App\Models\Pesan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_poll_device_notifications()
    {
        $user = User::factory()->create([
            'role' => 'siswa',
            'force_password_change' => false,
        ]);

        $sender = User::factory()->create([
            'role' => 'pembimbing_sekolah',
            'name' => 'Guru Pembimbing',
        ]);

        // Create a new notification
        $notif = Notifikasi::create([
            'from_user_id' => $sender->id,
            'to_user_id' => $user->id,
            'judul' => 'Jurnal Disetujui',
            'pesan' => 'Jurnal harian Anda tanggal 24 Agustus telah divalidasi.',
            'tipe' => 'jurnal_disetujui',
            'is_read' => false,
        ]);

        // Create a new message
        $pesan = Pesan::create([
            'from_user_id' => $sender->id,
            'to_user_id' => $user->id,
            'isi' => 'Halo, jangan lupa persiapkan lembar laporan akhir ya.',
            'dibaca_at' => null,
        ]);

        $response = $this->actingAs($user)->getJson(route('notifications.poll', [
            'last_notif_id' => 0,
            'last_pesan_id' => 0,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications' => [
                '*' => ['id', 'title', 'body', 'url', 'type', 'created_at']
            ],
            'messages' => [
                '*' => ['id', 'title', 'body', 'url', 'sender', 'created_at']
            ],
            'unread_notif_count',
            'unread_pesan_count',
            'max_notif_id',
            'max_pesan_id',
        ]);

        $this->assertEquals(1, $response->json('unread_notif_count'));
        $this->assertEquals(1, $response->json('unread_pesan_count'));
        $this->assertEquals($notif->id, $response->json('max_notif_id'));
        $this->assertEquals($pesan->id, $response->json('max_pesan_id'));
        $this->assertEquals('Jurnal Disetujui', $response->json('notifications.0.title'));
    }

    public function test_chat_message_is_not_duplicated_in_system_notifications()
    {
        $user = User::factory()->create([
            'role' => 'siswa',
            'force_password_change' => false,
        ]);

        $sender = User::factory()->create([
            'role' => 'pokja',
            'name' => 'Ketua Pokja',
        ]);

        // When a chat message is sent, both Pesan and a helper Notifikasi (tipe=pesan_baru) were created
        $pesan = Pesan::create([
            'from_user_id' => $sender->id,
            'to_user_id' => $user->id,
            'isi' => 'Pesan pengumuman',
            'dibaca_at' => null,
        ]);

        $notif = Notifikasi::create([
            'from_user_id' => $sender->id,
            'to_user_id' => $user->id,
            'judul' => 'Pesan Baru',
            'pesan' => 'Ketua Pokja: Pesan pengumuman',
            'tipe' => 'pesan_baru',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)->getJson(route('notifications.poll', [
            'last_notif_id' => 0,
            'last_pesan_id' => 0,
        ]));

        $response->assertStatus(200);
        // notifications array should be empty because pesan_baru is excluded
        $this->assertCount(0, $response->json('notifications'));
        // messages array should have exactly 1 item
        $this->assertCount(1, $response->json('messages'));
        $this->assertEquals(1, $response->json('unread_pesan_count'));
        $this->assertEquals(0, $response->json('unread_notif_count'));
    }

    public function test_guest_cannot_poll_device_notifications()
    {
        $response = $this->getJson(route('notifications.poll'));
        $response->assertStatus(401);
    }
}