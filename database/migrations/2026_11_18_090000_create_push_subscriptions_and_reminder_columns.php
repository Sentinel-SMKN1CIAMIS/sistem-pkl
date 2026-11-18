<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('push_subscriptions')) {
            Schema::create('push_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->text('endpoint');
                $table->char('endpoint_hash', 64)->unique();
                $table->string('public_key')->nullable();
                $table->string('auth_token')->nullable();
                $table->string('content_encoding', 32)->default('aes128gcm');
                $table->string('user_agent', 512)->nullable();
                $table->timestamps();

                $table->index('user_id');
            });
        }

        if (Schema::hasTable('absensis')) {
            Schema::table('absensis', function (Blueprint $table) {
                if (!Schema::hasColumn('absensis', 'reminded_checkout_at')) {
                    $table->timestamp('reminded_checkout_at')->nullable()->after('early_leave_approval_note');
                }
                if (!Schema::hasColumn('absensis', 'reminded_jurnal_at')) {
                    $table->timestamp('reminded_jurnal_at')->nullable()->after('reminded_checkout_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('absensis')) {
            Schema::table('absensis', function (Blueprint $table) {
                $cols = array_filter(['reminded_checkout_at', 'reminded_jurnal_at'], fn($c) => Schema::hasColumn('absensis', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        Schema::dropIfExists('push_subscriptions');
    }
};
