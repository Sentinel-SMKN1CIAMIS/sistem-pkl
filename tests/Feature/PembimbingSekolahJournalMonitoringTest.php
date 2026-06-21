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
            'status' => 'hadir',
            'approval_status' => 'pending',
        ]);
    }

    public function test_school_advisor_journal_monitoring_displays_correct_counts_and_splits()
    {
        $this->actingAs($this->pembimbingUser);

        $response = $this->get(route('pembimbing_sekolah.siswa.index'));
        $response->assertStatus(200);

        // Verify students categorizations sent to view
        $response->assertViewHas('students');
        $response->assertViewHas('studentsNotFilledToday');
        $response->assertViewHas('studentsHasFilledToday');
        $response->assertViewHas('studentsPendingApproval');

        $notFilled = $response->viewData('studentsNotFilledToday');
        $filled = $response->viewData('studentsHasFilledToday');
        $pending = $response->viewData('studentsPendingApproval');

        // Siswa 2 has not filled today
        $this->assertCount(1, $notFilled);
        $this->assertEquals($this->siswa2->id, $notFilled->first()->id);

        // Siswa 1 has filled today
        $this->assertCount(1, $filled);
        $this->assertEquals($this->siswa1->id, $filled->first()->id);

        // Siswa 1 has a pending journal
        $this->assertCount(1, $pending);
        $this->assertEquals($this->siswa1->id, $pending->first()->id);
    }
}
