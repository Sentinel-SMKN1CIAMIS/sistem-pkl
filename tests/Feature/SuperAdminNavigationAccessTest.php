<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminNavigationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_sees_and_can_access_all_pokja_and_admin_features(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'admin@test.com',
            'password' => Hash::make('AdminPass123'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        // Dashboard sidebar shows newly added menu groups & items
        $this->actingAs($superAdmin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pemetaan & Penempatan')
            ->assertSee('Validasi Pengajuan')
            ->assertSee('Pemetaan')
            ->assertSee('Peta DUDI')
            ->assertSee('Kelola Zona')
            ->assertSee('Monitoring & Evaluasi')
            ->assertSee('Monitoring Pembimbing')
            ->assertSee('Evaluasi PKL')
            ->assertSee('Feedback DUDI')
            ->assertSee('Grup Pokja')
            ->assertSee('Buku Panduan')
            ->assertSee('Impor Pemetaan PKL')
            ->assertSee('Template Sertifikat')
            ->assertSee('Template Surat PKL')
            ->assertSee('Kop Laporan PDF');

        // Every newly added route is accessible with 200 OK by super_admin
        $routes = [
            'pokja.pengajuan_pkl.index',
            'pokja.pemetaan.index',
            'pokja.pemetaan.maps',
            'pokja.zona.index',
            'pokja.monitoring.index',
            'pokja.evaluasi.index',
            'pokja.feedback.index',
            'pokja.kompetensi.index',
            'pokja.pengaturan.import_pemetaan',
            'pokja.pengaturan.sertifikat',
            'pokja.pengaturan.surat_pengantar',
            'pokja.pengaturan.kop_laporan',
            'admin.pokja-groups.index',
            'admin.panduan.index',
        ];

        foreach ($routes as $routeName) {
            $this->actingAs($superAdmin)
                ->get(route($routeName))
                ->assertOk();
        }
    }
}
