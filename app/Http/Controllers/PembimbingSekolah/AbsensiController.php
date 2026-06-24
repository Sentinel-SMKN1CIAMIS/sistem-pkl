<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Absensi;


class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        $query = Absensi::whereHas('siswa', function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id)
                  ->orWhere('pembimbing_sekolah_umum_id', $teacher->id);
            })
            ->with(['siswa', 'siswa.dudi']);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->filled('siswa_id')) {
            $query->where('siswa_id', $request->siswa_id);
        }

        $absensis = $query->latest('tanggal')->paginate(15)->withQueryString();
        $students = \App\Models\Siswa::where('pembimbing_sekolah_id', $teacher->id)
            ->orWhere('pembimbing_sekolah_umum_id', $teacher->id)
            ->get();

        return view('pembimbing-sekolah.absensi.index', compact('absensis', 'students'));
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date|before_or_equal:today',
            'status' => 'required|in:hadir,sakit,izin,alpha',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $teacher = auth()->user()->pembimbingSekolah;

        // Verify that the student is supervised by this teacher
        $student = \App\Models\Siswa::where('id', $request->siswa_id)
            ->where(function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id)
                  ->orWhere('pembimbing_sekolah_umum_id', $teacher->id);
            })->first();

        if (!$student) {
            return back()->with('error', 'Siswa tidak ditemukan atau tidak berada di bawah bimbingan Anda.');
        }

        // Create or Update Absensi
        $absensi = Absensi::updateOrCreate(
            [
                'siswa_id' => $student->id,
                'tanggal' => $request->tanggal,
            ],
            [
                'status' => $request->status,
                'keterangan' => $request->keterangan ?? ($request->status == 'alpha' ? 'Diubah secara manual oleh pembimbing' : null),
                'approval_status' => 'approved', // Auto approve since it's manual by teacher
                'approved_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Status absensi siswa berhasil disimpan.');
    }

    public function export(Request $request)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        $query = Absensi::whereHas('siswa', function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id)
                  ->orWhere('pembimbing_sekolah_umum_id', $teacher->id);
            })
            ->with(['siswa', 'siswa.dudi']);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->filled('siswa_id')) {
            $query->where('siswa_id', $request->siswa_id);
        }

        $data = $query->latest('tanggal')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pembimbing-sekolah.absensi.export', [
            'absensis' => $data,
            'teacher' => $teacher,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        return $pdf->download('rekap-absensi-' . now()->format('Y-m-d') . '.pdf');
    }
}
