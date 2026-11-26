<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\Jurnal;
use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaprogMonitoringSiswaTest extends TestCase
{
    use RefreshDatabase;

    private User $kaprogPplg;
    private Siswa $siswaRpl1;
    private Siswa $siswaRpl2;
    private Siswa $siswaAkl;
    private Kompetensi $kompetensi;

    protected function setUp(): void
    {
        parent::setUp();

        $programPplg = ProgramKeahlian::create(['kode' => 'PPLG', 'nama' => 'PPLG']);
        $konsRpl = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $programPplg->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        $programAkl = ProgramKeahlian::create(['kode' => 'AKL', 'nama' => 'Akuntansi']);
        $konsAkl = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $programAkl->id,
            'kode' => 'AKL',
            'nama' => 'Akuntansi',
            'durasi_pkl_bulan' => 6,
        ]);

        $dudi = Dudi::create([
            'nama' => 'PT Tech Ciamis',
            'konsentrasi_keahlian_id' => $konsRpl->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Sudirman',
            'is_active' => true,
        ]);

        $this->kompetensi = Kompetensi::create([
            'konsentrasi_keahlian_id' => $konsRpl->id,
            'nama' => 'Hard Skills',
            'tp' => '1. Membangun aplikasi',
            'kategori' => 'teknis',
            'cp' => 'Mampu membangun aplikasi.',
            'deskripsi' => 'App dev',
        ]);

        $this->kaprogPplg = User::create([
            'name' => 'Kaprog PPLG',
            'username' => 'kaprog_pplg_mon',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'program_keahlian_id' => $programPplg->id,
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $guruUser = User::create([
            'name' => 'Guru Pembimbing',
            'username' => '198001012005011077',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => false,
        ]);
        $guru = PembimbingSekolah::create([
            'user_id' => $guruUser->id,
            'konsentrasi_keahlian_id' => $konsRpl->id,
            'nama_lengkap' => 'Guru Pembimbing',
        ]);

        $u1 = User::create(['name' => 'Siswa RPL 1', 'username' => '242510301', 'password' => bcrypt('password'), 'role' => 'siswa', 'is_active' => true]);
        $this->siswaRpl1 = Siswa::create([
            'user_id' => $u1->id,
            'konsentrasi_keahlian_id' => $konsRpl->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $guru->id,
            'nis' => '242510301',
            'nama_lengkap' => 'Siswa RPL 1',
            'kelas' => 'XII RPL 1',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'L',
            'status_pkl' => 'sedang_pkl',
        ]);

        $u2 = User::create(['name' => 'Siswa RPL 2', 'username' => '242510302', 'password' => bcrypt('password'), 'role' => 'siswa', 'is_active' => true]);
        $this->siswaRpl2 = Siswa::create([
            'user_id' => $u2->id,
            'konsentrasi_keahlian_id' => $konsRpl->id,
            'dudi_id' => $dudi->id,
            'nis' => '242510302',
            'nama_lengkap' => 'Siswa RPL 2',
            'kelas' => 'XII RPL 2',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'P',
            'status_pkl' => 'sedang_pkl',
        ]);

        $u3 = User::create(['name' => 'Siswa AKL 1', 'username' => '242510303', 'password' => bcrypt('password'), 'role' => 'siswa', 'is_active' => true]);
        $this->siswaAkl = Siswa::create([
            'user_id' => $u3->id,
            'konsentrasi_keahlian_id' => $konsAkl->id,
            'dudi_id' => $dudi->id,
            'nis' => '242510303',
            'nama_lengkap' => 'Siswa AKL 1',
            'kelas' => 'XII AKL 1',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'P',
            'status_pkl' => 'sedang_pkl',
        ]);
    }

    public function test_kaprog_can_directly_monitor_all_students_in_own_program(): void
    {
        $res = $this->actingAs($this->kaprogPplg)->get(route('kaprog.monitoring_siswa.index'));
        $res->assertStatus(200);
        $res->assertSee('Siswa RPL 1', false);
        $res->assertSee('Siswa RPL 2', false);
        $res->assertDontSee('Siswa AKL 1', false);
    }

    public function test_kaprog_can_view_student_detail_journals_and_attendances(): void
    {
        $today = Carbon::today()->toDateString();

        Absensi::create([
            'siswa_id' => $this->siswaRpl1->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
            'waktu_pulang' => '16:30:00',
        ]);

        Jurnal::create([
            'siswa_id' => $this->siswaRpl1->id,
            'kompetensi_id' => $this->kompetensi->id,
            'tanggal' => $today,
            'deskripsi_pekerjaan' => 'Membuat halaman monitoring siswa langsung',
            'catatan' => 'Berhasil diuji dengan PHPUnit.',
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $res = $this->actingAs($this->kaprogPplg)->get(route('kaprog.monitoring_siswa.show', $this->siswaRpl1));
        $res->assertStatus(200);
        $res->assertSee('Siswa RPL 1', false);
        $res->assertSee('Membuat halaman monitoring siswa langsung', false);
        $res->assertSee('08:00:00', false);
    }

    public function test_kaprog_cannot_view_student_from_other_program(): void
    {
        $this->actingAs($this->kaprogPplg)
            ->get(route('kaprog.monitoring_siswa.show', $this->siswaAkl))
            ->assertStatus(403);
    }
}
