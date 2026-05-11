<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->unsignedBigInteger('pc_endorsed_by')->nullable()->after('reviewed_at');
            $table->timestamp('pc_endorsed_at')->nullable()->after('pc_endorsed_by');
            $table->text('pc_remarks')->nullable()->after('pc_endorsed_at');
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['pc_endorsed_by', 'pc_endorsed_at', 'pc_remarks']);
        });
    }
};
