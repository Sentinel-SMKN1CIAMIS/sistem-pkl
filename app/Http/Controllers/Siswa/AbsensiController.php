use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()->siswa;
        $today = Carbon::today();
        
        $absensiToday = Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->first();

        $history = Absensi::where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('siswa.absensi.index', compact('absensiToday', 'history'));
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'signature' => 'required', // Base64 signature
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        $siswa = auth()->user()->siswa;
        $today = Carbon::today();

        // Check if already clocked in
        $exists = Absensi::where('siswa_id', $siswa->id)->where('tanggal', $today)->exists();
        if ($exists) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        // Save signature
        $signature = $request->signature;
        $signature = str_replace('data:image/png;base64,', '', $signature);
        $signature = str_replace(' ', '+', $signature);
        $fileName = 'signatures/in_' . $siswa->id . '_' . time() . '.png';
        Storage::disk('public')->put($fileName, base64_decode($signature));

        Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'waktu_datang' => Carbon::now()->toTimeString(),
            'ttd_siswa_path' => $fileName,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('siswa.absensi.index')->with('success', 'Berhasil melakukan Absen Datang.');
    }

    public function clockOut(Request $request)
    {
        $siswa = auth()->user()->siswa;
        $today = Carbon::today();

        $absensi = Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->first();

        if (!$absensi) {
            return back()->with('error', 'Anda belum melakukan absen datang hari ini.');
        }

        if ($absensi->waktu_pulang) {
            return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        $absensi->update([
            'waktu_pulang' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->route('siswa.absensi.index')->with('success', 'Berhasil melakukan Absen Pulang.');
    }
}
