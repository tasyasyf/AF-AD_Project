<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Claim;
use App\Models\ClaimAudit;
use App\Models\ClaimDocument;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClaimSeeder extends Seeder
{
    public function run(): void
    {
        $executive  = User::where('email', 'executive@system.my')->first();
        $afad1User  = User::where('email', 'afad1@system.my')->first();
        $profile1   = Profile::whereHas('user', fn($q) => $q->where('email', 'afad1@system.my'))->first();
        $appt1      = Appointment::where('course_code', 'BBB1013')->first();
        $appt2      = Appointment::where('course_code', 'MPU3412')->first();

        // Claim 1: Approved (teaching for BBB1013)
        $claim1 = Claim::create([
            'appointment_id' => $appt1->id,
            'profile_id'     => $profile1->id,
            'claim_type'     => 'teaching',
            'period_from'    => '2024-09-01',
            'period_to'      => '2024-10-31',
            'total_hours'    => 20,
            'rate_per_hour'  => 50.00,
            'total_amount'   => 1000.00,
            'status'         => 'approved',
            'submitted_at'   => now()->subDays(25),
            'reviewed_by'    => $executive->id,
            'reviewed_at'    => now()->subDays(20),
            'executive_remarks' => 'Claim verified and approved. Documents in order.',
        ]);

        ClaimDocument::create(['claim_id' => $claim1->id, 'document_type' => 'attendance_sheet', 'label' => 'Attendance Sheet', 'is_required' => true, 'is_uploaded' => true, 'uploaded_at' => now()->subDays(26), 'sort_order' => 1]);
        ClaimDocument::create(['claim_id' => $claim1->id, 'document_type' => 'lesson_plan', 'label' => 'Lesson Plan', 'is_required' => true, 'is_uploaded' => true, 'uploaded_at' => now()->subDays(26), 'sort_order' => 2]);
        ClaimDocument::create(['claim_id' => $claim1->id, 'document_type' => 'student_list', 'label' => 'Student List', 'is_required' => false, 'is_uploaded' => true, 'uploaded_at' => now()->subDays(26), 'sort_order' => 3]);

        // Seed audit trail for claim1 (set performed_by manually since auth() won't work in seeder)
        ClaimAudit::create(['claim_id' => $claim1->id, 'performed_by' => $afad1User->id, 'action' => 'created', 'from_status' => null, 'to_status' => 'draft', 'created_at' => now()->subDays(28)]);
        ClaimAudit::create(['claim_id' => $claim1->id, 'performed_by' => $afad1User->id, 'action' => 'document_uploaded', 'from_status' => 'draft', 'to_status' => 'draft', 'remarks' => 'Uploaded: Attendance Sheet', 'created_at' => now()->subDays(26)]);
        ClaimAudit::create(['claim_id' => $claim1->id, 'performed_by' => $afad1User->id, 'action' => 'submitted', 'from_status' => 'draft', 'to_status' => 'submitted', 'created_at' => now()->subDays(25)]);
        ClaimAudit::create(['claim_id' => $claim1->id, 'performed_by' => $executive->id, 'action' => 'approved', 'from_status' => 'submitted', 'to_status' => 'approved', 'remarks' => 'Claim verified and approved. Documents in order.', 'created_at' => now()->subDays(20)]);

        // Claim 2: Under Review (module development for MPU3412)
        $claim2 = Claim::create([
            'appointment_id' => $appt2->id,
            'profile_id'     => $profile1->id,
            'claim_type'     => 'module_development',
            'period_from'    => '2024-09-01',
            'period_to'      => '2024-12-31',
            'total_hours'    => 40,
            'rate_per_hour'  => 60.00,
            'total_amount'   => 2400.00,
            'status'         => 'under_review',
            'submitted_at'   => now()->subDays(5),
        ]);

        ClaimDocument::create(['claim_id' => $claim2->id, 'document_type' => 'lesson_plan', 'label' => 'Module Draft / Outline', 'is_required' => true, 'is_uploaded' => true, 'uploaded_at' => now()->subDays(6), 'sort_order' => 1]);
        ClaimDocument::create(['claim_id' => $claim2->id, 'document_type' => 'other', 'label' => 'Approval Letter', 'is_required' => true, 'is_uploaded' => false, 'sort_order' => 2]);

        ClaimAudit::create(['claim_id' => $claim2->id, 'performed_by' => $afad1User->id, 'action' => 'created', 'from_status' => null, 'to_status' => 'draft', 'created_at' => now()->subDays(8)]);
        ClaimAudit::create(['claim_id' => $claim2->id, 'performed_by' => $afad1User->id, 'action' => 'submitted', 'from_status' => 'draft', 'to_status' => 'submitted', 'created_at' => now()->subDays(5)]);

        // Claim 3: Draft (consultation for BBB1013)
        $claim3 = Claim::create([
            'appointment_id' => $appt1->id,
            'profile_id'     => $profile1->id,
            'claim_type'     => 'consultation',
            'period_from'    => '2024-11-01',
            'period_to'      => '2024-12-15',
            'total_hours'    => 15,
            'rate_per_hour'  => 45.00,
            'total_amount'   => 675.00,
            'status'         => 'draft',
        ]);

        ClaimAudit::create(['claim_id' => $claim3->id, 'performed_by' => $afad1User->id, 'action' => 'created', 'from_status' => null, 'to_status' => 'draft', 'created_at' => now()->subDays(2)]);
    }
}
