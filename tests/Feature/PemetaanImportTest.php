<?php

namespace Tests\Feature;

use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingDudi;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use App\Services\PemetaanImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemetaanImportTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private ProgramKeahlian $progPplg;
    private ProgramKeahlian $progAkl;
    private KonsentrasiKeahlian $konPplg;
    private KonsentrasiKeahlian $konAkl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->progPplg = ProgramKeahlian::create(['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak dan Gim']);
        $this->konPplg = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->progPplg->id,
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
            'durasi_pkl_bulan' => 6,
        ]);

        $this->progAkl = ProgramKeahlian::create(['kode' => 'AKL', 'nama' => 'Akuntansi dan Keuangan Lembaga']);
        $this->konAkl = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $this->progAkl->id,
            'kode' => 'AKL',
            'nama' => 'Akuntansi',
            'durasi_pkl_bulan' => 6,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => false,
        ]);
    }

    public function test_teacher_disambiguation_logic_word1_and_word2(): void
    {
        $service = new PemetaanImportService();

        // 1. Two teachers sharing word 1 "Yana"
        $this->assertEquals('Yana Hendrayana', $service->resolveCanonicalTeacher('Yana Hendrayana'));
        $this->assertEquals('Yana Soviyana', $service->resolveCanonicalTeacher('32.Yana Soviyana'));
        $this->assertEquals('Yana Soviyana', $service->resolveCanonicalTeacher('Yana Soviyana'));
        // Initial H matches Hendrayana
        $this->assertEquals('Yana Hendrayana', $service->resolveCanonicalTeacher('Yana H'));

        // 2. Unique first names matching full names
        $this->assertEquals('Kiki Supendi', $service->resolveCanonicalTeacher('1.Kiki Supendi '));
        $this->assertEquals('Kiki Supendi', $service->resolveCanonicalTeacher('Kiki S.'));
        $this->assertEquals('Kiki Supendi', $service->resolveCanonicalTeacher('Kiki'));
        $this->assertEquals('Angga Febrian Pratama', $service->resolveCanonicalTeacher('3. Angga Febrian Pratama'));
        $this->assertEquals('Angga Febrian Pratama', $service->resolveCanonicalTeacher('Angga'));
        $this->assertEquals('Tita Puspita', $service->resolveCanonicalTeacher('4. Tita Puspita'));
        $this->assertEquals('Tita Puspita', $service->resolveCanonicalTeacher('Tita'));
        $this->assertEquals('Dhinda Aghita Mahardika', $service->resolveCanonicalTeacher('12. Dhinda'));
        $this->assertEquals('Dhinda Aghita Mahardika', $service->resolveCanonicalTeacher('Dhinda'));
        $this->assertEquals('Bepi Apriansyah', $service->resolveCanonicalTeacher('2. Bepi, S.Pd.'));
        $this->assertEquals('Bepi Apriansyah', $service->resolveCanonicalTeacher('Bepi Apriansyah'));
        $this->assertEquals('Dini Yudi Kasimamora', $service->resolveCanonicalTeacher('21. Dini Yudi K, S.Tr.Par.'));
        $this->assertEquals('Dini Yudi Kasimamora', $service->resolveCanonicalTeacher('Dini Yudikasimamora'));
    }

    public function test_import_service_maps_students_and_creates_pembimbing_and_dudi(): void
    {
        // Setup Siswa in DB
        $userSiswa1 = User::create([
            'name' => 'Fahreza Akbar Maulana',
            'username' => '242510001',
            'email' => 'siswa1@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $siswa1 = Siswa::create([
            'user_id' => $userSiswa1->id,
            'konsentrasi_keahlian_id' => $this->konPplg->id,
            'nis' => '242510001',
            'nama_lengkap' => 'Fahreza Akbar Maulana',
            'kelas' => 'XII PPLG',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'belum_mulai',
        ]);

        $userSiswa2 = User::create([
            'name' => 'Rio Octavian Pratama',
            'username' => '242510002',
            'email' => 'siswa2@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $siswa2 = Siswa::create([
            'user_id' => $userSiswa2->id,
            'konsentrasi_keahlian_id' => $this->konAkl->id,
            'nis' => '242510002',
            'nama_lengkap' => 'Rio Octavian Pratama',
            'kelas' => 'XII AKL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'belum_mulai',
        ]);

        $csvData = <<<CSV
No,Wilayah,Nama Pembimbing ,Nama DU/DI Tempat PKL,NAMA SISWA,KELAS,JUMLAH PERMINTAAN DUDI
1,Yogyakarta,1.Kiki Supendi ,1. Visitaga,Fahreza Akbar Maulana,PPLG,2
2,Ciamis,Yana Hendrayana,56. Dinas Pengelolaan Keuangan Daerah Ciamis,Rio Octavian Pratama,AKL 1,2
3,Bandung,Tita Puspita,DKV Studio,Siswa DKV,DKV 1,1
CSV;

        $service = new PemetaanImportService();

        // Parse with default filter (DKV excluded)
        $parsed = $service->parseCsv($csvData, ['AKL', 'PPLG']);
        $this->assertCount(2, $parsed['items']);
        $this->assertEquals(1, $parsed['skipped_count']); // DKV skipped

        $stats = $service->executeImport($parsed['items'], ['default_password' => 'smkn1ciamis']);

        $this->assertEquals(2, $stats['pembimbing_created']);
        $this->assertEquals(2, $stats['dudi_created']);
        $this->assertEquals(2, $stats['siswa_mapped']);

        // Check teacher accounts
        $this->assertDatabaseHas('users', [
            'name' => 'Kiki Supendi',
            'role' => 'pembimbing_sekolah',
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Yana Hendrayana',
            'role' => 'pembimbing_sekolah',
        ]);

        // Ensure DUDI account was NOT created
        $this->assertDatabaseMissing('users', [
            'role' => 'pembimbing_dudi',
        ]);

        // Check DUDI master created
        $this->assertDatabaseHas('dudis', [
            'nama' => 'Visitaga',
            'kota' => 'Yogyakarta',
        ]);

        // Check student 1 updated
        $siswa1->refresh();
        $this->assertNotNull($siswa1->dudi_id);
        $this->assertEquals('Visitaga', $siswa1->dudi->nama);
        $this->assertEquals('sedang_pkl', $siswa1->status_pkl);
        $this->assertEquals('Kiki Supendi', $siswa1->pembimbingSekolah->nama_lengkap);

        // Check student 2 updated
        $siswa2->refresh();
        $this->assertNotNull($siswa2->dudi_id);
        $this->assertEquals('Dinas Pengelolaan Keuangan Daerah Ciamis', $siswa2->dudi->nama);
        $this->assertEquals('sedang_pkl', $siswa2->status_pkl);
        $this->assertEquals('Yana Hendrayana', $siswa2->pembimbingSekolah->nama_lengkap);
    }

    public function test_pembimbing_sekolah_can_create_pembimbing_dudi_account_for_assigned_dudi(): void
    {
        $userGuru = User::create([
            'name' => 'Kiki Supendi',
            'username' => 'kiki.supendi',
            'email' => 'kiki@test.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);
        $pembimbingSekolah = PembimbingSekolah::create([
            'user_id' => $userGuru->id,
            'nama_lengkap' => 'Kiki Supendi',
            'tipe' => 'kejuruan',
        ]);

        $dudi = Dudi::create([
            'nama' => 'Visitaga',
            'konsentrasi_keahlian_id' => $this->konPplg->id,
            'kota' => 'Yogyakarta',
            'alamat' => 'Yogyakarta',
            'is_active' => true,
        ]);

        $userSiswa = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa.test',
            'email' => 'siswatest@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);
        $siswa = Siswa::create([
            'user_id' => $userSiswa->id,
            'konsentrasi_keahlian_id' => $this->konPplg->id,
            'nis' => '999999',
            'nama_lengkap' => 'Siswa Test',
            'kelas' => 'XII PPLG',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $pembimbingSekolah->id,
            'status_pkl' => 'sedang_pkl',
        ]);

        // Guru Pembimbing Sekolah creates Pembimbing DUDI
        $response = $this->actingAs($userGuru)
            ->post(route('pembimbing_sekolah.dudi_mentor.store'), [
                'dudi_id' => $dudi->id,
                'nama_lengkap' => 'Bpk. Ahmad Visitaga',
                'email' => 'mentor@visitaga.com',
                'username' => 'ahmad.visitaga',
                'password' => 'visitaga123',
                'jabatan' => 'Senior Developer',
                'no_hp' => '081234567890',
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'username' => 'ahmad.visitaga',
            'email' => 'mentor@visitaga.com',
            'role' => 'pembimbing_dudi',
        ]);

        $this->assertDatabaseHas('pembimbing_dudis', [
            'dudi_id' => $dudi->id,
            'nama_lengkap' => 'Bpk. Ahmad Visitaga',
            'jabatan' => 'Senior Developer',
        ]);

        // Student automatically linked to new DUDI mentor
        $siswa->refresh();
        $this->assertNotNull($siswa->pembimbing_dudi_id);
        $this->assertEquals('Bpk. Ahmad Visitaga', $siswa->pembimbingDudi->nama_lengkap);
    }

    public function test_config_controller_import_pemetaan_endpoint_via_ajax(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('admin.config.import-pemetaan'), [
                'jurusan_filter' => ['PPLG', 'AKL'],
                'batch_size' => 10,
                'offset' => 0,
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
            ]);
    }

    public function test_import_with_uploaded_xlsx_and_cache_chunking(): void
    {
        $xlsxPath = 'C:/Users/Thinkpad/.gemini/antigravity/brain/bc78d3c0-e257-4b23-8737-ab9002b08a4a/.user_uploaded/media_1794656150685_89ee6ae8.xlsx';
        if (!file_exists($xlsxPath)) {
            $this->markTestSkipped('Fixture xlsx file not found.');
        }

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $xlsxPath,
            'Pemetaan.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        // Batch 1 (Offset 0)
        $response1 = $this->actingAs($this->superAdmin)
            ->post(route('admin.config.import-pemetaan'), [
                'file' => $uploadedFile,
                'jurusan_filter' => ['PPLG', 'AKL', 'KLN'],
                'batch_size' => 20,
                'offset' => 0,
            ], ['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest']);

        $response1->assertOk()
            ->assertJsonStructure([
                'status',
                'cache_id',
                'total',
                'offset',
                'stats',
            ]);

        $data1 = $response1->json();
        $this->assertEquals('success', $data1['status']);
        $this->assertNotEmpty($data1['cache_id']);
        $this->assertGreaterThan(0, $data1['total']);
        $this->assertEquals(20, $data1['offset']);

        // Batch 2 (Using cache_id without re-uploading file)
        $response2 = $this->actingAs($this->superAdmin)
            ->post(route('admin.config.import-pemetaan'), [
                'cache_id' => $data1['cache_id'],
                'jurusan_filter' => ['PPLG', 'AKL', 'KLN'],
                'batch_size' => 20,
                'offset' => 20,
            ], ['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest']);

        $response2->assertOk();
        $data2 = $response2->json();
        $this->assertEquals('success', $data2['status']);
        $this->assertEquals(40, $data2['offset']);
    }
}
