<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->string('course_code', 20);
            $table->string('course_name', 255);
            $table->enum('role_type', ['af', 'ad']);
            $table->string('semester', 20);
            $table->string('academic_session', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('venue', 255)->nullable();
            $table->unsignedSmallInteger('student_count')->nullable();
            $table->foreignId('appointed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
