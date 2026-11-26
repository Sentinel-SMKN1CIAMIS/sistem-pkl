<?php

namespace Tests\Feature;

use App\Models\KonsentrasiKeahlian;
use App\Models\Pesan;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesanSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_whatsapp_style_search_finds_contacts_by_name_or_nis_and_messages_by_keyword_with_privacy(): void
    {
        $prog = ProgramKeahlian::create(['kode' => 'PPLG', 'nama' => 'PPLG']);
        $kon = KonsentrasiKeahlian::create(['program_keahlian_id' => $prog->id, 'kode' => 'RPL', 'nama' => 'RPL']);

        $admin = User::factory()->create(['name' => 'Super Admin', 'role' => 'super_admin', 'is_active' => true]);
        $studentUser = User::factory()->create(['name' => 'Adam Gunawan', 'role' => 'siswa', 'is_active' => true]);
        Siswa::create([
            'user_id' => $studentUser->id,
            'nis' => '242510999',
            'nama_lengkap' => 'Adam Gunawan',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2026/2027',
            'kelas' => '12RPL1',
            'konsentrasi_keahlian_id' => $kon->id,
            'status_pkl' => 'sedang_pkl',
        ]);

        $otherUser1 = User::factory()->create(['name' => 'Guru Lain', 'role' => 'pembimbing_sekolah', 'is_active' => true]);
        $otherUser2 = User::factory()->create(['name' => 'Siswa Lain', 'role' => 'siswa', 'is_active' => true]);

        $msg1 = Pesan::create([
            'from_user_id' => $admin->id,
            'to_user_id' => $studentUser->id,
            'isi' => 'Halo Adam, jangan lupa kumpulkan laporan akhir PKL besok ya.',
        ]);

        // Private message between two unrelated users — must NEVER leak into admin's search
        Pesan::create([
            'from_user_id' => $otherUser1->id,
            'to_user_id' => $otherUser2->id,
            'isi' => 'Rahasia khusus obrolan pribadi mereka.',
        ]);

        // 1. Both index and show pages render cleanly with search input
        $this->actingAs($admin)->get(route('pesan.index'))->assertOk()->assertSee('Cari nama, NIS, atau isi chat');
        $this->actingAs($admin)->get(route('pesan.show', $studentUser))->assertOk()->assertSee('Cari nama, NIS, atau isi chat');

        // 2. Search by student name & NIS
        $this->actingAs($admin)
            ->getJson(route('pesan.search', ['q' => '242510999']))
            ->assertOk()
            ->assertJsonPath('contacts.0.name', 'Adam Gunawan');

        // 3. Search by chat sentence/word
        $this->actingAs($admin)
            ->getJson(route('pesan.search', ['q' => 'laporan akhir']))
            ->assertOk()
            ->assertJsonPath('messages.0.id', $msg1->id)
            ->assertJsonPath('messages.0.contact_name', 'Adam Gunawan')
            ->assertJsonFragment(['url' => route('pesan.show', $studentUser->id) . '?highlight=' . $msg1->id]);

        // 4. Privacy check: cannot see unrelated users' messages
        $this->actingAs($admin)
            ->getJson(route('pesan.search', ['q' => 'Rahasia khusus']))
            ->assertOk()
            ->assertJsonCount(0, 'messages');
    }
}
