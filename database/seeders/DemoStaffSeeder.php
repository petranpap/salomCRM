<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StaffProfile;

class DemoStaffSeeder extends Seeder
{
    public function run()
    {
        $staff = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'password' => bcrypt('password'),
                'working_hours' => json_encode(['monday' => '09:00-17:00', 'tuesday' => '09:00-17:00']),
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob@example.com',
                'password' => bcrypt('password'),
                'working_hours' => json_encode(['wednesday' => '09:00-17:00', 'thursday' => '09:00-17:00']),
            ],
            [
                'name' => 'Cathy Brown',
                'email' => 'cathy@example.com',
                'password' => bcrypt('password'),
                'working_hours' => json_encode(['friday' => '09:00-17:00', 'saturday' => '10:00-15:00']),
            ],
        ];

        foreach ($staff as $member) {
            $user = User::create([
                'name' => $member['name'],
                'email' => $member['email'],
                'password' => $member['password'],
            ]);

            StaffProfile::create([
                'user_id' => $user->id,
                'working_hours' => $member['working_hours'],
            ]);
        }
    }
}