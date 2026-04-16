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
        Schema::create('konsentrasi_keahlians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_keahlian_id')->constrained('program_keahlians')->cascadeOnDelete();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->integer('durasi_pkl_bulan')->default(4);
            $table->date('tanggal_mulai_pkl')->nullable();
            $table->date('tanggal_selesai_pkl')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsentrasi_keahlians');
    }
};
