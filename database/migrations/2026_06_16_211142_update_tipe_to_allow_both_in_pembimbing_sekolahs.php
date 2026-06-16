<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembimbing_sekolahs', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
        Schema::table('pembimbing_sekolahs', function (Blueprint $table) {
            $table->enum('tipe', ['kejuruan', 'umum', 'keduanya'])->default('umum')->after('nama_lengkap');
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
        Schema::table('pembimbing_sekolahs', function (Blueprint $table) {
            $table->enum('tipe', ['kejuruan', 'umum'])->default('umum')->after('nama_lengkap');
        });
    }
};
