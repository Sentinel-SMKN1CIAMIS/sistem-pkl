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
        Schema::table('pembimbing_sekolahs', function (Blueprint $table) {
            $table->integer('kapasitas')->default(10)->after('no_hp')->comment('Jumlah maksimal siswa yang dibimbing (recommended: 10)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembimbing_sekolahs', function (Blueprint $table) {
            $table->dropColumn('kapasitas');
        });
    }
};
