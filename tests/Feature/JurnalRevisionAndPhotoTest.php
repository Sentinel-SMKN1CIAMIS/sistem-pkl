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

class JurnalRevisionAndPhotoTest extends TestCase
{
    use RefreshDatabase;

    private User $siswaUser;
    private Siswa $siswa;
    private User $guruUser;
    private PembimbingSekolah $guru;
    private Kompetensi $kompetensi;

    protected function setUp(): void
    {
        parent::setUp();

        $program = ProgramKeahlian::create([
            'kode' => 'RPL',
            'nama' => 'Pengembangan Perangkat Lunak',
        ]);

        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        $dudi = Dudi::create([
            'nama' => 'PT Digital Ciamis',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Merdeka No. 1',
            'is_active' => true,
        ]);

        $this->kompetensi = Kompetensi::create([
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nama' => 'Penerapan Hard Skills',
            'tp' => '1. Mengimplementasikan User Interface',
            'kategori' => 'teknis',
            'cp' => 'Mampu menerapkan kompetensi teknis sesuai POS.',
            'deskripsi' => 'UI implementation',
        ]);

        $this->guruUser = User::create([
            'name' => 'Guru Pembimbing',
            'username' => '198001012005011055',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->guru = PembimbingSekolah::create([
            'user_id' => $this->guruUser->id,
            'nama_lengkap' => 'Guru Pembimbing',
        ]);

        $this->siswaUser = User::create([
            'name' => 'Siswa Revisi',
            'username' => '242510777',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $this->siswaUser->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $this->guru->id,
            'nis' => '242510777',
            'nama_lengkap' => 'Siswa Revisi',
            'kelas' => 'XII RPL 1',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567877',
            'status_pkl' => 'sedang_pkl',
            'reminder_settings_configured' => true,
        ]);
    }

    public function test_student_can_revise_journal_rejected_by_pembimbing_sekolah_and_resets_to_pending(): void
    {
        $today = Carbon::today()->toDateString();

        Absensi::create([
            'siswa_id' => $this->siswa->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
        ]);

        $jurnal = Jurnal::create([
            'siswa_id' => $this->siswa->id,
            'kompetensi_id' => $this->kompetensi->id,
            'cp_id' => $this->kompetensi->id,
            'cp' => $this->kompetensi->cp,
            'tanggal' => $today,
            'deskripsi_pekerjaan' => 'Kerja singkat',
            'catatan' => 'Kurang detail',
            'status' => 'invalid',
            'approval_status' => 'rejected',
            'approved_by' => $this->guruUser->id,
            'approved_at' => now(),
            'approval_notes' => 'Tolong jelaskan lebih detail langkah kerjanya.',
        ]);

        // Calendar page should display Revisi Jurnal button
        $indexRes = $this->actingAs($this->siswaUser)->get(route('siswa.jurnal.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Revisi Jurnal', false);

        // Edit page should open and show rejection note
        $editRes = $this->actingAs($this->siswaUser)->get(route('siswa.jurnal.edit', $jurnal));
        $editRes->assertStatus(200);
        $editRes->assertSee('Jurnal Ditolak', false);
        $editRes->assertSee('Tolong jelaskan lebih detail langkah kerjanya.', false);

        // Submitting revision resets status & approval_status to pending
        $updateRes = $this->actingAs($this->siswaUser)->put(route('siswa.jurnal.update', $jurnal), [
            'kompetensi_id' => $this->kompetensi->id,
            'cp_id' => $this->kompetensi->id,
            'cp' => $this->kompetensi->cp,
            'tanggal' => $today,
            'kegiatan' => 'Implementasi halaman login dan validasi form',
            'catatan' => 'Sudah diperbaiki dengan penjelasan detail langkah kerja dan kendala yang dihadapi.',
        ]);

        $updateRes->assertRedirect(route('siswa.jurnal.index'));
        $updateRes->assertSessionHas('success');

        $jurnal->refresh();
        $this->assertEquals('pending', $jurnal->status);
        $this->assertEquals('pending', $jurnal->approval_status);
        $this->assertNull($jurnal->approved_by);
        $this->assertNull($jurnal->approved_at);
        $this->assertEquals('Implementasi halaman login dan validasi form', $jurnal->deskripsi_pekerjaan);

        // Pembimbing sekolah receives revision notification
        $this->assertDatabaseHas('notifikasis', [
            'to_user_id' => $this->guruUser->id,
            'judul' => 'Revisi Jurnal dari Siswa Revisi',
        ]);

        // Pembimbing sekolah can approve the revised journal
        $approveRes = $this->actingAs($this->guruUser)->post(route('pembimbing_sekolah.jurnal.approve', $jurnal), [
            'approval_notes' => 'Bagus, sudah lengkap.',
        ]);
        $approveRes->assertRedirect();
        $jurnal->refresh();
        $this->assertEquals('approved', $jurnal->approval_status);
        $this->assertEquals('valid', $jurnal->status);
    }

    public function test_student_can_revise_journal_rejected_by_pembimbing_dudi(): void
    {
        $today = Carbon::today()->toDateString();

        Absensi::create([
            'siswa_id' => $this->siswa->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
        ]);

        $jurnal = Jurnal::create([
            'siswa_id' => $this->siswa->id,
            'kompetensi_id' => $this->kompetensi->id,
            'cp_id' => $this->kompetensi->id,
            'tanggal' => $today,
            'deskripsi_pekerjaan' => 'Isian awal',
            'catatan' => 'Singkat',
            'status' => 'invalid',
            'approval_status' => 'pending',
            'catatan_pembimbing' => 'Lengkapi foto dan uraian kerja.',
        ]);

        $this->assertTrue($jurnal->isRejected());
        $this->assertTrue($jurnal->isEditableByStudent());

        $editRes = $this->actingAs($this->siswaUser)->get(route('siswa.jurnal.edit', $jurnal));
        $editRes->assertStatus(200);
        $editRes->assertSee('Lengkapi foto dan uraian kerja.', false);

        $updateRes = $this->actingAs($this->siswaUser)->put(route('siswa.jurnal.update', $jurnal), [
            'kompetensi_id' => $this->kompetensi->id,
            'cp_id' => $this->kompetensi->id,
            'tanggal' => $today,
            'kegiatan' => 'Perbaikan laporan revisi DUDI',
            'catatan' => 'Sudah dilengkapi uraian pekerjaan.',
        ]);

        $updateRes->assertRedirect(route('siswa.jurnal.index'));
        $jurnal->refresh();
        $this->assertEquals('pending', $jurnal->status);
        $this->assertEquals('pending', $jurnal->approval_status);
    }

    public function test_student_can_revise_old_rejected_journal_beyond_backdate_window_on_same_date(): void
    {
        $oldDate = Carbon::today()->subDays(12)->toDateString();

        Absensi::create([
            'siswa_id' => $this->siswa->id,
            'tanggal' => $oldDate,
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
        ]);

        $jurnal = Jurnal::create([
            'siswa_id' => $this->siswa->id,
            'kompetensi_id' => $this->kompetensi->id,
            'cp_id' => $this->kompetensi->id,
            'tanggal' => $oldDate,
            'deskripsi_pekerjaan' => 'Jurnal lama ditolak',
            'catatan' => 'Perlu revisi',
            'status' => 'invalid',
            'approval_status' => 'rejected',
            'approval_notes' => 'Perbaiki catatan.',
        ]);

        // Revising on the same original date succeeds even though > 7 days old
        $updateRes = $this->actingAs($this->siswaUser)->put(route('siswa.jurnal.update', $jurnal), [
            'kompetensi_id' => $this->kompetensi->id,
            'cp_id' => $this->kompetensi->id,
            'tanggal' => $oldDate,
            'kegiatan' => 'Revisi jurnal 12 hari lalu',
            'catatan' => 'Sudah diperbaiki dengan lengkap.',
        ]);

        $updateRes->assertRedirect(route('siswa.jurnal.index'));
        $jurnal->refresh();
        $this->assertEquals('pending', $jurnal->status);
        $this->assertEquals('Revisi jurnal 12 hari lalu', $jurnal->deskripsi_pekerjaan);
    }

    public function test_student_cannot_edit_approved_journal(): void
    {
        $today = Carbon::today()->toDateString();

        $jurnal = Jurnal::create([
            'siswa_id' => $this->siswa->id,
            'kompetensi_id' => $this->kompetensi->id,
            'cp_id' => $this->kompetensi->id,
            'tanggal' => $today,
            'deskripsi_pekerjaan' => 'Sudah ACC',
            'catatan' => 'Valid',
            'status' => 'valid',
            'approval_status' => 'approved',
        ]);

        $this->assertFalse($jurnal->isEditableByStudent());

        $editRes = $this->actingAs($this->siswaUser)->get(route('siswa.jurnal.edit', $jurnal));
        $editRes->assertRedirect(route('siswa.jurnal.index'));
        $editRes->assertSessionHas('error');

        $updateRes = $this->actingAs($this->siswaUser)->put(route('siswa.jurnal.update', $jurnal), [
            'kompetensi_id' => $this->kompetensi->id,
            'tanggal' => $today,
            'kegiatan' => 'Coba ubah yang sudah ACC',
        ]);
        $updateRes->assertRedirect(route('siswa.jurnal.index'));
        $updateRes->assertSessionHas('error');

        $jurnal->refresh();
        $this->assertEquals('Sudah ACC', $jurnal->deskripsi_pekerjaan);
    }

    public function test_create_and_edit_views_allow_15mb_raw_photo_before_compression(): void
    {
        $createRes = $this->actingAs($this->siswaUser)->get(route('siswa.jurnal.create'));
        $createRes->assertStatus(200);
        $createRes->assertSee('15 * 1024 * 1024', false);
        $createRes->assertSee('URL.createObjectURL(file)', false);
        $createRes->assertDontSee('2 * 1024 * 1024', false);

        $jurnal = Jurnal::create([
            'siswa_id' => $this->siswa->id,
            'kompetensi_id' => $this->kompetensi->id,
            'cp_id' => $this->kompetensi->id,
            'tanggal' => Carbon::today()->toDateString(),
            'deskripsi_pekerjaan' => 'Pending',
            'status' => 'pending',
        ]);

        $editRes = $this->actingAs($this->siswaUser)->get(route('siswa.jurnal.edit', $jurnal));
        $editRes->assertStatus(200);
        $editRes->assertSee('15 * 1024 * 1024', false);
        $editRes->assertSee('URL.createObjectURL(file)', false);
        $editRes->assertDontSee('2 * 1024 * 1024', false);
    }
}
