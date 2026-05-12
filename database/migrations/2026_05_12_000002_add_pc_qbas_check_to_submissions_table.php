<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('pc_qbas_status', 20)->nullable();
            $table->unsignedTinyInteger('pc_qbas_set_count')->nullable();
            $table->foreignId('pc_qbas_checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pc_qbas_checked_at')->nullable();
            $table->text('pc_qbas_remarks')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['pc_qbas_checked_by']);
            $table->dropColumn([
                'pc_qbas_status',
                'pc_qbas_set_count',
                'pc_qbas_checked_by',
                'pc_qbas_checked_at',
                'pc_qbas_remarks',
            ]);
        });
    }
};
