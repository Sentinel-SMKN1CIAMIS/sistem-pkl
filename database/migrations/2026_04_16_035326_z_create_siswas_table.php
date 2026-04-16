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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('konsentrasi_keahlian_id')->constrained('konsentrasi_keahlians')->cascadeOnDelete();
            $table->foreignId('dudi_id')->nullable()->constrained('dudis')->nullOnDelete();
            $table->foreignId('pembimbing_sekolah_id')->nullable()->constrained('pembimbing_sekolahs')->nullOnDelete();
            $table->foreignId('pembimbing_dudi_id')->nullable()->constrained('pembimbing_dudis')->nullOnDelete();
            $table->string('nis')->unique();
            $table->string('nama_lengkap');
            $table->string('kelas');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('tahun_ajaran');
            $table->enum('status_pkl', ['belum_mulai', 'sedang_pkl', 'selesai', 'dibatalkan'])->default('belum_mulai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
