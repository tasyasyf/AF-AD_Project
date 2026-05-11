<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('claim_documents')
            ->where('document_type', '!=', 'attendance_sheet')
            ->delete();

        DB::table('claim_documents')
            ->where('document_type', 'attendance_sheet')
            ->update([
                'label' => 'Attendance Sheet',
                'is_required' => true,
            ]);
    }

    public function down(): void
    {
        // Other claim document rows cannot be safely restored after deletion.
    }
};
