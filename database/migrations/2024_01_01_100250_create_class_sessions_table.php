<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->string('course_code', 20);
            $table->string('course_name', 255);
            $table->string('section', 50)->nullable();
            $table->string('day', 20);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue', 255)->nullable();
            $table->string('semester', 20);
            $table->string('academic_session', 20);
            $table->unsignedSmallInteger('student_count')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
