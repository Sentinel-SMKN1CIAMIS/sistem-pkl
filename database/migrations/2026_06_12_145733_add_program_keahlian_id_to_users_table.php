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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('program_keahlian_id')
                ->nullable()
                ->after('konsentrasi_keahlian_id')
                ->constrained('program_keahlians')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignKey(['program_keahlian_id']);
            $table->dropColumn('program_keahlian_id');
        });
    }
};
