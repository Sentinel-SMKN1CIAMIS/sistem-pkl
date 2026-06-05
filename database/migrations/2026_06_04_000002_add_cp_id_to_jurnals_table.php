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
            // Add reference to Kompetensi as CP/TP master
            $table->foreignId('cp_id')->nullable()->after('kompetensi_id')->constrained('kompetensis')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnals', function (Blueprint $table) {
            $table->dropForeign(['cp_id']);
            $table->dropColumn('cp_id');
        });
    }
};
