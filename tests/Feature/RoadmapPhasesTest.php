<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\HariLiburNasional;
use App\Models\Jurnal;
use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;
use App\Models\KunjunganMonitoring;
use App\Models\Notifikasi;
use App\Models\PembimbingDudi;
use App\Models\PembimbingSekolah;
use App\Models\Pengumuman;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use App\Services\EarlyWarningService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadmapPhasesTest extends TestCase
{
    use RefreshDatabase;

    private ProgramKeahlian $programPplg;
    private KonsentrasiKeahlian $konsRpl;
    private ProgramKeahlian $programAkl;
    private KonsentrasiKeahlian $konsAkl;
    private Dudi $dudi;
    private Kompetensi $kompetensi;
    private User $superAdmin;
    private User $kaprogPplg;
    private User $guruUser1;
    private PembimbingSekolah $guru1;
    private User $guruUser2;
    private PembimbingSekolah $guru2;
    private User $dudiUser;
    private PembimbingDudi $mentorDudi;
    private User $siswaUser1;
    private Siswa $siswa1;
    private User $siswaUser2;
    private Siswa $siswa2;
    private User $siswaAklUser;
    private Siswa $siswaAkl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->programPplg = ProgramKeahlian::create(['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak dan Gim']);
        $this->konsRpl = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->programPplg->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        $this->programAkl = ProgramKeahlian::create(['kode' => 'AKL', 'nama' => 'Akuntansi']);
        $this->konsAkl = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->programAkl->id,
            'kode' => 'AKL',
            'nama' => 'Akuntansi dan Keuangan Lembaga',
            'durasi_pkl_bulan' => 6,
        ]);

        $this->dudi = Dudi::create([
            'nama' => 'PT Solusi Teknologi Nusantara',
            'konsentrasi_keahlian_id' => $this->konsRpl->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Sudirman No. 10',
            'is_active' => true,
        ]);

        $this->kompetensi = Kompetensi::create([
            'konsentrasi_keahlian_id' => $this->konsRpl->id,
            'nama' => 'Penerapan Hard Skills',
            'tp' => '1. Membangun aplikasi web',
            'kategori' => 'teknis',
            'cp' => 'Mampu membangun aplikasi web.',
            'deskripsi' => 'Web dev',
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'username' => 'admin_roadmap',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->kaprogPplg = User::create([
            'name' => 'Kaprog PPLG',
            'username' => 'kaprog_pplg',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'program_keahlian_id' => $this->programPplg->id,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->guruUser1 = User::create([
            'name' => 'Guru Satu',
            'username' => '198001012005011001',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => false,
        ]);
        $this->guru1 = PembimbingSekolah::create([
            'user_id' => $this->guruUser1->id,
            'konsentrasi_keahlian_id' => $this->konsRpl->id,
            'nama_lengkap' => 'Guru Satu',
        ]);

        $this->guruUser2 = User::create([
            'name' => 'Guru Dua',
            'username' => '198001012005011002',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => false,
        ]);
        $this->guru2 = PembimbingSekolah::create([
            'user_id' => $this->guruUser2->id,
            'konsentrasi_keahlian_id' => $this->konsRpl->id,
            'nama_lengkap' => 'Guru Dua',
        ]);

        $this->dudiUser = User::create([
            'name' => 'Mentor DUDI',
            'username' => 'mentor_dudi_1',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_dudi',
            'is_active' => true,
            'force_password_change' => false,
        ]);
        $this->mentorDudi = PembimbingDudi::create([
            'user_id' => $this->dudiUser->id,
            'dudi_id' => $this->dudi->id,
            'nama_lengkap' => 'Mentor DUDI',
            'jabatan' => 'Lead Engineer',
            'no_hp' => '081234567800',
        ]);

        // Siswa 1 (RPL, dibimbing Guru 1)
        $this->siswaUser1 = User::create([
            'name' => 'Siswa RPL Satu',
            'username' => '242510101',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);
        $this->siswa1 = Siswa::create([
            'user_id' => $this->siswaUser1->id,
            'konsentrasi_keahlian_id' => $this->konsRpl->id,
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $this->guru1->id,
            'pembimbing_dudi_id' => $this->mentorDudi->id,
            'nis' => '242510101',
            'nama_lengkap' => 'Siswa RPL Satu',
            'kelas' => 'XII RPL 1',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'L',
            'status_pkl' => 'sedang_pkl',
            'reminder_settings_configured' => true,
            'hari_libur_rutin' => [6, 7],
        ]);

        // Siswa 2 (RPL, dibimbing Guru 2 — BUKAN guru1)
        $this->siswaUser2 = User::create([
            'name' => 'Siswa RPL Dua',
            'username' => '242510102',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);
        $this->siswa2 = Siswa::create([
            'user_id' => $this->siswaUser2->id,
            'konsentrasi_keahlian_id' => $this->konsRpl->id,
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $this->guru2->id,
            'pembimbing_dudi_id' => $this->mentorDudi->id,
            'nis' => '242510102',
            'nama_lengkap' => 'Siswa RPL Dua',
            'kelas' => 'XII RPL 2',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'P',
            'status_pkl' => 'sedang_pkl',
            'reminder_settings_configured' => true,
            'hari_libur_rutin' => [6, 7],
        ]);

        $dudiAkl = Dudi::create([
            'nama' => 'PT Akuntansi Ciamis',
            'konsentrasi_keahlian_id' => $this->konsAkl->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Ahmad Yani No. 2',
            'is_active' => true,
        ]);

        // Siswa AKL (program & DUDI lain — tidak boleh muncul di Kaprog PPLG atau mentor DUDI RPL)
        $this->siswaAklUser = User::create([
            'name' => 'Siswa AKL Lain',
            'username' => '242510201',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);
        $this->siswaAkl = Siswa::create([
            'user_id' => $this->siswaAklUser->id,
            'konsentrasi_keahlian_id' => $this->konsAkl->id,
            'dudi_id' => $dudiAkl->id,
            'pembimbing_sekolah_id' => $this->guru2->id,
            'nis' => '242510201',
            'nama_lengkap' => 'Siswa AKL Lain',
            'kelas' => 'XII AKL 1',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'P',
            'status_pkl' => 'sedang_pkl',
            'reminder_settings_configured' => true,
            'hari_libur_rutin' => [6, 7],
        ]);
    }

    public function test_phase1_early_warning_and_kaprog_full_program_attendance_recap(): void
    {
        // Thursday 2026-11-26. Siswa 1 has NO attendance on Mon 23, Tue 24, Wed 25 -> 3 consecutive absent workdays!
        Carbon::setTestNow(Carbon::parse('2026-11-26 09:00:00'));

        // Siswa 2 attended Wed 25 and filled journal -> safe
        Absensi::create([
            'siswa_id' => $this->siswa2->id,
            'tanggal' => '2026-11-25',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
        ]);
        Jurnal::create([
            'siswa_id' => $this->siswa2->id,
            'kompetensi_id' => $this->kompetensi->id,
            'tanggal' => '2026-11-25',
            'deskripsi_pekerjaan' => 'Coding API',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $ew = app(EarlyWarningService::class)->getWarningsForUser($this->kaprogPplg, Carbon::today());
        $this->assertEquals(1, $ew['total_at_risk']);
        $this->assertEquals(1, $ew['critical_count']);
        $this->assertEquals($this->siswa1->id, $ew['items'][0]['siswa_id']);

        // Kaprog PPLG Rekap Kehadiran must show BOTH Siswa RPL Satu & Siswa RPL Dua (not just one teacher's students)
        // and MUST NOT show Siswa AKL Lain
        $res = $this->actingAs($this->kaprogPplg)->get(route('kaprog.rekap_kehadiran.index'));
        $res->assertStatus(200);
        $res->assertSee('Siswa RPL Satu', false);
        $res->assertSee('Siswa RPL Dua', false);
        $res->assertDontSee('Siswa AKL Lain', false);
    }

    public function test_phase2_teacher_field_visit_logging_and_pokja_recap(): void
    {
        $res = $this->actingAs($this->guruUser1)->post(route('pembimbing_sekolah.kunjungan.store'), [
            'dudi_id' => $this->dudi->id,
            'tanggal_kunjungan' => Carbon::today()->toDateString(),
            'catatan_monitoring' => 'Siswa bekerja dengan sangat baik dan disiplin mengikuti arahan mentor.',
            'kendala' => 'Tidak ada kendala berarti.',
            'latitude' => -7.3275,
            'longitude' => 108.3552,
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('kunjungan_monitorings', [
            'pembimbing_sekolah_id' => $this->guru1->id,
            'dudi_id' => $this->dudi->id,
        ]);

        // Super Admin / Pokja can view the recap
        $recap = $this->actingAs($this->superAdmin)->get(route('pokja.kunjungan.index'));
        $recap->assertStatus(200);
        $recap->assertSee('Guru Satu', false);
        $recap->assertSee('PT Solusi Teknologi Nusantara', false);
    }

    public function test_phase3_pembimbing_dudi_bulk_approve_journals(): void
    {
        $j1 = Jurnal::create([
            'siswa_id' => $this->siswa1->id,
            'kompetensi_id' => $this->kompetensi->id,
            'tanggal' => '2026-11-24',
            'deskripsi_pekerjaan' => 'Kerja 1',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $j2 = Jurnal::create([
            'siswa_id' => $this->siswa2->id,
            'kompetensi_id' => $this->kompetensi->id,
            'tanggal' => '2026-11-25',
            'deskripsi_pekerjaan' => 'Kerja 2',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        // Unrelated student's journal (not supervised by this DUDI mentor)
        $jOther = Jurnal::create([
            'siswa_id' => $this->siswaAkl->id,
            'kompetensi_id' => $this->kompetensi->id,
            'tanggal' => '2026-11-25',
            'deskripsi_pekerjaan' => 'Kerja AKL',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $res = $this->actingAs($this->dudiUser)->post(route('pembimbing_dudi.jurnal.bulk-approve'), [
            'jurnal_ids' => [$j1->id, $j2->id, $jOther->id],
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('success');

        $this->assertEquals('valid', $j1->fresh()->status);
        $this->assertEquals('valid', $j2->fresh()->status);
        // Unrelated student journal MUST remain untouched
        $this->assertEquals('pending', $jOther->fresh()->status);
    }

    public function test_phase4_national_holiday_calendar_and_reminder_skip(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-26 08:00:00'));

        // Admin adds today as a national holiday
        $res = $this->actingAs($this->superAdmin)->post(route('pokja.hari_libur.store'), [
            'tanggal' => '2026-11-26',
            'nama' => 'Hari Libur Nasional Uji Coba',
            'keterangan' => 'Cuti Bersama',
        ]);
        $res->assertRedirect();
        $this->assertNotNull(HariLiburNasional::findByDate('2026-11-26'));

        // Student calendar shows Libur Nasional name
        $calRes = $this->actingAs($this->siswaUser1)->get(route('siswa.jurnal.index', ['month' => '11', 'year' => '2026']));
        $calRes->assertStatus(200);
        $calRes->assertSee('Hari Libur Nasional Uji Coba', false);

        // Morning reminder skips on national holiday
        $this->artisan('pkl:send-reminders')->assertExitCode(0);
        $this->assertDatabaseMissing('notifikasis', [
            'to_user_id' => $this->siswaUser1->id,
            'judul' => 'Jangan Lupa Absen Masuk PKL',
        ]);
    }

    public function test_phase5_broadcast_announcement_scoped_by_program(): void
    {
        $res = $this->actingAs($this->superAdmin)->post(route('pokja.pengumuman.store'), [
            'judul' => 'Deadline Laporan PPLG',
            'isi' => 'Harap seluruh siswa PPLG mengumpulkan laporan PKL paling lambat 15 Desember.',
            'target_role' => 'siswa',
            'program_keahlian_id' => $this->programPplg->id,
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('success');

        // PPLG students receive in-app notification & see banner on dashboard
        $this->assertDatabaseHas('notifikasis', [
            'to_user_id' => $this->siswaUser1->id,
            'judul' => 'Pengumuman: Deadline Laporan PPLG',
        ]);
        // AKL student does NOT receive PPLG-only announcement
        $this->assertDatabaseMissing('notifikasis', [
            'to_user_id' => $this->siswaAklUser->id,
            'judul' => 'Pengumuman: Deadline Laporan PPLG',
        ]);

        $dashPplg = $this->actingAs($this->siswaUser1)->get(route('dashboard'));
        $dashPplg->assertStatus(200);
        $dashPplg->assertSee('Deadline Laporan PPLG', false);

        $dashAkl = $this->actingAs($this->siswaAklUser)->get(route('dashboard'));
        $dashAkl->assertStatus(200);
        $dashAkl->assertDontSee('Deadline Laporan PPLG', false);
    }
}
