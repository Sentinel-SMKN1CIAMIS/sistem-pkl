<?php

namespace Tests\Feature;

use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\Notifikasi;
use App\Models\PembimbingSekolah;
use App\Models\Pesan;
use App\Models\ProgramKeahlian;
use App\Models\PushSubscription;
use App\Models\Siswa;
use App\Models\User;
use App\Services\WebPushService;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\WebPush;
use Mockery;
use Tests\TestCase;

class EarlyWarningBroadcastTest extends TestCase
{
    use RefreshDatabase;

    private function seedScenario(): array
    {
        $progA = ProgramKeahlian::create(['kode' => 'PPLG', 'nama' => 'PPLG']);
        $konA = KonsentrasiKeahlian::create(['program_keahlian_id' => $progA->id, 'kode' => 'RPL', 'nama' => 'RPL']);

        $progB = ProgramKeahlian::create(['kode' => 'KLN', 'nama' => 'Kuliner']);
        $konB = KonsentrasiKeahlian::create(['program_keahlian_id' => $progB->id, 'kode' => 'KLN', 'nama' => 'Kuliner']);

        $dudi = Dudi::create([
            'nama' => 'PT Nusantara Digital',
            'alamat' => 'Jl. Merdeka 1',
            'kota' => 'Ciamis',
            'konsentrasi_keahlian_id' => $konA->id,
            'is_active' => true,
        ]);

        $kaprogA = User::factory()->create([
            'name' => 'Kaprog PPLG',
            'role' => 'kaprog',
            'program_keahlian_id' => $progA->id,
        ]);

        $guruUser = User::factory()->create(['name' => 'Pak Guru', 'role' => 'pembimbing_sekolah']);
        $guru = PembimbingSekolah::create([
            'user_id' => $guruUser->id,
            'nama_lengkap' => 'Pak Guru',
            'nip' => '198501012010011001',
            'konsentrasi_keahlian_id' => $konA->id,
            'tipe' => 'kejuruan',
        ]);

        // Student A (PPLG, at-risk because zero attendance in last 14 days)
        $studentAUser = User::factory()->create(['name' => 'Budi Santoso', 'role' => 'siswa']);
        $studentA = Siswa::create([
            'user_id' => $studentAUser->id,
            'nis' => '242510101',
            'nama_lengkap' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'kelas' => '12RPL1',
            'konsentrasi_keahlian_id' => $konA->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $guru->id,
            'status_pkl' => 'sedang_pkl',
        ]);

        // Student B (Kuliner, at-risk, OTHER program)
        $studentBUser = User::factory()->create(['name' => 'Siti Aminah', 'role' => 'siswa']);
        $studentB = Siswa::create([
            'user_id' => $studentBUser->id,
            'nis' => '242510202',
            'nama_lengkap' => 'Siti Aminah',
            'jenis_kelamin' => 'P',
            'tahun_ajaran' => '2026/2027',
            'kelas' => '12KLN1',
            'konsentrasi_keahlian_id' => $konB->id,
            'dudi_id' => $dudi->id,
            'status_pkl' => 'sedang_pkl',
        ]);

        return compact('kaprogA', 'guruUser', 'guru', 'studentAUser', 'studentA', 'studentBUser', 'studentB');
    }

