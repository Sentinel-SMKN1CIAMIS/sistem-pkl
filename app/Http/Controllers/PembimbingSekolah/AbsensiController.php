<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Absensi;


    public function index(Request $request)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        $query = Absensi::whereHas('siswa', function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id);
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
        $students = \App\Models\Siswa::where('pembimbing_sekolah_id', $teacher->id)->get();

        return view('pembimbing-sekolah.absensi.index', compact('absensis', 'students'));
    }

    public function export(Request $request)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        $query = Absensi::whereHas('siswa', function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id);
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
