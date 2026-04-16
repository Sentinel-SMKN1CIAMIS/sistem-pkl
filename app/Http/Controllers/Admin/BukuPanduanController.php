use App\Http\Controllers\Controller;
use App\Models\BukuPanduan;
use App\Models\KonsentrasiKeahlian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuPanduanController extends Controller
{
    public function index()
    {
        $panduans = BukuPanduan::with('konsentrasiKeahlian')->latest()->paginate(10);
        return view('admin.panduan.index', compact('panduans'));
    }

    public function create()
    {
        $concentrations = KonsentrasiKeahlian::all();
        return view('admin.panduan.create', compact('concentrations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:siswa,dudi,umum',
            'file' => 'required|file|mimes:pdf|max:10240',
            'konsentrasi_keahlian_id' => 'nullable|exists:konsentrasi_keahlians,id',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('panduan', 'public');
            
            BukuPanduan::create([
                'judul' => $request->judul,
                'tipe' => $request->tipe,
                'deskripsi' => $request->deskripsi,
                'file_path' => $path,
                'konsentrasi_keahlian_id' => $request->konsentrasi_keahlian_id
            ]);
        }

        return redirect()->route('admin.panduan.index')->with('success', 'Buku panduan berhasil diunggah.');
    }

    public function destroy(BukuPanduan $panduan)
    {
        Storage::disk('public')->delete($panduan->file_path);
        $panduan->delete();
        return back()->with('success', 'Buku panduan berhasil dihapus.');
    }
}
