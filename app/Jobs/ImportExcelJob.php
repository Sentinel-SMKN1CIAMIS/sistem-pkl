<?php

namespace App\Jobs;

use App\Imports\ExcelImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ImportExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 3600;
    public int $backoff = 60;
    public string $queue = 'imports';

    public string $type;
    public string $filePath;
    public ?int $userId;

    public function __construct(string $type, string $filePath, ?int $userId = null)
    {
        $this->type = $type;
        $this->filePath = $filePath;
        $this->userId = $userId;
    }

    public function handle(ExcelImportService $service)
    {
        $absolutePath = Storage::disk('local')->path($this->filePath);
        $service->process($this->type, $absolutePath, $this->userId);
    }
}
