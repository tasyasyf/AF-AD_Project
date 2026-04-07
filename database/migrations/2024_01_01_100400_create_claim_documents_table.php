<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', ['attendance_sheet', 'lesson_plan', 'marking_scheme', 'assignment_sample', 'student_list', 'other']);
            $table->string('label', 255);
            $table->string('file_path', 500)->nullable();
            $table->string('file_original_name', 255)->nullable();
            $table->string('file_mime', 100)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_uploaded')->default(false);
            $table->timestamp('uploaded_at')->nullable();
            $table->string('notes', 500)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_documents');
    }
};
