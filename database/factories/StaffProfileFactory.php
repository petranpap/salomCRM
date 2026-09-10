<?php

namespace Database\Factories;

use App\Models\StaffProfile;
use App\Models\User;
use App\Support\WeeklySchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffProfileFactory extends Factory
{
    protected $model = StaffProfile::class;

    public function definition(): array
    {
        // Every day active, wide open — so a test scheduling "tomorrow" doesn't
        // spuriously fail AppointmentRequest's staff-working-hours check depending
        // on which day of the week the test happens to run on.
        $wideOpen = collect(WeeklySchedule::DAYS)
            ->mapWithKeys(fn ($day) => [$day => ['active' => true, 'start' => '00:00', 'end' => '23:59']])
            ->all();

        return [
            'user_id'       => User::factory(),
            'working_hours' => $wideOpen,
            'color'         => $this->faker->hexColor(),
            'specialty'     => $this->faker->word(),
            'job_title'     => 'Stylist',
            'is_active'     => true,
        ];
    }
}
