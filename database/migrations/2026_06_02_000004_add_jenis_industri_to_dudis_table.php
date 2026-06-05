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
        Schema::table('dudis', function (Blueprint $table) {
            $table->enum('jenis_industri', ['pemerintahan', 'industri', 'layanan', 'perdagangan', 'pendidikan', 'kesehatan', 'teknologi', 'pertanian', 'lainnya'])->nullable()->after('bidang_usaha')->comment('Tipe/Klasifikasi industri DUDI');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dudis', function (Blueprint $table) {
            $table->dropColumn('jenis_industri');
        });
    }
};
