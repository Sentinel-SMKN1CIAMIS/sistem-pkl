<?php

namespace Tests\Feature;

use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\LaporanPkl;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_can_export_laporan_pdf()
    {
        $program = ProgramKeahlian::create([
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak'
        ]);

        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'RPL-1',
            'nama' => 'Rekayasa Perangkat Lunak'
        ]);

        $userKaprog = User::factory()->create([
            'role' => 'kaprog',
            'program_keahlian_id' => $program->id,
            'name' => 'Kaprog RPL',
        ]);

        $userPembimbing = User::factory()->create([
            'role' => 'pembimbing_sekolah',
            'name' => 'Guru Pembimbing',
        ]);

        $pembimbing = PembimbingSekolah::create([
            'user_id' => $userPembimbing->id,
            'nama_lengkap' => 'Guru Pembimbing, S.Kom.',
            'nip' => '198501012010011001',
        ]);

        $dudi = Dudi::create([
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nama' => 'PT Solusi Digital',
            'alamat' => 'Jl. Merdeka No. 1',
            'kota' => 'Ciamis',
        ]);

        $userSiswa = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Budi Santoso',
        ]);

        $siswa = Siswa::create([
            'user_id' => $userSiswa->id,
            'nama_lengkap' => 'Budi Santoso',
            'nis' => '12345678',
            'kelas' => 'XII RPL 1',
            'jenis_kelamin' => 'L',
            'tahun_ajaran' => '2025/2026',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $pembimbing->id,
            'status_pkl' => 'sedang_pkl',
        ]);

        $laporan = LaporanPkl::create([
            'siswa_id' => $siswa->id,
            'judul' => 'Laporan Akhir Pembuatan Aplikasi PKL',
            'deskripsi' => 'Pengembangan modul absensi dan jurnal harian.',
            'link_media_sosial' => ['https://youtube.com/watch?v=sample123'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($userSiswa)
            ->get(route('siswa.laporan.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pembimbing_sekolah_can_export_siswa_laporan_pdf()
    {
        $program = ProgramKeahlian::create([
            'kode' => 'TKJ',
            'nama' => 'Teknik Komputer dan Jaringan'
        ]);

        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'kode' => 'TKJ-1',
            'nama' => 'Teknik Komputer dan Jaringan'
        ]);

        $userPembimbing = User::factory()->create([
            'role' => 'pembimbing_sekolah',
            'name' => 'Guru Pembimbing TKJ',
        ]);

        $pembimbing = PembimbingSekolah::create([
            'user_id' => $userPembimbing->id,
            'nama_lengkap' => 'Guru Pembimbing TKJ, M.Kom.',
            'nip' => '198701012010011002',
        ]);

        $dudi = Dudi::create([
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'nama' => 'PT Jaringan Nusantara',
            'alamat' => 'Jl. Sudirman No. 10',
            'kota' => 'Ciamis',
        ]);

        $userSiswa = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Siti Nurhaliza',
        ]);

        $siswa = Siswa::create([
            'user_id' => $userSiswa->id,
            'nama_lengkap' => 'Siti Nurhaliza',
            'nis' => '87654321',
            'kelas' => 'XII TKJ 2',
            'jenis_kelamin' => 'P',
            'tahun_ajaran' => '2025/2026',
            'konsentrasi_keahlian_id' => $konsentrasi->id,
            'dudi_id' => $dudi->id,
            'pembimbing_sekolah_id' => $pembimbing->id,
            'status_pkl' => 'sedang_pkl',
        ]);

        $laporan = LaporanPkl::create([
            'siswa_id' => $siswa->id,
            'judul' => 'Laporan Akhir Konfigurasi Routing',
            'deskripsi' => 'Implementasi OSPF dan VLAN pada core network.',
            'link_media_sosial' => ['https://drive.google.com/sample-file'],
            'status' => 'approved',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($userPembimbing)
            ->get(route('pembimbing_sekolah.laporan.export', $laporan));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
