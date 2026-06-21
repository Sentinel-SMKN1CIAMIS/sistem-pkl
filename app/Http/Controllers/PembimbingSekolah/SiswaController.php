<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Profil pembimbing sekolah tidak ditemukan.');
        }

        $query = Siswa::where(function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id)
                  ->orWhere('pembimbing_sekolah_umum_id', $teacher->id);
            })
            ->with(['konsentrasiKeahlian', 'dudi'])
            ->withCount([
                'jurnal',
                'absensi',
                'jurnal as approved_jurnal_count' => function($q) {
                    $q->where('approval_status', 'approved');
                },
                'jurnal as pending_jurnal_count' => function($q) {
                    $q->where('approval_status', 'pending');
                },
                'jurnal as rejected_jurnal_count' => function($q) {
                    $q->where('approval_status', 'rejected');
                }
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->get();
        $today = \Carbon\Carbon::today()->toDateString();
        
        // Fetch today's journals for these students
        $todayJournals = \App\Models\Jurnal::whereIn('siswa_id', $students->pluck('id'))
            ->where('tanggal', $today)
            ->get()
            ->keyBy('siswa_id');

        $students = $students->map(function($student) use ($todayJournals) {
            $student->has_filled_today = isset($todayJournals[$student->id]);
            $student->today_journal = $todayJournals[$student->id] ?? null;
            return $student;
        });

        // Grouping for tabs/filters
        $studentsNotFilledToday = $students->filter(fn($s) => !$s->has_filled_today);
        $studentsHasFilledToday = $students->filter(fn($s) => $s->has_filled_today);
        $studentsPendingApproval = $students->filter(fn($s) => $s->pending_jurnal_count > 0);

        return view('pembimbing-sekolah.siswa.index', compact(
            'students',
            'studentsNotFilledToday',
            'studentsHasFilledToday',
            'studentsPendingApproval'
        ));
    }
}
