<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Program Keahlian
        DB::table('program_keahlians')->where('kode', 'AKL')->update(['nama' => 'Akuntansi dan Keuangan Lembaga']);
        DB::table('program_keahlians')->where('kode', 'MPLB')->update(['nama' => 'Manajemen Perkantoran dan Layanan Bisnis']);
        DB::table('program_keahlians')->where('kode', 'PBR')->update(['kode' => 'PM', 'nama' => 'Pemasaran']);
        DB::table('program_keahlians')->where('kode', 'HOTEL')->update(['kode' => 'HTL', 'nama' => 'Perhotelan']);

        // Update Konsentrasi Keahlian
        DB::table('konsentrasi_keahlians')->where('id', 3)->update(['nama' => 'Manajemen Perkantoran']);
        DB::table('konsentrasi_keahlians')->where('id', 4)->update(['nama' => 'Bisnis Ritel']);
        DB::table('konsentrasi_keahlians')->where('id', 5)->update(['kode' => 'PBD', 'nama' => 'Bisnis Digital']);
        DB::table('konsentrasi_keahlians')->where('id', 6)->update(['kode' => 'HTL']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('program_keahlians')->where('kode', 'AKL')->update(['nama' => 'Akuntansi']);
        DB::table('program_keahlians')->where('kode', 'MPLB')->update(['nama' => 'Managemen Bisnis']);
        DB::table('program_keahlians')->where('kode', 'PM')->update(['kode' => 'PBR', 'nama' => 'Pemasaran Ritel']);
        DB::table('program_keahlians')->where('kode', 'HTL')->update(['kode' => 'HOTEL', 'nama' => 'Perhotelan']);

        DB::table('konsentrasi_keahlians')->where('id', 3)->update(['nama' => 'Managemen Bisnis']);
        DB::table('konsentrasi_keahlians')->where('id', 4)->update(['nama' => 'Pemasaran Ritel']);
        DB::table('konsentrasi_keahlians')->where('id', 5)->update(['kode' => 'PDG', 'nama' => 'Pemasaran Digital']);
        DB::table('konsentrasi_keahlians')->where('id', 6)->update(['kode' => 'HOTEL']);
    }
};
