<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutineOffDayAndBackdateLiburTest extends TestCase
{
    use RefreshDatabase;

    private User $siswaUser;
    private Siswa $siswa;

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
            'nama' => 'Setda Kabupaten Ciamis',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Jend. Sudirman',
            'is_active' => true,
        ]);

        $guruUser = User::create([
            'name' => 'Pembimbing Sekolah',
            'username' => '198001012005011099',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);

        $guru = PembimbingSekolah::create([
            'user_id' => $guruUser->id,
            'nama_lengkap' => 'Pembimbing Sekolah',
        ]);

        $this->siswaUser = User::create([
            'name' => 'Siswa Test Libur',
            'username' => '242510888',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $this->siswaUser->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $guru->id,
            'nis' => '242510888',
            'nama_lengkap' => 'Siswa Test Libur',
            'kelas' => 'XII RPL 1',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567899',
            'status_pkl' => 'sedang_pkl',
            'jam_notif_masuk' => '07:00',
            'jam_notif_pulang' => '16:00',
            'jam_notif_jurnal' => '19:30',
            'reminder_settings_configured' => true,
            'hari_libur_rutin' => [6, 7], // Sabtu & Minggu
        ]);
    }

    public function test_student_can_save_routine_off_days_and_rolling_shift(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->postJson(route('siswa.profile.reminder-settings'), [
                'jam_notif_masuk' => '08:00',
                'jam_notif_pulang' => '17:00',
                'jam_notif_jurnal' => '19:30',
                'hari_libur_rutin' => [6, 7],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.hari_libur_rutin', [6, 7]);

        $this->siswa->refresh();
        $this->assertEquals([6, 7], $this->siswa->hari_libur_rutin);
        $this->assertEquals('Sabtu, Minggu', $this->siswa->hari_libur_rutin_label);
        $this->assertTrue($this->siswa->isRoutineDayOff('2026-11-21')); // Saturday
        $this->assertTrue($this->siswa->isRoutineDayOff('2026-11-22')); // Sunday
        $this->assertFalse($this->siswa->isRoutineDayOff('2026-11-23')); // Monday

        // Rolling shift (empty array)
        $resRolling = $this->actingAs($this->siswaUser)
            ->postJson(route('siswa.profile.reminder-settings'), [
                'jam_notif_masuk' => '08:00',
                'hari_libur_rutin' => [],
            ]);

        $resRolling->assertStatus(200);
        $this->siswa->refresh();
        $this->assertEquals('Shift Rolling / Tidak Tetap', $this->siswa->hari_libur_rutin_label);
        $this->assertFalse($this->siswa->isRoutineDayOff('2026-11-22'));
    }

    public function test_invalid_routine_off_day_number_is_rejected(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->postJson(route('siswa.profile.reminder-settings'), [
                'jam_notif_masuk' => '08:00',
                'hari_libur_rutin' => [9],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['hari_libur_rutin.0']);
    }

    public function test_calendar_marks_routine_off_days_and_hides_missing_attendance_warning_on_off_day(): void
    {
        // Set today to Sunday (routine off day)
        Carbon::setTestNow(Carbon::parse('2026-11-22 10:00:00'));

        $response = $this->actingAs($this->siswaUser)
            ->get(route('siswa.jurnal.index', ['month' => '11', 'year' => '2026']));

        $response->assertStatus(200);
        $response->assertSee('Libur Rutin DUDI', false);
        $response->assertDontSee('Anda belum melakukan absensi hari ini.', false);
    }

    public function test_morning_reminder_skips_student_on_routine_off_day(): void
    {
        // Sunday 08:00 WIB (student's routine off-day is [6, 7])
        Carbon::setTestNow(Carbon::parse('2026-11-22 08:00:00'));

        $this->artisan('pkl:send-reminders')->assertExitCode(0);

        $this->assertDatabaseMissing('notifikasis', [
            'to_user_id' => $this->siswaUser->id,
            'judul' => 'Jangan Lupa Absen Masuk PKL',
        ]);

        // Monday 08:00 WIB (working day) -> should receive notification
        Carbon::setTestNow(Carbon::parse('2026-11-23 08:00:00'));

        $this->artisan('pkl:send-reminders')->assertExitCode(0);

        $this->assertDatabaseHas('notifikasis', [
            'to_user_id' => $this->siswaUser->id,
            'judul' => 'Jangan Lupa Absen Masuk PKL',
        ]);
    }

    public function test_absensi_page_shows_missing_workday_banner_and_skips_routine_off_days(): void
    {
        // Tuesday 2026-11-24. Student worked Friday 2026-11-20.
        // Sat (21) & Sun (22) are routine off-days. Monday (23) has no attendance -> should appear in banner!
        Carbon::setTestNow(Carbon::parse('2026-11-24 08:30:00'));

        Absensi::create([
            'siswa_id' => $this->siswa->id,
            'tanggal' => '2026-11-20',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
            'waktu_pulang' => '16:00:00',
        ]);

        $response = $this->actingAs($this->siswaUser)->get(route('siswa.absensi.index'));
        $response->assertStatus(200);
        $response->assertSee('Tandai Kemarin Libur', false);
        $response->assertSee('2026-11-23', false);
        // Routine off-days (21 & 22) should NOT be flagged in the dropdown
        $response->assertDontSee('value="2026-11-21"', false);
        $response->assertDontSee('value="2026-11-22"', false);
    }

    public function test_student_can_backdate_libur_for_yesterday_and_rejects_future_or_duplicate(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-24 08:30:00'));

        // Backdate yesterday (Monday 2026-11-23) as libur
        $res = $this->actingAs($this->siswaUser)
            ->post(route('siswa.absensi.submit-absence-request'), [
                'tanggal' => '2026-11-23',
                'status' => 'libur',
                'alasan' => 'Libur mendadak dari instansi karena kegiatan rapat dinas.',
            ]);

        $res->assertRedirect();
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $this->siswa->id,
            'tanggal' => '2026-11-23',
            'status' => 'libur',
            'approval_status' => 'pending',
        ]);

        // Duplicate same date rejected
        $resDup = $this->actingAs($this->siswaUser)
            ->post(route('siswa.absensi.submit-absence-request'), [
                'tanggal' => '2026-11-23',
                'status' => 'libur',
                'alasan' => 'Coba duplikat libur untuk tanggal yang sama.',
            ]);
        $resDup->assertSessionHas('error');

        // Future date rejected
        $resFuture = $this->actingAs($this->siswaUser)
            ->post(route('siswa.absensi.submit-absence-request'), [
                'tanggal' => '2026-11-25',
                'status' => 'libur',
                'alasan' => 'Mencoba isi libur untuk besok yang belum terjadi.',
            ]);
        $resFuture->assertSessionHasErrors(['tanggal']);
    }
}
