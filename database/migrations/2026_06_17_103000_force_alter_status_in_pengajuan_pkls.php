<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pengajuan_pkls')) {
            if (config('database.default') === 'mysql') {
                // Explicitly modify column to VARCHAR for MySQL
                DB::statement("ALTER TABLE `pengajuan_pkls` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'menunggu'");
            } else {
                Schema::table('pengajuan_pkls', function (Blueprint $table) {
                    $table->string('status', 50)->default('menunggu')->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pengajuan_pkls')) {
            if (config('database.default') === 'mysql') {
                DB::statement("ALTER TABLE `pengajuan_pkls` MODIFY COLUMN `status` ENUM('menunggu', 'disetujui', 'ditolak') NOT NULL DEFAULT 'menunggu'");
            } else {
                Schema::table('pengajuan_pkls', function (Blueprint $table) {
                    $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu')->change();
                });
            }
        }
    }
};
