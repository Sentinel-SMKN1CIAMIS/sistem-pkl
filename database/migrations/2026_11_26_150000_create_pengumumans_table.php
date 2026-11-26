<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('isi');
            $table->enum('target_role', ['semua', 'siswa', 'pembimbing_sekolah'])->default('siswa');
            $table->foreignId('program_keahlian_id')->nullable()->constrained('program_keahlians')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'target_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
    }
};
