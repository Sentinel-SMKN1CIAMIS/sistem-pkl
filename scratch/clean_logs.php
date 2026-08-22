<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = DB::table('activity_logs')->where('description', 'like', '%data:image%')->get();
$count = 0;

foreach ($logs as $log) {
    $newDesc = preg_replace('/data:image\\\\?\/[^\s"}]+/', '[DATA_GAMBAR_BASE64]', $log->description);
    DB::table('activity_logs')->where('id', $log->id)->update(['description' => $newDesc]);
    $count++;
}

echo "Berhasil membersihkan {$count} data log lama dari database.\n";
