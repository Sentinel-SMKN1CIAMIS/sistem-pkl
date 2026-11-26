<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pesan;
use App\Models\Siswa;
use App\Services\EarlyWarningService;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarlyWarningBroadcastController extends Controller
{
    public function send(
        Request $request,
        EarlyWarningService $earlyWarningService,
        WebPushService $webPushService
    ) {
        $user = Auth::user();

        $validated = $request->validate([
            'mode' => ['required', 'in:bulk,single'],
            'siswa_id' => ['nullable', 'integer', 'required_if:mode,single'],
            'filter_severity' => ['nullable', 'in:all,critical,warning'],
            'judul' => ['required', 'string', 'min:3', 'max:150'],
            'pesan' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'judul.required' => 'Judul pengingat wajib diisi.',
            'pesan.required' => 'Isi pesan pengingat wajib diisi.',
            'pesan.min' => 'Isi pesan minimal 10 karakter.',
        ]);

        // Evaluate ALL at-risk students within the sender's role scope (no limit)
        $eval = $earlyWarningService->getWarningsForUser($user, Carbon::today(), 5000);
        $atRiskItems = $eval['items'];

        if ($validated['mode'] === 'single') {
            $siswaId = (int) $validated['siswa_id'];
            $matched = $atRiskItems->firstWhere('siswa_id', $siswaId);

            if ($matched) {
                $targets = collect([$matched]);
            } else {
                // Allow targeting a specific student inside sender's scope even if they just cleared warning
                $student = $this->findScopedStudent($user, $siswaId);
                if (!$student) {
                    abort(403, 'Anda tidak memiliki wewenang untuk mengirim pengingat ke siswa tersebut.');
                }
                $targets = collect([[
                    'siswa' => $student,
                    'siswa_id' => $student->id,
                    'severity' => 'warning',
                    'reasons' => ['kewajiban absensi dan pengisian jurnal PKL'],
                ]]);
            }
        } else {
            $severity = $validated['filter_severity'] ?? 'all';
            $targets = $atRiskItems->when(
                in_array($severity, ['critical', 'warning'], true),
                fn($c) => $c->where('severity', $severity)->values()
            );
        }

        if ($targets->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa berisiko yang cocok dengan target broadcast saat ini.');
        }

        $judul = trim($validated['judul']);
        $template = trim($validated['pesan']);
        $chatUrl = route('pesan.show', $user->id);
        $isBulk = $validated['mode'] === 'bulk';

        $sentCount = 0;
        $pushDelivered = 0;
        $pushAttemptedUsers = [];

        foreach ($targets as $item) {
            /** @var Siswa $siswa */
            $siswa = $item['siswa'];
            if (!$siswa || !$siswa->user_id) {
                continue;
            }

            $alasanText = !empty($item['reasons'])
                ? implode('; ', $item['reasons'])
                : 'kewajiban absensi dan jurnal PKL';

            $personalizedBody = str_replace(
                ['{nama}', '{alasan}'],
                [$siswa->nama_lengkap, $alasanText],
                $template
            );

            // 1. Kirim ke Pesan (Chat internal) agar siswa bisa langsung membalas
            Pesan::create([
                'from_user_id' => $user->id,
                'to_user_id' => $siswa->user_id,
                'isi' => $personalizedBody,
                'is_broadcast' => $isBulk,
            ]);

            // 2. Kirim ke Notifikasi Lonceng & Real-time Web Polling (tipe peringatan_dini)
            Notifikasi::create([
                'from_user_id' => $user->id,
                'to_user_id' => $siswa->user_id,
                'judul' => $judul,
                'pesan' => $personalizedBody,
                'tipe' => 'peringatan_dini',
                'is_read' => false,
            ]);

            $sentCount++;
            $pushAttemptedUsers[] = [
                'user_id' => (int) $siswa->user_id,
                'body' => $personalizedBody,
            ];
        }

        // 3. Kirim Web Push Notification ke HP / Browser siswa
        if (count($pushAttemptedUsers) <= 50) {
            foreach ($pushAttemptedUsers as $entry) {
                $res = $webPushService->sendToUser($entry['user_id'], [
                    'title' => $judul,
                    'body' => mb_strimwidth($entry['body'], 0, 140, '...'),
                    'url' => $chatUrl,
                    'tag' => 'early-warning-' . $user->id . '-' . now()->format('Ymd'),
                ]);
                $pushDelivered += (int) ($res['sent'] ?? 0);
            }
        } else {
            $genericPushBody = str_replace(
                ['{nama}', '{alasan}'],
                ['Siswa PKL', 'absensi & jurnal PKL'],
                $template
            );
            $webPushService->dispatchBroadcast(
                array_column($pushAttemptedUsers, 'user_id'),
                [
                    'title' => $judul,
                    'body' => mb_strimwidth($genericPushBody, 0, 140, '...'),
                    'url' => $chatUrl,
                    'tag' => 'early-warning-' . $user->id . '-' . now()->format('Ymd'),
                ]
            );
        }

        $pushNote = count($pushAttemptedUsers) <= 50
            ? " Push HP terkirim ke {$pushDelivered} perangkat aktif."
            : ' Push HP sedang diproses di latar belakang.';

        return back()->with(
            'success',
            "Pengingat berhasil dikirim ke {$sentCount} siswa (masuk ke Pesan Chat & Notifikasi Web).{$pushNote}"
        );
    }

    private function findScopedStudent($user, int $siswaId): ?Siswa
    {
        $role = $user->activeWorkspace();
        $query = Siswa::where('id', $siswaId);

        if ($role === 'kaprog') {
            if (!$user->program_keahlian_id) {
                return null;
            }
            $query->whereHas('konsentrasiKeahlian', fn($q) => $q->where('program_keahlian_id', $user->program_keahlian_id));
        } elseif ($role === 'pembimbing_sekolah') {
            $teacherId = $user->pembimbingSekolah?->id;
            if (!$teacherId) {
                return null;
            }
            $query->where(function ($q) use ($teacherId) {
                $q->where('pembimbing_sekolah_id', $teacherId)
                    ->orWhere('pembimbing_sekolah_umum_id', $teacherId);
            });
        } elseif (!in_array($role, ['pokja', 'super_admin', 'kepala_sekolah'], true)) {
            return null;
        }

        return $query->first();
    }
}
