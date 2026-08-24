<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Pesan;
use App\Models\ProgramKeahlian;
use App\Models\KonsentrasiKeahlian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesanTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_can_see_and_read_messages_from_pokja_or_other_senders()
    {
        $prog = ProgramKeahlian::create(['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak']);
        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $prog->id,
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'durasi_pkl_bulan' => 6,
        ]);

        $siswaUser = User::factory()->create([
            'role' => 'siswa',
            'force_password_change' => false,
        ]);

        $siswa = Siswa::create([
            'user_id' => $siswaUser->id,
            'nama_lengkap' => 'Test Siswa',
            'nis' => '123456',
            'kelas' => 'XII RPL 1',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2025/2026',
        ]);

        $pokjaUser = User::factory()->create([
            'role' => 'pokja',
            'name' => 'Ketua Pokja',
        ]);

        // Send unread message from Pokja to Siswa
        $pesan = Pesan::create([
            'from_user_id' => $pokjaUser->id,
            'to_user_id' => $siswaUser->id,
            'isi' => 'Pengumuman Penting untuk Siswa',
            'dibaca_at' => null,
        ]);

        // 1. Check student index sees Pokja in contact list
        $response = $this->actingAs($siswaUser)->get(route('pesan.index'));
        $response->assertStatus(200);
        $response->assertSee('Ketua Pokja');
        $response->assertSee('Pengumuman Penting untuk Siswa');

        // 2. Open chat thread with Pokja
        $showResponse = $this->actingAs($siswaUser)->get(route('pesan.show', $pokjaUser));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Pengumuman Penting untuk Siswa');

        // 3. Verify message is marked as read in database
        $pesan->refresh();
        $this->assertNotNull($pesan->dibaca_at);

        // 4. Verify unread count is now 0
        $unreadCount = Pesan::where('to_user_id', $siswaUser->id)->whereNull('dibaca_at')->count();
        $this->assertEquals(0, $unreadCount);
    }
}