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
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'user_level_id' => $levels['Super Administrator'] ?? 1,
                'username' => 'admin',
                'password' => Hash::make('Password@1'),
                'is_active' => true,
            ],
            [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'email' => 'john.doe@example.com',
                'email_verified_at' => now(),
                'user_level_id' => $levels['Administrator'] ?? 2,
                'username' => 'john.doe',
                'password' => Hash::make('Password@1'),
                'is_active' => true,
            ],
            [
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'email' => 'jane.smith@example.com',
                'email_verified_at' => now(),
                'user_level_id' => $levels['Manager'] ?? 3,
                'username' => 'jane.smith',
                'password' => Hash::make('Password@1'),
                'is_active' => true,
            ],
            [
                'firstname' => 'Mike',
                'lastname' => 'Jones',
                'email' => 'mike.jones@example.com',
                'email_verified_at' => now(),
                'user_level_id' => $levels['Staff'] ?? 4,
                'username' => 'mike.jones',
                'password' => Hash::make('Password@1'),
                'is_active' => true,
            ],
            [
                'firstname' => 'Sara',
                'lastname' => 'White',
                'email' => 'sara.white@example.com',
                'email_verified_at' => now(),
                'user_level_id' => $levels['Viewer'] ?? 5,
                'username' => 'sara.white',
                'password' => Hash::make('Password@1'),
                'is_active' => false,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
