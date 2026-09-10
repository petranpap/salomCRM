<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class DemoServicesSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'Haircut',
                'duration_min' => 30,
                'base_price' => 25.00,
            ],
            [
                'name' => 'Manicure',
                'duration_min' => 45,
                'base_price' => 20.00,
            ],
            [
                'name' => 'Facial Treatment',
                'duration_min' => 60,
                'base_price' => 50.00,
            ],
            [
                'name' => 'Pedicure',
                'duration_min' => 60,
                'base_price' => 30.00,
            ],
            [
                'name' => 'Hair Coloring',
                'duration_min' => 90,
                'base_price' => 75.00,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}