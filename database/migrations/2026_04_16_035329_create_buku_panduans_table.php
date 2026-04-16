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
        Schema::create('buku_panduans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->enum('tipe', ['siswa', 'dudi', 'umum']);
            $table->foreignId('konsentrasi_keahlian_id')->nullable()->constrained('konsentrasi_keahlians')->nullOnDelete();
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_panduans');
    }
};
