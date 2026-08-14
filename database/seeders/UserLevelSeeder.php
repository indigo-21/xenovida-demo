<?php

namespace Database\Seeders;

use App\Models\UserLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserLevelSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * Uses updateOrCreate so the seeder is safe to run multiple times.
     */
    public function run(): void
    {
        $levels = [
            ['description' => 'Super Administrator'],
            ['description' => 'Administrator'],
            ['description' => 'Manager'],
            ['description' => 'Staff'],
            ['description' => 'Viewer'],
        ];

        foreach ($levels as $level) {
            UserLevel::updateOrCreate(
                ['description' => $level['description']],
                $level
            );
        }
    }
}
