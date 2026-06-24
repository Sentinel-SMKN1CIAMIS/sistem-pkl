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
        Schema::table('siswas', function (Blueprint $table) {
            $table->foreignId('pembimbing_sekolah_umum_id')
                ->nullable()
                ->after('pembimbing_sekolah_id')
                ->constrained('pembimbing_sekolahs')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropForeign(['pembimbing_sekolah_umum_id']);
            $table->dropColumn('pembimbing_sekolah_umum_id');
        });
    }
};
