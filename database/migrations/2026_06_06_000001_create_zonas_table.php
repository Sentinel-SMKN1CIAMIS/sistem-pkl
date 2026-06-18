<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('warna', 7)->default('#3b82f6')->comment('Hex color code for polygon fill');
            $table->string('warna_border', 7)->default('#1e40af')->comment('Hex color code for polygon stroke');
            $table->json('koordinat_geojson')->comment('GeoJSON coordinates array for the polygon');
            $table->integer('nomor_zona')->nullable();
            $table->timestamps();
        });

        Schema::table('dudis', function (Blueprint $table) {
            $table->foreignId('zona_id')->nullable()->after('jenis_industri')->constrained('zonas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dudis', function (Blueprint $table) {
            $table->dropForeign(['zona_id']);
            $table->dropColumn('zona_id');
        });

        Schema::dropIfExists('zonas');
    }
};
