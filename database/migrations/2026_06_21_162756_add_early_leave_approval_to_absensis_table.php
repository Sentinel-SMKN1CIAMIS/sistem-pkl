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
        Schema::table('absensis', function (Blueprint $table) {
            $table->enum('early_leave_request_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('alasan')->comment('Status permintaan izin pulang cepat');
            $table->text('early_leave_reason')->nullable()->after('early_leave_request_status')->comment('Alasan izin pulang cepat');
            $table->timestamp('early_leave_requested_at')->nullable()->after('early_leave_reason')->comment('Waktu pengajuan izin pulang cepat');
            $table->foreignId('early_leave_approved_by')->nullable()->after('early_leave_requested_at')->constrained('users')->comment('User ID pembimbing DUDI yang approve/reject');
            $table->timestamp('early_leave_approved_at')->nullable()->after('early_leave_approved_by')->comment('Waktu approve/reject');
            $table->text('early_leave_approval_note')->nullable()->after('early_leave_approved_at')->comment('Catatan dari pembimbing DUDI');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['early_leave_approved_by']);
            $table->dropColumn([
                'early_leave_request_status',
                'early_leave_reason',
                'early_leave_requested_at',
                'early_leave_approved_by',
                'early_leave_approved_at',
                'early_leave_approval_note'
            ]);
        });
    }
};
