<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\MonitoringPembimbing;
use App\Models\Dudi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaprogMonitoringTest extends TestCase
{
    use RefreshDatabase;

    private $prog1;
    private $prog2;
    private $konsentrasi1;
    private $konsentrasi2;
    private $kaprog1;
    private $kaprog2;
    private $pembimbing1;
    private $pembimbing2;
    private $siswa1;
    private $siswa2;
    private $siswa3;

    protected function setUp(): void
    {
        parent::setUp();

        // Create program keahlian
        $this->prog1 = ProgramKeahlian::create(['kode' => 'AKL', 'nama' => 'Akuntansi']);
        $this->prog2 = ProgramKeahlian::create(['kode' => 'MPLB', 'nama' => 'Manajemen Bisnis']);

        // Create konsentrasi keahlian
        $this->konsentrasi1 = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->prog1->id,
            'kode' => 'AKL',
            'nama' => 'Akuntansi',
            'durasi_pkl_bulan' => 4,
        ]);

        $this->konsentrasi2 = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->prog2->id,
            'kode' => 'MPLB',
            'nama' => 'Manajemen Bisnis',
            'durasi_pkl_bulan' => 4,
        ]);

        // Create Kaprog users
        $this->kaprog1 = User::create([
            'name' => 'Kaprog Akuntansi',
            'username' => 'kaprog_akl',
            'email' => 'kaprog_akl@test.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'konsentrasi_keahlian_id' => $this->konsentrasi1->id,
            'program_keahlian_id' => $this->prog1->id,
            'is_active' => true,
        ]);

        $this->kaprog2 = User::create([
            'name' => 'Kaprog Bisnis',
            'username' => 'kaprog_mplb',
            'email' => 'kaprog_mplb@test.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'konsentrasi_keahlian_id' => $this->konsentrasi2->id,
            'program_keahlian_id' => $this->prog2->id,
            'is_active' => true,
        ]);

        // Create Pembimbing users
        $pUser1 = User::create([
            'name' => 'Pembimbing Sekolah 1',
            'username' => 'pembimbing1',
            'email' => 'pembimbing1@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        $pUser2 = User::create([
            'name' => 'Pembimbing Sekolah 2',
            'username' => 'pembimbing2',
            'email' => 'pembimbing2@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        // Create Pembimbing Sekolah records
        $this->pembimbing1 = PembimbingSekolah::create([
            'user_id' => $pUser1->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi1->id,
            'nip' => '12345',
            'nama_lengkap' => 'Pembimbing Sekolah 1',
            'tipe' => 'kejuruan',
            'no_hp' => '0812345678',
        ]);

        $this->pembimbing2 = PembimbingSekolah::create([
            'user_id' => $pUser2->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi2->id,
            'nip' => '67890',
            'nama_lengkap' => 'Pembimbing Sekolah 2',
            'tipe' => 'umum',
            'no_hp' => '0823456789',
        ]);

        // Create DUDI
        $dudi = Dudi::create([
            'nama' => 'PT Dudi Sukses',
            'nama_pimpinan' => 'Pak Dudi',
            'alamat' => 'Jalan Industri',
            'kota' => 'Kota Dudi',
            'no_telp' => '021-123456',
            'konsentrasi_keahlian_id' => $this->konsentrasi1->id,
            'is_active' => true,
        ]);

        // Create student users
        $student1 = User::create([
            'name' => 'Siswa AKL 1', 'username' => 'siswa_akl1', 'email' => 'siswa_akl1@test.com',
            'password' => bcrypt('password'), 'role' => 'siswa', 'is_active' => true,
        ]);
        $student2 = User::create([
            'name' => 'Siswa AKL 2', 'username' => 'siswa_akl2', 'email' => 'siswa_akl2@test.com',
            'password' => bcrypt('password'), 'role' => 'siswa', 'is_active' => true,
        ]);
        $student3 = User::create([
            'name' => 'Siswa MPLB 1', 'username' => 'siswa_mplb1', 'email' => 'siswa_mplb1@test.com',
            'password' => bcrypt('password'), 'role' => 'siswa', 'is_active' => true,
        ]);

        // Create siswas
        $this->siswa1 = Siswa::create([
            'user_id' => $student1->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi1->id,
            'pembimbing_sekolah_id' => $this->pembimbing1->id,
            'dudi_id' => $dudi->id,
            'nis' => '001',
            'nama_lengkap' => 'Siswa AKL 1',
            'kelas' => 'XII AKL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2025-2026',
        ]);

        $this->siswa2 = Siswa::create([
            'user_id' => $student2->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi1->id,
            'pembimbing_sekolah_id' => $this->pembimbing1->id,
            'nis' => '002',
            'nama_lengkap' => 'Siswa AKL 2',
            'kelas' => 'XII AKL 2',
            'jenis_kelamin' => 'P',
            'tahun_ajaran' => '2025-2026',
        ]);

        $this->siswa3 = Siswa::create([
            'user_id' => $student3->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi2->id,
            'pembimbing_sekolah_id' => $this->pembimbing2->id,
            'nis' => '003',
            'nama_lengkap' => 'Siswa MPLB 1',
            'kelas' => 'XII MPLB',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2025-2026',
        ]);
    }

    /**
     * Test Kaprog can see the list of pembimbing in their program
     */
    public function test_kaprog_can_view_monitoring_index()
    {
        $response = $this->actingAs($this->kaprog1)
            ->get(route('kaprog.monitoring.index'));

        $response->assertStatus(200);
        $response->assertViewHas('mentors');
        
        $mentors = $response->viewData('mentors');
        // Kaprog 1 should see Pembimbing 1 (same department) but not Pembimbing 2 (other department)
        $this->assertTrue($mentors->contains('id', $this->pembimbing1->id));
        $this->assertFalse($mentors->contains('id', $this->pembimbing2->id));
    }

    /**
     * Test Kaprog can filter monitoring index by search, status, and tipe
     */
    public function test_kaprog_can_filter_monitoring()
    {
        // Filter by search name
        $response = $this->actingAs($this->kaprog1)
            ->get(route('kaprog.monitoring.index', ['search' => 'Sekolah 1']));
        $response->assertStatus(200);
        $this->assertCount(1, $response->viewData('mentors'));

        // Filter by type "kejuruan"
        $response = $this->actingAs($this->kaprog1)
            ->get(route('kaprog.monitoring.index', ['tipe' => 'kejuruan']));
        $response->assertStatus(200);
        $this->assertCount(1, $response->viewData('mentors'));

        // Filter by type "umum" (Pembimbing 1 is kejuruan, so should get 0)
        $response = $this->actingAs($this->kaprog1)
            ->get(route('kaprog.monitoring.index', ['tipe' => 'umum']));
        $response->assertStatus(200);
        $this->assertCount(0, $response->viewData('mentors'));
    }

    /**
     * Test Kaprog can view detail of advisor in their program (read-only)
     */
    public function test_kaprog_can_view_monitoring_detail_view_only()
    {
        $response = $this->actingAs($this->kaprog1)
            ->get(route('kaprog.monitoring.show', $this->pembimbing1->id));

        $response->assertStatus(200);
        $response->assertViewHas('pembimbingSekolah');
        $response->assertViewHas('students');
        $response->assertViewHas('monitoringLogs');
        
        // Assert no evaluation form/input is present in the HTML response
        $response->assertDontSee('Input Catatan Evaluasi');
        $response->assertDontSee('Simpan Catatan Evaluasi');
        $response->assertDontSee('action="' . route('pokja.monitoring.storeNote', $this->pembimbing1->id) . '"', false);
    }

    /**
     * Test Kaprog cannot view detail of advisor from other program
     */
    public function test_kaprog_cannot_view_other_program_advisor_detail()
    {
        $response = $this->actingAs($this->kaprog1)
            ->get(route('kaprog.monitoring.show', $this->pembimbing2->id));

        // Must abort with 403 Forbidden
        $response->assertStatus(403);
    }

    /**
     * Test Kaprog can filter Laporan by kelas
     */
    public function test_kaprog_can_filter_laporan_by_kelas()
    {
        $response = $this->actingAs($this->kaprog1)
            ->get(route('kaprog.laporan.index', ['kelas' => 'XII AKL 1']));

        $response->assertStatus(200);
        $response->assertViewHas('siswas');
        
        $siswas = $response->viewData('siswas')->items();
        $this->assertCount(1, $siswas);
        $this->assertEquals('Siswa AKL 1', $siswas[0]->nama_lengkap);
    }

    /**
     * Test Kaprog can download rekap PDF
     */
    public function test_kaprog_can_download_rekap_pdf()
    {
        $response = $this->actingAs($this->kaprog1)
            ->get(route('kaprog.laporan.export', ['kelas' => 'XII AKL 1']));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=rekap-penempatan-pkl-akl-xii-akl-1-' . now()->format('Y-m-d') . '.pdf');
    }
}
