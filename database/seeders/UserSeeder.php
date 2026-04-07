<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'System Administrator',
            'email'    => 'admin@system.my',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name'     => 'Dr. Sarah Ahmad',
            'email'    => 'executive@system.my',
            'password' => Hash::make('password'),
            'role'     => 'executive',
            'is_active' => true,
        ]);

        User::create([
            'name'     => 'Ahmad bin Ali',
            'email'    => 'afad1@system.my',
            'password' => Hash::make('password'),
            'role'     => 'afad',
            'is_active' => true,
        ]);

        User::create([
            'name'     => 'Siti binti Hassan',
            'email'    => 'afad2@system.my',
            'password' => Hash::make('password'),
            'role'     => 'afad',
            'is_active' => true,
        ]);
    }
}
