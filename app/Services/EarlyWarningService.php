<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\KonsentrasiKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EarlyWarningService
{
    public const ABSENT_WORKDAYS_THRESHOLD = 3;
    public const JOURNAL_MISSING_DAYS_THRESHOLD = 5;

    /**
     * Get early warning summary and at-risk students scoped to the given user's active role.
     *
     * @return array{total_at_risk: int, critical_count: int, warning_count: int, items: Collection}
     */
    public function getWarningsForUser(User $user, ?Carbon $asOf = null, int $limit = 10): array
    {
        $asOf = $asOf ? $asOf->copy()->startOfDay() : Carbon::today();
        $role = $user->activeWorkspace();

        $query = Siswa::with(['dudi:id,nama', 'konsentrasiKeahlian:id,kode,nama', 'pembimbingSekolah:id,nama_lengkap'])
            ->where('status_pkl', 'sedang_pkl');

        if ($role === 'kaprog') {
            $concentrationIds = $user->program_keahlian_id
                ? KonsentrasiKeahlian::where('program_keahlian_id', $user->program_keahlian_id)->pluck('id')->all()
                : [];
            if (empty($concentrationIds)) {
                return ['total_at_risk' => 0, 'critical_count' => 0, 'warning_count' => 0, 'items' => collect()];
            }
            $query->whereIn('konsentrasi_keahlian_id', $concentrationIds);
        } elseif ($role === 'pembimbing_sekolah') {
            $teacherId = $user->pembimbingSekolah?->id;
            if (!$teacherId) {
                return ['total_at_risk' => 0, 'critical_count' => 0, 'warning_count' => 0, 'items' => collect()];
            }
            $query->where(function ($q) use ($teacherId) {
                $q->where('pembimbing_sekolah_id', $teacherId)
                    ->orWhere('pembimbing_sekolah_umum_id', $teacherId);
            });
        } elseif (!in_array($role, ['pokja', 'super_admin', 'kepala_sekolah'], true)) {
            return ['total_at_risk' => 0, 'critical_count' => 0, 'warning_count' => 0, 'items' => collect()];
        }

        $students = $query->get();

        return $this->evaluateStudents($students, $asOf, $limit);
    }

    /**
     * Evaluate a collection of students for early warning flags.
     *
     * @param  Collection<int, Siswa>  $students
     * @return array{total_at_risk: int, critical_count: int, warning_count: int, items: Collection}
     */
    public function evaluateStudents(Collection $students, ?Carbon $asOf = null, ?int $limit = null): array
    {
        $asOf = $asOf ? $asOf->copy()->startOfDay() : Carbon::today();

        if ($students->isEmpty()) {
            return ['total_at_risk' => 0, 'critical_count' => 0, 'warning_count' => 0, 'items' => collect()];
        }

        $siswaIds = $students->pluck('id')->all();
        $lookbackStart = $asOf->copy()->subDays(21)->toDateString();
        $yesterdayStr = $asOf->copy()->subDay()->toDateString();

        // Batch fetch attendance records in lookback window
        $absensiBySiswa = Absensi::whereIn('siswa_id', $siswaIds)
            ->whereBetween('tanggal', [$lookbackStart, $asOf->toDateString()])
            ->get(['siswa_id', 'tanggal', 'status'])
            ->groupBy('siswa_id');

        // Batch fetch latest journal date per student
        $latestJurnalBySiswa = Jurnal::whereIn('siswa_id', $siswaIds)
            ->selectRaw('siswa_id, MAX(tanggal) as last_jurnal_date')
            ->groupBy('siswa_id')
            ->pluck('last_jurnal_date', 'siswa_id');

        // Optional national holiday lookup (forward-compatible with Phase 4)
        $nationalHolidays = $this->getNationalHolidayDates($lookbackStart, $asOf->toDateString());

        $atRisk = collect();

        foreach ($students as $siswa) {
            $records = $absensiBySiswa->get($siswa->id, collect());
            $attendedDates = $records->mapWithKeys(fn($r) => [Carbon::parse($r->tanggal)->toDateString() => $r->status]);

            // 1. Count consecutive missing working days ending yesterday (H-1 backwards)
            $consecutiveAbsent = 0;
            for ($d = 1; $d <= 14; $d++) {
                $checkDate = $asOf->copy()->subDays($d);
                $dateStr = $checkDate->toDateString();

                // Skip student's routine weekly off-days (or default Sat/Sun if not configured)
                if ($this->isDayOffForStudent($siswa, $checkDate, $nationalHolidays)) {
                    continue;
                }

                if ($attendedDates->has($dateStr)) {
                    break;
                }

                $consecutiveAbsent++;
            }

            // 2. Check journal backlog (> 5 days since last journal, only if student has hadir attendance)
            $hadirRecords = $records->filter(fn($r) => $r->status === 'hadir');
            $daysWithoutJournal = 0;
            if ($hadirRecords->isNotEmpty()) {
                $lastJurnalStr = $latestJurnalBySiswa->get($siswa->id);
                if ($lastJurnalStr) {
                    $daysWithoutJournal = (int) Carbon::parse($lastJurnalStr)->startOfDay()->diffInDays($asOf, false);
                } else {
                    $earliestHadir = $hadirRecords->min('tanggal');
                    $daysWithoutJournal = (int) Carbon::parse($earliestHadir)->startOfDay()->diffInDays($asOf, false);
                }
            }

            $isAbsentCritical = $consecutiveAbsent >= self::ABSENT_WORKDAYS_THRESHOLD;
            $isJournalOverdue = $daysWithoutJournal > self::JOURNAL_MISSING_DAYS_THRESHOLD;

            if (!$isAbsentCritical && !$isJournalOverdue) {
                continue;
            }

            $reasons = [];
            if ($isAbsentCritical) {
                $reasons[] = "Tidak absen {$consecutiveAbsent} hari kerja berturut-turut";
            }
            if ($isJournalOverdue) {
                $reasons[] = "Belum isi jurnal {$daysWithoutJournal} hari";
            }

            $atRisk->push([
                'siswa' => $siswa,
                'siswa_id' => $siswa->id,
                'severity' => $isAbsentCritical ? 'critical' : 'warning',
                'consecutive_absent' => $consecutiveAbsent,
                'days_without_journal' => $daysWithoutJournal,
                'reasons' => $reasons,
            ]);
        }

        $sorted = $atRisk->sortByDesc(function ($item) {
            return ($item['severity'] === 'critical' ? 1000 : 0)
                + ($item['consecutive_absent'] * 10)
                + $item['days_without_journal'];
        })->values();

        $criticalCount = $sorted->where('severity', 'critical')->count();
        $warningCount = $sorted->where('severity', 'warning')->count();

        return [
            'total_at_risk' => $sorted->count(),
            'critical_count' => $criticalCount,
            'warning_count' => $warningCount,
            'items' => $limit !== null ? $sorted->take($limit)->values() : $sorted,
        ];
    }

    protected function isDayOffForStudent(Siswa $siswa, Carbon $date, array $nationalHolidays): bool
    {
        if (isset($nationalHolidays[$date->toDateString()])) {
            return true;
        }

        // If student has explicitly configured routine off-days, use them
        if ($siswa->reminder_settings_configured) {
            return $siswa->isRoutineDayOff($date);
        }

        // Fallback default for unconfigured students: Saturday & Sunday
        return $date->isWeekend();
    }

    /**
     * @return array<string, bool>
     */
    protected function getNationalHolidayDates(string $from, string $to): array
    {
        if (!class_exists(\App\Models\HariLiburNasional::class)) {
            return [];
        }

        try {
            return \App\Models\HariLiburNasional::whereBetween('tanggal', [$from, $to])
                ->pluck('tanggal')
                ->mapWithKeys(fn($d) => [Carbon::parse($d)->toDateString() => true])
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
