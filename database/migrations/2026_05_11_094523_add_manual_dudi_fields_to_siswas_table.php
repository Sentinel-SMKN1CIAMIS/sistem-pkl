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
            $table->string('pembimbing_dudi_nama')->nullable()->after('pembimbing_dudi_id');
            $table->string('pembimbing_dudi_jabatan')->nullable()->after('pembimbing_dudi_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['pembimbing_dudi_nama', 'pembimbing_dudi_jabatan']);
        });
    }
};
