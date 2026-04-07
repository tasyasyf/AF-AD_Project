<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $executive = User::where('email', 'executive@system.my')->first();
        $profile1  = Profile::whereHas('user', fn($q) => $q->where('email', 'afad1@system.my'))->first();

        Appointment::create([
            'profile_id'       => $profile1->id,
            'course_code'      => 'BBB1013',
            'course_name'      => 'Bahasa Melayu Komunikasi',
            'role_type'        => 'af',
            'semester'         => '2024/2025-1',
            'academic_session' => '2024/2025',
            'start_date'       => '2024-09-01',
            'end_date'         => '2025-01-31',
            'venue'            => 'Dewan Kuliah A, Kampus Utama',
            'student_count'    => 30,
            'appointed_by'     => $executive->id,
            'is_active'        => true,
        ]);

        Appointment::create([
            'profile_id'       => $profile1->id,
            'course_code'      => 'MPU3412',
            'course_name'      => 'Penghayatan Etika dan Peradaban',
            'role_type'        => 'ad',
            'semester'         => '2024/2025-1',
            'academic_session' => '2024/2025',
            'start_date'       => '2024-09-01',
            'end_date'         => '2025-01-31',
            'venue'            => 'Online',
            'student_count'    => 45,
            'appointed_by'     => $executive->id,
            'is_active'        => true,
        ]);
    }
}
