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
        Schema::table('jurnals', function (Blueprint $table) {
            $table->index('tanggal');
            $table->index('status');
            $table->index('approval_status');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->index('tanggal');
            $table->index('approval_status');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->index('status_pkl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnals', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['status']);
            $table->dropIndex(['approval_status']);
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['approval_status']);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropIndex(['status_pkl']);
        });
    }
};
