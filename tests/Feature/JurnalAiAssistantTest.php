<?php

namespace Tests\Feature;

use App\Http\Controllers\Siswa\JurnalAiAssistantController;
use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JurnalAiAssistantTest extends TestCase
{
    use RefreshDatabase;

    private User $targetUser;
    private Siswa $targetSiswa;
    private User $regularUser;
    private Siswa $regularSiswa;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.groq.key' => 'gsk_test_mock_key',
            'services.groq.model' => 'qwen/qwen3.8-27b',
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

        $dudi = Dudi::create([
            'nama' => 'PT Teknologi Digital Ciamis',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'kota' => 'Ciamis',
            'alamat' => 'Jl. Merdeka',
            'is_active' => true,
        ]);

        $guruUser = User::create([
            'name' => 'Guru Pembimbing',
            'username' => '198501012010011002',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_sekolah',
            'is_active' => true,
        ]);

        $guru = PembimbingSekolah::create([
            'user_id' => $guruUser->id,
            'nama_lengkap' => 'Guru Pembimbing',
        ]);

        $allowedNis = JurnalAiAssistantController::getAllowedNis();

        // Target user with secret NIS
        $this->targetUser = User::create([
            'name' => 'Siswa Spesial',
            'username' => $allowedNis,
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->targetSiswa = Siswa::create([
            'user_id' => $this->targetUser->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $guru->id,
            'nis' => $allowedNis,
            'nama_lengkap' => 'Siswa Spesial',
            'kelas' => 'XII RPL 1',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567890',
            'status_pkl' => 'sedang_pkl',
        ]);

        // Regular student with different NIS
        $this->regularUser = User::create([
            'name' => 'Siswa Biasa',
            'username' => '242510001',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->regularSiswa = Siswa::create([
            'user_id' => $this->regularUser->id,
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $guru->id,
            'nis' => '242510001',
            'nama_lengkap' => 'Siswa Biasa',
            'kelas' => 'XII RPL 2',
            'tahun_ajaran' => '2024/2025',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567891',
            'status_pkl' => 'sedang_pkl',
        ]);
    }

    public function test_ai_assist_route_forbidden_for_regular_student(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->postJson(route('siswa.jurnal.ai-assist'), [
                'mode' => 'auto',
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Fitur ini tidak tersedia untuk akun Anda.',
        ]);
    }

    public function test_ai_assist_route_allowed_and_successful_for_target_nis(): void
    {
        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'kegiatan' => 'Slicing UI Dashboard dan Perapian CSS',
                                'catatan' => 'Hari ini saya mengerjakan slicing antarmuka dashboard admin. Ada sedikit kendala di bagian tata letak responsif saat dibuka di layar HP, tapi berhasil diselesaikan dengan mengatur flexbox.',
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->targetUser)
            ->postJson(route('siswa.jurnal.ai-assist'), [
                'mode' => 'refine',
                'catatan_kasar' => 'benerin tampilan dashboard di hp tadi berantakan',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'kegiatan' => 'Slicing UI Dashboard dan Perapian CSS',
                'catatan' => 'Hari ini saya mengerjakan slicing antarmuka dashboard admin. Ada sedikit kendala di bagian tata letak responsif saat dibuka di layar HP, tapi berhasil diselesaikan dengan mengatur flexbox.',
            ],
        ]);
    }

    public function test_ai_assist_auto_mode_without_catatan_kasar(): void
    {
        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'kegiatan' => 'Pengujian Endpoint REST API Siswa',
                                'catatan' => 'Mengerjakan uji coba endpoint API menggunakan Postman dan integrasi ke halaman daftar siswa.',
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->targetUser)
            ->postJson(route('siswa.jurnal.ai-assist'), [
                'mode' => 'auto',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'kegiatan' => 'Pengujian Endpoint REST API Siswa',
                'catatan' => 'Mengerjakan uji coba endpoint API menggunakan Postman dan integrasi ke halaman daftar siswa.',
            ],
        ]);
    }

    public function test_ai_assist_handles_groq_error_gracefully(): void
    {
        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'error' => [
                    'message' => 'Rate limit exceeded',
                ],
            ], 429),
        ]);

        $response = $this->actingAs($this->targetUser)
            ->postJson(route('siswa.jurnal.ai-assist'), [
                'mode' => 'auto',
            ]);

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_ai_widget_rendered_only_for_target_nis_in_create_view(): void
    {
        // Target student visits create journal page
        $response = $this->actingAs($this->targetUser)
            ->get(route('siswa.jurnal.create'));

        $response->assertStatus(200);
        $response->assertSee('Smart Journal Assistant', false);
        $response->assertSee('Beta', false);
        $response->assertSee('generateAiJurnal', false);

        // Regular student visits create journal page
        $responseRegular = $this->actingAs($this->regularUser)
            ->get(route('siswa.jurnal.create'));

        $responseRegular->assertStatus(200);
        $responseRegular->assertDontSee('Smart Journal Assistant', false);
        $responseRegular->assertDontSee('generateAiJurnal', false);
    }
}
