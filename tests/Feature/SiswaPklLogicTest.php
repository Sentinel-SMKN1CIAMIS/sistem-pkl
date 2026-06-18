<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\PengajuanPkl;
use App\Models\PembimbingSekolah;
use App\Models\Notifikasi;
use App\Models\PokjaGroup;
use App\Models\Dudi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiswaPklLogicTest extends TestCase
{
    use RefreshDatabase;

    private $siswa;
    private $pokja;
    private $pembimbing;
    private $konsentrasi;
    private $dudi;

    protected function setUp(): void
    {
        parent::setUp();

        // Create program and concentration
        $prog = ProgramKeahlian::create(['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak']);
        $this->konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $prog->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        // Create a DUDI record
        $this->dudi = Dudi::create([
            'nama' => 'PT Test DUDI',
            'alamat' => 'Alamat DUDI',
            'kota' => 'Kota DUDI',
            'is_active' => true,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
        ]);

        // Create Pokja
        $this->pokja = User::create([
            'name' => 'Pokja User',
            'username' => 'pokja1',
            'email' => 'pokja@test.com',
            'password' => bcrypt('password'),
            'role' => 'pokja',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        // Create Pokja Group and attach Pokja User
        $group = PokjaGroup::create([
            'name' => 'Grup Pokja Utama',
            'is_active' => true,
        ]);
        $group->users()->attach($this->pokja->id);

        // Create Pembimbing Sekolah
        $pembimbingUser = User::create([
            'name' => 'Guru Pembimbing',
            'username' => 'guru1',
            'email' => 'guru@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->pembimbing = PembimbingSekolah::create([
            'user_id' => $pembimbingUser->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nip' => '12345678',
            'nama_lengkap' => 'Guru Pembimbing',
            'tipe' => 'umum',
            'kapasitas' => 10,
        ]);

        // Create Student
        $studentUser = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa1',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'konsentrasi_keahlian_id' => $this->konsentrasi->id,
            'nis' => 'siswa1',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'belum_mulai',
        ]);
    }

    /**
     * Test mapping notifications.
     */
    public function test_notifications_sent_on_pembimbing_assignment()
    {
        $this->actingAs($this->pokja);

        $response = $this->put(route('pokja.siswa.update', $this->siswa->id), [
            'nis' => $this->siswa->nis,
            'nama_lengkap' => $this->siswa->nama_lengkap,
            'konsentrasi_keahlian_id' => $this->siswa->konsentrasi_keahlian_id,
            'kelas' => $this->siswa->kelas,
            'jenis_kelamin' => $this->siswa->jenis_kelamin,
            'tahun_ajaran' => $this->siswa->tahun_ajaran,
            'pembimbing_sekolah_id' => $this->pembimbing->id,
        ]);

        $response->assertRedirect(route('pokja.siswa.index'));
        $this->siswa = $this->siswa->fresh();

        $this->assertEquals($this->pembimbing->id, $this->siswa->pembimbing_sekolah_id);

        // Assert teacher notification is sent
        $this->assertDatabaseHas('notifikasis', [
            'to_user_id' => $this->pembimbing->user_id,
            'judul' => 'Penugasan Bimbingan Baru',
        ]);

        // Assert student notification is sent
        $this->assertDatabaseHas('notifikasis', [
            'to_user_id' => $this->siswa->user_id,
            'judul' => 'Pembimbing Sekolah Ditugaskan',
        ]);
    }

    /**
     * Test PKL cancellation logic.
     */
    public function test_pkl_cancellation_logic()
    {
        Storage::fake('public');

        // Setup student with existing penempatan and approved proposal
        $pengajuan = PengajuanPkl::create([
            'siswa_id' => $this->siswa->id,
            'dudi_id' => $this->dudi->id,
            'nama_perusahaan' => $this->dudi->nama,
            'pimpinan' => 'Pimpinan Test',
            'alamat' => 'Alamat Test',
            'kota' => 'Kota Test',
            'status' => 'disetujui',
            'bukti_balasan' => 'bukti_balasan/test_bukti.pdf',
        ]);

        // Put fake file on disk
        Storage::disk('public')->put('bukti_balasan/test_bukti.pdf', 'fake PDF content');

        $this->siswa->update([
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $this->pembimbing->id,
            'status_pkl' => 'sedang_pkl',
        ]);

        $this->actingAs($this->pokja);

        // Pokja cancels the PKL
        $response = $this->put(route('pokja.siswa.update', $this->siswa->id), [
            'nis' => $this->siswa->nis,
            'nama_lengkap' => $this->siswa->nama_lengkap,
            'konsentrasi_keahlian_id' => $this->siswa->konsentrasi_keahlian_id,
            'kelas' => $this->siswa->kelas,
            'jenis_kelamin' => $this->siswa->jenis_kelamin,
            'tahun_ajaran' => $this->siswa->tahun_ajaran,
            'status_pkl' => 'dibatalkan',
        ]);

        $response->assertRedirect(route('pokja.siswa.index'));
        $this->siswa = $this->siswa->fresh();
        $pengajuan = $pengajuan->fresh();

        // 1. Assert penempatan fields are cleared
        $this->assertNull($this->siswa->dudi_id);
        $this->assertNull($this->siswa->pembimbing_sekolah_id);
        $this->assertNull($this->siswa->pembimbing_dudi_id);
        $this->assertEquals('dibatalkan', $this->siswa->status_pkl);

        // 2. Assert proposal status is set to ditolak
        $this->assertEquals('ditolak', $pengajuan->status);
        $this->assertEquals('PKL dibatalkan oleh Pokja.', $pengajuan->catatan);

        // 3. Assert file is deleted from storage and database column is null
        $this->assertNull($pengajuan->bukti_balasan);
        $this->assertFalse(Storage::disk('public')->exists('bukti_balasan/test_bukti.pdf'));

        // 4. Assert notification of cancellation is sent to student
        $this->assertDatabaseHas('notifikasis', [
            'to_user_id' => $this->siswa->user_id,
            'judul' => 'PKL Dibatalkan oleh Pokja',
        ]);
    }

    /**
     * Test file cleanup when student re-applies.
     */
    public function test_file_cleanup_on_reapply()
    {
        Storage::fake('public');

        // Setup student with rejected proposal containing file
        $pengajuan = PengajuanPkl::create([
            'siswa_id' => $this->siswa->id,
            'nama_perusahaan' => 'PT Test',
            'pimpinan' => 'Pimpinan Test',
            'alamat' => 'Alamat Test',
            'kota' => 'Kota Test',
            'status' => 'ditolak',
            'bukti_balasan' => 'bukti_balasan/old_test_bukti.pdf',
        ]);

        Storage::disk('public')->put('bukti_balasan/old_test_bukti.pdf', 'fake PDF content');

        $this->actingAs($this->siswa->user);

        // Student submits a new application
        $response = $this->post(route('siswa.pengajuan_pkl.store'), [
            'nama_perusahaan' => 'PT Baru',
            'pimpinan' => 'Pimpinan Baru',
            'alamat' => 'Alamat Baru',
            'kota' => 'Kota Baru',
            'no_telp' => '08123456789',
        ]);

        $response->assertRedirect(route('siswa.pengajuan_pkl.status'));

        // Assert old file deleted from disk
        $this->assertFalse(Storage::disk('public')->exists('bukti_balasan/old_test_bukti.pdf'));

        // Assert new proposal is created
        $this->assertDatabaseHas('pengajuan_pkls', [
            'siswa_id' => $this->siswa->id,
            'nama_perusahaan' => 'PT Baru',
            'status' => 'menunggu',
        ]);
    }

    /**
     * Test Jurnal access control based on today's attendance.
     */
    public function test_student_cannot_access_create_journal_without_absen_today()
    {
        $this->siswa->update([
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $this->pembimbing->id,
            'status_pkl' => 'sedang_pkl',
        ]);

        $this->actingAs($this->siswa->user);

        // Access index page - check hasAbsenToday is false
        $response = $this->get(route('siswa.jurnal.index'));
        $response->assertStatus(200);
        $response->assertViewHas('hasAbsenToday', false);

        // Try to access create page directly
        $responseCreate = $this->get(route('siswa.jurnal.create'));
        $responseCreate->assertRedirect(route('siswa.jurnal.index'));
        $responseCreate->assertSessionHas('error', 'Anda belum melakukan absensi hari ini. Silakan melakukan absensi terlebih dahulu sebelum mengisi jurnal.');
    }

    public function test_student_can_access_create_journal_after_absen_today()
    {
        $this->siswa->update([
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $this->pembimbing->id,
            'status_pkl' => 'sedang_pkl',
        ]);

        // Create attendance record for today
        \App\Models\Absensi::create([
            'siswa_id' => $this->siswa->id,
            'tanggal' => \Carbon\Carbon::today()->toDateString(),
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
        ]);

        $this->actingAs($this->siswa->user);

        // Access index page - check hasAbsenToday is true
        $response = $this->get(route('siswa.jurnal.index'));
        $response->assertStatus(200);
        $response->assertViewHas('hasAbsenToday', true);

        // Access create page
        $responseCreate = $this->get(route('siswa.jurnal.create'));
        $responseCreate->assertStatus(200);
    }

    /**
     * Test Kaprog approval behavior on existing DUDI.
     */
    public function test_kaprog_approval_bypasses_pokja_validation_for_existing_dudi()
    {
        // 1. Create a Kaprog user
        $kaprogUser = User::create([
            'name' => 'Kaprog User',
            'username' => 'kaprog1',
            'email' => 'kaprog@test.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'is_active' => true,
            'force_password_change' => false,
            'program_keahlian_id' => $this->konsentrasi->program_keahlian_id,
        ]);

        // 2. Create proposal with existing dudi_id
        $pengajuan = PengajuanPkl::create([
            'siswa_id' => $this->siswa->id,
            'dudi_id' => $this->dudi->id,
            'nama_perusahaan' => $this->dudi->nama,
            'pimpinan' => 'Pimpinan Test',
            'alamat' => 'Alamat Test',
            'kota' => 'Kota Test',
            'status' => 'menunggu',
        ]);

        $this->actingAs($kaprogUser);

        // 3. Kaprog approves the proposal
        $response = $this->patch(route('kaprog.pengajuan_pkl.update', $pengajuan->id), [
            'status' => 'disetujui',
            'catatan' => 'Disetujui Kaprog langsung final.',
        ]);

        $response->assertRedirect();
        
        $pengajuan = $pengajuan->fresh();
        $this->siswa = $this->siswa->fresh();

        // 4. Assert status is 'disetujui' directly and dudi is assigned to siswa
        $this->assertEquals('disetujui', $pengajuan->status);
        $this->assertEquals($this->dudi->id, $this->siswa->dudi_id);
        $this->assertEquals('belum_mulai', $this->siswa->status_pkl);

        // 5. Assert Pokja notification for mapping is sent
        $this->assertDatabaseHas('notifikasis', [
            'to_user_id' => $this->pokja->id,
            'judul' => 'Penempatan Baru (Butuh Pemetaan)',
        ]);
    }

    /**
     * Test Kaprog approval behavior on new/manual DUDI.
     */
    public function test_kaprog_approval_requires_pokja_validation_for_new_dudi()
    {
        // 1. Create a Kaprog user
        $kaprogUser = User::create([
            'name' => 'Kaprog User 2',
            'username' => 'kaprog2',
            'email' => 'kaprog2@test.com',
            'password' => bcrypt('password'),
            'role' => 'kaprog',
            'is_active' => true,
            'force_password_change' => false,
            'program_keahlian_id' => $this->konsentrasi->program_keahlian_id,
        ]);

        // 2. Create proposal with manual DUDI (dudi_id = null)
        $pengajuan = PengajuanPkl::create([
            'siswa_id' => $this->siswa->id,
            'dudi_id' => null,
            'nama_perusahaan' => 'CV Baru Unregistered',
            'pimpinan' => 'Pimpinan Baru',
            'alamat' => 'Alamat Baru',
            'kota' => 'Kota Baru',
            'status' => 'menunggu',
        ]);

        $this->actingAs($kaprogUser);

        // 3. Kaprog approves
        $response = $this->patch(route('kaprog.pengajuan_pkl.update', $pengajuan->id), [
            'status' => 'disetujui',
            'catatan' => 'Kaprog setuju, butuh Pokja.',
        ]);

        $response->assertRedirect();
        
        $pengajuan = $pengajuan->fresh();
        $this->siswa = $this->siswa->fresh();

        // 4. Assert status is 'disetujui_kaprog' and dudi is NOT assigned to siswa yet
        $this->assertEquals('disetujui_kaprog', $pengajuan->status);
        $this->assertNull($this->siswa->dudi_id);

        // 5. Assert Pokja notification for validation is sent
        $this->assertDatabaseHas('notifikasis', [
            'to_user_id' => $this->pokja->id,
            'judul' => 'Pengajuan PKL Baru (Butuh Validasi)',
        ]);
    }

    public function test_new_dudi_submission_requires_phone_number()
    {
        $this->actingAs($this->siswa->user);

        // Submit without no_telp
        $response = $this->post(route('siswa.pengajuan_pkl.store'), [
            'nama_perusahaan' => 'PT Baru Tanpa Telepon',
            'pimpinan' => 'Pimpinan Baru',
            'alamat' => 'Alamat Baru',
            'kota' => 'Kota Baru',
            // no_telp is missing
        ]);

        $response->assertSessionHasErrors('no_telp');
    }
}
