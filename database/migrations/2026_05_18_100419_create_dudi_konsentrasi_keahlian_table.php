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
        Schema::create('dudi_konsentrasi_keahlian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dudi_id')->constrained('dudis')->cascadeOnDelete();
            $table->foreignId('konsentrasi_keahlian_id')->constrained('konsentrasi_keahlians')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dudi_konsentrasi_keahlian');
    }
};
