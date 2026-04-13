<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('resume_path', 500)->nullable()->after('area_of_expertise');
            $table->string('resume_original_name', 255)->nullable()->after('resume_path');
            $table->unsignedInteger('resume_size')->nullable()->after('resume_original_name');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['resume_path', 'resume_original_name', 'resume_size']);
        });
    }
};
