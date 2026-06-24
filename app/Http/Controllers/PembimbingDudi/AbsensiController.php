<?php

namespace App\Http\Controllers\PembimbingDudi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Absensi;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        $mentor = auth()->user()->pembimbingDudi;

        if (!$mentor) {
            return redirect()->route('dashboard')->with('error', 'Anda bukan Pembimbing DUDI.');
        }
        
        // Fetch pending early leave requests for students of this DUDI
        $pendingEarlyLeaves = Absensi::whereHas('siswa', function($q) use ($mentor) {
                $q->where('dudi_id', $mentor->dudi_id);
            })
            ->where('early_leave_request_status', 'pending')
            ->with(['siswa', 'siswa.user'])
            ->latest('early_leave_requested_at')
            ->get();

        $absensis = Absensi::whereHas('siswa', function($q) use ($mentor) {
                $q->where('dudi_id', $mentor->dudi_id);
            })
            ->with('siswa')
            ->latest('tanggal')
            ->paginate(15);

        return view('pembimbing-dudi.absensi.index', compact('absensis', 'pendingEarlyLeaves'));
    }

    public function approveEarlyLeave(Request $request, Absensi $absensi)
    {
        $mentor = auth()->user()->pembimbingDudi;
        
        // Auth check: Is the student in the same DUDI as the mentor?
        if (!$mentor || $absensi->siswa->dudi_id !== $mentor->dudi_id) {
            return back()->with('error', 'Tidak diizinkan.');
        }

        if ($absensi->early_leave_request_status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $absensi->update([
            'early_leave_request_status' => 'approved',
            'early_leave_approved_by' => auth()->id(),
            'early_leave_approved_at' => now(),
            'early_leave_approval_note' => $request->approval_note,
        ]);

        // Create notification for Student
        \App\Models\Notifikasi::create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $absensi->siswa->user_id,
            'judul' => 'Izin Pulang Cepat Disetujui',
            'pesan' => 'Permintaan izin pulang cepat Anda pada tanggal ' . Carbon::parse($absensi->tanggal)->isoFormat('D MMMM YYYY') . ' telah disetujui oleh Pembimbing DUDI.',
            'tipe' => 'early_leave_response',
            'link' => route('siswa.absensi.index'),
            'is_read' => 0,
        ]);

        return back()->with('success', 'Izin pulang cepat siswa berhasil disetujui.');
    }

    public function rejectEarlyLeave(Request $request, Absensi $absensi)
    {
        $request->validate([
            'approval_note' => 'required|string|min:5',
        ], [
            'approval_note.required' => 'Catatan penolakan wajib diisi.',
            'approval_note.min' => 'Catatan penolakan minimal 5 karakter.',
        ]);

        $mentor = auth()->user()->pembimbingDudi;
        
        // Auth check: Is the student in the same DUDI as the mentor?
        if (!$mentor || $absensi->siswa->dudi_id !== $mentor->dudi_id) {
            return back()->with('error', 'Tidak diizinkan.');
        }

        if ($absensi->early_leave_request_status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $absensi->update([
            'early_leave_request_status' => 'rejected',
            'early_leave_approved_by' => auth()->id(),
            'early_leave_approved_at' => now(),
            'early_leave_approval_note' => $request->approval_note,
        ]);

        // Create notification for Student
        \App\Models\Notifikasi::create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $absensi->siswa->user_id,
            'judul' => 'Izin Pulang Cepat Ditolak',
            'pesan' => 'Permintaan izin pulang cepat Anda pada tanggal ' . Carbon::parse($absensi->tanggal)->isoFormat('D MMMM YYYY') . ' telah ditolak oleh Pembimbing DUDI dengan alasan: ' . $request->approval_note,
            'tipe' => 'early_leave_response',
            'link' => route('siswa.absensi.index'),
            'is_read' => 0,
        ]);

        return back()->with('success', 'Izin pulang cepat siswa berhasil ditolak.');
    }
}
