<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use App\Models\Dudi;
use App\Models\KunjunganMonitoring;
use App\Models\Siswa;
use App\Services\PdfKopService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KunjunganMonitoringController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->pembimbingSekolah;
        if (!$teacher) {
            return redirect()->route('dashboard')->with('error', 'Profil Pembimbing Sekolah tidak ditemukan.');
        }

        // Prioritize DUDIs where this teacher supervises students, fallback to all active DUDIs
        $supervisedDudiIds = Siswa::where(function ($q) use ($teacher) {
            $q->where('pembimbing_sekolah_id', $teacher->id)
                ->orWhere('pembimbing_sekolah_umum_id', $teacher->id);
        })->whereNotNull('dudi_id')->distinct()->pluck('dudi_id')->all();

        $dudis = Dudi::where('is_active', true)
            ->when(!empty($supervisedDudiIds), fn($q) => $q->whereIn('id', $supervisedDudiIds))
            ->orderBy('nama')
            ->get();

        if ($dudis->isEmpty()) {
            $dudis = Dudi::where('is_active', true)->orderBy('nama')->get();
        }

        $kunjungans = KunjunganMonitoring::with('dudi:id,nama,kota,alamat')
            ->where('pembimbing_sekolah_id', $teacher->id)
            ->orderByDesc('tanggal_kunjungan')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('pembimbing-sekolah.kunjungan.index', compact('dudis', 'kunjungans'));
    }

    public function exportPdf()
    {
        $teacher = Auth::user()->pembimbingSekolah;
        if (!$teacher) {
            abort(403, 'Profil Pembimbing Sekolah tidak ditemukan.');
        }

        $kunjungans = KunjunganMonitoring::with('dudi:id,nama,kota,alamat')
            ->where('pembimbing_sekolah_id', $teacher->id)
            ->orderByDesc('tanggal_kunjungan')
            ->orderByDesc('id')
            ->get();

        $kopData = PdfKopService::getKopData();
        $pdf = Pdf::loadView('pembimbing-sekolah.kunjungan.export-pdf', array_merge($kopData, compact('teacher', 'kunjungans')))
            ->setPaper('a4', 'portrait');

        return $pdf->download('rekap-log-kunjungan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->pembimbingSekolah;
        if (!$teacher) {
            abort(403, 'Profil Pembimbing Sekolah tidak ditemukan.');
        }

        $validated = $request->validate([
            'dudi_id' => ['required', 'exists:dudis,id'],
            'tanggal_kunjungan' => ['required', 'date', 'before_or_equal:today'],
            'catatan_monitoring' => ['required', 'string', 'min:10', 'max:2000'],
            'kendala' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:15360'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'dudi_id.required' => 'Silakan pilih DUDI yang dikunjungi.',
            'tanggal_kunjungan.before_or_equal' => 'Tanggal kunjungan tidak boleh melebihi hari ini.',
            'catatan_monitoring.min' => 'Catatan hasil monitoring minimal 10 karakter.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $binary = file_get_contents($request->file('foto')->getRealPath());
            if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {
                $img = @imagecreatefromstring($binary);
                if ($img !== false) {
                    $w = imagesx($img);
                    $h = imagesy($img);
                    $maxDim = 1200;
                    if ($w > $maxDim || $h > $maxDim) {
                        $scale = min($maxDim / $w, $maxDim / $h);
                        $nw = (int) round($w * $scale);
                        $nh = (int) round($h * $scale);
                        $resized = imagecreatetruecolor($nw, $nh);
                        imagecopyresampled($resized, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
                        imagedestroy($img);
                        $img = $resized;
                    }
                    ob_start();
                    imagejpeg($img, null, 80);
                    $binary = ob_get_clean();
                    imagedestroy($img);
                }
            }
            $fotoPath = 'kunjungan/' . $teacher->id . '_' . time() . '.jpg';
            Storage::disk('public')->put($fotoPath, $binary);
        }

        KunjunganMonitoring::create([
            'pembimbing_sekolah_id' => $teacher->id,
            'dudi_id' => $validated['dudi_id'],
            'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
            'catatan_monitoring' => $validated['catatan_monitoring'],
            'kendala' => $validated['kendala'] ?? null,
            'foto_path' => $fotoPath,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        return back()->with('success', 'Log kunjungan lapangan berhasil dicatat.');
    }

    public function destroy(KunjunganMonitoring $kunjungan)
    {
        $teacher = Auth::user()->pembimbingSekolah;
        if (!$teacher || $kunjungan->pembimbing_sekolah_id !== $teacher->id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus log kunjungan ini.');
        }

        if ($kunjungan->foto_path) {
            Storage::disk('public')->delete($kunjungan->foto_path);
        }

        $kunjungan->delete();

        return back()->with('success', 'Log kunjungan berhasil dihapus.');
    }
}
