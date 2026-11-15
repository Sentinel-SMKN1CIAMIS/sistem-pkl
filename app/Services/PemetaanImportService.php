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
        'Acep Muhammad Soleh',
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
     * Daftar 97 Guru Resmi SMKN 1 Ciamis beserta NIP resmi masing-masing.
     */
    protected array $officialTeachers = [
        ['name' => 'ACEP MUHAMMAD SOLEH, S.Pd.', 'nip' => '199409152022211004'],
        ['name' => 'AGUNG SOFIYANI, S.E.', 'nip' => '199206122024212036'],
        ['name' => 'AI SITI MUNAWAROH, S.Pd.', 'nip' => '199502082024212017'],
        ['name' => 'ALISAH, S.Pd. HJ.', 'nip' => '197204111996012001'],
        ['name' => 'AMIRUDIN, S.Pd.', 'nip' => '198605012023211012'],
        ['name' => 'ANE MARYANI, S.Pd.', 'nip' => '198703162022212007'],
        ['name' => 'ANGGA FEBRIAN PRATAMA, S.Pd.', 'nip' => '199902022025211076'],
        ['name' => 'ANI HERLIANI, S.Kom.', 'nip' => '198009162014082003'],
        ['name' => 'ANING NURGANAH, S.Pd.', 'nip' => '199711272022212002'],
        ['name' => 'ANIS KURLY, S.Pd.', 'nip' => '199705152024212025'],
        ['name' => 'ARIE IMAM MAULANI, S.Pd.', 'nip' => '198910182024211007'],
        ['name' => 'ARIF ZAPAR SIDIK, ST.', 'nip' => '198909182025211135'],
        ['name' => 'ARINARYANI SURYANI, S.Pd.', 'nip' => '199504052024212034'],
        ['name' => 'ASEP CAHRINA Drs.', 'nip' => '196801231994121006'],
        ['name' => 'ATI SUKMAWATI, S.Pd.', 'nip' => '198806272022212014'],
        ['name' => 'ATIN HERAWATI,  M.Pd. Hj.', 'nip' => '198001192008012006'],
        ['name' => 'ATIN KUDRIATIN, S.Pd.', 'nip' => '197602292006042009'],
        ['name' => 'AZIZ MUSLIM, S.Pd.', 'nip' => '197906232025211006'],
        ['name' => 'BEFI APRIANSYAH, S.Pd.', 'nip' => '199204222025211151'],
        ['name' => 'CAHYAMAN N., S.Pd.', 'nip' => '197301312021211002'],
        ['name' => 'CARKIM, S.Pd.', 'nip' => '197512252014081001'],
        ['name' => 'CUCU CAHYANI, S.Pd.', 'nip' => '198001102008012006'],
        ['name' => 'CUCU HERMAWAN, M.Pd. H.', 'nip' => '197401011997021001'],
        ['name' => 'DADAN SUGIARNA, S.Pd.', 'nip' => '196608091997021001'],
        ['name' => 'DADANG NURDIN Drs.', 'nip' => '196609071994121001'],
        ['name' => 'DADI RUSYADI, S.Pd.', 'nip' => '198104182024211005'],
        ['name' => 'DEASY PUTRI LESTARI, S.Pd.', 'nip' => '198612132022212017'],
        ['name' => 'DEDEH K. S.Pd.,M.Pd.', 'nip' => '197010091996012001'],
        ['name' => 'DELA HIKMATUL AMBIA, S.Pd. Hj.', 'nip' => '199005042025212167'],
        ['name' => 'DESLITA SENIATSANI, S.Pd.', 'nip' => '199112052022212008'],
        ['name' => 'DESTY SOBARYANTINI, S.Pd.', 'nip' => '199112142024212028'],
        ['name' => 'DEVI RODIANA, S.T.', 'nip' => '198712232022211009'],
        ['name' => 'DEWI ROSITA, S.Pd.', 'nip' => '198507132022212025'],
        ['name' => 'DHINDA AGHITA M., S.Pd.', 'nip' => '199508162025212120'],
        ['name' => 'DINI APRIANI NURRAMDAN, S.Pd.', 'nip' => '199004092025212141'],
        ['name' => 'DINI YUDI K, S.Tr.Par.', 'nip' => '199303112020122022'],
        ['name' => 'EGI SAMSUL MU\'ARIF, S.Pd.', 'nip' => '199603122020121014'],
        ['name' => 'ELIN KARLINAH Dra.', 'nip' => '196806051994122006'],
        ['name' => 'ELWIN ADLIAN R., S.Pd.', 'nip' => '199205062023211017'],
        ['name' => 'ENDAH RAHAYU D., S.Pd., M.Pd.', 'nip' => '197207122006042006'],
        ['name' => 'ERVIN MAULANA, A.Md.Par.', 'nip' => '199606272025211116'],
        ['name' => 'FAHMI AGNIYATU RAHMAN, S.Pd.', 'nip' => '199708242022211001'],
        ['name' => 'FARIK SAMSUL PATONI', 'nip' => '198712222023211009'],
        ['name' => 'FITRIA AGUSTINA, S.Pd.', 'nip' => '197908232024212005'],
        ['name' => 'HARIMAN HENDRIANA, S.Pd. H.', 'nip' => '197708292008011002'],
        ['name' => 'HARINI, S.Pd.', 'nip' => '197806052023212018'],
        ['name' => 'HERNA NOVITASARI, S.Pd.', 'nip' => '199506302024212029'],
        ['name' => 'IIP MASRIPAH, S.Ag.', 'nip' => '197408012021212002'],
        ['name' => 'IKA HASANAH, S.Pd', 'nip' => '198601122022212047'],
        ['name' => 'IKHSAN, N.R., S.Pd.,M.Pd.', 'nip' => '197510112008011004'],
        ['name' => 'IMAS DEWI ARIYANTI, S.Kom.', 'nip' => '199707182022212004'],
        ['name' => 'IMAS MAESAROH, S.Pd.', 'nip' => '199609292022212018'],
        ['name' => 'IMAS MASRIAH, S.Pd.', 'nip' => '197903102006042025'],
        ['name' => 'IQBAL FAUZI LISYANTO, S.Par.', 'nip' => '199610022025211081'],
        ['name' => 'IRFAN NUR MUTTAQIN, S.Pd.I.', 'nip' => '199305202023211006'],
        ['name' => 'IRMA SUKMARINI S.Pd.', 'nip' => '198212102009012007'],
        ['name' => 'JAJANG IKBAL H., S.Pd.', 'nip' => '199602022025211150'],
        ['name' => 'JULIA MELIANI ROSADI, S.Pd.', 'nip' => '199505262023212023'],
        ['name' => 'KIKI SUPENDI, MT.', 'nip' => '197701202009011007'],
        ['name' => 'MAHANI GUNAWAN, S.Pd.', 'nip' => '199511012022212012'],
        ['name' => 'MAMAN NUROHMAN, S.Pd.', 'nip' => '198508132024211006'],
        ['name' => 'MAULINA FAJRIN, S.Par.', 'nip' => '199806252025212074'],
        ['name' => 'META NUR MAWATY, S.Pd.', 'nip' => '198503072024212009'],
        ['name' => 'MIMING MIPTAHUDIN, S.Pd.', 'nip' => '198010262025211052'],
        ['name' => 'MUHAMAD AFRIZAL, S.Pd.', 'nip' => '199704272024211007'],
        ['name' => 'NANANG AZIS S. S.Pd.,M.Pd. H.', 'nip' => '196704281990031002'],
        ['name' => 'NASTITI, S.Pd.', 'nip' => '198909242014012001'],
        ['name' => 'NENI RAHMAWATI, S.Pd.', 'nip' => '199303072022212009'],
        ['name' => 'NERI SONDARI, S.Pd.', 'nip' => '199501262022212017'],
        ['name' => 'NINA, S.Pd.', 'nip' => '198609232025212079'],
        ['name' => 'ONENG DALILAH, M.Pd.', 'nip' => '196706221991032006'],
        ['name' => 'PEBI DINASTRIANI, S.Pd.', 'nip' => '198802052011012004'],
        ['name' => 'PIA AMANDA NURHUSNI, S.Pd.', 'nip' => '198812292022212007'],
        ['name' => 'RANTI PURNAMA DEWI, S.Pd.', 'nip' => '198909132019032014'],
        ['name' => 'RATNA NUR INDAH SARI, S.Pd.', 'nip' => '198709112023212031'],
        ['name' => 'RIRIS SRI BUDIYANTI, S.Pd.', 'nip' => '197405042024212003'],
        ['name' => 'SAHRI AGUSTIAN, S.Pd.I', 'nip' => '198303072023211002'],
        ['name' => 'SINTIYA NURI ROSMALIA, S.Pd', 'nip' => '199610102022212009'],
        ['name' => 'SITI NURASYIAH, S.Pd.', 'nip' => '199506162025212010'],
        ['name' => 'SITI QOMARIYAH, S.Pd.', 'nip' => '198405252022212026'],
        ['name' => 'SRI YULIANI, S.Pd.', 'nip' => '199007142022212013'],
        ['name' => 'SUCI AZMIYATI, S.Pd.I.', 'nip' => '198303072023212019'],
        ['name' => 'TENIA OCTAVIANA, S.Pd.', 'nip' => '199210102022212015'],
        ['name' => 'TETI SRI M.H., S.Pd.', 'nip' => '198211152022212018'],
        ['name' => 'TIKA MUSTIKAWATI, M.Pd.', 'nip' => '198202102005012008'],
        ['name' => 'TITA PUSPITA, S.Pd.,M.Pd.', 'nip' => '198102252008012006'],
        ['name' => 'TRESNA YUNIAWATI, S.Pd.', 'nip' => '199008152025212159'],
        ['name' => 'WIANA YULIAN, S.Pd.', 'nip' => '199207152023212039'],
        ['name' => 'WILIANDINI, S.Pd.', 'nip' => '198202022024212016'],
        ['name' => 'YANA HENDRAYANA, S.Pd., M.Pd.', 'nip' => '197105281998021004'],
        ['name' => 'YANA SOVIYANA, SE.', 'nip' => '198104282022211006'],
        ['name' => 'YATI SETIAWATI, S.Ag.', 'nip' => '197608272021212001'],
        ['name' => 'YENI MARYANI. S.Pd.', 'nip' => '197811172024212006'],
        ['name' => 'YENI MULYANINGSIH, S.Si.', 'nip' => '198209122023212017'],
        ['name' => 'YEYET ROHAETI, S.Pd.', 'nip' => '197501032021212005'],
        ['name' => 'YUSEF ABDUL AZIZ, S.Pd.', 'nip' => '198911282022211010'],
        ['name' => 'ZAKIYATUNNISA ISWAHYUDI, S.Pd.', 'nip' => '199202142025212011'],
    ];

    /**
     * Kamus alias manual untuk variasi ejaan, gelar berbeda, atau inisial.
     */
    protected array $manualTeacherAliases = [
        'budi santoso' => '198701012010011001',
        'yana hendayana' => '197105281998021004',
        'ikhsan nur rokhmat' => '197510112008011004',
        'bepi apriansyah' => '199204222025211151',
        'nanang aziz supriadi' => '196704281990031002',
        'dini yudi kasimamora' => '199303112020122022',
        'dhinda aghita mahardika' => '199508162025212120',
        'ervin maulana herdiansyah' => '199606272025211116',
        'dedeh kurniasih' => '197010091996012001',
        'teti sri mulyati hidayat' => '198211152022212018',
        'deslita' => '199112052022212008',
    ];

    /**
     * Mencari NIP guru secara cerdas berdasarkan nama lengkap atau variasinya.
     */
    public function findTeacherNip(string $rawName): ?string
    {
        $clean = strtolower(trim(preg_replace('/,.*$/', '', $rawName)));
        $clean = preg_replace('/\b(drs\.|dra\.|hj\.|h\.|s\.pd|s\.kom|mt|se|s\.ag|s\.si)\b/i', '', $clean);
        $clean = trim(preg_replace('/\s+/', ' ', $clean));

        if (empty($clean)) {
            return null;
        }

        if (isset($this->manualTeacherAliases[$clean])) {
            return $this->manualTeacherAliases[$clean];
        }

        $cleanOfficialList = [];
        foreach ($this->officialTeachers as $off) {
            $nameWithoutTitle = preg_replace('/,.*$/', '', $off['name']);
            $nameWithoutTitle = preg_replace('/\b(Drs\.|Dra\.|Hj\.|H\.|S\.Pd|S\.Kom|MT|SE|S\.Ag|S\.Si).*$/i', '', $nameWithoutTitle);
            $cName = strtolower(trim($nameWithoutTitle, " \t\n\r\0\x0B,.\""));
            $cleanOfficialList[] = [
                'clean_name' => $cName,
                'nip' => $off['nip'],
            ];
            if ($cName === $clean) {
                return $off['nip'];
            }
        }

        $words = explode(' ', $clean);
        $w1 = $words[0] ?? '';
        $w2 = $words[1] ?? '';

        $matches = [];
        foreach ($cleanOfficialList as $off) {
            $offWords = explode(' ', $off['clean_name']);
            $ow1 = $offWords[0] ?? '';
            $ow2 = $offWords[1] ?? '';

            if ($ow1 === $w1) {
                if (empty($w2) || empty($ow2) || $ow2 === $w2 || str_starts_with($ow2, $w2) || str_starts_with($w2, $ow2)) {
                    $matches[] = $off;
                }
            }
        }

        if (count($matches) === 1) {
            return $matches[0]['nip'];
        }

        $noSpaceClean = str_replace([' ', '.', '-'], '', $clean);
        foreach ($cleanOfficialList as $off) {
            $noSpaceOff = str_replace([' ', '.', '-'], '', $off['clean_name']);
            if (str_contains($noSpaceOff, $noSpaceClean) || str_contains($noSpaceClean, $noSpaceOff)) {
                return $off['nip'];
            }
        }

        return null;
    }

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
     * Resolusi nama jurusan / kode konsentrasi keahlian menjadi model KonsentrasiKeahlian.
     */
    public function resolveKonsentrasiKeahlian(?string $rawJurusan): ?\App\Models\KonsentrasiKeahlian
    {
        if (empty($rawJurusan) || trim($rawJurusan) === '-' || trim($rawJurusan) === '0') {
            return null;
        }

        $j = strtoupper(trim($rawJurusan));
        $all = \App\Models\KonsentrasiKeahlian::with('programKeahlian')->get();

        // 1. Exact match kode atau nama
        foreach ($all as $k) {
            if (strcasecmp($k->kode, $j) === 0 || strcasecmp($k->nama, $j) === 0) {
                return $k;
            }
        }

        // 2. Keyword & Alias matching
        if (str_contains($j, 'PPLG') || str_contains($j, 'RPL') || str_contains($j, 'PERANGKAT LUNAK') || str_contains($j, 'GIM')) {
            return $all->firstWhere('kode', 'PPLG');
        }
        if (str_contains($j, 'DKV') || str_contains($j, 'DESAIN') || str_contains($j, 'MULTIMEDIA') || str_contains($j, 'MM')) {
            return $all->firstWhere('kode', 'DKV');
        }
        if (str_contains($j, 'HTL') || str_contains($j, 'HOTEL') || str_contains($j, 'PERHOTELAN') || str_contains($j, 'APH')) {
            return $all->firstWhere('kode', 'HTL');
        }
        if (str_contains($j, 'KLN') || str_contains($j, 'KULINER') || str_contains($j, 'BOGA') || str_contains($j, 'TBG')) {
            return $all->firstWhere('kode', 'KLN');
        }
        if (str_contains($j, 'AKL') || str_contains($j, 'AKUNTANSI') || str_contains($j, 'AK')) {
            return $all->firstWhere('kode', 'AKL');
        }
        if (str_contains($j, 'MPLB') || str_contains($j, 'PERKANTORAN') || str_contains($j, 'OTKP') || str_contains($j, 'MP')) {
            return $all->firstWhere('kode', 'MPLB');
        }
        if (str_contains($j, 'PBS') || str_contains($j, 'PERBANKAN') || str_contains($j, 'SYARIAH')) {
            return $all->firstWhere('kode', 'PBS');
        }
        if (str_contains($j, 'PBD') || str_contains($j, 'BISNIS DIGITAL')) {
            return $all->firstWhere('kode', 'PBD');
        }
        if (str_contains($j, 'PEMASARAN') || str_contains($j, 'RITEL') || str_contains($j, 'PBR') || str_contains($j, 'PM')) {
            return $all->firstWhere('kode', 'PBR') ?: $all->firstWhere('kode', 'PM');
        }

        return null;
    }

    /**
     * Membaca dan mem-parsing berkas (Excel .xlsx / .xls maupun .csv) menjadi array item terstruktur.
     */
    public function parseFile(string $filePath, array $allowedJurusan = ['AKL', 'PBS', 'DKV', 'PPLG', 'KLN', 'MPLB', 'PM', 'PBR', 'PBD', 'HTL'], ?string $extension = null): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Berkas tidak ditemukan: {$filePath}");
        }

        $ext = strtolower($extension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($ext, ['xlsx', 'xls', 'ods'])) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

            // Cari sheet terbaik yang berisi data pemetaan siswa
            $sheetToUse = $spreadsheet->getActiveSheet();
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $title = strtolower($sheet->getTitle());
                if (str_contains($title, 'data') || str_contains($title, 'siswa') || str_contains($title, 'pemetaan') || str_contains($title, 'lengkap')) {
                    $sheetToUse = $sheet;
                    break;
                }
            }

            $rows = $sheetToUse->toArray();
            return $this->parseRows($rows, $allowedJurusan);
        }

        return $this->parseCsv($filePath, $allowedJurusan);
    }

    /**
     * Membaca dan mem-parsing baris CSV menjadi array item terstruktur.
     */
    public function parseCsv(string $csvContentOrPath, array $allowedJurusan = ['AKL', 'PBS', 'DKV', 'PPLG', 'KLN', 'MPLB', 'PM', 'PBR', 'PBD', 'HTL']): array
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
    public function parseRows(array $rows, array $allowedJurusan = ['AKL', 'PBS', 'DKV', 'PPLG', 'KLN', 'MPLB', 'PM', 'PBR', 'PBD', 'HTL']): array
    {
        // 1. Cek apakah ini berkas Master Guru & NIP (contoh: "Nama Lengkap Guru,NIP Guru" atau "Nama,NIP")
        $isGuruNipFormat = false;
        $nameIdx = -1;
        $nipIdx = -1;
        $jurusanIdx = -1;

        foreach ($rows as $cols) {
            if (!is_array($cols) || empty($cols)) continue;
            $lineText = implode(' ', array_filter($cols, fn($v) => $v !== ''));
            // Jika ada kolom nama siswa atau kelas, ini jelas format pemetaan siswa
            if (stripos($lineText, 'NAMA SISWA') !== false || stripos($lineText, 'TEMPAT PKL') !== false || stripos($lineText, 'KELAS') !== false) {
                break;
            }
            if (stripos($lineText, 'nip') !== false) {
                $isGuruNipFormat = true;
                foreach ($cols as $cIdx => $cVal) {
                    $val = strtolower(trim((string)$cVal));
                    if (str_contains($val, 'nip')) {
                        $nipIdx = $cIdx;
                    } elseif (str_contains($val, 'nama') || str_contains($val, 'guru') || str_contains($val, 'pembimbing')) {
                        $nameIdx = $cIdx;
                    } elseif (str_contains($val, 'jurusan') || str_contains($val, 'kaprog') || str_contains($val, 'konsentrasi') || str_contains($val, 'program')) {
                        $jurusanIdx = $cIdx;
                    }
                }
                break;
            }
        }

        if ($isGuruNipFormat) {
            if ($nameIdx === -1) $nameIdx = 0;
            if ($nipIdx === -1) $nipIdx = 1;

            $parsedGuruItems = [];
            foreach ($rows as $cols) {
                if (!is_array($cols) || empty($cols)) continue;
                $cols = array_map(fn($v) => is_null($v) ? '' : trim((string)$v), $cols);
                $lineText = implode(' ', array_filter($cols, fn($v) => $v !== ''));
                if (empty($lineText)) continue;

                // Berhenti jika sudah mencapai tabel rekapitulasi/distribusi di bawah
                if (
                    stripos($lineText, 'Dimensi Distribusi') !== false ||
                    stripos($lineText, 'DISTRIBUSI GURU') !== false ||
                    stripos($lineText, '--- REKAP') !== false ||
                    stripos($lineText, 'TOTAL KESELURUHAN') !== false
                ) {
                    break;
                }

                $rawName = trim($cols[$nameIdx] ?? '');
                $rawNip = trim($cols[$nipIdx] ?? '');
                $rawJurusan = $jurusanIdx !== -1 ? trim($cols[$jurusanIdx] ?? '') : '';

                // Lewati baris header
                if (stripos($rawName, 'Nama') !== false && stripos($rawNip, 'NIP') !== false) {
                    continue;
                }

                $cleanNip = preg_replace('/\D/', '', $rawNip);
                if (!empty($rawName) && (strlen($cleanNip) >= 8 || !empty($rawJurusan))) {
                    $parsedGuruItems[] = [
                        'type' => 'guru_nip',
                        'teacher_name' => $rawName,
                        'nip' => $cleanNip,
                        'jurusan' => $rawJurusan,
                    ];
                }
            }

            return [
                'items' => $parsedGuruItems,
                'skipped_count' => 0,
                'total_parsed' => count($parsedGuruItems),
                'format' => 'guru_nip',
            ];
        }

        $currentWilayah = '';
        $currentPembimbing = '';
        $currentPembimbingUmum = '';
        $currentDudi = '';
        $parsedItems = [];
        $skippedCount = 0;
        $headerFound = false;

        $siswaIdx = -1;
        $kelasIdx = -1;
        $dudiIdx = -1;
        $pembimbingIdx = -1;
        $penyerahanIdx = -1;
        $pembimbingUmumIdx = -1;
        $penarikanIdx = -1;
        $wilayahIdx = -1;
        $durasiIdx = -1;

        foreach ($rows as $cols) {
            if (!is_array($cols) || empty($cols)) {
                continue;
            }

            $cols = array_map(fn($v) => is_null($v) ? '' : trim((string)$v), $cols);
            $lineText = implode(' ', array_filter($cols, fn($v) => $v !== ''));
            if (empty($lineText)) {
                continue;
            }

            // Berhenti jika sudah mencapai tabel rekapitulasi/distribusi di bawah
            if (
                stripos($lineText, 'PEMETAAN BIMBINGAN SISWA PKL') !== false ||
                stripos($lineText, 'Dimensi Distribusi') !== false ||
                stripos($lineText, 'DISTRIBUSI GURU') !== false ||
                stripos($lineText, '--- REKAP') !== false ||
                stripos($lineText, 'TOTAL KESELURUHAN') !== false
            ) {
                break;
            }

            // Deteksi baris header secara dinamis
            if (!$headerFound) {
                $lowerCols = array_map(fn($c) => strtolower(trim((string)$c)), $cols);
                $hasSiswa = false;
                $hasDudi = false;
                $hasKelas = false;
                $hasGuru = false;

                foreach ($lowerCols as $col) {
                    if (str_contains($col, 'siswa') || str_contains($col, 'murid')) $hasSiswa = true;
                    if (str_contains($col, 'dudi') || str_contains($col, 'du/di') || str_contains($col, 'tempat pkl') || str_contains($col, 'perusahaan') || str_contains($col, 'industri') || str_contains($col, 'instansi')) $hasDudi = true;
                    if (str_contains($col, 'pembimbing') || str_contains($col, 'penyerahan') || str_contains($col, 'monitoring') || str_contains($col, 'penarikan')) $hasGuru = true;
                    if (str_contains($col, 'kelas') || str_contains($col, 'rombel')) $hasKelas = true;
                }

                if (
                    ($hasSiswa && ($hasDudi || $hasKelas || $hasGuru)) ||
                    (isset($cols[0]) && strtolower(trim((string)$cols[0])) === 'no' && ($hasDudi || $hasSiswa)) ||
                    stripos($lineText, 'nama pembimbing') !== false ||
                    stripos($lineText, 'nama siswa') !== false
                ) {
                    $headerFound = true;
                    foreach ($lowerCols as $cIdx => $h) {
                        if ($siswaIdx === -1 && (str_contains($h, 'siswa') || str_contains($h, 'murid') || ($h === 'nama' && !str_contains($h, 'guru') && !str_contains($h, 'dudi') && !str_contains($h, 'pembimbing')))) {
                            $siswaIdx = $cIdx;
                        } elseif ($kelasIdx === -1 && (str_contains($h, 'kelas') || str_contains($h, 'rombel'))) {
                            $kelasIdx = $cIdx;
                        } elseif ($dudiIdx === -1 && (str_contains($h, 'dudi') || str_contains($h, 'du/di') || str_contains($h, 'tempat pkl') || str_contains($h, 'perusahaan') || str_contains($h, 'industri') || str_contains($h, 'instansi') || str_contains($h, 'mitra'))) {
                            $dudiIdx = $cIdx;
                        } elseif ($penyerahanIdx === -1 && str_contains($h, 'penyerahan')) {
                            $penyerahanIdx = $cIdx;
                        } elseif ($pembimbingUmumIdx === -1 && (str_contains($h, 'monitoring') || str_contains($h, 'umum'))) {
                            $pembimbingUmumIdx = $cIdx;
                        } elseif ($penarikanIdx === -1 && str_contains($h, 'penarikan')) {
                            $penarikanIdx = $cIdx;
                        } elseif ($pembimbingIdx === -1 && (str_contains($h, 'pembimbing') || str_contains($h, 'guru') || str_contains($h, 'kejuruan'))) {
                            $pembimbingIdx = $cIdx;
                        } elseif ($wilayahIdx === -1 && (str_contains($h, 'wilayah') || str_contains($h, 'kota') || str_contains($h, 'kabupaten') || str_contains($h, 'lokasi'))) {
                            $wilayahIdx = $cIdx;
                        } elseif ($durasiIdx === -1 && str_contains($h, 'durasi')) {
                            $durasiIdx = $cIdx;
                        }
                    }

                    if ($pembimbingIdx === -1) {
                        $pembimbingIdx = $penyerahanIdx !== -1 ? $penyerahanIdx : $penarikanIdx;
                    }
                    continue;
                }
                continue;
            }

            // Ambil data berdasarkan indeks yang terdeteksi secara cerdas
            $s = $siswaIdx >= 0 ? ($cols[$siswaIdx] ?? '') : ($cols[4] ?? '');
            $k = $kelasIdx >= 0 ? ($cols[$kelasIdx] ?? '') : ($cols[5] ?? '');
            $d = $dudiIdx >= 0 ? ($cols[$dudiIdx] ?? '') : ($cols[3] ?? '');
            $p = $pembimbingIdx >= 0 ? ($cols[$pembimbingIdx] ?? '') : '';
            $pu = $pembimbingUmumIdx >= 0 ? ($cols[$pembimbingUmumIdx] ?? '') : '';
            $w = $wilayahIdx >= 0 ? ($cols[$wilayahIdx] ?? '') : '';

            // Lewati baris subheader atau baris rekapitulasi yang tidak memiliki nama siswa valid
            if (empty($s) || strtoupper($s) === 'NAMA SISWA' || strtoupper($s) === 'JUMLAH SISWA' || empty($k) || strtoupper($k) === 'KELAS') {
                continue;
            }

            // Logika fill-down
            if (!empty($w)) $currentWilayah = $w;
            if (!empty($p)) $currentPembimbing = $p;
            if (!empty($pu)) $currentPembimbingUmum = $pu;
            if (!empty($d)) $currentDudi = $d;

            $prog = $this->detectProgramKeahlian($k);

            // Smart filter: Jika jurusan tidak diizinkan, lewati
            if (!empty($allowedJurusan) && !in_array($prog, $allowedJurusan) && !in_array($this->mapToBroadJurusan($prog), $allowedJurusan)) {
                $skippedCount++;
                continue;
            }

            $cleanDudi = $this->cleanDudiName($currentDudi);
            $canonicalTeacher = $this->resolveCanonicalTeacher($currentPembimbing);
            $canonicalTeacherUmum = $this->resolveCanonicalTeacher($currentPembimbingUmum);

            $parsedItems[] = [
                'wilayah' => $currentWilayah ?: 'Ciamis',
                'pembimbing_raw' => $currentPembimbing,
                'pembimbing_canonical' => $canonicalTeacher,
                'pembimbing_umum_raw' => $currentPembimbingUmum,
                'pembimbing_umum_canonical' => $canonicalTeacherUmum,
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
            return strtolower(trim($this->cleanTeacherName($p->nama_lengkap)));
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
                // Penanganan berkas format Master Guru & NIP (beserta Kolom Jurusan/Kaprog)
                if (($item['type'] ?? '') === 'guru_nip') {
                    $rawTeacher = $item['teacher_name'];
                    $nip = $item['nip'];
                    $rawJurusan = $item['jurusan'] ?? null;
                    $konsentrasi = $this->resolveKonsentrasiKeahlian($rawJurusan);

                    $canonicalTeacher = $this->resolveCanonicalTeacher($rawTeacher);
                    $cleanTeacher = $this->cleanTeacherName($canonicalTeacher ?: $rawTeacher);
                    $teacherKey = strtolower(trim($cleanTeacher));

                    // 1. Cari guru berdasarkan memory key
                    $pembimbingSekolah = $existingTeachers->get($teacherKey);

                    // 2. Jika belum ketemu, cari berdasarkan NIP di memory
                    if (!$pembimbingSekolah && !empty($nip)) {
                        $pembimbingSekolah = $existingTeachers->first(fn($t) => !empty($t->nip) && $t->nip === $nip);
                    }

                    // 3. Jika belum ketemu, cari dengan toleransi kemiripan nama
                    if (!$pembimbingSekolah) {
                        foreach ($existingTeachers as $t) {
                            $cName = strtolower($this->cleanTeacherName($t->nama_lengkap));
                            similar_text($cName, $teacherKey, $percent);
                            if ($percent >= 85) {
                                $pembimbingSekolah = $t;
                                break;
                            }
                        }
                    }

                    if ($pembimbingSekolah) {
                        $isTeacherDirty = false;

                        // Sinkronkan NIP guru jika ada dan berbeda
                        if (!empty($nip) && $pembimbingSekolah->nip !== $nip) {
                            $pembimbingSekolah->nip = $nip;
                            $isTeacherDirty = true;
                        }

                        // Sinkronkan Jurusan / Konsentrasi Keahlian jika ada pada berkas
                        if ($konsentrasi) {
                            if ($pembimbingSekolah->konsentrasi_keahlian_id !== $konsentrasi->id) {
                                $pembimbingSekolah->konsentrasi_keahlian_id = $konsentrasi->id;
                                $isTeacherDirty = true;
                            }
                        }

                        if ($isTeacherDirty) {
                            $pembimbingSekolah->saveQuietly();
                        }

                        // Sinkronkan User Akun: Username = NIP, Password Default = NIP, Email = null
                        $user = $pembimbingSekolah->user;
                        if ($user) {
                            $isDirty = false;
                            if (!empty($nip) && $user->username !== $nip) {
                                $conflictUser = User::where('username', $nip)->where('id', '!=', $user->id)->first();
                                if ($conflictUser) {
                                    $conflictUser->username = $conflictUser->username . '_old_' . $conflictUser->id;
                                    $conflictUser->saveQuietly();
                                }
                                $user->username = $nip;
                                $user->email = null;
                                $isDirty = true;
                            }
                            if ($konsentrasi && $user->konsentrasi_keahlian_id !== $konsentrasi->id) {
                                $user->konsentrasi_keahlian_id = $konsentrasi->id;
                                $user->program_keahlian_id = $konsentrasi->program_keahlian_id;
                                $isDirty = true;
                            }
                            if (!empty($nip) && ($user->force_password_change || Hash::check('smkn1ciamis', $user->password) || Hash::check($pembimbingSekolah->nama_lengkap, $user->password))) {
                                $user->password = $nip;
                                $user->force_password_change = true;
                                $isDirty = true;
                            }
                            if ($isDirty) {
                                $user->saveQuietly();
                            }
                        }
                        $stats['pembimbing_matched']++;
                    } else {
                        // Buat Akun Guru Baru dengan Username & Password = NIP (atau slug jika NIP kosong)
                        $username = $nip ?: Str::slug($cleanTeacher, '.');
                        $baseUsername = $username;
                        $counter = 1;
                        while (User::where('username', $username)->exists()) {
                            $username = $baseUsername . '_' . $counter;
                            $counter++;
                        }

                        $user = User::create([
                            'name' => $cleanTeacher,
                            'username' => $username,
                            'email' => null,
                            'password' => $nip ?: $defaultPassword,
                            'role' => 'pembimbing_sekolah',
                            'is_active' => true,
                            'force_password_change' => true,
                            'konsentrasi_keahlian_id' => $konsentrasi ? $konsentrasi->id : null,
                            'program_keahlian_id' => $konsentrasi ? $konsentrasi->program_keahlian_id : null,
                        ]);

                        $pembimbingSekolah = PembimbingSekolah::create([
                            'user_id' => $user->id,
                            'nama_lengkap' => $cleanTeacher,
                            'nip' => $nip ?: null,
                            'konsentrasi_keahlian_id' => $konsentrasi ? $konsentrasi->id : null,
                            'tipe' => 'kejuruan',
                        ]);

                        $existingTeachers->put($teacherKey, $pembimbingSekolah);
                        $existingUsers->put($teacherKey, $user);
                        $stats['pembimbing_created']++;
                    }

                    continue;
                }

                $teacherName = $item['pembimbing_canonical'] ?? '';
                $teacherUmumName = $item['pembimbing_umum_canonical'] ?? '';
                $dudiName = $item['dudi_name'] ?? '';
                $studentName = $item['siswa_nama'] ?? '';
                $wilayah = $item['wilayah'] ?? '';

                if (empty($studentName) || (empty($dudiName) && empty($teacherName) && empty($teacherUmumName))) {
                    continue;
                }

                $dudiKey = strtolower(trim($dudiName));
                $studentKey = strtolower(trim($studentName));

                // 1. RESOLUSI / PEMBUATAN AKUN PEMBIMBING SEKOLAH (KEJURUAN)
                $pembimbingSekolah = null;
                if (!empty($teacherName)) {
                    $cleanTeacher = $this->cleanTeacherName($teacherName);
                    $teacherKey = strtolower(trim($cleanTeacher));
                    $pembimbingSekolah = $existingTeachers->get($teacherKey);
                    $nip = $this->findTeacherNip($teacherName) ?: $this->findTeacherNip($cleanTeacher);

                    // Cari berdasarkan NIP jika belum ketemu lewat nama persis
                    if (!$pembimbingSekolah && !empty($nip)) {
                        $pembimbingSekolah = $existingTeachers->first(fn($t) => !empty($t->nip) && $t->nip === $nip);
                    }

                    // Cari dengan toleransi kemiripan nama
                    if (!$pembimbingSekolah) {
                        foreach ($existingTeachers as $t) {
                            $cName = strtolower($this->cleanTeacherName($t->nama_lengkap));
                            similar_text($cName, $teacherKey, $percent);
                            if ($percent >= 85) {
                                $pembimbingSekolah = $t;
                                break;
                            }
                        }
                    }

                    if (!$pembimbingSekolah) {
                        $user = null;
                        if (!empty($nip)) {
                            $existingUserWithNip = User::where('username', $nip)->first();
                            if ($existingUserWithNip && $existingUserWithNip->role === 'pembimbing_sekolah' && !$existingUserWithNip->pembimbingSekolah) {
                                $user = $existingUserWithNip;
                            } elseif ($existingUserWithNip) {
                                $existingUserWithNip->username = $existingUserWithNip->username . '_old_' . $existingUserWithNip->id;
                                $existingUserWithNip->saveQuietly();
                            }
                        }

                        if (!$user) {
                            $username = $nip ?: Str::slug($cleanTeacher, '.');
                            $baseUsername = $username;
                            $counter = 1;
                            while (User::where('username', $username)->exists()) {
                                $username = $baseUsername . '_' . $counter;
                                $counter++;
                            }

                            $teacherPassword = $nip ?: $defaultPassword;

                            $user = User::create([
                                'name' => $cleanTeacher,
                                'username' => $username,
                                'email' => null,
                                'password' => $teacherPassword,
                                'role' => 'pembimbing_sekolah',
                                'is_active' => true,
                                'force_password_change' => true,
                            ]);
                        }

                        $pembimbingSekolah = PembimbingSekolah::create([
                            'user_id' => $user->id,
                            'nama_lengkap' => $cleanTeacher,
                            'nip' => $nip,
                            'tipe' => 'kejuruan',
                        ]);

                        $existingTeachers->put($teacherKey, $pembimbingSekolah);
                        $existingUsers->put($teacherKey, $user);
                        $stats['pembimbing_created']++;
                    } else {
                        // FITUR PINTAR: Sinkronkan NIP & username tanpa tabrakan duplikat constraint
                        if ($nip) {
                            if ($pembimbingSekolah->nip !== $nip) {
                                $pembimbingSekolah->nip = $nip;
                                $pembimbingSekolah->saveQuietly();
                            }

                            $user = $pembimbingSekolah->user;
                            if ($user) {
                                $isUserDirty = false;
                                if ($user->username !== $nip) {
                                    $conflictUser = User::where('username', $nip)->where('id', '!=', $user->id)->first();
                                    if ($conflictUser) {
                                        $conflictUser->username = $conflictUser->username . '_old_' . $conflictUser->id;
                                        $conflictUser->saveQuietly();
                                    }
                                    $user->username = $nip;
                                    $user->email = null;
                                    $isUserDirty = true;
                                }
                                if ($user->force_password_change || Hash::check('smkn1ciamis', $user->password)) {
                                    $user->password = $nip;
                                    $user->force_password_change = true;
                                    $isUserDirty = true;
                                }
                                if ($isUserDirty) {
                                    $user->saveQuietly();
                                }
                            }
                        }
                        $stats['pembimbing_matched']++;
                    }
                }

                // 2. RESOLUSI / PEMBUATAN AKUN PEMBIMBING SEKOLAH UMUM (MONITORING)
                $pembimbingSekolahUmum = null;
                if (!empty($teacherUmumName)) {
                    $cleanTeacherUmum = $this->cleanTeacherName($teacherUmumName);
                    $teacherUmumKey = strtolower(trim($cleanTeacherUmum));
                    $pembimbingSekolahUmum = $existingTeachers->get($teacherUmumKey);
                    $nipUmum = $this->findTeacherNip($teacherUmumName) ?: $this->findTeacherNip($cleanTeacherUmum);

                    // Cari berdasarkan NIP jika belum ketemu lewat nama
                    if (!$pembimbingSekolahUmum && !empty($nipUmum)) {
                        $pembimbingSekolahUmum = $existingTeachers->first(fn($t) => !empty($t->nip) && $t->nip === $nipUmum);
                    }

                    // Cari dengan toleransi kemiripan nama
                    if (!$pembimbingSekolahUmum) {
                        foreach ($existingTeachers as $t) {
                            $cName = strtolower($this->cleanTeacherName($t->nama_lengkap));
                            similar_text($cName, $teacherUmumKey, $percent);
                            if ($percent >= 85) {
                                $pembimbingSekolahUmum = $t;
                                break;
                            }
                        }
                    }

                    if (!$pembimbingSekolahUmum) {
                        $userUmum = null;
                        if (!empty($nipUmum)) {
                            $existingUserWithNip = User::where('username', $nipUmum)->first();
                            if ($existingUserWithNip && $existingUserWithNip->role === 'pembimbing_sekolah' && !$existingUserWithNip->pembimbingSekolah) {
                                $userUmum = $existingUserWithNip;
                            } elseif ($existingUserWithNip) {
                                $existingUserWithNip->username = $existingUserWithNip->username . '_old_' . $existingUserWithNip->id;
                                $existingUserWithNip->saveQuietly();
                            }
                        }

                        if (!$userUmum) {
                            $usernameUmum = $nipUmum ?: Str::slug($cleanTeacherUmum, '.');
                            $baseUsernameUmum = $usernameUmum;
                            $counterUmum = 1;
                            while (User::where('username', $usernameUmum)->exists()) {
                                $usernameUmum = $baseUsernameUmum . '_' . $counterUmum;
                                $counterUmum++;
                            }

                            $userUmum = User::create([
                                'name' => $cleanTeacherUmum,
                                'username' => $usernameUmum,
                                'email' => null,
                                'password' => $nipUmum ?: $defaultPassword,
                                'role' => 'pembimbing_sekolah',
                                'is_active' => true,
                                'force_password_change' => true,
                            ]);
                        }

                        $pembimbingSekolahUmum = PembimbingSekolah::create([
                            'user_id' => $userUmum->id,
                            'nama_lengkap' => $cleanTeacherUmum,
                            'nip' => $nipUmum,
                            'tipe' => 'umum',
                        ]);

                        $existingTeachers->put($teacherUmumKey, $pembimbingSekolahUmum);
                        $existingUsers->put($teacherUmumKey, $userUmum);
                        $stats['pembimbing_created']++;
                    } else {
                        if ($nipUmum) {
                            if ($pembimbingSekolahUmum->nip !== $nipUmum) {
                                $pembimbingSekolahUmum->nip = $nipUmum;
                                $pembimbingSekolahUmum->saveQuietly();
                            }

                            $userUmum = $pembimbingSekolahUmum->user;
                            if ($userUmum) {
                                $isUserUmumDirty = false;
                                if ($userUmum->username !== $nipUmum) {
                                    $conflictUser = User::where('username', $nipUmum)->where('id', '!=', $userUmum->id)->first();
                                    if ($conflictUser) {
                                        $conflictUser->username = $conflictUser->username . '_old_' . $conflictUser->id;
                                        $conflictUser->saveQuietly();
                                    }
                                    $userUmum->username = $nipUmum;
                                    $userUmum->email = null;
                                    $isUserUmumDirty = true;
                                }
                                if ($userUmum->force_password_change || Hash::check('smkn1ciamis', $userUmum->password)) {
                                    $userUmum->password = $nipUmum;
                                    $userUmum->force_password_change = true;
                                    $isUserUmumDirty = true;
                                }
                                if ($isUserUmumDirty) {
                                    $userUmum->saveQuietly();
                                }
                            }
                        }
                        $stats['pembimbing_matched']++;
                    }
                }

                // 3. PEMETAAN SISWA (Pencocokan Cerdas: Eksak, Prefix Nama Panggilan, & Typo Tolerant)
                $siswa = $this->findStudent($studentName, $item['kelas'], $allStudents);

                // 4. RESOLUSI / PEMBUATAN MASTER DUDI
                $dudi = null;
                if (!empty($dudiName)) {
                    $dudi = $existingDudis->get($dudiKey);
                    if (!$dudi) {
                        $konsentrasiId = $siswa ? $siswa->konsentrasi_keahlian_id : ($allKonsentrasi->first()?->id ?? 1);
                        $dudi = Dudi::create([
                            'nama' => $dudiName,
                            'konsentrasi_keahlian_id' => $konsentrasiId,
                            'kota' => $wilayah ?: 'Ciamis',
                            'alamat' => $wilayah ?: 'Ciamis',
                            'is_active' => true,
                        ]);
                        $existingDudis->put($dudiKey, $dudi);
                        $stats['dudi_created']++;
                    } else {
                        $stats['dudi_matched']++;
                    }
                }

                if ($siswa) {
                    $updateData = [];
                    if ($dudi) {
                        $updateData['dudi_id'] = $dudi->id;
                        if ($siswa->status_pkl === 'belum_mulai' || empty($siswa->status_pkl)) {
                            $updateData['status_pkl'] = 'sedang_pkl';
                        }
                    }
                    if ($pembimbingSekolah) {
                        $updateData['pembimbing_sekolah_id'] = $pembimbingSekolah->id;
                    }
                    if ($pembimbingSekolahUmum) {
                        $updateData['pembimbing_sekolah_umum_id'] = $pembimbingSekolahUmum->id;
                    }

                    if (!empty($updateData)) {
                        $siswa->update($updateData);
                    }

                    // Hubungkan DUDI dengan Konsentrasi Keahlian siswa jika belum terhubung
                    if ($dudi && $siswa->konsentrasi_keahlian_id) {
                        $dudi->konsentrasiKeahlians()->syncWithoutDetaching([$siswa->konsentrasi_keahlian_id]);

                        // Jika pembimbing sekolah belum memiliki konsentrasi, set dari siswa
                        if ($pembimbingSekolah && !$pembimbingSekolah->konsentrasi_keahlian_id) {
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
