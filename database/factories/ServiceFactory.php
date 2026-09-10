<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name'         => ucfirst($this->faker->words(2, true)),
            'duration_min' => $this->faker->randomElement([15, 30, 45, 60]),
            'base_price'   => $this->faker->randomFloat(2, 10, 100),
            'is_active'    => true,
        ];
    }
}
