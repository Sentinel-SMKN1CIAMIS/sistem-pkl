<?php

namespace Tests\Feature;

use App\Jobs\SendWebPushBatchJob;
use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\Jurnal;
use App\Models\KonsentrasiKeahlian;
use App\Models\Notifikasi;
use App\Models\ProgramKeahlian;
use App\Models\PushSubscription;
use App\Models\Siswa;
use App\Models\User;
use App\Services\WebPushService;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Request as PsrRequest;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\WebPush;
use Mockery;
use Tests\TestCase;

class WebPushReminderTest extends TestCase
{
    use RefreshDatabase;

    private KonsentrasiKeahlian $konsentrasi;
    private Dudi $dudi;

    protected function setUp(): void
    {
        parent::setUp();

        $program = ProgramKeahlian::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
        ]);

        $this->konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
            'durasi_pkl_bulan' => 6,
        ]);

        $this->dudi = Dudi::create([
            'nama' => 'PT Digital Ciamis',
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Jend. Sudirman',
            'is_active' => true,
        ]);
    }

    private function createStudent(string $nis, string $name = 'Siswa Uji'): Siswa
    {
        $user = User::create([
            'name' => $name,
            'username' => $nis,
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        return Siswa::create([
            'user_id' => $user->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'dudi_id' => $this->dudi->id,
            'nis' => $nis,
            'nama_lengkap' => $name,
            'kelas' => 'XII PPLG',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);
    }

    public function test_authenticated_user_can_subscribe_reassign_and_unsubscribe_push(): void
    {
        $siswa1 = $this->createStudent('2425001', 'Siswa Satu');
        $siswa2 = $this->createStudent('2425002', 'Siswa Dua');

        $endpoint = 'https://fcm.googleapis.com/fcm/send/example-very-long-endpoint-' . str_repeat('a', 200);
        $payload = [
            'endpoint' => $endpoint,
            'keys' => [
                'p256dh' => 'BPh_test_public_key_value',
                'auth' => 'test_auth_token_value',
            ],
            'contentEncoding' => 'aes128gcm',
        ];

        // Guest cannot subscribe
        $this->postJson(route('push.subscribe'), $payload)->assertStatus(401);

        // User 1 subscribes
        $this->actingAs($siswa1->user)
            ->postJson(route('push.subscribe'), $payload)
            ->assertOk()
            ->assertJson(['status' => 'subscribed']);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $siswa1->user_id,
            'endpoint_hash' => hash('sha256', $endpoint),
        ]);

        // User 2 logs in on the same browser/device and subscribes -> endpoint cleanly reassigned
        $this->actingAs($siswa2->user)
            ->postJson(route('push.subscribe'), $payload)
            ->assertOk();

        $this->assertDatabaseCount('push_subscriptions', 1);
        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $siswa2->user_id,
            'endpoint_hash' => hash('sha256', $endpoint),
        ]);

        // Unsubscribe removes the record
        $this->actingAs($siswa2->user)
            ->postJson(route('push.unsubscribe'), ['endpoint' => $endpoint])
            ->assertOk()
            ->assertJson(['status' => 'unsubscribed']);

        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    public function test_relative_checkout_reminder_supports_day_and_night_shifts_without_duplicates(): void
    {
        // Freeze time at 16:00 today
        Carbon::setTestNow(Carbon::create(2026, 11, 18, 16, 0, 0, 'Asia/Jakarta'));

        $dayLate = $this->createStudent('2425010', 'Shift Pagi Lupa Pulang');
        $dayRecent = $this->createStudent('2425011', 'Shift Siang Baru Masuk');
        $dayAlreadyOut = $this->createStudent('2425012', 'Shift Pagi Sudah Pulang');
        $izinStudent = $this->createStudent('2425013', 'Siswa Izin');

        // 1. Clocked in at 08:00 (8 hours ago) -> should get checkout reminder
        Absensi::create([
            'siswa_id' => $dayLate->id,
            'tanggal' => '2026-11-18',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
        ]);

        // 2. Clocked in at 13:00 (3 hours ago) -> should NOT get checkout reminder yet
        Absensi::create([
            'siswa_id' => $dayRecent->id,
            'tanggal' => '2026-11-18',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '13:00:00',
        ]);

        // 3. Clocked in at 08:00 & clocked out at 15:45 -> should NOT get checkout reminder
        Absensi::create([
            'siswa_id' => $dayAlreadyOut->id,
            'tanggal' => '2026-11-18',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
            'waktu_pulang' => '15:45:00',
        ]);

        // 4. Status izin -> should NOT get any reminder
        Absensi::create([
            'siswa_id' => $izinStudent->id,
            'tanggal' => '2026-11-18',
            'status' => 'izin',
            'approval_status' => 'approved',
        ]);

        $this->artisan('pkl:send-reminders')->assertExitCode(0);

        $this->assertEquals(1, Notifikasi::where('to_user_id', $dayLate->user_id)->count());
        $this->assertEquals(0, Notifikasi::where('to_user_id', $dayRecent->user_id)->count());
        $this->assertEquals(0, Notifikasi::where('to_user_id', $dayAlreadyOut->user_id)->count());
        $this->assertEquals(0, Notifikasi::where('to_user_id', $izinStudent->user_id)->count());

        // Running again must NOT duplicate notifications
        $this->artisan('pkl:send-reminders')->assertExitCode(0);
        $this->assertEquals(1, Notifikasi::where('to_user_id', $dayLate->user_id)->count());

        // Test Night Shift: Clocked in yesterday at 21:00, now 05:00 (+8 hours)
        Carbon::setTestNow(Carbon::create(2026, 11, 19, 5, 0, 0, 'Asia/Jakarta'));
        $nightShift = $this->createStudent('2425014', 'Shift Malam');
        Absensi::create([
            'siswa_id' => $nightShift->id,
            'tanggal' => '2026-11-18',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '21:00:00',
        ]);

        $this->artisan('pkl:send-reminders')->assertExitCode(0);
        $this->assertEquals(1, Notifikasi::where('to_user_id', $nightShift->user_id)->count());

        Carbon::setTestNow();
    }

    public function test_jurnal_reminder_triggers_after_checkout_or_evening_and_skips_filled_journals(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 18, 19, 45, 0, 'Asia/Jakarta'));

        $checkedOutNoJurnal = $this->createStudent('2425020', 'Pulang Tanpa Jurnal');
        $checkedOutWithJurnal = $this->createStudent('2425021', 'Pulang Sudah Jurnal');
        $forgotCheckoutNoJurnal = $this->createStudent('2425022', 'Lupa Pulang Dan Jurnal');

        Absensi::create([
            'siswa_id' => $checkedOutNoJurnal->id,
            'tanggal' => '2026-11-18',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
            'waktu_pulang' => '16:30:00',
            'reminded_checkout_at' => now()->subHours(3),
        ]);

        Absensi::create([
            'siswa_id' => $checkedOutWithJurnal->id,
            'tanggal' => '2026-11-18',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
            'waktu_pulang' => '16:30:00',
            'reminded_checkout_at' => now()->subHours(3),
        ]);

        Jurnal::create([
            'siswa_id' => $checkedOutWithJurnal->id,
            'tanggal' => '2026-11-18',
            'kegiatan' => 'Membuat fitur web PKL.',
            'status' => 'pending',
        ]);

        Absensi::create([
            'siswa_id' => $forgotCheckoutNoJurnal->id,
            'tanggal' => '2026-11-18',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:30:00',
            'reminded_checkout_at' => now()->subHours(3),
        ]);

        $this->artisan('pkl:send-reminders')->assertExitCode(0);

        $this->assertEquals(1, Notifikasi::where('to_user_id', $checkedOutNoJurnal->user_id)->where('tipe', 'warning')->count());
        $this->assertEquals(0, Notifikasi::where('to_user_id', $checkedOutWithJurnal->user_id)->where('tipe', 'warning')->count());
        $this->assertEquals(1, Notifikasi::where('to_user_id', $forgotCheckoutNoJurnal->user_id)->where('tipe', 'warning')->count());

        Carbon::setTestNow();
    }

    public function test_webpush_service_cleans_up_expired_subscriptions_and_batches_broadcasts(): void
    {
        $siswa1 = $this->createStudent('2425030', 'User Active Push');
        $siswa2 = $this->createStudent('2425031', 'User Expired Push');

        // Valid 65-byte uncompressed P-256 public key & 16-byte auth secret base64url encoded
        $validP256 = rtrim(strtr(base64_encode("\x04" . str_repeat('a', 64)), '+/', '-_'), '=');
        $validAuth = rtrim(strtr(base64_encode(str_repeat('b', 16)), '+/', '-_'), '=');

        $sub1 = PushSubscription::create([
            'user_id' => $siswa1->user_id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/active-token',
            'endpoint_hash' => hash('sha256', 'https://fcm.googleapis.com/fcm/send/active-token'),
            'public_key' => $validP256,
            'auth_token' => $validAuth,
            'content_encoding' => 'aes128gcm',
        ]);

        $sub2 = PushSubscription::create([
            'user_id' => $siswa2->user_id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/expired-token',
            'endpoint_hash' => hash('sha256', 'https://fcm.googleapis.com/fcm/send/expired-token'),
            'public_key' => $validP256,
            'auth_token' => $validAuth,
            'content_encoding' => 'aes128gcm',
        ]);

        $reportSuccess = new MessageSentReport(
            new PsrRequest('POST', $sub1->endpoint),
            new PsrResponse(201),
            true
        );
        $reportExpired = new MessageSentReport(
            new PsrRequest('POST', $sub2->endpoint),
            new PsrResponse(410),
            false,
            'Gone'
        );

        $mockClient = Mockery::mock(WebPush::class);
        $mockClient->shouldReceive('queueNotification')->twice();
        $mockClient->shouldReceive('flush')->once()->andReturnUsing(function () use ($reportSuccess, $reportExpired): \Generator {
            yield $reportSuccess;
            yield $reportExpired;
        });

        $service = new WebPushService($mockClient);
        $stats = $service->sendToUsers([$siswa1->user_id, $siswa2->user_id], [
            'title' => 'Tes Broadcast',
            'body' => 'Pesan uji coba',
            'url' => '/dashboard',
        ]);

        $this->assertEquals(['sent' => 1, 'failed' => 0, 'expired' => 1], $stats);
        $this->assertDatabaseHas('push_subscriptions', ['id' => $sub1->id]);
        $this->assertDatabaseMissing('push_subscriptions', ['id' => $sub2->id]);

        // Test queue batching splits 250 user IDs into 3 jobs (100, 100, 50)
        Queue::fake();
        config(['services.webpush.public_key' => 'test_pub', 'services.webpush.private_key' => 'test_priv']);
        $realService = new WebPushService();
        $jobCount = $realService->dispatchBroadcast(range(1, 250), ['title' => 'Mass', 'body' => 'Test']);

        $this->assertEquals(3, $jobCount);
        Queue::assertPushed(SendWebPushBatchJob::class, 3);
    }

    public function test_morning_checkin_reminder_targets_only_unattended_sedang_pkl_students_without_duplicates(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 18, 7, 15, 0, 'Asia/Jakarta'));

        $needsCheckin = $this->createStudent('2425040', 'Siswa Belum Absen Pagi');
        $alreadyCheckedIn = $this->createStudent('2425041', 'Siswa Sudah Absen Pagi');
        $notStarted = $this->createStudent('2425042', 'Siswa Belum Mulai');
        $notStarted->update(['status_pkl' => 'belum_mulai']);
        $nightShiftActive = $this->createStudent('2425043', 'Siswa Shift Malam Aktif');

        Absensi::create([
            'siswa_id' => $alreadyCheckedIn->id,
            'tanggal' => '2026-11-18',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '06:50:00',
        ]);

        Absensi::create([
            'siswa_id' => $nightShiftActive->id,
            'tanggal' => '2026-11-17',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '21:00:00',
        ]);

        $this->artisan('pkl:send-reminders')->assertExitCode(0);

        $this->assertEquals(1, Notifikasi::where('to_user_id', $needsCheckin->user_id)->where('judul', 'Jangan Lupa Absen Masuk PKL')->count());
        $this->assertEquals(0, Notifikasi::where('to_user_id', $alreadyCheckedIn->user_id)->where('judul', 'Jangan Lupa Absen Masuk PKL')->count());
        $this->assertEquals(0, Notifikasi::where('to_user_id', $notStarted->user_id)->where('judul', 'Jangan Lupa Absen Masuk PKL')->count());
        $this->assertEquals(0, Notifikasi::where('to_user_id', $nightShiftActive->user_id)->where('judul', 'Jangan Lupa Absen Masuk PKL')->count());

        // Running again at 07:30 must NOT send duplicate
        Carbon::setTestNow(Carbon::create(2026, 11, 18, 7, 30, 0, 'Asia/Jakarta'));
        $this->artisan('pkl:send-reminders')->assertExitCode(0);
        $this->assertEquals(1, Notifikasi::where('to_user_id', $needsCheckin->user_id)->where('judul', 'Jangan Lupa Absen Masuk PKL')->count());

        Carbon::setTestNow();
    }

    public function test_vapid_artisan_command_generates_keys(): void
    {
        $this->artisan('webpush:vapid --show')
            ->expectsOutputToContain('VAPID_PUBLIC_KEY=')
            ->expectsOutputToContain('VAPID_PRIVATE_KEY=')
            ->assertExitCode(0);
    }
}
