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
            $table->foreignId('konsentrasi_keahlian_id')
                ->nullable()
                ->after('role')
                ->constrained('konsentrasi_keahlians')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignKey(['konsentrasi_keahlian_id']);
            $table->dropColumn('konsentrasi_keahlian_id');
        });
    }
};
