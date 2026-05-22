<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->text('isi');
            $table->boolean('is_broadcast')->default(false);
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();

            $table->index(['from_user_id', 'to_user_id']);
            $table->index(['to_user_id', 'dibaca_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesans');
    }
};
