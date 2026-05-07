<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->decimal('claim_hours', 8, 2)->nullable()->after('tutorial_number');
            $table->decimal('rate_per_hour', 8, 2)->nullable()->after('claim_hours');
            $table->decimal('total_amount', 10, 2)->nullable()->after('rate_per_hour');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['claim_hours', 'rate_per_hour', 'total_amount']);
        });
    }
};
