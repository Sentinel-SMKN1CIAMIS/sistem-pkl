<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsensiLiburStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $siswaUser;
    private Siswa $siswa;
    private User $guruUser;
    private PembimbingSekolah $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $program = ProgramKeahlian::create([
            'kode' => 'MPLB',
            'nama' => 'Manajemen Perkantoran',
        ]);

        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'MPLB',
            'nama' => 'Manajemen Perkantoran',
            'durasi_pkl_bulan' => 4,
        ]);

        $dudi = Dudi::create([
            'nama' => 'Setda Kabupaten Ciamis',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Jend. Sudirman',
            'is_active' => true,
        ]);

        $this->guruUser = User::create([
            'name' => 'Pembimbing Sekolah',
            'username' => '198001012005011001',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);

        $this->guru = PembimbingSekolah::create([
            'user_id' => $this->guruUser->id,
            'nama_lengkap' => 'Pembimbing Sekolah',
            'nip' => '198001012005011001',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'tipe' => 'kejuruan',
        ]);

        $this->siswaUser = User::create([
            'name' => 'Siswa Setda',
            'username' => '2425999',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $this->siswaUser->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $this->guru->id,
            'nis' => '2425999',
            'nama_lengkap' => 'Siswa Setda',
            'kelas' => 'XII MPLB 1',
            'jenis_kelamin' => 'P',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);
    }

    public function test_student_requires_alasan_when_submitting_libur_status(): void
    {
        $this->actingAs($this->siswaUser)
            ->post(route('siswa.absensi.submit-absence-request'), [
                'status' => 'libur',
                'alasan' => '',
            ])
            ->assertSessionHasErrors('alasan');

        $this->actingAs($this->siswaUser)
            ->post(route('siswa.absensi.submit-absence-request'), [
                'status' => 'libur',
                'alasan' => 'Libur', // less than 10 chars
            ])
            ->assertSessionHasErrors('alasan');

        $this->assertDatabaseCount('absensis', 0);
    }

    public function test_student_can_submit_libur_with_reason_and_teacher_can_approve_it(): void
    {
        $this->actingAs($this->siswaUser)
            ->post(route('siswa.absensi.submit-absence-request'), [
                'status' => 'libur',
                'alasan' => 'Hari libur rutin DUDI Setda Kabupaten Ciamis pada hari Minggu.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $this->siswa->id,
            'status' => 'libur',
            'alasan' => 'Hari libur rutin DUDI Setda Kabupaten Ciamis pada hari Minggu.',
            'approval_status' => 'pending',
        ]);

        $absensi = Absensi::where('siswa_id', $this->siswa->id)->first();

        // Teacher sees libur request in approval index & can approve
        $this->actingAs($this->guruUser)
            ->get(route('pembimbing_sekolah.absensi.approval.index'))
            ->assertOk()
            ->assertSee('Siswa Setda');

        $this->actingAs($this->guruUser)
            ->patch(route('pembimbing_sekolah.absensi.approve', $absensi))
            ->assertRedirect()
            ->assertSessionHas('success');

        $absensi->refresh();
        $this->assertEquals('approved', $absensi->approval_status);
    }

    public function test_teacher_can_store_manual_libur_and_reminders_skip_libur_students(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 18, 8, 0, 0, 'Asia/Jakarta'));

        $this->actingAs($this->guruUser)
            ->post(route('pembimbing_sekolah.absensi.store-manual'), [
                'siswa_id' => $this->siswa->id,
                'tanggal' => '2026-11-18',
                'status' => 'libur',
                'keterangan' => 'Libur instansi pemerintah',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $this->siswa->id,
            'tanggal' => '2026-11-18',
            'status' => 'libur',
            'keterangan' => 'Libur instansi pemerintah',
        ]);

        // Morning reminder must skip students who submitted libur today
        $this->artisan('pkl:send-reminders')->assertExitCode(0);
        $this->assertDatabaseCount('notifikasis', 0);

        $kompetensi = Kompetensi::create([
            'konsentrasi_keahlian_id' => $this->siswa->konsentrasi_keahlian_id,
            'kode' => 'KP-01',
            'nama' => 'Administrasi Perkantoran',
        ]);

        // Journal creation blocked on libur day
        $this->actingAs($this->siswaUser)
            ->post(route('siswa.jurnal.store'), [
                'tanggal' => '2026-11-18',
                'kompetensi_id' => $kompetensi->id,
                'kegiatan' => 'Mengisi kegiatan padahal libur',
            ])
            ->assertSessionHas('error');

        Carbon::setTestNow();
    }
}
