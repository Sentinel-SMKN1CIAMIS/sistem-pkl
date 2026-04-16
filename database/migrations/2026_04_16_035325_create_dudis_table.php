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
        Schema::create('dudis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsentrasi_keahlian_id')->constrained('konsentrasi_keahlians')->cascadeOnDelete();
            $table->string('nama');
            $table->text('alamat');
            $table->string('kota');
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('nama_pimpinan')->nullable();
            $table->string('bidang_usaha')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dudis');
    }
};
