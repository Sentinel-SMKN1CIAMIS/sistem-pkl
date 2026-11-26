<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
    /**
     * Show the 2-card workspace picker for dual-role users (Pembimbing Sekolah + Pokja).
     */
    public function select()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->hasDualWorkspace()) {
            return redirect()->route('dashboard');
        }

        $activeWorkspace = session('active_workspace');

        return view('workspace.select', compact('user', 'activeWorkspace'));
    }

    /**
     * Switch active workspace in session and redirect to dashboard.
     */
    public function switch(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->hasDualWorkspace()) {
            abort(403, 'Akun Anda tidak memiliki akses multi-workspace.');
        }

        $validated = $request->validate([
            'workspace' => ['required', 'string', 'in:pokja,pembimbing_sekolah'],
        ]);

        $request->session()->put('active_workspace', $validated['workspace']);

        $label = $validated['workspace'] === 'pokja' ? 'Workspace Pokja (Shared)' : 'Workspace Pembimbing Sekolah';

        return redirect()->route('dashboard')->with('success', "Berhasil masuk ke {$label}.");
    }

    /**
     * Poll recent shared Pokja data changes made by other Pokja/Super Admin accounts.
     */
    public function activityFeed(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || (!$user->canAccessPokja() && $user->role !== 'super_admin')) {
            return response()->json(['events' => [], 'latest_id' => 0], 403);
        }

        $afterId = (int) $request->query('after_id', 0);

        // First poll on page load: return current max ID so we only notify NEW live changes
        if ($afterId <= 0) {
            $latestId = (int) (ActivityLog::max('id') ?? 0);

            return response()->json([
                'latest_id' => $latestId,
                'events' => [],
            ]);
        }

        $logs = ActivityLog::with('user:id,name,username,role,is_pokja')
            ->where('id', '>', $afterId)
            ->whereNotNull('user_id')
            ->where('user_id', '!=', $user->id)
            ->whereIn('action', ['CREATED', 'UPDATED', 'DELETED'])
            ->where('created_at', '>=', now()->subMinutes(15))
            ->whereHas('user', function ($q) {
                $q->whereIn('role', ['pokja', 'super_admin'])
                    ->orWhere(function ($q2) {
                        $q2->where('role', 'pembimbing_sekolah')->where('is_pokja', true);
                    });
            })
            ->orderBy('id', 'asc')
            ->limit(5)
            ->get();

        $latestId = $logs->isNotEmpty() ? (int) $logs->last()->id : $afterId;

        $events = $logs->map(function (ActivityLog $log) {
            return [
                'id' => $log->id,
                'action' => $log->action,
                'actor' => $log->user?->name ?: ($log->user?->username ?: 'Pengguna Pokja'),
                'summary' => $this->formatDescription($log->action, $log->description),
                'time' => $log->created_at?->diffForHumans() ?? 'Baru saja',
            ];
        })->values();

        return response()->json([
            'latest_id' => $latestId,
            'events' => $events,
        ]);
    }

    /**
     * Format raw AuditLog description into clean Indonesian for the toast popup.
     */
    protected function formatDescription(string $action, string $description): string
    {
        $tableLabels = [
            'siswas' => 'Data Siswa',
            'dudis' => 'Data DUDI',
            'pengajuan_pkls' => 'Pengajuan PKL',
            'pembimbing_sekolahs' => 'Pembimbing Sekolah',
            'pembimbing_dudis' => 'Pembimbing DUDI',
            'jurnals' => 'Jurnal Siswa',
            'absensis' => 'Absensi Siswa',
            'kompetensis' => 'Data CP/TP',
            'users' => 'Akun Pengguna',
            'zonas' => 'Zona Pemetaan',
            'penilaians' => 'Penilaian PKL',
        ];

        $actionLabels = [
            'CREATED' => 'menambahkan',
            'UPDATED' => 'memperbarui',
            'DELETED' => 'menghapus',
        ];

        if (preg_match('/Data\s+([a-z0-9_]+)\s+dengan\s+ID\s+#(\d+)/i', $description, $m)) {
            $table = strtolower($m[1]);
            $id = $m[2];
            $entityLabel = $tableLabels[$table] ?? ucwords(str_replace('_', ' ', $table));
            $verb = $actionLabels[$action] ?? 'mengubah';

            return "Baru saja {$verb} {$entityLabel} (#{$id})";
        }

        return $description;
    }
}
