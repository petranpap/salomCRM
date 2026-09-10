<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'service_id' => Service::factory(),
            'staff_id' => StaffProfile::factory(),
            'start' => $this->faker->dateTimeBetween('now', '+1 month'),
            'end' => $this->faker->dateTimeBetween('+1 hour', '+2 months'),
            'status' => $this->faker->randomElement(['booked', 'confirmed', 'completed', 'no-show', 'canceled']),
            'notes' => $this->faker->sentence(),
        ];
    }
}