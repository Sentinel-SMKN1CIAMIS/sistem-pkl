<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            $path = $request->path();

            // Exclude polling routes and notifications check/polling to prevent spamming
            if ($request->routeIs('pesan.poll') || str_contains($path, 'poll') || str_contains($path, 'notifikasi/read-all')) {
                return $response;
            }

            $method = $request->method();
            $routeName = $request->route()?->getName();
            
            // Determine action type
            $action = 'VIEW_PAGE';
            if ($method === 'POST') {
                $action = 'CREATED';
            } elseif (in_array($method, ['PUT', 'PATCH'])) {
                $action = 'UPDATED';
            } elseif ($method === 'DELETE') {
                $action = 'DELETED';
            }

            // Generate friendly description
            $friendlyDesc = $this->getFriendlyRouteDescription($routeName, $path, $method);

            // Append response status
            $statusCode = $response->getStatusCode();
            $statusText = ($statusCode >= 200 && $statusCode < 400) ? "Sukses (Status: {$statusCode})" : "Gagal (Status: {$statusCode})";
            $description = "{$friendlyDesc} - Status: {$statusText}.";

            // If write request, append non-sensitive payload details
            if ($method !== 'GET') {
                $payload = $request->except([
                    'password',
                    'password_confirmation',
                    'current_password',
                    'new_password',
                    'new_password_confirmation',
                    '_token',
                    '_method',
                    'file',
                    'image',
                    'photo'
                ]);

                if (!empty($payload)) {
                    $description .= " Data: " . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }

            // Save to database
            try {
                $ip = $request->ip();
                $location = ActivityLog::getLocationFromIp($ip);

                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => $action,
                    'description' => $description,
                    'ip_address' => $ip,
                    'location' => $location,
                ]);
            } catch (\Exception $e) {
                // Silently fail to avoid breaking requests if DB or IP lookup fails
            }
        }

        return $response;
    }

    /**
     * Get a friendly human-readable description for common routes.
     */
    private function getFriendlyRouteDescription($routeName, $path, $method): string
    {
        $map = [
            'dashboard' => 'Membuka Dashboard',
            'login' => 'Membuka Halaman Login',
            
            // Siswa routes
            'siswa.jurnal.index' => 'Membuka daftar Jurnal PKL',
            'siswa.jurnal.create' => 'Membuka form tambah Jurnal PKL',
            'siswa.jurnal.store' => 'Membuat Jurnal PKL baru',
            'siswa.jurnal.edit' => 'Membuka form edit Jurnal PKL',
            'siswa.jurnal.update' => 'Memperbarui Jurnal PKL',
            'siswa.jurnal.destroy' => 'Menghapus Jurnal PKL',
            'siswa.jurnal.export' => 'Mengekspor Jurnal PKL',
            'siswa.jurnal.portofolio' => 'Melihat Portofolio PKL',
            'siswa.jurnal.sertifikat' => 'Melihat/Mengunduh Sertifikat PKL',
            'siswa.absensi.index' => 'Membuka halaman Absensi',
            'siswa.absensi.clock-in' => 'Melakukan Clock-In (Absen Masuk)',
            'siswa.absensi.clock-out' => 'Melakukan Clock-Out (Absen Pulang)',
            'siswa.absensi.request-early-leave' => 'Mengajukan izin pulang cepat',
            'siswa.absensi.submit-absence-request' => 'Mengajukan izin ketidakhadiran (Sakit/Izin)',
            'siswa.laporan.index' => 'Membuka halaman Laporan PKL',
            'siswa.laporan.store' => 'Mengunggah Laporan PKL',
            'siswa.profile.index' => 'Membuka halaman Profil',
            'siswa.profile.update' => 'Memperbarui data profil',
            'siswa.profile.update-lokasi-dudi' => 'Memperbarui koordinat GPS lokasi DUDI',
            'siswa.pengajuan_pkl.create' => 'Membuka form pengajuan PKL',
            'siswa.pengajuan_pkl.store' => 'Mengirim pengajuan tempat PKL',
            'siswa.pengajuan_pkl.status' => 'Melihat status pengajuan tempat PKL',
            'siswa.pengajuan_pkl.upload_bukti' => 'Mengunggah bukti penerimaan PKL',
            
            // Pembimbing Sekolah
            'pembimbing_sekolah.siswa.index' => 'Membuka daftar Siswa Bimbingan',
            'pembimbing_sekolah.siswa.remind' => 'Kirim notifikasi pengingat jurnal ke siswa',
            'pembimbing_sekolah.siswa.remind_all' => 'Kirim notifikasi pengingat jurnal ke semua siswa bimbingan',
            'pembimbing_sekolah.siswa.change_password' => 'Mengubah password akun siswa bimbingan',
            'pembimbing_sekolah.jurnal.index' => 'Membuka halaman evaluasi Jurnal Siswa',
            'pembimbing_sekolah.jurnal.approve' => 'Menyetujui Jurnal PKL siswa',
            'pembimbing_sekolah.jurnal.reject' => 'Menolak Jurnal PKL siswa',
            'pembimbing_sekolah.absensi.index' => 'Membuka rekap Absensi Siswa',
            'pembimbing_sekolah.absensi.export' => 'Ekspor data Absensi Siswa ke Excel',
            'pembimbing_sekolah.absensi.approval.index' => 'Membuka halaman persetujuan izin Absensi',
            'pembimbing_sekolah.absensi.approve' => 'Menyetujui izin Absensi siswa',
            'pembimbing_sekolah.absensi.reject' => 'Menolak izin Absensi siswa',
            'pembimbing_sekolah.laporan.index' => 'Membuka rekap Laporan PKL Siswa',
            
            // Pembimbing Dudi
            'pembimbing_dudi.siswa.index' => 'Membuka rekap Siswa PKL',
            'pembimbing_dudi.jurnal.index' => 'Membuka rekap Jurnal Siswa',
            'pembimbing_dudi.absensi.index' => 'Membuka rekap Absensi Siswa',
            'pembimbing_dudi.absensi.approve-early-leave' => 'Menyetujui izin pulang cepat siswa',
            'pembimbing_dudi.absensi.reject-early-leave' => 'Menolak izin pulang cepat siswa',
            'pembimbing_dudi.feedback.index' => 'Membuka halaman Nilai/Feedback PKL',
            'pembimbing_dudi.feedback.create' => 'Membuka form penilaian/feedback PKL siswa',
            'pembimbing_dudi.feedback.store' => 'Menyimpan penilaian/feedback PKL siswa',
            
            // Pokja
            'pokja.siswa.index' => 'Membuka halaman kelola Siswa',
            'pokja.siswa.create' => 'Membuka form tambah Siswa',
            'pokja.siswa.store' => 'Menambahkan data Siswa baru',
            'pokja.siswa.edit' => 'Membuka form edit data Siswa',
            'pokja.siswa.update' => 'Memperbarui data Siswa',
            'pokja.siswa.destroy' => 'Menghapus data Siswa',
            'pokja.siswa.import' => 'Impor data Siswa via Excel',
            'pokja.dudi.index' => 'Membuka halaman kelola DUDI',
            'pokja.dudi.create' => 'Membuka form tambah DUDI',
            'pokja.dudi.store' => 'Menambahkan data DUDI baru',
            'pokja.dudi.edit' => 'Membuka form edit data DUDI',
            'pokja.dudi.update' => 'Memperbarui data DUDI',
            'pokja.dudi.destroy' => 'Menghapus data DUDI',
            'pokja.dudi.import' => 'Impor data DUDI via Excel',
            'pokja.pembimbing_sekolah.index' => 'Membuka kelola Pembimbing Sekolah',
            'pokja.pembimbing_sekolah.create' => 'Membuka form tambah Pembimbing Sekolah',
            'pokja.pembimbing_sekolah.store' => 'Menambahkan Pembimbing Sekolah baru',
            'pokja.pembimbing_sekolah.edit' => 'Membuka form edit Pembimbing Sekolah',
            'pokja.pembimbing_sekolah.update' => 'Memperbarui data Pembimbing Sekolah',
            'pokja.pembimbing_sekolah.destroy' => 'Menghapus data Pembimbing Sekolah',
            'pokja.pembimbing_sekolah.import' => 'Impor data Pembimbing Sekolah via Excel',
            'pokja.zona.index' => 'Membuka halaman kelola Zona Wilayah',
            'pokja.zona.store' => 'Menambahkan Zona Wilayah baru',
            'pokja.zona.update' => 'Memperbarui data Zona Wilayah',
            'pokja.zona.destroy' => 'Menghapus data Zona Wilayah',
            'pokja.monitoring.index' => 'Membuka halaman Monitoring PKL',
            'pokja.monitoring.show' => 'Membuka detail Monitoring Pembimbing Sekolah',
            'pokja.monitoring.storeNote' => 'Menyimpan catatan monitoring pembimbing',
            
            // Admin (Super Admin)
            'admin.users.index' => 'Membuka halaman kelola User Pengguna',
            'admin.users.create' => 'Membuka form tambah User',
            'admin.users.store' => 'Menambahkan User baru',
            'admin.users.edit' => 'Membuka form edit User',
            'admin.users.update' => 'Memperbarui data User',
            'admin.users.destroy' => 'Menghapus data User',
            'admin.users.bulk-destroy' => 'Menghapus data User massal',
            'admin.logs.index' => 'Membuka halaman Audit Log Sistem',
            'admin.config.index' => 'Membuka halaman Pengaturan Sistem',
            'admin.config.update' => 'Memperbarui konfigurasi sistem',
            'admin.config.backup' => 'Membuat backup database',
            'admin.config.download-backup' => 'Mengunduh file backup database',
            'admin.config.delete-backup' => 'Menghapus file backup database',
            'admin.config.wipe' => 'Melakukan pembersihan data sistem (wipe database)',
        ];

        if (isset($map[$routeName])) {
            return $map[$routeName];
        }

        // Generic descriptions
        $actionWord = 'Mengakses';
        if ($method === 'POST') $actionWord = 'Menambah data baru di';
        elseif ($method === 'PUT' || $method === 'PATCH') $actionWord = 'Memperbarui data di';
        elseif ($method === 'DELETE') $actionWord = 'Menghapus data di';

        return "{$actionWord} path `/{$path}`";
    }
}
