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
    private User $pokja;
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

        $this->pokja = User::create([
            'name' => 'Petugas Pokja',
            'username' => 'pokja',
            'email' => 'pokja@test.com',
            'password' => bcrypt('password'),
            'role' => 'pokja',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $group = \App\Models\PokjaGroup::create([
            'name' => 'Grup Pokja',
            'is_active' => true,
        ]);
        $group->users()->attach($this->pokja->id);
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

    public function test_import_smart_overwrite_existing_teacher_credentials_without_affecting_dudi_mappings(): void
    {
        $service = new PemetaanImportService();

        // 1. Setup existing teacher with legacy credentials
        $legacyUser = User::create([
            'name' => 'Acep Muhammad Soleh',
            'username' => 'acep.legacy',
            'email' => 'acep.legacy@smkn1ciamis.sch.id',
            'password' => 'smkn1ciamis',
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => true,
        ]);

        $pembimbing = PembimbingSekolah::create([
            'user_id' => $legacyUser->id,
            'nama_lengkap' => 'Acep Muhammad Soleh',
            'nip' => null, // Initially null
            'tipe' => 'kejuruan',
            'konsentrasi_keahlian_id' => $this->konPplg->id,
        ]);

        // 2. Setup existing DUDI and assigned student
        $dudi = Dudi::create([
            'nama' => 'PT Teknologi Unggul Ciamis',
            'konsentrasi_keahlian_id' => $this->konPplg->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Jendral Sudirman',
            'is_active' => true,
        ]);

        $userSiswa = User::create([
            'name' => 'Budi Siswa',
            'username' => '242510999',
            'email' => 'budi@test.com',
            'password' => '242510999',
            'role' => 'siswa',
        ]);

        $siswa = Siswa::create([
            'user_id' => $userSiswa->id,
            'konsentrasi_keahlian_id' => $this->konPplg->id,
            'nis' => '242510999',
            'nama_lengkap' => 'Budi Siswa',
            'kelas' => 'XII PPLG 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $pembimbing->id,
        ]);

        // 3. Import new batch involving Acep Muhammad Soleh
        $items = [
            [
                'wilayah' => 'Ciamis',
                'pembimbing_raw' => 'Acep Muhammad Soleh, S.Pd.',
                'pembimbing_canonical' => 'Acep Muhammad Soleh',
                'dudi_raw' => 'PT Teknologi Unggul Ciamis',
                'dudi_name' => 'PT Teknologi Unggul Ciamis',
                'siswa_nama' => 'Budi Siswa',
                'kelas' => 'XII PPLG 1',
                'program' => 'PPLG',
            ],
        ];

        $stats = $service->executeImport($items);

        $this->assertEquals(1, $stats['pembimbing_matched']);
        $this->assertEquals(0, $stats['pembimbing_created']);

        // 4. Verify teacher's credentials are smartly overwritten to official NIP
        $pembimbing->refresh();
        $legacyUser->refresh();

        $expectedNip = '199409152022211004';
        $this->assertEquals($expectedNip, $pembimbing->nip);
        $this->assertEquals($expectedNip, $legacyUser->username);
        $this->assertNull($legacyUser->email);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check($expectedNip, $legacyUser->password));
        $this->assertTrue($legacyUser->force_password_change);

        // 5. Verify student mapping and DUDI relationship remain completely intact
        $siswa->refresh();
        $this->assertEquals($pembimbing->id, $siswa->pembimbing_sekolah_id);
        $this->assertEquals($dudi->id, $siswa->dudi_id);
    }

    public function test_pokja_can_access_import_pemetaan_page_under_settings(): void
    {
        $response = $this->actingAs($this->pokja)->get(route('pokja.pengaturan.import_pemetaan'));

        $response->assertOk();
        $response->assertViewIs('pokja.pengaturan.import-pemetaan');
        $response->assertSeeText('Impor Pemetaan Pembimbing & DUDI PKL');
        $response->assertDontSeeText('Password Awal Akun Guru Baru');
    }

    public function test_super_admin_config_page_does_not_have_password_field(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.config.index'));

        $response->assertOk();
        $response->assertSeeText('Impor Pemetaan Pembimbing & DUDI PKL');
        $response->assertDontSeeText('Password Awal Akun Guru Baru');
    }

    public function test_pokja_can_process_import_pemetaan_via_ajax(): void
    {
        $response = $this->actingAs($this->pokja)
            ->postJson(route('pokja.pengaturan.import_pemetaan.process'), [
                'jurusan_filter' => ['PPLG', 'AKL'],
                'batch_size' => 10,
                'offset' => 0,
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
            ]);
    }

    public function test_import_guru_nip_file_syncs_existing_teachers_and_creates_new_accounts(): void
    {
        // 1. Setup existing teacher with old username & password
        $oldUser = User::create([
            'name' => 'Acep Muhammad Soleh',
            'username' => 'acep.muhammad.soleh',
            'email' => 'acep@smkn1ciamis.sch.id',
            'password' => bcrypt('smkn1ciamis'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
            'force_password_change' => true,
        ]);

        $existingTeacher = PembimbingSekolah::create([
            'user_id' => $oldUser->id,
            'nama_lengkap' => 'Acep Muhammad Soleh',
            'nip' => null,
            'tipe' => 'kejuruan',
        ]);

        // Existing DUDI and student to ensure mappings are not touched
        $dudi = Dudi::create([
            'nama' => 'PT Gameloft Indonesia',
            'konsentrasi_keahlian_id' => $this->konPplg->id,
            'kota' => 'Yogyakarta',
            'alamat' => 'Yogyakarta',
            'is_active' => true,
        ]);

        $siswa = Siswa::create([
            'user_id' => User::factory()->create(['role' => 'siswa'])->id,
            'konsentrasi_keahlian_id' => $this->konPplg->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $existingTeacher->id,
            'nama_lengkap' => 'Budi Sanjaya',
            'nis' => '12345',
            'nisn' => '0012345678',
            'kelas' => 'XII PPLG 1',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Ciamis',
            'tanggal_lahir' => '2006-01-01',
            'alamat' => 'Ciamis',
            'no_hp' => '08123456789',
            'tahun_ajaran' => '2026/2027',
            'status_pkl' => 'sedang_pkl',
        ]);

        $service = app(PemetaanImportService::class);

        // CSV content containing existing teacher (Acep) and new teacher (Amirudin)
        $csvContent = "Nama Lengkap Guru,NIP Guru\n" .
            "\"ACEP MUHAMMAD SOLEH, S.Pd.\",199409152022211004\n" .
            "\"AMIRUDIN, S.Pd.\",198605012023211012\n" .
            "Dimensi Distribusi Guru,Kategori / Penugasan\n" .
            "Guru Produktif / Kejuruan,35 Orang\n";

        $parsed = $service->parseCsv($csvContent);

        $this->assertEquals('guru_nip', $parsed['format'] ?? null);
        $this->assertCount(2, $parsed['items']);

        $stats = $service->executeImport($parsed['items']);

        $this->assertEquals(1, $stats['pembimbing_matched']);
        $this->assertEquals(1, $stats['pembimbing_created']);

        // Check synced existing teacher:
        $existingTeacher->refresh();
        $oldUser->refresh();
        $this->assertEquals('199409152022211004', $existingTeacher->nip);
        $this->assertEquals('199409152022211004', $oldUser->username);
        $this->assertNull($oldUser->email);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('199409152022211004', $oldUser->password));

        // Check new teacher created:
        $newTeacher = PembimbingSekolah::where('nip', '198605012023211012')->first();
        $this->assertNotNull($newTeacher);
        $this->assertEquals('198605012023211012', $newTeacher->user->username);
        $this->assertNull($newTeacher->user->email);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('198605012023211012', $newTeacher->user->password));

        // Check DUDI and student relationship remain untouched:
        $siswa->refresh();
        $this->assertEquals($existingTeacher->id, $siswa->pembimbing_sekolah_id);
        $this->assertEquals($dudi->id, $siswa->dudi_id);
    }

    public function test_pokja_can_upload_guru_nip_csv_file_via_ajax(): void
    {
        $csvContent = "Nama Lengkap Guru,NIP Guru\n" .
            "\"ACEP MUHAMMAD SOLEH, S.Pd.\",199409152022211004\n" .
            "\"AGUNG SOFIYANI, S.E.\",199206122024212036\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('master_guru_nip.csv', $csvContent);

        $response = $this->actingAs($this->pokja)
            ->post(route('pokja.pengaturan.import_pemetaan.process'), [
                'file' => $file,
                'batch_size' => 50,
                'offset' => 0,
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'total' => 2,
                'format' => 'guru_nip',
                'stats' => [
                    'pembimbing_created' => 2,
                ],
            ]);
    }

    public function test_pokja_can_view_pembimbing_sekolah_list_even_when_konsentrasi_or_email_is_null(): void
    {
        $user = User::create([
            'name' => 'Guru Tanpa Jurusan',
            'username' => '199901012025011001',
            'email' => null,
            'password' => bcrypt('199901012025011001'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);

        $teacher = PembimbingSekolah::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Guru Tanpa Jurusan, S.Pd.',
            'nip' => '199901012025011001',
            'tipe' => 'kejuruan',
            'konsentrasi_keahlian_id' => null,
        ]);

        $response = $this->actingAs($this->pokja)->get(route('pokja.pembimbing_sekolah.index'));

        $response->assertOk();
        $response->assertSeeText('Guru Tanpa Jurusan, S.Pd.');
        $response->assertSeeText('199901012025011001');

        $showResponse = $this->actingAs($this->pokja)->get(route('pokja.pembimbing_sekolah.show', $teacher));
        $showResponse->assertOk();
        $showResponse->assertSeeText('Guru Tanpa Jurusan, S.Pd.');
    }
}
