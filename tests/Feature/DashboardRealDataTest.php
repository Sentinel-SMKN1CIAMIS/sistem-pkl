<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Dudi;
use App\Models\PembimbingSekolah;
use App\Models\PembimbingDudi;
use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\ProgramKeahlian;
use App\Models\KonsentrasiKeahlian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class DashboardRealDataTest extends TestCase
{
    use RefreshDatabase;

    private ?\App\Models\KonsentrasiKeahlian $konsentrasi = null;
    private ?\App\Models\Dudi $dudi = null;

    protected function setUp(): void
    {
        parent::setUp();

        $prog = ProgramKeahlian::create(['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak']);
        $this->konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $prog->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        $this->dudi = Dudi::create([
            'nama' => 'PT Test DUDI',
            'alamat' => 'Alamat DUDI',
            'kota' => 'Kota DUDI',
            'is_active' => true,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
        ]);
    }

    public function test_siswa_dashboard_retrieves_real_attendance_and_journals()
    {
        $user = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa1',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $siswa = Siswa::create([
            'user_id' => $user->id,
            'dudi_id' => $this->dudi->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);

        // Seed some journals
        Jurnal::create([
            'siswa_id' => $siswa->id,
            'tanggal' => Carbon::now()->toDateString(),
            'kegiatan' => 'Belajar Laravel',
            'status' => 'valid',
        ]);

        Jurnal::create([
            'siswa_id' => $siswa->id,
            'tanggal' => Carbon::now()->subDay()->toDateString(),
            'kegiatan' => 'Belajar PHPUnit',
            'status' => 'pending',
        ]);

        // Seed some attendance
        $startOfWeek = Carbon::now()->startOfWeek(1);
        Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $startOfWeek->toDateString(), // Monday
            'waktu_datang' => '07:15:00',
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response->viewData('stats');
        $this->assertEquals(2, $stats['jurnal_total']);
        $this->assertEquals(1, $stats['jurnal_valid']);
        $this->assertEquals(1, $stats['absensi_count']);
        
        // Monday (index 0) should be 7.25 decimal hour (7 + 15/60)
        $this->assertEquals(7.25, $stats['week_attendance'][0]);
        // Tuesday (index 1) should be null
        $this->assertNull($stats['week_attendance'][1]);
    }

    public function test_pembimbing_sekolah_dashboard_retrieves_real_statistics()
    {
        $teacherUser = User::create([
            'name' => 'Guru Pembimbing',
            'username' => 'guru1',
            'email' => 'guru@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $teacher = PembimbingSekolah::create([
            'user_id' => $teacherUser->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nip' => '12345678',
            'nama_lengkap' => 'Guru Pembimbing',
            'tipe' => 'umum',
            'kapasitas' => 10,
        ]);

        $siswaUser = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa1',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $siswa = Siswa::create([
            'user_id' => $siswaUser->id,
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $teacher->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);

        // Seed journals under this teacher
        Jurnal::create([
            'siswa_id' => $siswa->id,
            'tanggal' => Carbon::now()->toDateString(),
            'kegiatan' => 'Belajar Laravel',
            'status' => 'pending',
        ]);

        Jurnal::create([
            'siswa_id' => $siswa->id,
            'tanggal' => Carbon::now()->toDateString(),
            'kegiatan' => 'Belajar MySQL',
            'status' => 'valid',
        ]);

        $response = $this->actingAs($teacherUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stats');

        $stats = $response->viewData('stats');
        $this->assertEquals(1, $stats['siswa_count']);
        $this->assertEquals(1, $stats['jurnal_pending']);
        $this->assertEquals(2, $stats['jurnal_masuk']);
        $this->assertIsArray($stats['weeks_evaluated']);
        $this->assertCount(4, $stats['weeks_evaluated']);
        // The last week (current week) has 1 evaluated journal ('valid')
        $this->assertEquals(1, $stats['weeks_evaluated'][3]);
    }

    public function test_pembimbing_dudi_dashboard_retrieves_real_statistics()
    {
        $mentorUser = User::create([
            'name' => 'Pembimbing Dudi',
            'username' => 'dudi1',
            'email' => 'dudi@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_dudi',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        PembimbingDudi::create([
            'user_id' => $mentorUser->id,
            'dudi_id' => $this->dudi->id,
            'nama_lengkap' => 'Pembimbing Dudi',
        ]);

        $siswaUser = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa1',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        Siswa::create([
            'user_id' => $siswaUser->id,
            'dudi_id' => $this->dudi->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);

        $response = $this->actingAs($mentorUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stats');

        $stats = $response->viewData('stats');
        $this->assertEquals(1, $stats['siswa_count']);
        $this->assertEquals(0, $stats['jurnal_pending']);
    }

    public function test_kaprog_dashboard_retrieves_real_statistics()
    {
        $user = User::create([
            'name' => 'Kaprog User',
            'username' => 'kaprog1',
            'email' => 'kaprog@test.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $siswaUser = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa1',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $siswa = Siswa::create([
            'user_id' => $siswaUser->id,
            'dudi_id' => $this->dudi->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);

        // Absensi today
        Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_datang' => '07:00:00',
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stats');

        $stats = $response->viewData('stats');
        $this->assertEquals(1, $stats['total_siswa']);
        $this->assertEquals(1, $stats['total_dudi']);
        $this->assertEquals(100, $stats['attendance_rate']); // 1 of 1 active students present
        $this->assertEquals(0, $stats['journal_rate']); // 0 of 1 active students filled journal
    }

    public function test_pokja_dashboard_retrieves_real_weekly_stats()
    {
        $user = User::create([
            'name' => 'Pokja User',
            'username' => 'pokja1',
            'email' => 'pokja@test.com',
            'password' => bcrypt('password'),
            'role' => 'pokja',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $siswaUser = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa1',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $siswa = Siswa::create([
            'user_id' => $siswaUser->id,
            'dudi_id' => $this->dudi->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);

        // Jurnal on Monday
        $startOfWeek = Carbon::now()->startOfWeek(1);
        Jurnal::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $startOfWeek->toDateString(),
            'kegiatan' => 'Belajar Laravel',
            'status' => 'valid',
        ]);

        // Absensi on Monday
        Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $startOfWeek->toDateString(),
            'waktu_datang' => '07:30:00',
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stats');

        $stats = $response->viewData('stats');
        $this->assertEquals(1, $stats['total_siswa']);
        $this->assertEquals(1, $stats['total_dudi']);
        
        // Monday (index 0) should be 1
        $this->assertEquals(1, $stats['week_jurnal'][0]);
        $this->assertEquals(1, $stats['week_absensi'][0]);
        // Tuesday (index 1) should be 0
        $this->assertEquals(0, $stats['week_jurnal'][1]);
        $this->assertEquals(0, $stats['week_absensi'][1]);
    }

    public function test_pembimbing_sekolah_can_approve_jurnal_and_sync_status()
    {
        $teacherUser = User::create([
            'name' => 'Guru Pembimbing',
            'username' => 'guru1',
            'email' => 'guru@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $teacher = PembimbingSekolah::create([
            'user_id' => $teacherUser->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nip' => '12345678',
            'nama_lengkap' => 'Guru Pembimbing',
            'tipe' => 'umum',
            'kapasitas' => 10,
        ]);

        $siswaUser = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa1',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $siswa = Siswa::create([
            'user_id' => $siswaUser->id,
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $teacher->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);

        $jurnal = Jurnal::create([
            'siswa_id' => $siswa->id,
            'tanggal' => Carbon::now()->toDateString(),
            'deskripsi_pekerjaan' => 'Belajar Laravel',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $response = $this->actingAs($teacherUser)
            ->post(route('pembimbing_sekolah.jurnal.approve', $jurnal), [
                'approval_notes' => 'Bagus sekali',
            ]);

        $response->assertRedirect();
        
        $jurnal->refresh();
        $this->assertEquals('approved', $jurnal->approval_status);
        $this->assertEquals('valid', $jurnal->status);
    }

    public function test_pembimbing_sekolah_can_reject_jurnal_and_sync_status()
    {
        $teacherUser = User::create([
            'name' => 'Guru Pembimbing',
            'username' => 'guru1',
            'email' => 'guru@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $teacher = PembimbingSekolah::create([
            'user_id' => $teacherUser->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nip' => '12345678',
            'nama_lengkap' => 'Guru Pembimbing',
            'tipe' => 'umum',
            'kapasitas' => 10,
        ]);

        $siswaUser = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa1',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $siswa = Siswa::create([
            'user_id' => $siswaUser->id,
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $teacher->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);

        $jurnal = Jurnal::create([
            'siswa_id' => $siswa->id,
            'tanggal' => Carbon::now()->toDateString(),
            'deskripsi_pekerjaan' => 'Belajar Laravel',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $response = $this->actingAs($teacherUser)
            ->post(route('pembimbing_sekolah.jurnal.reject', $jurnal), [
                'approval_notes' => 'Tolong diperbaiki deskripsinya',
            ]);

        $response->assertRedirect();
        
        $jurnal->refresh();
        $this->assertEquals('rejected', $jurnal->approval_status);
        $this->assertEquals('invalid', $jurnal->status);
    }
}
