<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->string('full_name', 150);
            $table->string('ic_number', 20)->unique();
            $table->string('phone', 20);
            $table->text('address');
            $table->string('contact_email', 150);
            $table->string('qualification', 255);
            $table->enum('qualification_level', ['diploma', 'degree', 'masters', 'phd', 'professional']);
            $table->string('specialisation', 255)->nullable();
            $table->string('bank_name', 100);
            $table->string('bank_account_number', 30);
            $table->string('bank_account_holder', 150);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
