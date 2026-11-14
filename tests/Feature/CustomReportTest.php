<?php

namespace Tests\Feature;

use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomReportTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $pokja;
    private ProgramKeahlian $program;
    private KonsentrasiKeahlian $konsentrasi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->program = ProgramKeahlian::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
        ]);

        $this->konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->program->id,
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
            'durasi_pkl_bulan' => 6,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'email' => 'admin@smkn1ciamis.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->pokja = User::create([
            'name' => 'Petugas Pokja PKL',
            'username' => 'pokja',
            'email' => 'pokja@smkn1ciamis.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'pokja',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $group = \App\Models\PokjaGroup::create([
            'name' => 'Grup Pokja Utama',
            'is_active' => true,
        ]);
        $group->users()->attach($this->pokja->id);
    }

    public function test_super_admin_can_access_custom_report_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.custom_report.index'));
        $response->assertOk();
        $response->assertViewIs('reports.custom-report');
        $response->assertSeeText('Custom Dynamic Report & Automated Excel/Pivot Builder');
    }

    public function test_super_admin_can_access_config_page_with_export_action(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.config.index'));
        $response->assertOk();
        $response->assertSeeText('Custom Dynamic Report');
    }

    public function test_pokja_can_access_siswa_page_with_export_action(): void
    {
        $response = $this->actingAs($this->pokja)->get(route('pokja.siswa.index'));
        $response->assertOk();
        $response->assertSeeText('Ekspor Kredensial / Data');
    }

    public function test_export_kredensial_siswa_excel_with_pivot_stream(): void
    {
        // Create DUDI and Teacher
        $dudi = Dudi::create([
            'nama' => 'PT Cipta Digital Nusantara',
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Jenderal Sudirman No. 10',
            'is_active' => true,
        ]);

        $userGuru = User::create([
            'name' => 'Acep Muhammad Soleh',
            'username' => 'acep.muhammad',
            'email' => 'acep@smkn1ciamis.sch.id',
            'password' => bcrypt('smkn1ciamis'),
            'role' => 'pembimbing_sekolah',
            'force_password_change' => true,
        ]);
        $pembimbing = PembimbingSekolah::create([
            'user_id' => $userGuru->id,
            'nama_lengkap' => 'Acep Muhammad Soleh',
            'tipe' => 'kejuruan',
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
        ]);

        // Create Siswa
        $userSiswa = User::create([
            'name' => 'Fahreza Akbar',
            'username' => '242510001',
            'email' => 'siswa@smkn1ciamis.sch.id',
            'password' => bcrypt('242510001'),
            'role' => 'siswa',
            'force_password_change' => true,
        ]);
        $siswa = Siswa::create([
            'user_id' => $userSiswa->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => '242510001',
            'nama_lengkap' => 'Fahreza Akbar',
            'kelas' => 'XII PPLG 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $pembimbing->id,
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.custom_report.export'), [
            'module' => 'kredensial_siswa',
            'columns' => ['nama_siswa', 'nis', 'kelas', 'jurusan', 'username', 'password_default', 'status_password', 'nama_dudi', 'pembimbing_sekolah'],
            'format' => 'xlsx',
            'include_pivot' => '1',
        ]);

        $response->assertOk();
        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );
        $this->assertStringContainsString('data-akun-kredensial-siswa-angkatan', $response->headers->get('Content-Disposition'));
    }

    public function test_export_kredensial_guru_excel_stream(): void
    {
        $nip = '197701202009011007'; // NIP resmi Kiki Supendi
        $userGuru = User::create([
            'name' => 'Kiki Supendi',
            'username' => $nip,
            'email' => "{$nip}@smkn1ciamis.sch.id",
            'password' => bcrypt($nip),
            'role' => 'pembimbing_sekolah',
            'force_password_change' => true,
        ]);
        PembimbingSekolah::create([
            'user_id' => $userGuru->id,
            'nama_lengkap' => 'Kiki Supendi',
            'nip' => $nip,
            'tipe' => 'kejuruan',
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
        ]);

        $response = $this->actingAs($this->pokja)->post(route('pokja.custom_report.export'), [
            'module' => 'kredensial_guru',
            'columns' => ['nama_guru', 'nip', 'username', 'password_default', 'status_password', 'email', 'tipe_guru', 'jumlah_bimbingan'],
            'format' => 'xlsx',
            'include_pivot' => '1',
        ]);

        $response->assertOk();
        $this->assertStringContainsString('data-akun-pembimbing-sekolah', $response->headers->get('Content-Disposition'));

        // Inspect content to confirm NIP is used in credentials
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $tmpFile = tempnam(sys_get_temp_dir(), 'test_excel_');
        file_put_contents($tmpFile, $content);
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmpFile);
        $sheet = $spreadsheet->getActiveSheet();

        // Row 2 is Kiki Supendi
        $exportedNip = $sheet->getCell('C2')->getValue();
        $exportedUsername = $sheet->getCell('D2')->getValue();
        $exportedPasswordDefault = $sheet->getCell('E2')->getValue();
        $exportedStatus = $sheet->getCell('F2')->getValue();

        $this->assertEquals($nip, $exportedNip);
        $this->assertEquals($nip, $exportedUsername);
        $this->assertEquals($nip, $exportedPasswordDefault);
        $this->assertEquals('Default (NIP Guru)', $exportedStatus);

        @unlink($tmpFile);
    }

    public function test_export_csv_format(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.custom_report.export'), [
            'module' => 'penempatan_pkl',
            'columns' => ['nama_siswa', 'nis', 'kelas', 'nama_dudi', 'nama_pembimbing'],
            'format' => 'csv',
        ]);

        $response->assertOk();
        $this->assertStringContainsString('text/csv', strtolower($response->headers->get('Content-Type')));
        $this->assertStringContainsString('rekap-penempatan-pkl', $response->headers->get('Content-Disposition'));
    }
}
