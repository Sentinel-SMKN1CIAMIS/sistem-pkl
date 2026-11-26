<?php

namespace Tests\Feature;

use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingDudi;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationAndPdfExportTest extends TestCase
{
    use RefreshDatabase;

    private function seedBaseContext(): array
    {
        $program = ProgramKeahlian::create([
            'kode' => 'KLN',
            'nama' => 'Kuliner',
        ]);

        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'KLN',
            'nama' => 'Kuliner',
        ]);

        $dudi = Dudi::create([
            'nama' => 'Hotel Santika Tasikmalaya',
            'alamat' => 'Jl. Yudanegara No. 57',
            'kota' => 'Tasikmalaya',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'is_active' => true,
        ]);

        $kaprog = User::factory()->create([
            'name' => 'Mahani Gunawan',
            'role' => 'kaprog',
            'program_keahlian_id' => $program->id,
        ]);

        $pokja = User::factory()->create([
            'name' => 'Admin Pokja',
            'role' => 'super_admin',
        ]);

        $guruUser = User::factory()->create([
            'name' => 'Guru Pembimbing',
            'role' => 'pembimbing_sekolah',
        ]);
        $guru = PembimbingSekolah::create([
            'user_id' => $guruUser->id,
            'nama_lengkap' => 'Guru Pembimbing',
            'nip' => '198001012010011001',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'tipe' => 'kejuruan',
        ]);

        $dudiMentorUser = User::factory()->create([
            'name' => 'Mentor DUDI',
            'role' => 'pembimbing_dudi',
        ]);
        $dudiMentor = PembimbingDudi::create([
            'user_id' => $dudiMentorUser->id,
            'dudi_id' => $dudi->id,
            'nama_lengkap' => 'Mentor DUDI',
        ]);

        return compact('program', 'konsentrasi', 'dudi', 'kaprog', 'pokja', 'guruUser', 'guru', 'dudiMentorUser', 'dudiMentor');
    }

    public function test_kaprog_rekap_kehadiran_paginates_and_exports_pdf(): void
    {
        $ctx = $this->seedBaseContext();

        for ($i = 1; $i <= 18; $i++) {
            $u = User::factory()->create(['role' => 'siswa']);
            Siswa::create([
                'user_id' => $u->id,
                'nis' => '242510' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'nama_lengkap' => 'Siswa Kuliner ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'jenis_kelamin' => 'L',
                'tahun_ajaran' => '2026/2027',
                'kelas' => '12KLN1',
                'konsentrasi_keahlian_id' => $ctx['konsentrasi']->id,
                'dudi_id' => $ctx['dudi']->id,
                'pembimbing_sekolah_id' => $ctx['guru']->id,
                'pembimbing_dudi_id' => $ctx['dudiMentor']->id,
                'status_pkl' => 'sedang_pkl',
            ]);
        }

        // Page 1 should have 15 rows, page 2 should have 3 rows
        $res1 = $this->actingAs($ctx['kaprog'])->get(route('kaprog.rekap_kehadiran.index'));
        $res1->assertOk();
        $res1->assertSee('Menampilkan');
        $res1->assertSee('Siswa Kuliner 01');
        $res1->assertDontSee('Siswa Kuliner 18');

        $res2 = $this->actingAs($ctx['kaprog'])->get(route('kaprog.rekap_kehadiran.index', ['page' => 2]));
        $res2->assertOk();
        $res2->assertSee('Siswa Kuliner 18');

        // PDF Export
        $pdfRes = $this->actingAs($ctx['kaprog'])->get(route('kaprog.rekap_kehadiran.export_pdf'));
        $pdfRes->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $pdfRes->headers->get('content-type'));
    }

    public function test_all_other_rekap_pdf_exports_return_valid_pdf(): void
    {
        $ctx = $this->seedBaseContext();

        $this->actingAs($ctx['kaprog'])
            ->get(route('kaprog.monitoring_siswa.export_pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($ctx['kaprog'])
            ->get(route('kaprog.monitoring.export_pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($ctx['pokja'])
            ->get(route('pokja.monitoring.export_pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($ctx['pokja'])
            ->get(route('pokja.kunjungan.export_pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($ctx['pokja'])
            ->get(route('pokja.evaluasi.export_pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($ctx['guruUser'])
            ->get(route('pembimbing_sekolah.kunjungan.export_pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($ctx['dudiMentorUser'])
            ->get(route('pembimbing_dudi.absensi.export_pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_unpaginated_list_pages_now_render_pagination_cleanly(): void
    {
        $ctx = $this->seedBaseContext();

        $this->actingAs($ctx['kaprog'])->get(route('kaprog.monitoring.index'))->assertOk();
        $this->actingAs($ctx['pokja'])->get(route('pokja.monitoring.index'))->assertOk();
        $this->actingAs($ctx['dudiMentorUser'])->get(route('pembimbing_dudi.siswa.index'))->assertOk();
        $this->actingAs($ctx['pokja'])->get(route('pokja.hari_libur.index'))->assertOk();
    }
}
