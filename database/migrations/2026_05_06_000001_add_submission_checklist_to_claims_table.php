<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->boolean('has_mark_entry_forms')->default(false)->after('total_amount');
            $table->boolean('has_graded_scripts')->default(false)->after('has_mark_entry_forms');
            $table->boolean('has_qa')->default(false)->after('has_graded_scripts');
            $table->boolean('has_question_bank_answer_sheet')->default(false)->after('has_qa');
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn([
                'has_mark_entry_forms',
                'has_graded_scripts',
                'has_qa',
                'has_question_bank_answer_sheet',
            ]);
        });
    }
};
