<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\KonsentrasiKeahlian;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\ProgramKeahlian;
use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::with(['programKeahlian:id,kode,nama', 'creator:id,name'])
            ->latest('id')
            ->paginate(15);

        $programs = ProgramKeahlian::orderBy('kode')->get(['id', 'kode', 'nama']);

        return view('pokja.pengumuman.index', compact('pengumumans', 'programs'));
    }

    public function store(Request $request, WebPushService $webPush)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'isi' => ['required', 'string', 'min:10', 'max:2000'],
            'target_role' => ['required', 'in:semua,siswa,pembimbing_sekolah'],
            'program_keahlian_id' => ['nullable', 'exists:program_keahlians,id'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $pengumuman = Pengumuman::create([
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'target_role' => $validated['target_role'],
            'program_keahlian_id' => $validated['program_keahlian_id'] ?? null,
            'is_active' => true,
            'expires_at' => $validated['expires_at'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // Resolve target users for in-app notification & Web Push broadcast
        $targetUserIds = $this->resolveTargetUserIds($pengumuman);

        if (!empty($targetUserIds)) {
            $now = now();
            $rows = [];
            foreach ($targetUserIds as $uid) {
                $rows[] = [
                    'from_user_id' => Auth::id(),
                    'to_user_id' => $uid,
                    'judul' => 'Pengumuman: ' . $pengumuman->judul,
                    'pesan' => $pengumuman->isi,
                    'tipe' => 'info',
                    'is_read' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            foreach (array_chunk($rows, 200) as $chunk) {
                Notifikasi::insert($chunk);
            }

            $webPush->dispatchBroadcast($targetUserIds, [
                'title' => 'Pengumuman: ' . $pengumuman->judul,
                'body' => mb_strimwidth($pengumuman->isi, 0, 140, '...'),
                'url' => '/dashboard',
                'tag' => 'pengumuman-' . $pengumuman->id,
            ]);
        }

        return back()->with('success', 'Pengumuman berhasil dipublikasikan ke ' . count($targetUserIds) . ' pengguna.');
    }

    public function toggle(Pengumuman $pengumuman)
    {
        $pengumuman->update(['is_active' => !$pengumuman->is_active]);

        return back()->with('success', 'Status pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * @return list<int>
     */
    protected function resolveTargetUserIds(Pengumuman $pengumuman): array
    {
        $concentrationIds = $pengumuman->program_keahlian_id
            ? KonsentrasiKeahlian::where('program_keahlian_id', $pengumuman->program_keahlian_id)->pluck('id')->all()
            : [];

        $userIds = [];

        if (in_array($pengumuman->target_role, ['semua', 'siswa'], true)) {
            $siswaQuery = User::where('role', 'siswa')->where('is_active', true);
            if (!empty($concentrationIds)) {
                $siswaQuery->whereHas('siswa', fn($q) => $q->whereIn('konsentrasi_keahlian_id', $concentrationIds));
            }
            $userIds = array_merge($userIds, $siswaQuery->pluck('id')->all());
        }

        if (in_array($pengumuman->target_role, ['semua', 'pembimbing_sekolah'], true)) {
            $guruQuery = User::where('role', 'pembimbing_sekolah')->where('is_active', true);
            if (!empty($concentrationIds)) {
                $guruQuery->whereHas('pembimbingSekolah', fn($q) => $q->whereIn('konsentrasi_keahlian_id', $concentrationIds));
            }
            $userIds = array_merge($userIds, $guruQuery->pluck('id')->all());
        }

        return array_values(array_unique(array_map('intval', $userIds)));
    }
}
