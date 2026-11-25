<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\Notifikasi;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReminderSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $userA;
    private Siswa $siswaA;
    private User $userB;
    private Siswa $siswaB;

    protected function setUp(): void
    {
        parent::setUp();

        $program = ProgramKeahlian::create([
            'kode' => 'RPL',
            'nama' => 'Pengembangan Perangkat Lunak',
        ]);

        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        $dudi = Dudi::create([
            'nama' => 'PT Ciamis Teknologi',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Jenderal Sudirman No. 1',
            'is_active' => true,
        ]);

        $guruUser = User::create([
            'name' => 'Guru Pembimbing',
            'username' => '198501012010011005',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);

        $guru = PembimbingSekolah::create([
            'user_id' => $guruUser->id,
            'nama_lengkap' => 'Guru Pembimbing',
        ]);

        $this->userA = User::create([
            'name' => 'Siswa A (Shift 07:00)',
            'username' => '242510010',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->siswaA = Siswa::create([
            'user_id' => $this->userA->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $guru->id,
            'nis' => '242510010',
            'nama_lengkap' => 'Siswa A',
            'kelas' => 'XII RPL 1',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567801',
            'status_pkl' => 'sedang_pkl',
            'jam_notif_masuk' => '07:00',
            'jam_notif_pulang' => '16:00',
            'jam_notif_jurnal' => '19:30',
            'reminder_settings_configured' => false,
        ]);

        $this->userB = User::create([
            'name' => 'Siswa B (Shift 08:30)',
            'username' => '242510020',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->siswaB = Siswa::create([
            'user_id' => $this->userB->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $guru->id,
            'nis' => '242510020',
            'nama_lengkap' => 'Siswa B',
            'kelas' => 'XII RPL 2',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'P',
            'no_hp' => '081234567802',
            'status_pkl' => 'sedang_pkl',
            'jam_notif_masuk' => '08:30',
            'jam_notif_pulang' => '17:00',
            'jam_notif_jurnal' => '20:00',
            'reminder_settings_configured' => true,
        ]);
    }

    public function test_student_can_update_reminder_settings_via_api(): void
    {
        $response = $this->actingAs($this->userA)
            ->postJson(route('siswa.profile.reminder-settings'), [
                'jam_notif_masuk' => '08:00',
                'jam_notif_pulang' => '16:30',
                'jam_notif_jurnal' => '19:00',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'jam_notif_masuk' => '08:00',
                'jam_notif_pulang' => '16:30',
                'jam_notif_jurnal' => '19:00',
            ],
        ]);

        $this->assertDatabaseHas('siswas', [
            'id' => $this->siswaA->id,
            'jam_notif_masuk' => '08:00',
            'jam_notif_pulang' => '16:30',
            'jam_notif_jurnal' => '19:00',
            'reminder_settings_configured' => true,
        ]);
    }

    public function test_validation_rejects_invalid_time_format(): void
    {
        $response = $this->actingAs($this->userA)
            ->postJson(route('siswa.profile.reminder-settings'), [
                'jam_notif_masuk' => '25:00', // Invalid hour
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['jam_notif_masuk']);
    }

    public function test_modal_rendered_with_correct_configuration_state(): void
    {
        // Siswa A has not configured yet
        $resA = $this->actingAs($this->userA)->get(route('dashboard'));
        $resA->assertStatus(200);
        $resA->assertSee('reminder-settings-modal', false);
        $resA->assertSee('data-configured="0"', false);

        // Siswa B has already configured
        $resB = $this->actingAs($this->userB)->get(route('dashboard'));
        $resB->assertStatus(200);
        $resB->assertSee('data-configured="1"', false);
    }

    public function test_profile_page_displays_alarm_settings_section(): void
    {
        $response = $this->actingAs($this->userA)->get(route('siswa.profile.index'));
        $response->assertStatus(200);
        $response->assertSee('Buka Pengaturan Alarm', false);
        $response->assertSee('07:00 WIB', false);
    }

    public function test_send_pkl_reminders_respects_custom_morning_hour(): void
    {
        // Simulate 07:15 WIB
        Carbon::setTestNow(Carbon::parse('2026-11-23 07:15:00'));

        $this->artisan('pkl:send-reminders')->assertExitCode(0);

        // Siswa A (07:00) should have received notification
        $this->assertDatabaseHas('notifikasis', [
            'to_user_id' => $this->userA->id,
            'judul' => 'Jangan Lupa Absen Masuk PKL',
        ]);

        // Siswa B (08:30) should NOT have received notification yet at 07:15
        $this->assertDatabaseMissing('notifikasis', [
            'to_user_id' => $this->userB->id,
            'judul' => 'Jangan Lupa Absen Masuk PKL',
        ]);

        // Now advance time to 08:30 WIB
        Carbon::setTestNow(Carbon::parse('2026-11-23 08:30:00'));

        $this->artisan('pkl:send-reminders')->assertExitCode(0);

        // Siswa B (08:30) should now receive notification!
        $this->assertDatabaseHas('notifikasis', [
            'to_user_id' => $this->userB->id,
            'judul' => 'Jangan Lupa Absen Masuk PKL',
        ]);

        // Total notifications for Siswa A should still be 1 (no duplicates)
        $countA = Notifikasi::where('to_user_id', $this->userA->id)
            ->where('judul', 'Jangan Lupa Absen Masuk PKL')
            ->count();
        $this->assertEquals(1, $countA);
    }

    public function test_send_pkl_reminders_supports_afternoon_shift_and_rescheduled_alarm_same_day(): void
    {
        // Siswa A sets alarm to 13:00 (afternoon shift / live test after 11:00)
        $this->siswaA->update(['jam_notif_masuk' => '13:00']);

        Carbon::setTestNow(Carbon::parse('2026-11-23 12:55:00'));
        $this->artisan('pkl:send-reminders')->assertExitCode(0);
        $this->assertDatabaseMissing('notifikasis', [
            'to_user_id' => $this->userA->id,
            'judul' => 'Jangan Lupa Absen Masuk PKL',
        ]);

        // At 13:00 WIB -> alarm fires!
        Carbon::setTestNow(Carbon::parse('2026-11-23 13:00:00'));
        $this->artisan('pkl:send-reminders')->assertExitCode(0);
        $this->assertEquals(1, Notifikasi::where('to_user_id', $this->userA->id)
            ->where('judul', 'Jangan Lupa Absen Masuk PKL')
            ->count());

        // At 13:01 WIB -> does NOT duplicate
        Carbon::setTestNow(Carbon::parse('2026-11-23 13:01:00'));
        $this->artisan('pkl:send-reminders')->assertExitCode(0);
        $this->assertEquals(1, Notifikasi::where('to_user_id', $this->userA->id)
            ->where('judul', 'Jangan Lupa Absen Masuk PKL')
            ->count());

        // Student reschedules alarm to 14:15 same day to re-test -> fires at 14:15
        $this->siswaA->update(['jam_notif_masuk' => '14:15']);
        Carbon::setTestNow(Carbon::parse('2026-11-23 14:15:00'));
        $this->artisan('pkl:send-reminders')->assertExitCode(0);
        $this->assertEquals(2, Notifikasi::where('to_user_id', $this->userA->id)
            ->where('judul', 'Jangan Lupa Absen Masuk PKL')
            ->count());
    }
}
