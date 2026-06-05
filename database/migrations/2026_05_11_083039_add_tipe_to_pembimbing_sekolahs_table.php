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
            $table->enum('tipe', ['normatif', 'adaptif', 'produktif'])->default('produktif')->after('nama_lengkap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembimbing_sekolahs', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
