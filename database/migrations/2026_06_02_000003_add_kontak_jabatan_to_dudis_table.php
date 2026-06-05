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
            $table->string('kontak')->nullable()->after('nama_pimpinan')->comment('Nama penanggung jawab/contact person');
            $table->string('jabatan')->nullable()->after('kontak')->comment('Jabatan penanggung jawab');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dudis', function (Blueprint $table) {
            $table->dropColumn(['kontak', 'jabatan']);
        });
    }
};
