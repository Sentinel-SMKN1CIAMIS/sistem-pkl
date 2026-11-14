<?php

namespace App\Services;

use App\Models\Dudi;
use App\Models\KonsentrasiKeahlian;
use App\Models\PembimbingSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PemetaanImportService
{
    /**
     * Daftar 70 guru resmi SMKN 1 Ciamis sebagai kamus acuan kanonikal.
     */
    protected array $canonicalTeachers = [
        'Acep Muhammad Soleh, S.Pd.',
        'Agung Sofiyani',
        'Ai Siti Munawaroh',
        'Alisah',
        'Ane Maryani',
        'Angga Febrian Pratama',
        'Aning Nurganah',
        'Anis Kurly',
        'Asep Cahrina',
        'Atin Herawati',
        'Atin Kudriatin',
        'Befi Apriansyah',
        'Carkim',
        'Cucu Cahyani',
        'Cucu Hermawan',
        'Dadi Rusyadi',
        'Deasy Putri Lestari',
        'Dedeh Kurniasih',
        'Dela Hikmatul Ambia',
        'Deslita',
        'Desty Sobaryantini',
        'Devi Rodiana',
        'Dewi Rosita',
        'Dhinda Aghita Mahardika',
        'Dini Apriani Nurramdan',
        'Dini Yudi Kasimamora',
        'Elin Karlinah',
        'Ervin Maulana Herdiansyah',
        'Farik Samsul Patoni',
        'Hariman Hendriana',
        'Harini',
        'Herna Novitasari',
        'Iip Masripah',
        'Ika Hasanah',
        'Ikhsan Nur Rokhmat',
        'Imas Masriah',
        'Iqbal Fauzi Lisyanto',
        'Irfan Nur Muttaqin',
        'Irma Sukmarini',
        'Julia Meliani Rosadi',
        'Kiki Supendi',
        'Mahani Gunawan',
        'Maulina Fajrin',
        'Meta Nur Mawaty',
        'Miming Miptahudin',
        'Nanang Aziz Supriadi',
        'Neni Rahmawati',
        'Neri Sondari',
        'Nina',
        'Oneng Dalilah',
        'Pebi Dinastriani',
        'Pia Amanda Nurhusni',
        'Ratna Nur Indah Sari',
        'Sahri Agustian',
        'Sintiya Nuri Rosmalia',
        'Siti Nurasyiah',
        'Siti Qomariyah',
        'Sri Yuliani',
        'Suci Azmiyati',
        'Teti Sri Mulyati Hidayat',
        'Tika Mustikawati',
        'Tita Puspita',
        'Wiana Yulian',
        'Wiliandini',
        'Yana Hendrayana',
        'Yana Soviyana',
        'Yati Setiawati',
        'Yeni Maryani',
        'Yeni Mulyaningsih',
        'Yusef Abdul Aziz',
    ];

    /**
     * Membersihkan nama guru dari angka urut dan gelar.
     */
    public function cleanTeacherName(string $raw): string
    {
        // Hapus angka urut di depan: "1. Kiki Supendi", "14. Yeni Mulyaningsih"
        $clean = preg_replace('/^\d+[\.\s]*/', '', trim($raw));
        // Hapus gelar akademik di belakang
        $clean = preg_replace('/,\s*(S\.Pd|M\.Pd|S\.Tr\.Par|S\.Par|S\.Kom|M\.Kom|S\.T|M\.T|S\.E|M\.M|B\.Sc|M\.Sc).*$/i', '', $clean);
        // Hapus gelar di depan (Hj., H., Drs., Dra.)
        $clean = preg_replace('/\b(Hj\.|H\.|Drs\.|Dra\.)\s*/i', '', $clean);
        // Hapus tanda kutip dan koma liar
        $clean = str_replace(['"', "'"], '', $clean);
        return trim($clean, " \t\n\r\0\x0B,.");
    }

    /**
     * Resolusi nama pembimbing dengan aturan Kata 1 dan Kata 2 (Disambiguasi Cerdas).
     */
    public function resolveCanonicalTeacher(string $rawTeacher): string
    {
        $rawClean = $this->cleanTeacherName($rawTeacher);
        if (empty($rawClean)) {
            return '';
        }

        // Variasi khusus ejaan: Bepi -> Befi Apriansyah / Bepi Apriansyah
        if (stripos($rawClean, 'Bepi') !== false) {
            return 'Bepi Apriansyah';
        }

        $rawWords = preg_split('/\s+/', $rawClean);
        $firstWord = strtolower($rawWords[0] ?? '');
        $secondWord = strtolower($rawWords[1] ?? '');

        // Buat index pencarian guru
        $candidates = [];
        foreach ($this->canonicalTeachers as $canonical) {
            $cleaned = $this->cleanTeacherName($canonical);
            if (strcasecmp($rawClean, $cleaned) === 0) {
                return $canonical;
            }

            $cWords = preg_split('/\s+/', $cleaned);
            $cFirst = strtolower($cWords[0] ?? '');
            $cSecond = strtolower($cWords[1] ?? '');

            if ($cFirst === $firstWord) {
                $candidates[] = [
                    'canonical' => $canonical,
                    'cleaned' => $cleaned,
                    'first' => $cFirst,
                    'second' => $cSecond,
                ];
            }
        }

        // Jika hanya ada 1 guru dengan nama depan tersebut di sekolah
        if (count($candidates) === 1) {
            return $candidates[0]['canonical'];
        }

        // Jika ada lebih dari 1 guru dengan nama depan yang sama (misal: "Yana Hendrayana" vs "Yana Soviyana")
        if (count($candidates) > 1) {
            if (!empty($secondWord)) {
                // 1. Cocokkan kata kedua persis
                foreach ($candidates as $c) {
                    if ($c['second'] === $secondWord) {
                        return $c['canonical'];
                    }
                }
                // 2. Cocokkan inisial kata kedua (contoh: "Yana H" -> "Yana Hendrayana")
                foreach ($candidates as $c) {
                    if (str_starts_with($c['second'], $secondWord)) {
                        return $c['canonical'];
                    }
                }
            }
        }

        // Substring / no-space match fallback (contoh: "Dini Yudikasimamora" vs "Dini Yudi Kasimamora")
        $rNoSpace = str_replace(' ', '', strtolower($rawClean));
        foreach ($this->canonicalTeachers as $canonical) {
            $cClean = $this->cleanTeacherName($canonical);
            $cNoSpace = str_replace(' ', '', strtolower($cClean));
            if ($cNoSpace === $rNoSpace || str_starts_with($cNoSpace, $rNoSpace) || str_starts_with($rNoSpace, $cNoSpace)) {
                return $canonical;
            }
        }

        return $rawClean;
    }

    /**
     * Membersihkan nama DUDI dari nomor urut dan karakter pembuka.
     */
    public function cleanDudiName(string $raw): string
    {
        $clean = preg_replace('/^\d+[\.\s]*/', '', trim($raw));
        $clean = trim($clean, " \t\n\r\0\x0B,\"");
        return $clean;
    }

    /**
     * Deteksi kode jurusan / program keahlian dari nama kelas.
     */
    public function detectProgramKeahlian(string $kelas): string
    {
        $k = strtoupper(trim($kelas));

        if (str_contains($k, 'PPLG') || str_contains($k, 'RPL')) {
            return 'PPLG';
        }
        if (str_contains($k, 'DKV') || str_contains($k, 'MM')) {
            return 'DKV';
        }
        if (str_contains($k, 'KLN') || str_contains($k, 'BOGA') || str_contains($k, 'TBG')) {
            return 'KLN';
        }
        if (str_contains($k, 'HTL') || str_contains($k, 'HOTEL') || str_contains($k, 'APH')) {
            return 'HTL';
        }
        if (str_contains($k, 'PBD') || str_contains($k, 'DIGITAL')) {
            return 'PBD';
        }
        if (str_contains($k, 'PBR') || str_contains($k, 'RITEL') || str_contains($k, 'PM')) {
            return 'PM';
        }
        if (str_contains($k, 'MPLB') || str_contains($k, 'MP') || str_contains($k, 'OTKP')) {
            return 'MPLB';
        }
        if (str_contains($k, 'PBS') || str_contains($k, 'SYARIAH')) {
            return 'PBS';
        }
        if (str_contains($k, 'AKL') || str_contains($k, 'AK')) {
            return 'AKL';
        }

        return 'LAINNYA';
    }

    /**
     * Membaca dan mem-parsing berkas (Excel .xlsx / .xls maupun .csv) menjadi array item terstruktur.
     */
    public function parseFile(string $filePath, array $allowedJurusan = ['AKL', 'PBS', 'MPLB', 'PM', 'PBD', 'KLN', 'HTL', 'PPLG'], ?string $extension = null): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Berkas tidak ditemukan: {$filePath}");
        }

        $ext = strtolower($extension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($ext, ['xlsx', 'xls', 'ods'])) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            return $this->parseRows($rows, $allowedJurusan);
        }

        return $this->parseCsv($filePath, $allowedJurusan);
    }

    /**
     * Membaca dan mem-parsing baris CSV menjadi array item terstruktur.
     */
    public function parseCsv(string $csvContentOrPath, array $allowedJurusan = ['AKL', 'PBS', 'MPLB', 'PM', 'PBD', 'KLN', 'HTL', 'PPLG']): array
    {
        if (file_exists($csvContentOrPath)) {
            $ext = strtolower(pathinfo($csvContentOrPath, PATHINFO_EXTENSION));
            if (in_array($ext, ['xlsx', 'xls', 'ods'])) {
                return $this->parseFile($csvContentOrPath, $allowedJurusan, $ext);
            }

            $rows = [];
            $handle = fopen($csvContentOrPath, 'r');
            if ($handle !== false) {
                while (($cols = fgetcsv($handle)) !== false) {
                    $rows[] = $cols;
                }
                fclose($handle);
            }
            return $this->parseRows($rows, $allowedJurusan);
        }

        $lines = preg_split('/\r\n|\r|\n/', $csvContentOrPath);
        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $rows[] = str_getcsv($line);
            }
        }
        return $this->parseRows($rows, $allowedJurusan);
    }

    /**
     * Parsing baris-baris array terstruktur (dari spreadsheet atau fgetcsv).
     */
    public function parseRows(array $rows, array $allowedJurusan = ['AKL', 'PBS', 'MPLB', 'PM', 'PBD', 'KLN', 'HTL', 'PPLG']): array
    {
        $currentWilayah = '';
        $currentPembimbing = '';
        $currentDudi = '';
        $parsedItems = [];
        $skippedCount = 0;
        $headerFound = false;

        foreach ($rows as $cols) {
            if (!is_array($cols) || empty($cols)) {
                continue;
            }

            $cols = array_map(fn($v) => is_null($v) ? '' : trim((string)$v), $cols);
            $lineText = implode(' ', array_filter($cols, fn($v) => $v !== ''));
            if (empty($lineText)) {
                continue;
            }

            // Berhenti jika sudah mencapai tabel rekapitulasi di bawah
            if (stripos($lineText, 'PEMETAAN BIMBINGAN SISWA PKL') !== false) {
                break;
            }

            // Deteksi baris header secara dinamis
            if (!$headerFound) {
                if (
                    stripos($lineText, 'Nama Pembimbing') !== false ||
                    stripos($lineText, 'NAMA SISWA') !== false ||
                    (isset($cols[0]) && strtolower($cols[0]) === 'no')
                ) {
                    $headerFound = true;
                }
                continue;
            }

            // Lewati baris subheader jurusan jika tidak ada nama siswa (contoh: ,,,,,,AK,PBS,PM...)
            if (empty($cols[0]) && empty($cols[1]) && empty($cols[2]) && empty($cols[4])) {
                continue;
            }

            $w = $cols[1] ?? '';
            $p = $cols[2] ?? '';
            $d = $cols[3] ?? '';
            $s = $cols[4] ?? '';
            $k = $cols[5] ?? '';

            // Logika fill-down
            if (!empty($w)) $currentWilayah = $w;
            if (!empty($p)) $currentPembimbing = $p;
            if (!empty($d)) $currentDudi = $d;

            if (empty($s) || strtoupper($s) === 'NAMA SISWA' || empty($k)) {
                continue;
            }

            $prog = $this->detectProgramKeahlian($k);

            // Smart filter: Jika jurusan tidak diizinkan (misal DKV), lewati!
            if (!in_array($prog, $allowedJurusan) && !in_array($this->mapToBroadJurusan($prog), $allowedJurusan)) {
                $skippedCount++;
                continue;
            }

            $cleanDudi = $this->cleanDudiName($currentDudi);
            $canonicalTeacher = $this->resolveCanonicalTeacher($currentPembimbing);

            $parsedItems[] = [
                'wilayah' => $currentWilayah ?: 'Ciamis',
                'pembimbing_raw' => $currentPembimbing,
                'pembimbing_canonical' => $canonicalTeacher,
                'dudi_raw' => $currentDudi,
                'dudi_name' => $cleanDudi,
                'siswa_nama' => $s,
                'kelas' => $k,
                'program' => $prog,
            ];
        }

        return [
            'items' => $parsedItems,
            'skipped_count' => $skippedCount,
            'total_parsed' => count($parsedItems),
        ];
    }

    /**
     * Pencarian siswa cerdas: Exact match, Prefix/Truncated name, & Toleransi Typo (Similar Text).
     */
    public function findStudent(string $studentName, ?string $kelas, $allStudents)
    {
        $cleanSearch = strtolower(trim(preg_replace('/\s+/', ' ', $studentName)));
        if (empty($cleanSearch)) {
            return null;
        }

        // 1. Exact match
        if ($allStudents->has($cleanSearch)) {
            return $allStudents->get($cleanSearch);
        }

        // 2. Prefix match & First 2 Words match (Contoh: "Syaila Nadira" mencocokkan "Syaila Nadira Azzahra Kurniawan")
        $searchWords = explode(' ', $cleanSearch);
        $searchTwoWords = implode(' ', array_slice($searchWords, 0, 2));

        foreach ($allStudents as $candidateKey => $siswa) {
            if (str_starts_with($candidateKey, $cleanSearch) || str_starts_with($cleanSearch, $candidateKey)) {
                return $siswa;
            }
            if (count($searchWords) >= 2 && str_starts_with($candidateKey, $searchTwoWords)) {
                return $siswa;
            }
        }

        // 3. Similar text / Typo tolerance (Contoh: "Citra Siti Wahyni" vs "Citra Siti Wahyuni")
        $bestMatch = null;
        $highestSim = 0;

        foreach ($allStudents as $candidateKey => $siswa) {
            // Huruf pertama harus sama untuk efisiensi dan akurasi
            if (empty($candidateKey) || $candidateKey[0] !== $cleanSearch[0]) {
                continue;
            }

            similar_text($cleanSearch, $candidateKey, $percent);
            if ($percent > 85 && $percent > $highestSim) {
                $highestSim = $percent;
                $bestMatch = $siswa;
            }
        }

        return $bestMatch;
    }

    /**
     * Map spesifik kode ke grup program keahlian standar
     */
    protected function mapToBroadJurusan(string $prog): string
    {
        if ($prog === 'PBS') return 'AKL';
        if ($prog === 'PBD') return 'PM';
        return $prog;
    }

    /**
     * Eksekusi impor pemetaan ke dalam database.
     */
    public function executeImport(array $items, array $options = []): array
    {
        $defaultPassword = $options['default_password'] ?? 'smkn1ciamis';

        // Preload database collections untuk kecepatan maksimal (O(1) Memory Lookup, zero N+1)
        $existingUsers = User::where('role', 'pembimbing_sekolah')->with('pembimbingSekolah')->get()->keyBy(function($u) {
            return strtolower(trim($u->name));
        });

        $existingTeachers = PembimbingSekolah::all()->keyBy(function($p) {
            return strtolower(trim($p->nama_lengkap));
        });

        $existingDudis = Dudi::all()->keyBy(function($d) {
            return strtolower(trim($d->nama));
        });

        $allStudents = Siswa::with('konsentrasiKeahlian')->get()->keyBy(function($s) {
            return strtolower(trim($s->nama_lengkap));
        });

        $allKonsentrasi = KonsentrasiKeahlian::all();

        $stats = [
            'pembimbing_created' => 0,
            'pembimbing_matched' => 0,
            'dudi_created' => 0,
            'dudi_matched' => 0,
            'siswa_mapped' => 0,
            'siswa_not_found' => [],
        ];

        DB::transaction(function () use ($items, $defaultPassword, &$stats, &$existingUsers, &$existingTeachers, &$existingDudis, &$allStudents, $allKonsentrasi) {
            foreach ($items as $item) {
                $teacherName = $item['pembimbing_canonical'];
                $dudiName = $item['dudi_name'];
                $studentName = $item['siswa_nama'];
                $wilayah = $item['wilayah'];

                if (empty($teacherName) || empty($dudiName)) {
                    continue;
                }

                $teacherKey = strtolower(trim($teacherName));
                $dudiKey = strtolower(trim($dudiName));
                $studentKey = strtolower(trim($studentName));

                // 1. RESOLUSI / PEMBUATAN AKUN PEMBIMBING SEKOLAH
                $pembimbingSekolah = $existingTeachers->get($teacherKey);

                if (!$pembimbingSekolah) {
                    $cleanTeacher = $this->cleanTeacherName($teacherName);
                    $username = Str::slug($cleanTeacher, '.');
                    // Pastikan username unik
                    $baseUsername = $username;
                    $counter = 1;
                    while (User::where('username', $username)->exists()) {
                        $username = $baseUsername . $counter;
                        $counter++;
                    }

                    $user = User::create([
                        'name' => $cleanTeacher,
                        'username' => $username,
                        'email' => $username . '@smkn1ciamis.sch.id',
                        'password' => $defaultPassword, // di-hash otomatis via cast 'password' => 'hashed'
                        'role' => 'pembimbing_sekolah',
                        'is_active' => true,
                        'force_password_change' => true,
                    ]);

                    $pembimbingSekolah = PembimbingSekolah::create([
                        'user_id' => $user->id,
                        'nama_lengkap' => $cleanTeacher,
                        'tipe' => 'kejuruan',
                    ]);

                    $existingTeachers->put($teacherKey, $pembimbingSekolah);
                    $existingUsers->put($teacherKey, $user);
                    $stats['pembimbing_created']++;
                } else {
                    $stats['pembimbing_matched']++;
                }

                // 2. PEMETAAN SISWA (Pencocokan Cerdas: Eksak, Prefix Nama Panggilan, & Typo Tolerant)
                $siswa = $this->findStudent($studentName, $item['kelas'], $allStudents);

                // 3. RESOLUSI / PEMBUATAN MASTER DUDI
                $dudi = $existingDudis->get($dudiKey);
                if (!$dudi) {
                    $konsentrasiId = $siswa ? $siswa->konsentrasi_keahlian_id : ($allKonsentrasi->first()?->id ?? 1);
                    $dudi = Dudi::create([
                        'nama' => $dudiName,
                        'konsentrasi_keahlian_id' => $konsentrasiId,
                        'kota' => $wilayah,
                        'alamat' => $wilayah,
                        'is_active' => true,
                    ]);
                    $existingDudis->put($dudiKey, $dudi);
                    $stats['dudi_created']++;
                } else {
                    $stats['dudi_matched']++;
                }

                if ($siswa) {
                    $siswa->update([
                        'dudi_id' => $dudi->id,
                        'pembimbing_sekolah_id' => $pembimbingSekolah->id,
                        'status_pkl' => 'sedang_pkl',
                    ]);

                    // Hubungkan DUDI dengan Konsentrasi Keahlian siswa jika belum terhubung
                    if ($siswa->konsentrasi_keahlian_id) {
                        $dudi->konsentrasiKeahlians()->syncWithoutDetaching([$siswa->konsentrasi_keahlian_id]);

                        // Jika pembimbing sekolah belum memiliki konsentrasi, set dari siswa
                        if (!$pembimbingSekolah->konsentrasi_keahlian_id) {
                            $pembimbingSekolah->update([
                                'konsentrasi_keahlian_id' => $siswa->konsentrasi_keahlian_id,
                            ]);
                        }
                    }

                    $stats['siswa_mapped']++;
                } else {
                    $stats['siswa_not_found'][] = "{$studentName} ({$item['kelas']})";
                }
            }
        });

        return $stats;
    }
}
