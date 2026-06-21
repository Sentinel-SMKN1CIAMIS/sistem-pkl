<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiApprovalController extends Controller
{
    /**
     * List pending absence requests for approval
     */
    public function index()
    {
        $user = auth()->user();
        $pembimbing = $user->pembimbingSekolah;

        if (!$pembimbing) {
            return redirect()->route('pembimbing_sekolah.dashboard')
                ->with('error', 'Anda bukan guru pembimbing.');
        }

        // Get all pending absence requests for students assigned to this advisor
        $pendingAbsences = Absensi::whereHas('siswa', function ($query) use ($pembimbing) {
            $query->where('pembimbing_sekolah_id', $pembimbing->id)
                  ->orWhere('pembimbing_sekolah_umum_id', $pembimbing->id);
        })
        ->where('approval_status' . '', 'pending')
        ->whereIn('status' . '', ['izin', 'sakit', 'alpha'])
        ->with(['siswa', 'siswa.user', 'approvedBy'])
        ->orderBy('tanggal' . '', 'desc')
        ->paginate(15);

        // Get approved/rejected history
        $approvalHistory = Absensi::whereHas('siswa', function ($query) use ($pembimbing) {
            $query->where('pembimbing_sekolah_id', $pembimbing->id)
                  ->orWhere('pembimbing_sekolah_umum_id', $pembimbing->id);
        })
        ->whereIn('approval_status' . '', ['approved', 'rejected'])
        ->whereIn('status' . '', ['izin', 'sakit', 'alpha'])
        ->with(['siswa', 'siswa.user', 'approvedBy'])
        ->orderBy('updated_at', 'desc')
        ->limit(50)
        ->get();

        return view('pembimbing_sekolah.absensi.approval', compact('pendingAbsences', 'approvalHistory'));
    }

    /**
     * Approve an absence request
     */
    public function approve(Absensi $absensi)
    {
        $user = auth()->user();
        $pembimbing = $user->pembimbingSekolah;

        // Authorization check
        if (!$pembimbing || ($absensi->siswa->pembimbing_sekolah_id !== $pembimbing->id && $absensi->siswa->pembimbing_sekolah_umum_id !== $pembimbing->id)) {
            return back()->with('error', 'Tidak diizinkan.');
        }

        if ($absensi->approval_status !== 'pending') {
            return back()->with('error', 'Absensi ini sudah diproses sebelumnya.');
        }

        $absensi->update([
            'approval_status' => 'approved',
            'approved_by' => $user->id,
        ]);

        return back()->with('success', "Persetujuan untuk {$absensi->siswa->user->name} ({$absensi->status}) berhasil diberikan.");
    }

    /**
     * Reject an absence request
     */
    public function reject(Request $request, Absensi $absensi)
    {
        $request->validate([
            'approval_note' => 'required|min:5',
        ]);

        $user = auth()->user();
        $pembimbing = $user->pembimbingSekolah;

        // Authorization check
        if (!$pembimbing || ($absensi->siswa->pembimbing_sekolah_id !== $pembimbing->id && $absensi->siswa->pembimbing_sekolah_umum_id !== $pembimbing->id)) {
            return back()->with('error', 'Tidak diizinkan.');
        }

        if ($absensi->approval_status !== 'pending') {
            return back()->with('error', 'Absensi ini sudah diproses sebelumnya.');
        }

        $absensi->update([
            'approval_status' => 'rejected',
            'approved_by' => $user->id,
            'approval_note' => $request->approval_note,
            'status' => 'alpha',
        ]);

        return back()->with('success', "Penolakan untuk {$absensi->siswa->user->name} ({$absensi->status}) berhasil disimpan.");
    }


}
