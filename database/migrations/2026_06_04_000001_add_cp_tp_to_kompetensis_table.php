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
        Schema::table('kompetensis', function (Blueprint $table) {
            // Add CP/TP fields to Kompetensi as master data
            $table->string('cp')->nullable()->comment('Capaian Pembelajaran (CP)');
            $table->string('tp')->nullable()->comment('Tujuan Pembelajaran (TP)');
            $table->text('deskripsi')->nullable()->comment('Deskripsi detail CP/TP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kompetensis', function (Blueprint $table) {
            $table->dropColumn(['cp', 'tp', 'deskripsi']);
        });
    }
};
