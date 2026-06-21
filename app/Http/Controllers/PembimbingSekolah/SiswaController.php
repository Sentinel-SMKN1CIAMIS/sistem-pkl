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

        // Fetch today's attendance for these students to prevent N+1 queries
        $todayAbsensi = \App\Models\Absensi::whereIn('siswa_id', $students->pluck('id'))
            ->whereDate('created_at', $today)
            ->get()
            ->keyBy('siswa_id');

        $students = $students->map(function($student) use ($todayJournals, $todayAbsensi) {
            $student->has_filled_today = isset($todayJournals[$student->id]);
            $student->today_journal = $todayJournals[$student->id] ?? null;
            
            // Compute status hari ini in memory
            $absensi = $todayAbsensi[$student->id] ?? null;
            if ($student->status_pkl !== 'sedang_pkl') {
                $student->status_hari_ini_computed = str_replace('_', ' ', $student->status_pkl);
            } elseif ($absensi) {
                if ($absensi->waktu_pulang) {
                    $student->status_hari_ini_computed = 'pulang kerja';
                } else {
                    $student->status_hari_ini_computed = 'masuk kerja';
                }
            } else {
                $student->status_hari_ini_computed = 'belum absen';
            }
            return $student;
        });

        // Grouping for tabs/filters
        $studentsNotFilledToday = $students->filter(fn($s) => !$s->has_filled_today);
        $studentsHasFilledToday = $students->filter(fn($s) => $s->has_filled_today);
        $studentsPendingApproval = $students->filter(fn($s) => $s->pending_jurnal_count > 0);

        // Calculate attendance status statistics for chart
        $attendanceCounts = [
            'masuk_kerja' => 0,
            'pulang_kerja' => 0,
            'belum_absen' => 0,
            'lainnya' => 0
        ];
        foreach ($students as $student) {
            $status = strtolower($student->status_hari_ini_computed);
            if ($status === 'masuk kerja') {
                $attendanceCounts['masuk_kerja']++;
            } elseif ($status === 'pulang kerja') {
                $attendanceCounts['pulang_kerja']++;
            } elseif ($status === 'belum absen') {
                $attendanceCounts['belum_absen']++;
            } else {
                $attendanceCounts['lainnya']++;
            }
        }

        // Calculate DUDI distribution for chart
        $dudiRawCounts = [];
        foreach ($students as $student) {
            $dudiName = $student->dudi ? $student->dudi->nama : 'Belum diplot';
            $dudiRawCounts[$dudiName] = ($dudiRawCounts[$dudiName] ?? 0) + 1;
        }
        arsort($dudiRawCounts);

        $dudiCounts = [];
        $otherDudiCount = 0;
        $idx = 0;
        foreach ($dudiRawCounts as $name => $count) {
            if ($idx < 5) {
                $dudiCounts[$name] = $count;
            } else {
                $otherDudiCount += $count;
            }
            $idx++;
        }
        if ($otherDudiCount > 0) {
            $dudiCounts['Lainnya'] = $otherDudiCount;
        }

        return view('pembimbing-sekolah.siswa.index', compact(
            'students',
            'studentsNotFilledToday',
            'studentsHasFilledToday',
            'studentsPendingApproval',
            'attendanceCounts',
            'dudiCounts'
        ));
    }
}
