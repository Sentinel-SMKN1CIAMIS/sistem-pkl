<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Dudi;
use App\Models\PembimbingSekolah;
use App\Models\Absensi;
use App\Models\ProgramKeahlian;
use App\Models\KonsentrasiKeahlian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AbsensiApprovalEditTest extends TestCase
{
    use RefreshDatabase;

    private $program;
    private $konsentrasi;
    private $dudi;
    private $teacherUser;
    private $teacher;
    private $studentUser;
    private $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->program = ProgramKeahlian::create(['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak']);
        $this->konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->program->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6
        ]);

        $this->dudi = Dudi::create([
            'nama' => 'PT Test DUDI',
            'alamat' => 'Alamat DUDI',
            'kota' => 'Kota DUDI',
            'is_active' => true,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
        ]);

        $this->teacherUser = User::create([
            'name' => 'Guru Pembimbing',
            'username' => 'guru1',
            'email' => 'guru@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->teacher = PembimbingSekolah::create([
            'user_id' => $this->teacherUser->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nip' => '12345678',
            'nama_lengkap' => 'Guru Pembimbing',
            'tipe' => 'umum',
            'kapasitas' => 10,
        ]);

        $this->studentUser = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa1',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $this->studentUser->id,
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $this->teacher->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);
    }

    public function test_siswa_can_submit_sakit_absence_request()
    {
        $response = $this->actingAs($this->studentUser)
            ->post(route('siswa.absensi.submit-absence-request'), [
                'status' => 'sakit',
                'alasan' => 'Saya tidak bisa hadir hari ini karena sakit.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $this->siswa->id,
            'status' => 'sakit',
            'alasan' => 'Saya tidak bisa hadir hari ini karena sakit.',
            'approval_status' => 'pending',
        ]);
    }

    public function test_pembimbing_sekolah_can_see_pending_alpha_absences()
    {
        // Seed a pending alpha absence
        $absensi = Absensi::create([
            'siswa_id' => $this->siswa->id,
            'tanggal' => Carbon::now()->subDays(2)->toDateString(),
            'status' => 'alpha',
            'alasan' => 'Terlambat mengisi jurnal',
            'approval_status' => 'pending',
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->get(route('pembimbing_sekolah.absensi.approval.index'));

        $response->assertStatus(200);
        $response->assertSee('Siswa Test');
        $response->assertSee('Alpa');
        $response->assertSee('Terlambat mengisi jurnal');
    }





    public function test_pembimbing_sekolah_can_see_correct_attendance_status_on_index_page()
    {
        // Seed an alpha attendance record
        $absensiAlpha = Absensi::create([
            'siswa_id' => $this->siswa->id,
            'tanggal' => Carbon::now()->subDays(1)->toDateString(),
            'status' => 'alpha',
            'approval_status' => 'approved',
        ]);

        // Seed an izin attendance record
        $absensiIzin = Absensi::create([
            'siswa_id' => $this->siswa->id,
            'tanggal' => Carbon::now()->toDateString(),
            'status' => 'izin',
            'approval_status' => 'approved',
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->get(route('pembimbing_sekolah.absensi.index'));

        $response->assertStatus(200);
        $response->assertSee('Siswa Test');
        
        // Assert that the page displays 'Alpa' and 'Izin' instead of hardcoded 'Hadir'
        $response->assertSee('Alpa');
        $response->assertSee('Izin');
    }
}
