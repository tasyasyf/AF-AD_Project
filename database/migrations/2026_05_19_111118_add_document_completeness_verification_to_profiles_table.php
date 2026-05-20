<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->foreignId('documents_verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->timestamp('documents_verified_at')->nullable()->after('documents_verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('documents_verified_by');
            $table->dropColumn('documents_verified_at');
        });
    }
};
