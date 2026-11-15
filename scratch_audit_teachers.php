<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$teachers = \App\Models\PembimbingSekolah::with('user')->get();
echo "Total teachers: " . $teachers->count() . "\n";

$issues = [];
$successCount = 0;
$canLoginWithNip = 0;
$canLoginWithSmkn = 0;
$canLoginWithName = 0;
$noUser = 0;
$inactiveUser = 0;

foreach ($teachers as $t) {
    $u = $t->user;
    if (!$u) {
        $noUser++;
        $issues[] = "Teacher ID {$t->id} ({$t->nama_lengkap}): NO USER ASSOCIATED!";
        continue;
    }

    if (!$u->is_active) {
        $inactiveUser++;
        $issues[] = "Teacher ID {$t->id} ({$t->nama_lengkap}): USER IS INACTIVE (is_active = false)! Username: {$u->username}";
    }

    $nip = $t->nip;
    $hasNip = !empty($nip);

    $loginNip = false;
    $loginSmkn = false;

    if ($hasNip) {
        $loginNip = \Illuminate\Support\Facades\Hash::check($nip, $u->password);
    }
    $loginSmkn = \Illuminate\Support\Facades\Hash::check('smkn1ciamis', $u->password);
    $loginUsername = \Illuminate\Support\Facades\Hash::check($u->username, $u->password);

    if ($loginNip) $canLoginWithNip++;
    if ($loginSmkn) $canLoginWithSmkn++;

    $status = [];
    if ($loginNip) $status[] = "NIP";
    if ($loginSmkn) $status[] = "smkn1ciamis";
    if ($loginUsername) $status[] = "username";

    if (empty($status)) {
        $issues[] = "Teacher ID {$t->id} ({$t->nama_lengkap}): PASSWORD DOES NOT MATCH NIP OR smkn1ciamis! Username: '{$u->username}', NIP: '{$nip}', Hash prefix: " . substr($u->password, 0, 15);
    }
}

echo "Summary:\n";
echo "- Total: " . $teachers->count() . "\n";
echo "- No user: {$noUser}\n";
echo "- Inactive user: {$inactiveUser}\n";
echo "- Password is NIP: {$canLoginWithNip}\n";
echo "- Password is smkn1ciamis: {$canLoginWithSmkn}\n";
echo "- Total Issues found: " . count($issues) . "\n";

echo "\nSample Issues (first 15):\n";
foreach (array_slice($issues, 0, 15) as $iss) {
    echo "  * {$iss}\n";
}

