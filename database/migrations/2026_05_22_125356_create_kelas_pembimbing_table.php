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
        Schema::create('kelas_pembimbing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembimbing_sekolah_id')->constrained()->cascadeOnDelete();
            $table->string('kelas'); // Nama kelas, e.g. "Kuliner 1"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_pembimbing');
    }
};
