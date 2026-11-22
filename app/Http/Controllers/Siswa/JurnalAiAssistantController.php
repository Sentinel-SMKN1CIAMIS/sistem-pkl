<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\AiJurnalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JurnalAiAssistantController extends Controller
{
    /**
     * Get allowed whitelisted NIS for feature rollout.
     */
    public static function getAllowedNis(): string
    {
        return (string) config('services.groq.whitelist_nis', '242510648');
    }

    protected AiJurnalService $aiService;

    public function __construct(AiJurnalService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Handle AI journal generation/refinement.
     */
    public function generate(Request $request): JsonResponse
    {
        $user = $request->user();
        $allowedNis = self::getAllowedNis();
        $isAllowed = ($user?->username === $allowedNis) || ($user?->siswa?->nis === $allowedNis);

        if (! $isAllowed) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini tidak tersedia untuk akun Anda.',
            ], 403);
        }

        $validated = $request->validate([
            'mode' => ['required', 'string', 'in:refine,auto'],
            'catatan_kasar' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $result = $this->aiService->generate(
                mode: $validated['mode'],
                catatanKasar: $validated['catatan_kasar'] ?? null,
                siswa: $user->siswa
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat memproses AI.',
            ], 500);
        }
    }
}
