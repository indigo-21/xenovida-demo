<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Order matters: UserLevels must exist before Users (foreign key).
     */
    public function run(): void
    {
        $this->call([
            UserLevelSeeder::class,
            UserSeeder::class,
        ]);
    }
}
