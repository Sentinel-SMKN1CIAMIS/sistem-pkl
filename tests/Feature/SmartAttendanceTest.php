<?php

namespace Tests\Feature;

use App\Http\Controllers\Siswa\JurnalAiAssistantController;
use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\Jurnal;
use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use App\Services\SmartAttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SmartAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $targetUser;
    private Siswa $targetSiswa;
    private User $regularUser;
    private Siswa $regularSiswa;
    private Dudi $dudi;
    private Kompetensi $kompetensi;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        config([
            'services.groq.key' => 'gsk_test_mock_key',
            'services.groq.model' => 'qwen/qwen3.8-27b',
            'services.groq.whitelist_nis' => '242510648',
        ]);

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

        $this->dudi = Dudi::create([
            'nama' => 'PT Teknologi Ciamis Mandiri',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Tentara Pelajar No. 10',
            'latitude' => -7.327500,
            'longitude' => 108.355200,
            'is_active' => true,
        ]);

        $this->kompetensi = Kompetensi::create([
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nama' => 'Penerapan Hard Skills',
            'tp' => '1. Menerapkan pengujian antarmuka aplikasi',
            'kategori' => 'teknis',
            'cp' => 'Mampu menerapkan kompetensi teknis pada pekerjaan sesuai POS.',
            'deskripsi' => 'Pengujian antarmuka aplikasi',
        ]);

        $guruUser = User::create([
            'name' => 'Guru Pembimbing',
            'username' => '198501012010011003',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);

        $guru = PembimbingSekolah::create([
            'user_id' => $guruUser->id,
            'nama_lengkap' => 'Guru Pembimbing',
        ]);

        $allowedNis = JurnalAiAssistantController::getAllowedNis();

        $this->targetUser = User::create([
            'name' => 'Zidny Siswa',
            'username' => $allowedNis,
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->targetSiswa = Siswa::create([
            'user_id' => $this->targetUser->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $guru->id,
            'nis' => $allowedNis,
            'nama_lengkap' => 'Zidny Siswa',
            'kelas' => 'XII RPL 1',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567890',
            'status_pkl' => 'sedang_pkl',
        ]);

        $this->regularUser = User::create([
            'name' => 'Siswa Lain',
            'username' => '242510999',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->regularSiswa = Siswa::create([
            'user_id' => $this->regularUser->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $this->dudi->id,
            'pembimbing_sekolah_id' => $guru->id,
            'nis' => '242510999',
            'nama_lengkap' => 'Siswa Lain',
            'kelas' => 'XII RPL 2',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567892',
            'status_pkl' => 'sedang_pkl',
        ]);
    }

    public function test_clock_in_creates_attendance_for_whitelisted_student(): void
    {
        $service = app(SmartAttendanceService::class);
        $mondayMorning = Carbon::parse('2026-11-23 08:00:00'); // Monday

        $result = $service->clockIn($this->targetSiswa, $mondayMorning);

        $this->assertEquals('success', $result['status']);
        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $this->targetSiswa->id,
            'tanggal' => '2026-11-23',
            'status' => 'hadir',
            'approval_status' => 'approved',
            'waktu_datang' => '08:00:00',
        ]);

        $absensi = Absensi::where('siswa_id', $this->targetSiswa->id)->first();
        $this->assertNotNull($absensi->latitude);
        $this->assertNotNull($absensi->longitude);
        $this->assertNotNull($absensi->ttd_siswa_path);
        Storage::disk('public')->assertExists($absensi->ttd_siswa_path);
    }

    public function test_clock_in_skips_on_weekend_unless_forced(): void
    {
        $service = app(SmartAttendanceService::class);
        $sundayMorning = Carbon::parse('2026-11-22 08:00:00'); // Sunday

        $result = $service->clockIn($this->targetSiswa, $sundayMorning, force: false);
        $this->assertEquals('skipped', $result['status']);
        $this->assertDatabaseMissing('absensis', [
            'siswa_id' => $this->targetSiswa->id,
            'tanggal' => '2026-11-22',
        ]);

        // When forced, it succeeds
        $resultForced = $service->clockIn($this->targetSiswa, $sundayMorning, force: true);
        $this->assertEquals('success', $resultForced['status']);
        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $this->targetSiswa->id,
            'tanggal' => '2026-11-22',
        ]);
    }

    public function test_clock_out_updates_time_and_creates_ai_journal(): void
    {
        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'kegiatan' => 'Implementasi API dan Uji Coba Form',
                                'catatan' => 'Sore ini berhasil menyelesaikan integrasi API dan melakukan uji coba pada form input data.',
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(SmartAttendanceService::class);
        $workDay = Carbon::parse('2026-11-23 08:02:00');

        // Clock in first
        $service->clockIn($this->targetSiswa, $workDay);

        // Clock out at 17:00
        $evening = Carbon::parse('2026-11-23 17:00:00');
        $result = $service->clockOut($this->targetSiswa, $evening, withAutoJournal: true);

        $this->assertEquals('success', $result['status']);
        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $this->targetSiswa->id,
            'tanggal' => '2026-11-23',
            'waktu_pulang' => '17:00:00',
        ]);

        $this->assertDatabaseHas('jurnals', [
            'siswa_id' => $this->targetSiswa->id,
            'tanggal' => '2026-11-23',
            'deskripsi_pekerjaan' => 'Implementasi API dan Uji Coba Form',
            'status' => 'pending',
        ]);
    }

    public function test_clock_out_skips_if_student_did_not_clock_in(): void
    {
        $service = app(SmartAttendanceService::class);
        $workDay = Carbon::parse('2026-11-23 17:00:00');

        $result = $service->clockOut($this->targetSiswa, $workDay);

        $this->assertEquals('skipped', $result['status']);
        $this->assertStringContainsString('belum melakukan absen masuk', $result['message']);
    }

    public function test_artisan_command_pkl_smart_attendance(): void
    {
        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'kegiatan' => 'Otomasi Pengujian Aplikasi',
                                'catatan' => 'Mengerjakan script otomasi untuk pengujian fungsional modul absensi.',
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->artisan('pkl:smart-attendance', [
            '--action' => 'in',
            '--force' => true,
        ])->assertExitCode(0);

        $today = Carbon::today()->toDateString();
        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $this->targetSiswa->id,
            'tanggal' => $today,
            'status' => 'hadir',
        ]);

        $this->artisan('pkl:smart-attendance', [
            '--action' => 'out',
            '--force' => true,
        ])->assertExitCode(0);

        $absensi = Absensi::where('siswa_id', $this->targetSiswa->id)->where('tanggal', $today)->first();
        $this->assertNotNull($absensi->waktu_pulang);
    }

    public function test_web_smart_trigger_forbidden_for_regular_student(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->postJson(route('siswa.absensi.smart-trigger'), [
                'action' => 'in',
            ]);

        $response->assertStatus(403);
    }

    public function test_web_smart_trigger_allowed_for_target_student(): void
    {
        $response = $this->actingAs($this->targetUser)
            ->postJson(route('siswa.absensi.smart-trigger'), [
                'action' => 'in',
                'force' => true,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }
}
