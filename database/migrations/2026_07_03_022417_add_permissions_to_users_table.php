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
            // List of viewable function keys the admin has granted (read-only access).
            $table->json('permissions')->nullable()->after('is_active');
            // Master switch: when false, all additional access is disabled without losing the toggles.
            $table->boolean('additional_access_enabled')->default(true)->after('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['permissions', 'additional_access_enabled']);
        });
    }
};
