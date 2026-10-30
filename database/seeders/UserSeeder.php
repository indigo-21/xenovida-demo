<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Resolve level IDs by description so ordering doesn't matter
        $levels = UserLevel::pluck('id', 'description');

        $users = [
            [
                'firstname' => 'Admin',
                'lastname' => 'User',
                'email' => 'master@example.com',
                'email_verified_at' => now(),
                'user_level_id' => $levels['Master-User'] ?? 1,
                'username' => 'master',
                'password' => Hash::make('Password123'),
                'is_active' => true,
            ],
            [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'email' => 'john.doe@example.com',
                'email_verified_at' => now(),
                'user_level_id' => $levels['Clinician'] ?? 2,
                'username' => 'john.doe',
                'password' => Hash::make('Password123'),
                'is_active' => true,
            ]
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
