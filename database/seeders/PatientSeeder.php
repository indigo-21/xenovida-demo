<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $patients = [
            [
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'email' => 'jane.smith@example.com',
                'dob' => '1990-05-14',
                'gender' => 'Female',
                'postcode' => '2000',
                'mobile' => '09171234567',
            ],
            [
                'firstname' => 'Robert',
                'lastname' => 'Johnson',
                'email' => 'robert.johnson@example.com',
                'dob' => '1985-11-02',
                'gender' => 'Male',
                'postcode' => '1001',
                'mobile' => '09987654321',
            ],
        ];

        foreach ($patients as $data) {
            Patient::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
