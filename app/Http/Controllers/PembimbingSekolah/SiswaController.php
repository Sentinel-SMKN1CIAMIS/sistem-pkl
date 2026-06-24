<?php

namespace App\Http\Controllers\PembimbingSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Notifikasi;

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
                if (in_array($absensi->status, ['sakit', 'izin', 'alpha'])) {
                    $student->status_hari_ini_computed = $absensi->status;
                } elseif ($absensi->waktu_pulang) {
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
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'belum_absen' => 0
        ];
        foreach ($students as $student) {
            if ($student->status_pkl === 'sedang_pkl') {
                $status = strtolower($student->status_hari_ini_computed);
                if (in_array($status, ['masuk kerja', 'pulang kerja', 'hadir'])) {
                    $attendanceCounts['hadir']++;
                } elseif ($status === 'sakit') {
                    $attendanceCounts['sakit']++;
                } elseif ($status === 'izin') {
                    $attendanceCounts['izin']++;
                } elseif ($status === 'alpha') {
                    $attendanceCounts['alpha']++;
                } elseif ($status === 'belum absen') {
                    $attendanceCounts['belum_absen']++;
                }
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

    public function remind(Request $request, Siswa $siswa)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Profil pembimbing sekolah tidak ditemukan.');
        }

        // Verify advisor permission
        if ($siswa->pembimbing_sekolah_id !== $teacher->id && $siswa->pembimbing_sekolah_umum_id !== $teacher->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk memberi peringatan pada siswa ini.');
        }

        // Create the notification in database
        Notifikasi::create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $siswa->user_id,
            'judul' => 'Segera Isi Jurnal',
            'pesan' => 'Pembimbing Sekolah mengingatkan Anda agar segera mengisi Jurnal PKL harian hari ini di sistem.',
            'tipe' => 'jurnal_reminder',
            'is_read' => 0
        ]);

        return redirect()->back()->with('success', 'Notifikasi peringatan berhasil dikirim ke ' . $siswa->nama_lengkap . '.');
    }

    public function remindAll(Request $request)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Profil pembimbing sekolah tidak ditemukan.');
        }

        // Get all students assigned to this teacher
        $students = Siswa::where(function($q) use ($teacher) {
                $q->where('pembimbing_sekolah_id', $teacher->id)
                  ->orWhere('pembimbing_sekolah_umum_id', $teacher->id);
            })->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa bimbingan ditemukan.');
        }

        // Get today's journals for these students
        $today = \Carbon\Carbon::today()->toDateString();
        $todayJournals = \App\Models\Jurnal::whereIn('siswa_id', $students->pluck('id'))
            ->where('tanggal', $today)
            ->pluck('siswa_id')
            ->toArray();

        // Filter students who have not filled today's journal
        $unfilledStudents = $students->filter(fn($student) => !in_array($student->id, $todayJournals));

        if ($unfilledStudents->isEmpty()) {
            return redirect()->back()->with('error', 'Semua siswa bimbingan Anda sudah mengisi jurnal hari ini.');
        }

        // Create notification for each student
        $count = 0;
        foreach ($unfilledStudents as $siswa) {
            Notifikasi::create([
                'from_user_id' => auth()->id(),
                'to_user_id' => $siswa->user_id,
                'judul' => 'Segera Isi Jurnal',
                'pesan' => 'Pembimbing Sekolah mengingatkan Anda agar segera mengisi Jurnal PKL harian hari ini di sistem.',
                'tipe' => 'jurnal_reminder',
                'is_read' => 0
            ]);
            $count++;
        }

        return redirect()->back()->with('success', "Notifikasi pengingat berhasil dikirim ke {$count} siswa sekaligus.");
    }

    public function changePassword(Request $request, Siswa $siswa)
    {
        $teacher = auth()->user()->pembimbingSekolah;
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Profil pembimbing sekolah tidak ditemukan.');
        }

        // Verify advisor permission
        if ($siswa->pembimbing_sekolah_id !== $teacher->id && $siswa->pembimbing_sekolah_umum_id !== $teacher->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk mengubah password siswa ini.');
        }

        // Validate the password
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Update student's user password
        $siswa->user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        return redirect()->back()->with('success', 'Password siswa ' . $siswa->nama_lengkap . ' berhasil diubah.');
    }
}
