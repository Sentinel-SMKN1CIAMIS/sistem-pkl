<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\Jurnal;
use App\Models\Kompetensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembimbingSekolahJournalMonitoringTest extends TestCase
{
    use RefreshDatabase;

    private $pembimbing;
    private $pembimbingUser;
    private $siswa1;
    private $siswa2;

    protected function setUp(): void
    {
        parent::setUp();

        $prog = ProgramKeahlian::create(['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak']);
        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $prog->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        // Create Kompetensi
        $kompetensi = Kompetensi::create([
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nama' => 'Dasar RPL',
            'kategori' => 'kejuruan',
            'cp' => 'Capaian',
            'tp' => 'Tujuan',
            'deskripsi' => 'Deskripsi',
        ]);

        // Create Advisor
        $this->pembimbingUser = User::create([
            'name' => 'Advisor Test',
            'username' => 'advisor1',
            'email' => 'advisor@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);
        $this->pembimbing = PembimbingSekolah::create([
            'user_id' => $this->pembimbingUser->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nip' => '12345678',
            'nama_lengkap' => 'Advisor Test',
            'tipe' => 'kejuruan',
        ]);

        // Create Students
        $siswaUser1 = User::create([
            'name' => 'Siswa 1',
            'username' => 'siswa1',
            'email' => 'siswa1@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $this->siswa1 = Siswa::create([
            'user_id' => $siswaUser1->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa 1',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'pembimbing_sekolah_id' => $this->pembimbing->id,
        ]);

        $siswaUser2 = User::create([
            'name' => 'Siswa 2',
            'username' => 'siswa2',
            'email' => 'siswa2@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $this->siswa2 = Siswa::create([
            'user_id' => $siswaUser2->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nis' => 'siswa2',
            'nama_lengkap' => 'Siswa 2',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'P',
            'tahun_ajaran' => '2026/2027',
            'pembimbing_sekolah_umum_id' => $this->pembimbing->id,
        ]);

        // Siswa 1 fills journal today
        Jurnal::create([
            'siswa_id' => $this->siswa1->id,
            'kompetensi_id' => $kompetensi->id,
            'tanggal' => \Carbon\Carbon::today()->toDateString(),
            'deskripsi_pekerjaan' => 'Working on task',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);
    }

    public function test_school_advisor_journal_monitoring_displays_correct_counts_and_splits()
    {
        $this->actingAs($this->pembimbingUser);

        // 1. Check default tab (belum-isi)
        $response = $this->get(route('pembimbing_sekolah.siswa.index'));
        $response->assertStatus(200);

        // Verify view variables
        $response->assertViewHas('students');
        $response->assertViewHas('totalStudentsCount', 2);
        $response->assertViewHas('hasFilledTodayCount', 1);
        $response->assertViewHas('notFilledTodayCount', 1);
        $response->assertViewHas('studentsPendingApprovalCount', 1);

        $students = $response->viewData('students');
        // Siswa 2 has not filled today
        $this->assertCount(1, $students);
        $this->assertEquals($this->siswa2->id, $students->first()->id);

        // 2. Check sudah-isi tab
        $responseFilled = $this->get(route('pembimbing_sekolah.siswa.index', ['tab' => 'sudah-isi']));
        $responseFilled->assertStatus(200);
        
        $studentsFilled = $responseFilled->viewData('students');
        // Siswa 1 has filled today
        $this->assertCount(1, $studentsFilled);
        $this->assertEquals($this->siswa1->id, $studentsFilled->first()->id);

        // 3. Check butuh-approval tab
        $responsePending = $this->get(route('pembimbing_sekolah.siswa.index', ['tab' => 'butuh-approval']));
        $responsePending->assertStatus(200);

        $studentsPending = $responsePending->viewData('students');
        // Siswa 1 has a pending journal
        $this->assertCount(1, $studentsPending);
        $this->assertEquals($this->siswa1->id, $studentsPending->first()->id);
    }

    public function test_school_advisor_can_send_jurnal_reminder_notification()
    {
        $this->actingAs($this->pembimbingUser);

        // Siswa 2 has not filled today, let's remind them
        $response = $this->post(route('pembimbing_sekolah.siswa.remind', $this->siswa2->id));

        $response->assertStatus(302); // Redirect back
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notifikasis', [
            'from_user_id' => $this->pembimbingUser->id,
            'to_user_id' => $this->siswa2->user_id,
            'judul' => 'Segera Isi Jurnal',
            'tipe' => 'jurnal_reminder',
            'is_read' => 0
        ]);
    }

    public function test_school_advisor_cannot_remind_unassigned_student()
    {
        // Create another advisor who is not associated with Siswa 2
        $otherUser = User::create([
            'name' => 'Other Advisor',
            'username' => 'otheradvisor',
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);
        $otherAdvisor = PembimbingSekolah::create([
            'user_id' => $otherUser->id,
            'nip' => '87654321',
            'nama_lengkap' => 'Other Advisor',
            'tipe' => 'kejuruan',
        ]);

        $this->actingAs($otherUser);

        // Try to remind Siswa 2 (who is assigned to $this->pembimbing)
        $response = $this->post(route('pembimbing_sekolah.siswa.remind', $this->siswa2->id));

        $response->assertStatus(302); // Redirect back
        $response->assertSessionHas('error', 'Anda tidak memiliki wewenang untuk memberi peringatan pada siswa ini.');

        // Assert notification was not created
        $this->assertDatabaseMissing('notifikasis', [
            'from_user_id' => $otherUser->id,
            'to_user_id' => $this->siswa2->user_id,
        ]);
    }

    public function test_school_advisor_can_remind_all_unfilled_students()
    {
        $this->actingAs($this->pembimbingUser);

        // Siswa 2 has not filled today. Siswa 1 has filled today.
        $response = $this->post(route('pembimbing_sekolah.siswa.remind_all'));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Siswa 2 should get reminded
        $this->assertDatabaseHas('notifikasis', [
            'from_user_id' => $this->pembimbingUser->id,
            'to_user_id' => $this->siswa2->user_id,
            'judul' => 'Segera Isi Jurnal',
            'tipe' => 'jurnal_reminder'
        ]);

        // Siswa 1 should NOT get reminded because they filled their journal today
        $this->assertDatabaseMissing('notifikasis', [
            'from_user_id' => $this->pembimbingUser->id,
            'to_user_id' => $this->siswa1->user_id,
            'judul' => 'Segera Isi Jurnal',
            'tipe' => 'jurnal_reminder'
        ]);
    }

    public function test_school_advisor_remind_all_fails_when_everyone_filled()
    {
        $this->actingAs($this->pembimbingUser);

        // Let's create a journal today for Siswa 2 too
        Jurnal::create([
            'siswa_id' => $this->siswa2->id,
            'kompetensi_id' => Jurnal::first()->kompetensi_id, // Reuse existing kompetensi
            'tanggal' => \Carbon\Carbon::today()->toDateString(),
            'deskripsi_pekerjaan' => 'Working on task 2',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        // Now everyone has filled today's journal
        $response = $this->post(route('pembimbing_sekolah.siswa.remind_all'));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Semua siswa bimbingan Anda sudah mengisi jurnal hari ini.');
    }

    public function test_school_advisor_can_filter_journals_by_siswa()
    {
        $this->actingAs($this->pembimbingUser);
        $kompetensiId = Jurnal::first()->kompetensi_id;

        // Create journals for both students
        $j1 = Jurnal::create([
            'siswa_id' => $this->siswa1->id,
            'kompetensi_id' => $kompetensiId,
            'tanggal' => '2026-06-01',
            'deskripsi_pekerjaan' => 'Jurnal Siswa 1',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $j2 = Jurnal::create([
            'siswa_id' => $this->siswa2->id,
            'kompetensi_id' => $kompetensiId,
            'tanggal' => '2026-06-02',
            'deskripsi_pekerjaan' => 'Jurnal Siswa 2',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        // Request with siswa_id filter for Siswa 1
        $response = $this->get(route('pembimbing_sekolah.jurnal.index', [
            'siswa_id' => $this->siswa1->id
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('jurnals');
        
        $jurnals = $response->viewData('jurnals');
        // Note: Siswa 1 also has 1 journal created in setUp(), so total is 2
        $this->assertEquals(2, $jurnals->total());
        $this->assertTrue($jurnals->contains($j1));
    }

    public function test_school_advisor_can_filter_journals_by_month_and_week()
    {
        $this->actingAs($this->pembimbingUser);
        $kompetensiId = Jurnal::first()->kompetensi_id;

        // Create journals on different dates
        // Bulan 6, Minggu 1 (Tgl 1-7)
        $j1 = Jurnal::create([
            'siswa_id' => $this->siswa1->id,
            'kompetensi_id' => $kompetensiId,
            'tanggal' => '2026-06-03',
            'deskripsi_pekerjaan' => 'Minggu 1',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        // Bulan 6, Minggu 2 (Tgl 8-14)
        $j2 = Jurnal::create([
            'siswa_id' => $this->siswa1->id,
            'kompetensi_id' => $kompetensiId,
            'tanggal' => '2026-06-10',
            'deskripsi_pekerjaan' => 'Minggu 2',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        // Request with bulan=6 and minggu=1
        $response = $this->get(route('pembimbing_sekolah.jurnal.index', [
            'bulan' => '6',
            'minggu' => '1',
            'tahun' => '2026'
        ]));

        $response->assertStatus(200);
        $jurnals = $response->viewData('jurnals');
        $this->assertEquals(1, $jurnals->total());
        $this->assertEquals($j1->id, $jurnals->first()->id);

        // Request with bulan=6 and minggu=2
        $response2 = $this->get(route('pembimbing_sekolah.jurnal.index', [
            'bulan' => '6',
            'minggu' => '2',
            'tahun' => '2026'
        ]));

        $response2->assertStatus(200);
        $jurnals2 = $response2->viewData('jurnals');
        $this->assertEquals(1, $jurnals2->total());
        $this->assertEquals($j2->id, $jurnals2->first()->id);
    }
}
