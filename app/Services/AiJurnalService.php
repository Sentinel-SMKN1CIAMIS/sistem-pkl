<?php

namespace App\Services;

use App\Models\Siswa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AiJurnalService
{
    protected ?string $apiKey;
    protected string $model;
    protected string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');
        $this->model = config('services.groq.model', 'qwen/qwen3.8-27b');
    }

    /**
     * Generate jurnal kegiatan & catatan using Groq AI.
     *
     * @param  string  $mode  'refine' or 'auto'
     * @param  string|null  $catatanKasar
     * @param  Siswa|null  $siswa
     * @return array{kegiatan: string, catatan: string}
     */
    public function generate(string $mode, ?string $catatanKasar = null, ?Siswa $siswa = null): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('GROQ_API_KEY belum dikonfigurasi di environment (.env).');
        }

        $namaDudi = $siswa?->dudi?->nama ?? 'Industri Mitra';
        $jurusan = $siswa?->konsentrasiKeahlian?->nama ?? 'Rekayasa Perangkat Lunak';

        $systemPrompt = <<<PROMPT
Kamu adalah asisten pintar untuk siswa SMK jurusan {$jurusan} yang sedang magang (PKL).
Tugasmu adalah membantu menulis atau merapikan jurnal harian magang.
GAYA BAHASA & ATURAN PENTING:
- Gunakan bahasa Indonesia yang santai, sopan, dan natural khas anak SMK kelas 12.
- BUKAN bahasa robot kaku dan BUKAN bahasa skripsi.
- JANGAN gunakan kata klise robot seperti: 'komprehensif', 'holistik', 'paradigma', 'sinergi', 'secara esensial', 'pada hakikatnya'.
- Ceritakan apa yang dikerjakan secara konkret, ada proses pengerjaan, dan kendala kecil realistis yang berhasil diselesaikan (contoh: salah nama variabel/typo, css bentrok, query lemot, import icon belum dipasang, dsb).
- Format output WAJIB JSON murni tanpa markdown/backticks tambahan:
{
  "kegiatan": "Judul pekerjaan ringkas (maksimal 10 kata)",
  "catatan": "Uraian proses pengerjaan dan kendala yang dihadapi (2-3 paragraf natural, pisahkan antar paragraf dengan \\n\\n)"
}
PROMPT;

        if ($mode === 'refine' && !empty(trim($catatanKasar ?? ''))) {
            $userPrompt = "Siswa memberikan catatan kasar pekerjaan hari ini:\n\"{$catatanKasar}\"\n\nPerusahaan/DUDI: {$namaDudi}\nJurusan: {$jurusan}\n\nSusun catatan tersebut menjadi judul pekerjaan dan uraian catatan jurnal magang yang terstruktur namun tetap natural.";
        } else {
            $userPrompt = "Siswa sedang magang di: {$namaDudi} ({$jurusan}). Buat 1 skenario kegiatan teknis realistis anak magang IT/RPL hari ini (misal slicing antarmuka, fixing styling responsive mobile, testing fitur form input, integrasi API, atau perapian komponen). Tulis judul dan catatan prosesnya secara natural.";
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(20)
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.7,
                    'max_tokens' => 800,
                ]);

            if ($response->failed()) {
                Log::error('Groq AI API error: ' . $response->body());
                throw new RuntimeException('Gagal menghubungi layanan AI: ' . ($response->json('error.message') ?? $response->status()));
            }

            $rawContent = $response->json('choices.0.message.content');
            if (empty($rawContent)) {
                throw new RuntimeException('Respons AI kosong.');
            }

            $data = json_decode($rawContent, true);
            if (!is_array($data) || empty($data['kegiatan']) || empty($data['catatan'])) {
                // Fallback attempt to extract json if wrapped
                if (preg_match('/\{[\s\S]*\}/', $rawContent, $matches)) {
                    $data = json_decode($matches[0], true);
                }
            }

            if (!is_array($data) || empty($data['kegiatan']) || empty($data['catatan'])) {
                throw new RuntimeException('Format respons AI tidak sesuai format JSON yang diharapkan.');
            }

            return [
                'kegiatan' => trim($data['kegiatan']),
                'catatan' => trim($data['catatan']),
            ];
        } catch (\Throwable $e) {
            Log::error('AiJurnalService Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
