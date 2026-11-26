<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kunjungan_monitorings')) {
            Schema::create('kunjungan_monitorings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pembimbing_sekolah_id')->constrained('pembimbing_sekolahs')->cascadeOnDelete();
                $table->foreignId('dudi_id')->constrained('dudis')->cascadeOnDelete();
                $table->date('tanggal_kunjungan');
                $table->text('catatan_monitoring');
                $table->text('kendala')->nullable();
                $table->string('foto_path')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamps();

                $table->index(['pembimbing_sekolah_id', 'tanggal_kunjungan'], 'km_pembimbing_tgl_idx');
                $table->index(['dudi_id', 'tanggal_kunjungan'], 'km_dudi_tgl_idx');
            });
        } else {
            Schema::table('kunjungan_monitorings', function (Blueprint $table) {
                $table->index(['pembimbing_sekolah_id', 'tanggal_kunjungan'], 'km_pembimbing_tgl_idx');
                $table->index(['dudi_id', 'tanggal_kunjungan'], 'km_dudi_tgl_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan_monitorings');
    }
};
