<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('absensis')) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `absensis` MODIFY COLUMN `status` ENUM('hadir', 'sakit', 'izin', 'alpha', 'libur') NOT NULL DEFAULT 'hadir'");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE absensis DROP CONSTRAINT IF EXISTS absensis_status_check');
            DB::statement("ALTER TABLE absensis ALTER COLUMN status TYPE VARCHAR(50)");
            DB::statement("ALTER TABLE absensis ADD CONSTRAINT absensis_status_check CHECK (status IN ('hadir', 'sakit', 'izin', 'alpha', 'libur'))");
        } else {
            Schema::table('absensis', function (Blueprint $table) {
                $table->string('status', 50)->default('hadir')->change();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('absensis')) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("UPDATE `absensis` SET `status` = 'izin' WHERE `status` = 'libur'");
            DB::statement("ALTER TABLE `absensis` MODIFY COLUMN `status` ENUM('hadir', 'sakit', 'izin', 'alpha') NOT NULL DEFAULT 'hadir'");
        } elseif ($driver === 'pgsql') {
            DB::statement("UPDATE absensis SET status = 'izin' WHERE status = 'libur'");
            DB::statement('ALTER TABLE absensis DROP CONSTRAINT IF EXISTS absensis_status_check');
            DB::statement("ALTER TABLE absensis ADD CONSTRAINT absensis_status_check CHECK (status IN ('hadir', 'sakit', 'izin', 'alpha'))");
        } else {
            DB::table('absensis')->where('status', 'libur')->update(['status' => 'izin']);
            Schema::table('absensis', function (Blueprint $table) {
                $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])->default('hadir')->change();
            });
        }
    }
};
