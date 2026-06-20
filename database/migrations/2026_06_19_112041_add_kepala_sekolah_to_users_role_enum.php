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
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('siswa', 'pembimbing_sekolah', 'pembimbing_dudi', 'pokja', 'super_admin', 'kaprog', 'kepala_sekolah') NOT NULL DEFAULT 'siswa'");
        } else {
            // PostgreSQL: drop old check constraint and create a new one
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('siswa', 'pembimbing_sekolah', 'pembimbing_dudi', 'pokja', 'super_admin', 'kaprog', 'kepala_sekolah'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('siswa', 'pembimbing_sekolah', 'pembimbing_dudi', 'pokja', 'super_admin', 'kaprog') NOT NULL DEFAULT 'siswa'");
        } else {
            // PostgreSQL: rollback check constraint (remove kepala_sekolah)
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('siswa', 'pembimbing_sekolah', 'pembimbing_dudi', 'pokja', 'super_admin', 'kaprog'))");
        }
    }
};
