<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->string('claim_reference', 30)->unique()->nullable();
            $table->enum('claim_type', ['teaching', 'marking', 'module_development', 'consultation']);
            $table->date('period_from');
            $table->date('period_to');
            $table->decimal('total_hours', 8, 2);
            $table->decimal('rate_per_hour', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'returned', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('executive_remarks')->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
