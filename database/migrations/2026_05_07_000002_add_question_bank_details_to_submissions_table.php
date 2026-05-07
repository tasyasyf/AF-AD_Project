<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('semester_intake', 20)->nullable()->after('tutorial_number');
            $table->string('course', 255)->nullable()->after('semester_intake');
            $table->string('programme', 255)->nullable()->after('course');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['semester_intake', 'course', 'programme']);
        });
    }
};
