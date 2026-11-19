<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    /**
     * Trigger scheduled PKL reminders (and daily model pruning) via a secret-protected URL.
     * Compatible with external cron services (e.g. cron-job.org) and ISPConfig URL Cron.
     */
    public function run(Request $request): JsonResponse
    {
        $expectedToken = (string) config('services.cron.token', '');

        if ($expectedToken === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'CRON_SECRET_TOKEN belum dikonfigurasi pada server.',
            ], 503);
        }

        $providedToken = (string) ($request->header('X-Cron-Token') ?: $request->query('token', ''));

        if ($providedToken === '' || !hash_equals($expectedToken, $providedToken)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $lock = Cache::lock('cron_http_run_lock', 120);

        if (!$lock->get()) {
            return response()->json([
                'status' => 'skipped',
                'message' => 'Proses cron sebelumnya masih berjalan.',
            ], 200);
        }

        try {
            Artisan::call('pkl:send-reminders');
            $remindersOutput = trim(Artisan::output());

            $pruned = false;
            $today = now()->toDateString();
            if (Cache::get('cron_last_prune_date') !== $today) {
                Artisan::call('model:prune');
                Cache::put('cron_last_prune_date', $today, now()->addHours(26));
                $pruned = true;
            }

            return response()->json([
                'status' => 'ok',
                'ran_at' => now()->toIso8601String(),
                'reminders' => $remindersOutput,
                'pruned' => $pruned,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('HTTP Cron execution failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menjalankan tugas cron.',
            ], 500);
        } finally {
            $lock->release();
        }
    }
}
