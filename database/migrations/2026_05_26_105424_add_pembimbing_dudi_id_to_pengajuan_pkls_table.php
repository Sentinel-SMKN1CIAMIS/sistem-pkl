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
        Schema::table('pengajuan_pkls', function (Blueprint $table) {
            $table->foreignId('pembimbing_dudi_id')->nullable()->constrained('pembimbing_dudis')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_pkls', function (Blueprint $table) {
            $table->dropForeign(['pembimbing_dudi_id']);
            $table->dropColumn('pembimbing_dudi_id');
        });
    }
};
