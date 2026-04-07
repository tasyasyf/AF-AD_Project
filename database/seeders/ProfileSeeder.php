<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $executive = User::where('email', 'executive@system.my')->first();
        $afad1     = User::where('email', 'afad1@system.my')->first();
        $afad2     = User::where('email', 'afad2@system.my')->first();

        // Verified profile for afad1
        $profile1 = Profile::create([
            'user_id'              => $afad1->id,
            'full_name'            => 'Ahmad bin Ali',
            'ic_number'            => '850101-01-1234',
            'phone'                => '012-3456789',
            'address'              => 'No. 12, Jalan Merdeka 1, Taman Bahagia, 47500 Subang Jaya, Selangor',
            'contact_email'        => 'ahmad.ali@mail.com',
            'qualification'        => 'Master of Education (Instructional Technology)',
            'qualification_level'  => 'masters',
            'specialisation'       => 'Instructional Design',
            'bank_name'            => 'Maybank',
            'bank_account_number'  => '1234567890',
            'bank_account_holder'  => 'Ahmad bin Ali',
            'status'               => 'verified',
            'verified_by'          => $executive->id,
            'verified_at'          => now()->subDays(30),
        ]);

        Certificate::create([
            'profile_id'          => $profile1->id,
            'title'               => 'Master of Education (Instructional Technology)',
            'issuing_institution' => 'Universiti Teknologi Malaysia',
            'year_obtained'       => 2012,
            'is_verified'         => true,
            'verified_by'         => $executive->id,
            'verified_at'         => now()->subDays(30),
        ]);

        Certificate::create([
            'profile_id'          => $profile1->id,
            'title'               => 'Bachelor of Education (TESL)',
            'issuing_institution' => 'Universiti Kebangsaan Malaysia',
            'year_obtained'       => 2008,
            'is_verified'         => true,
            'verified_by'         => $executive->id,
            'verified_at'         => now()->subDays(30),
        ]);

        // Pending profile for afad2
        $profile2 = Profile::create([
            'user_id'              => $afad2->id,
            'full_name'            => 'Siti binti Hassan',
            'ic_number'            => '900215-03-5678',
            'phone'                => '017-9876543',
            'address'              => 'No. 5, Jalan Utama 3, Taman Indah, 41000 Klang, Selangor',
            'contact_email'        => 'siti.hassan@mail.com',
            'qualification'        => 'Bachelor of Science (Mathematics)',
            'qualification_level'  => 'degree',
            'specialisation'       => 'Statistics',
            'bank_name'            => 'CIMB Bank',
            'bank_account_number'  => '9876543210',
            'bank_account_holder'  => 'Siti binti Hassan',
            'status'               => 'pending',
        ]);

        Certificate::create([
            'profile_id'          => $profile2->id,
            'title'               => 'Bachelor of Science (Mathematics)',
            'issuing_institution' => 'Universiti Malaya',
            'year_obtained'       => 2015,
            'is_verified'         => false,
        ]);
    }
}
