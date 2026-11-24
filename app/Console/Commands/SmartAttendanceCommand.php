<?php

namespace App\Console\Commands;

use App\Services\SmartAttendanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SmartAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkl:smart-attendance 
                            {--action=auto : in, out, or auto (determines action based on current time window)}
                            {--nis= : Whitelisted student NIS (defaults to config whitelist)}
                            {--force : Force execution ignoring weekend checks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automate daily check-in (08:00) and check-out (17:00) with AI journal for whitelisted student';

    public function handle(SmartAttendanceService $service): int
    {
        $nis = $this->option('nis');
        $action = strtolower((string) ($this->option('action') ?: 'auto'));
        $force = (bool) $this->option('force');

        $siswa = $service->getTargetSiswa($nis);

        if (!$siswa) {
            $targetNis = $nis ?: $service->getWhitelistedNis();
            $this->warn("Siswa dengan NIS {$targetNis} tidak ditemukan di database.");

            return self::SUCCESS;
        }

        $now = Carbon::now();

        if ($action === 'in') {
            $result = $service->clockIn($siswa, $now, $force);
        } elseif ($action === 'out') {
            $result = $service->clockOut($siswa, $now, withAutoJournal: true, force: $force);
        } else {
            $result = $service->processAuto($siswa, $now, $force);
        }

        if ($result['status'] === 'success') {
            $this->info("[SUCCESS] {$result['message']}");
        } elseif ($result['status'] === 'skipped') {
            $this->comment("[SKIPPED] {$result['message']}");
        } else {
            $this->error("[ERROR] {$result['message']}");
        }

        return self::SUCCESS;
    }
}
