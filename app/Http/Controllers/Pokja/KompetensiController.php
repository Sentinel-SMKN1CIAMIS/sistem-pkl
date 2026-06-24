<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kompetensi;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;

class KompetensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Kompetensi::with('konsentrasiKeahlian');

        if ($request->filled('konsentrasi_keahlian_id')) {
            $query->where('konsentrasi_keahlian_id', $request->input('konsentrasi_keahlian_id'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('cp', 'like', "%{$search}%")
                  ->orWhere('tp', 'like', "%{$search}%");
            });
        }

        $compentencies = $query->latest()->paginate(10)->withQueryString();
        $concentrations = KonsentrasiKeahlian::all();

        return view('pokja.kompetensi.index', compact('compentencies', 'concentrations'));
    }

    public function create()
    {
        $concentrations = KonsentrasiKeahlian::all();
        return view('pokja.kompetensi.create', compact('concentrations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'nama' => 'required|string|max:500',
            'tp' => 'nullable|string|max:500',
            'cp' => 'nullable|string|max:500',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string'
        ]);

        Kompetensi::create($request->all());

        return redirect()->route('pokja.kompetensi.index')
            ->with('success', 'Tujuan Pembelajaran berhasil ditambahkan.');
    }

    public function edit(Kompetensi $kompetensi)
    {
        $concentrations = KonsentrasiKeahlian::all();
        return view('pokja.kompetensi.edit', compact('kompetensi', 'concentrations'));
    }

    public function update(Request $request, Kompetensi $kompetensi)
    {
        $request->validate([
            'konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'nama' => 'required|string|max:500',
            'tp' => 'nullable|string|max:500',
            'cp' => 'nullable|string|max:500',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string'
        ]);

        $kompetensi->update($request->all());

        return redirect()->route('pokja.kompetensi.index')
            ->with('success', 'Tujuan Pembelajaran berhasil diperbarui.');
    }

    public function destroy(Kompetensi $kompetensi)
    {
        $kompetensi->delete();

        return redirect()->route('pokja.kompetensi.index')
            ->with('success', 'Tujuan Pembelajaran berhasil dihapus.');
    }

    public function showImportPdfForm()
    {
        $defaultPdfPath = public_path('BUKU PEDOMAN PKL 2025-2026.pdf');
        $defaultPdfExists = file_exists($defaultPdfPath);
        $defaultPdfSize = $defaultPdfExists ? round(filesize($defaultPdfPath) / 1024 / 1024, 2) : 0;

        return view('pokja.kompetensi.import_pdf', compact('defaultPdfExists', 'defaultPdfSize'));
    }

    public function parseImportPdf(Request $request)
    {
        $pdfPath = null;
        if ($request->hasFile('pdf_file')) {
            $request->validate([
                'pdf_file' => 'required|file|mimes:pdf|max:15360', // max 15MB
            ]);
            $pdfPath = $request->file('pdf_file')->getRealPath();
        } else {
            $pdfPath = public_path('BUKU PEDOMAN PKL 2025-2026.pdf');
            if (!file_exists($pdfPath)) {
                return back()->with('error', 'File PDF default tidak ditemukan. Silakan unggah file baru.');
            }
        }

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $pdfText = '';
            
            foreach ($pdf->getPages() as $page) {
                $pdfText .= $page->getText() . "\n";
            }
            
            $sections = [
                'AKL' => [
                    'start' => 'B. Alur Tujuan Pembelajaran PKL Akuntansi Keuangan Lembaga',
                    'end' => 'C. Alur Tujuan Pembelajaran PKL Pemasaran',
                    'program' => 'Akuntansi dan Keuangan Lembaga',
                    'konsentrasi' => 'Akuntansi'
                ],
                'Pemasaran' => [
                    'start' => 'C. Alur Tujuan Pembelajaran PKL Pemasaran',
                    'end' => 'D. Alur Tujuan Pembelajaran PKL Manajemen Perkantoran dan Layanan Bisnis',
                    'program' => 'Pemasaran',
                    'konsentrasi' => 'Bisnis Ritel'
                ],
                'MPLB' => [
                    'start' => 'D. Alur Tujuan Pembelajaran PKL Manajemen Perkantoran dan Layanan Bisnis',
                    'end' => 'E. Alur Tujuan Pembelajaran PKL Perhotelan',
                    'program' => 'Manajemen Perkantoran dan Layanan Bisnis',
                    'konsentrasi' => 'Manajemen Perkantoran'
                ],
                'Perhotelan' => [
                    'start' => 'E. Alur Tujuan Pembelajaran PKL Perhotelan',
                    'end' => 'F. Alur Tujuan Pembelajaran PKL Kuliner',
                    'program' => 'Perhotelan',
                    'konsentrasi' => 'Perhotelan'
                ],
                'Kuliner' => [
                    'start' => 'F. Alur Tujuan Pembelajaran PKL Kuliner',
                    'end' => 'G. Alur Tujuan Pembelajaran PKL DKV',
                    'program' => 'Kuliner',
                    'konsentrasi' => 'Kuliner'
                ],
                'DKV' => [
                    'start' => 'G. Alur Tujuan Pembelajaran PKL DKV',
                    'end' => 'H. Alur Tujuan Pembelajaran PKL PPLG',
                    'program' => 'Desain Komunikasi Visual',
                    'konsentrasi' => 'Desain Komunikasi Visual'
                ],
                'PPLG' => [
                    'start' => 'H. Alur Tujuan Pembelajaran PKL PPLG',
                    'end' => 'I. Sistematika Penyusunan Laporan PKL',
                    'program' => 'Pengembangan Perangkat Lunak dan Gim',
                    'konsentrasi' => 'Rekayasa Perangkat Lunak'
                ]
            ];

            $parsedData = [];

            foreach ($sections as $key => $meta) {
                $flexStartPattern = '/' . preg_replace('/\s+/', '\s+', preg_quote($meta['start'], '/')) . '/i';
                if (!preg_match($flexStartPattern, $pdfText, $startMatches, PREG_OFFSET_CAPTURE, 15000)) {
                    continue;
                }
                $startPos = $startMatches[0][1];
                
                $flexEndPattern = '/' . preg_replace('/\s+/', '\s+', preg_quote($meta['end'], '/')) . '/i';
                if (!preg_match($flexEndPattern, $pdfText, $endMatches, PREG_OFFSET_CAPTURE, $startPos)) {
                    $sectionText = substr($pdfText, $startPos);
                } else {
                    $endPos = $endMatches[0][1];
                    $sectionText = substr($pdfText, $startPos, $endPos - $startPos);
                }
                
                $sectionText = preg_replace('/--- PAGE \d+ ---/i', '', $sectionText);
                $lines = explode("\n", $sectionText);
                $cleanedLines = [];
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if ($trimmed === '') continue;
                    if (is_numeric($trimmed)) continue;
                    if (stripos($trimmed, 'Elemen Capaian Pembelajaran') !== false) continue;
                    if (stripos($trimmed, 'ALUR TUJUAN PEMBELAJARAN') !== false) continue;
                    if (stripos($trimmed, 'Mata Pelajaran') !== false) continue;
                    if (stripos($trimmed, 'Program Keahlian') !== false) continue;
                    if (stripos($trimmed, 'Konsentrasi Keahlian') !== false) continue;
                    $cleanedLines[] = $trimmed;
                }
                
                $elemenBlocks = [];
                $currentElemen = null;
                $elemenTextAccumulator = [];
                
                $totalCleaned = count($cleanedLines);
                for ($idx = 0; $idx < $totalCleaned; $idx++) {
                    $line = $cleanedLines[$idx];
                    $lookaheadArray = array_slice($cleanedLines, $idx, 4);
                    $lookahead = implode(' ', $lookaheadArray);
                    
                    $isElemenHeader = false;
                    $detectedElemen = '';
                    
                    if (preg_match('/Internalisasi\s+dan\s+penerapan\s+soft\s+skills/i', $lookahead)) {
                        $isElemenHeader = true;
                        $detectedElemen = 'Internalisasi dan penerapan soft skills';
                    } elseif (preg_match('/Penerapan\s+hard\s+skills/i', $lookahead)) {
                        $isElemenHeader = true;
                        $detectedElemen = 'Penerapan hard skills';
                    } elseif (preg_match('/Peningkatan\s+dan\s+pengembangan\s+hard\s+skills/i', $lookahead)) {
                        $isElemenHeader = true;
                        $detectedElemen = 'Peningkatan dan pengembangan hard skills';
                    } elseif (preg_match('/Penyiapan\s+Kemandirian\s+Berwirausaha/i', $lookahead)) {
                        $isElemenHeader = true;
                        $detectedElemen = 'Penyiapan Kemandirian Berwirausaha';
                    }
                    
                    if ($isElemenHeader) {
                        $words = explode(' ', strtolower($detectedElemen));
                        $span = 0;
                        foreach ($lookaheadArray as $lItem) {
                            $lItemLower = strtolower($lItem);
                            $hasWord = false;
                            foreach ($words as $w) {
                                if (strpos($lItemLower, $w) !== false) {
                                    $hasWord = true;
                                }
                            }
                            if ($hasWord) {
                                $span++;
                            } else {
                                break;
                            }
                        }
                        $skipLinesCount = max(0, $span - 1);
                        
                        if ($currentElemen !== null) {
                            $elemenBlocks[$currentElemen] = $elemenTextAccumulator;
                        }
                        $currentElemen = $detectedElemen;
                        $elemenTextAccumulator = [];
                        
                        $idx += $skipLinesCount;
                    } else {
                        if ($currentElemen !== null) {
                            if (in_array(strtolower(trim($line)), ['penerapan soft', 'skills', 'dan penerapan', 'kemandirian', 'berwirausaha', 'dan', 'pengembangan'])) {
                                continue;
                            }
                            $elemenTextAccumulator[] = $line;
                        }
                    }
                }
                if ($currentElemen !== null) {
                    $elemenBlocks[$currentElemen] = $elemenTextAccumulator;
                }
                
                $parsedData[$key] = [
                    'program' => $meta['program'],
                    'konsentrasi' => $meta['konsentrasi'],
                    'elemens' => []
                ];
                
                foreach ($elemenBlocks as $elemenName => $blockLines) {
                    $cpLines = [];
                    $tpLinesRaw = [];
                    
                    foreach ($blockLines as $line) {
                        if (preg_match('/^\s*\d+(\.\d+)?\./', $line)) {
                            $tpLinesRaw[] = $line;
                        } else {
                            if (empty($tpLinesRaw)) {
                                if (stripos($line, 'Capaian Pembelajaran') !== false) continue;
                                $cpLines[] = $line;
                            } else {
                                $tpLinesRaw[count($tpLinesRaw) - 1] .= ' ' . $line;
                            }
                        }
                    }
                    
                    $cp = implode(' ', $cpLines);
                    $cp = preg_replace('/\s+/', ' ', $cp);
                    
                    $tps = [];
                    foreach ($tpLinesRaw as $tpLine) {
                        $tpLine = preg_replace('/\s+/', ' ', $tpLine);
                        $tps[] = trim($tpLine);
                    }
                    
                    $uniqueTps = [];
                    $seenTexts = [];
                    
                    foreach ($tps as $tp) {
                        $textOnly = preg_replace('/^\s*\d+(\.\d+)?\.\s*/', '', $tp);
                        $textOnlyClean = trim($textOnly);
                        $textOnlyLower = strtolower(preg_replace('/[^a-z0-9]/', '', $textOnlyClean));
                        
                        if ($textOnlyLower === '') continue;
                        
                        $isDuplicate = false;
                        foreach ($seenTexts as $seen) {
                            if ($seen == $textOnlyLower || levenshtein($seen, $textOnlyLower) < 4 || strpos($seen, $textOnlyLower) !== false) {
                                $isDuplicate = true;
                                break;
                            }
                        }
                        
                        if (!$isDuplicate) {
                            $uniqueTps[] = trim($tp);
                            $seenTexts[] = $textOnlyLower;
                        }
                    }
                    
                    // Filter out short substring duplicates where one TP is a prefix of another
                    $filteredTps = [];
                    $countUnique = count($uniqueTps);
                    for ($i = 0; $i < $countUnique; $i++) {
                        $tpA = $uniqueTps[$i];
                        $textA = strtolower(preg_replace('/[^a-z0-9]/', '', preg_replace('/^\s*\d+(\.\d+)?\.\s*/', '', $tpA)));
                        $isSubstring = false;
                        for ($j = 0; $j < $countUnique; $j++) {
                            if ($i === $j) continue;
                            $tpB = $uniqueTps[$j];
                            $textB = strtolower(preg_replace('/[^a-z0-9]/', '', preg_replace('/^\s*\d+(\.\d+)?\.\s*/', '', $tpB)));
                            if (strlen($textA) < strlen($textB) && strpos($textB, $textA) === 0) {
                                $isSubstring = true;
                                break;
                            }
                        }
                        if (!$isSubstring) {
                            $filteredTps[] = $tpA;
                        }
                    }
                    
                    $parsedData[$key]['elemens'][] = [
                        'nama' => $elemenName,
                        'cp' => $cp,
                        'tps' => $filteredTps
                    ];
                }
            }

            $concentrations = KonsentrasiKeahlian::with('programKeahlian')->get();

            foreach ($parsedData as $key => &$section) {
                $section['mapped_id'] = null;
                $bestScore = 0;
                
                $targetName = $section['konsentrasi'];
                if ($key === 'AKL') $targetName = 'Akuntansi';
                if ($key === 'Pemasaran') $targetName = 'Pemasaran Ritel';
                if ($key === 'MPLB') $targetName = 'Managemen Bisnis';
                if ($key === 'PPLG') $targetName = 'Pengembangan Perangkat Lunak dan Gim';
                
                foreach ($concentrations as $con) {
                    if (strcasecmp($con->nama, $targetName) === 0 || strcasecmp($con->nama, $section['konsentrasi']) === 0) {
                        $section['mapped_id'] = $con->id;
                        break;
                    }
                    
                    similar_text(strtolower($con->nama), strtolower($targetName), $percent);
                    if ($percent > $bestScore && $percent > 50) {
                        $bestScore = $percent;
                        $section['mapped_id'] = $con->id;
                    }
                }
            }

            session(['parsed_pdf_data' => $parsedData]);
            return redirect()->route('pokja.kompetensi.import-pdf.preview');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file PDF: ' . $e->getMessage());
        }
    }

    public function showImportPdfPreview()
    {
        $parsedData = session('parsed_pdf_data');
        if (empty($parsedData)) {
            return redirect()->route('pokja.kompetensi.import-pdf.form')
                ->with('error', 'Silakan unggah atau pindai file pedoman PKL terlebih dahulu.');
        }

        $concentrations = KonsentrasiKeahlian::with('programKeahlian')->get();

        return view('pokja.kompetensi.import_pdf_preview', compact('parsedData', 'concentrations'));
    }

    public function storeImportPdf(Request $request)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.konsentrasi_keahlian_id' => 'required|exists:konsentrasi_keahlians,id',
            'sections.*.elemens' => 'required|array',
        ]);

        $clearOld = $request->boolean('clear_old');
        $importCount = 0;

        DB::beginTransaction();
        try {
            foreach ($request->sections as $sectionIndex => $sectionData) {
                // Only import checked sections
                if (!isset($sectionData['import'])) {
                    continue;
                }

                $konsentrasiId = $sectionData['konsentrasi_keahlian_id'];

                if ($clearOld) {
                    Kompetensi::where('konsentrasi_keahlian_id', $konsentrasiId)->delete();
                }

                foreach ($sectionData['elemens'] as $elemenIndex => $elemenData) {
                    $elemenNama = $elemenData['nama'];
                    $cpText = $elemenData['cp'] ?? null;
                    $tpsRaw = $elemenData['tps'] ?? [];

                    // Extract selected and edited TPs
                    $tps = [];
                    foreach ($tpsRaw as $tpIndex => $tpItem) {
                        if (isset($tpItem['selected']) && !empty($tpItem['text'])) {
                            $tps[] = $tpItem['text'];
                        }
                    }

                    $category = 'teknis';
                    $elemenLower = strtolower($elemenNama);
                    if (strpos($elemenLower, 'soft') !== false) {
                        $category = 'soft-skill';
                    } elseif (strpos($elemenLower, 'wirausaha') !== false) {
                        $category = 'wirausaha';
                    } elseif (strpos($elemenLower, 'peningkatan') !== false) {
                        $category = 'pengembangan';
                    }

                    if (!empty($tps)) {
                        foreach ($tps as $tpText) {
                            Kompetensi::create([
                                'konsentrasi_keahlian_id' => $konsentrasiId,
                                'nama' => $elemenNama,
                                'kategori' => $category,
                                'cp' => $cpText,
                                'tp' => $tpText,
                                'deskripsi' => $cpText,
                            ]);
                            $importCount++;
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('pokja.kompetensi.index')
                ->with('success', "Berhasil mengimpor {$importCount} Tujuan Pembelajaran ke database.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data kompetensi: ' . $e->getMessage())->withInput();
        }
    }
}