    public function test_kaprog_broadcast_delivers_pesan_web_notif_and_hp_push_scoped_to_own_program(): void
    {
        $ctx = $this->seedScenario();

        // Register a real push subscription for Student A's phone
        $endpoint = 'https://fcm.googleapis.com/fcm/send/test-device-token-123';
        PushSubscription::create([
            'user_id' => $ctx['studentAUser']->id,
            'endpoint' => $endpoint,
            'endpoint_hash' => hash('sha256', $endpoint),
            'public_key' => 'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBUYIHBQFLXYp5Nksh8U',
            'auth_token' => 'tBHItJI5svbpez7KI4CCXg',
            'content_encoding' => 'aes128gcm',
        ]);

        // Mock underlying Minishlink WebPush client to verify actual HP push payload delivery
        $capturedPayload = null;
        $mockWebPush = Mockery::mock(WebPush::class);
        $mockWebPush->shouldReceive('queueNotification')
            ->once()
            ->withArgs(function ($sub, $payload) use ($endpoint, &$capturedPayload) {
                $capturedPayload = json_decode($payload, true);
                return $sub->getEndpoint() === $endpoint;
            });

        $successReport = new MessageSentReport(
            new Psr7Request('POST', $endpoint),
            null,
            true
        );
        $mockWebPush->shouldReceive('flush')
            ->once()
            ->andReturn((function () use ($successReport) {
                yield $successReport;
            })());

        $this->app->instance(WebPushService::class, new WebPushService($mockWebPush));

        // 1. Kaprog PPLG sends bulk broadcast
        $response = $this->actingAs($ctx['kaprogA'])->post(route('early_warning.broadcast'), [
            'mode' => 'bulk',
            'filter_severity' => 'all',
            'judul' => 'Pengingat Absensi & Jurnal PKL',
            'pesan' => 'Halo {nama}, mohon segera cek kewajiban Anda: {alasan}.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', function ($msg) {
            return str_contains($msg, '1 siswa') && str_contains($msg, '1 perangkat aktif');
        });

        // 2. Verify Pesan Chat created for Student A (personalized), NOT for Student B (other program)
        $pesanA = Pesan::where('to_user_id', $ctx['studentAUser']->id)->first();
        $this->assertNotNull($pesanA);
        $this->assertTrue($pesanA->is_broadcast);
        $this->assertStringContainsString('Halo Budi Santoso', $pesanA->isi);
        $this->assertStringContainsString('Tidak absen', $pesanA->isi);
        $this->assertSame(0, Pesan::where('to_user_id', $ctx['studentBUser']->id)->count());

        // 3. Verify Notifikasi Lonceng created for Student A
        $notifA = Notifikasi::where('to_user_id', $ctx['studentAUser']->id)->first();
        $this->assertNotNull($notifA);
        $this->assertSame('peringatan_dini', $notifA->tipe);
        $this->assertSame('Pengingat Absensi & Jurnal PKL', $notifA->judul);

        // 4. Verify Web Push payload sent to HP
        $this->assertNotNull($capturedPayload);
        $this->assertSame('Pengingat Absensi & Jurnal PKL', $capturedPayload['title']);
        $this->assertStringContainsString('Halo Budi Santoso', $capturedPayload['body']);
        $this->assertStringContainsString('/pesan/' . $ctx['kaprogA']->id, $capturedPayload['url']);

        // 5. Verify Web Real-time Polling endpoint returns BOTH notification & message for Student A!
        $pollRes = $this->actingAs($ctx['studentAUser'])
            ->getJson(route('notifications.poll', ['last_notif_id' => 0, 'last_pesan_id' => 0]));

        $pollRes->assertOk()
            ->assertJsonPath('unread_notif_count', 1)
            ->assertJsonPath('unread_pesan_count', 1)
            ->assertJsonPath('notifications.0.title', 'Pengingat Absensi & Jurnal PKL')
            ->assertJsonPath('messages.0.from_user_id', $ctx['kaprogA']->id);
    }

    public function test_kaprog_cannot_send_single_warning_to_student_from_other_program(): void
    {
        $ctx = $this->seedScenario();

        $this->actingAs($ctx['kaprogA'])
            ->post(route('early_warning.broadcast'), [
                'mode' => 'single',
                'siswa_id' => $ctx['studentB']->id,
                'judul' => 'Teguran',
                'pesan' => 'Mohon segera melengkapi absensi PKL Anda hari ini.',
            ])
            ->assertForbidden();
    }

    public function test_pembimbing_sekolah_can_broadcast_to_own_students_and_student_cannot_broadcast(): void
    {
        $ctx = $this->seedScenario();

        // Pembimbing Sekolah can broadcast to Student A
        $this->actingAs($ctx['guruUser'])
            ->post(route('early_warning.broadcast'), [
                'mode' => 'bulk',
                'filter_severity' => 'all',
                'judul' => 'Pengingat dari Pembimbing',
                'pesan' => 'Halo {nama}, tolong segera lengkapi absensi dan jurnal PKL ya.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(1, Pesan::where('to_user_id', $ctx['studentAUser']->id)->count());
        $this->assertSame(0, Pesan::where('to_user_id', $ctx['studentBUser']->id)->count());

        // Student cannot access broadcast endpoint
        $this->actingAs($ctx['studentAUser'])
            ->post(route('early_warning.broadcast'), [
                'mode' => 'bulk',
                'judul' => 'Test',
                'pesan' => 'Ini percobaan tidak sah dari akun siswa.',
            ])
            ->assertForbidden();
    }
}
