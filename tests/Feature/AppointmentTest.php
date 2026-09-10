<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Service;
use App\Models\StaffProfile;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    // appointments.staff_id references staff_profiles.id, not users.id — a staff
    // member and the account calling the API are two different things.
    public function test_can_create_appointment()
    {
        $user = User::factory()->create();
        $staffProfile = StaffProfile::factory()->create();
        $customer = Customer::factory()->create();
        $service = Service::factory()->create(['base_price' => 50]);

        $response = $this->actingAs($user)->post('/api/appointments', [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'staff_id' => $staffProfile->id,
            'start' => now()->addDays(1)->toISOString(),
            'end' => now()->addDays(1)->addHours(1)->toISOString(),
            'status' => 'booked',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('appointments', [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'staff_id' => $staffProfile->id,
            'status' => 'booked',
        ]);
    }

    public function test_can_update_appointment()
    {
        $user = User::factory()->create();
        $staffProfile = StaffProfile::factory()->create();
        $appointment = Appointment::factory()->create(['staff_id' => $staffProfile->id]);

        $response = $this->actingAs($user)->put("/api/appointments/{$appointment->id}", [
            'status' => 'completed',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);
    }

    public function test_can_delete_appointment()
    {
        $user = User::factory()->create();
        $staffProfile = StaffProfile::factory()->create();
        $appointment = Appointment::factory()->create(['staff_id' => $staffProfile->id]);

        $response = $this->actingAs($user)->delete("/api/appointments/{$appointment->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id,
        ]);
    }
}