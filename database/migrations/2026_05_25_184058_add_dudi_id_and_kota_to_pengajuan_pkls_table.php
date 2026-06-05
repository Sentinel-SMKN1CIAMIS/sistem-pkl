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
        Schema::table('pengajuan_pkls', function (Blueprint $table) {
            $table->foreignId('dudi_id')->nullable()->after('siswa_id')->constrained('dudis')->nullOnDelete();
            $table->string('kota')->nullable()->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_pkls', function (Blueprint $table) {
            $table->dropForeign(['dudi_id']);
            $table->dropColumn(['dudi_id', 'kota']);
        });
    }
};
