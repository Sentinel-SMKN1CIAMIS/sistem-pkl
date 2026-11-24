<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('jam_notif_masuk', 5)->default('07:00')->after('status_pkl');
            $table->string('jam_notif_pulang', 5)->default('16:00')->after('jam_notif_masuk');
            $table->string('jam_notif_jurnal', 5)->default('19:30')->after('jam_notif_pulang');
            $table->boolean('reminder_settings_configured')->default(false)->after('jam_notif_jurnal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'jam_notif_masuk',
                'jam_notif_pulang',
                'jam_notif_jurnal',
                'reminder_settings_configured',
            ]);
        });
    }
};
