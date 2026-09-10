<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class DemoCustomersSeeder extends Seeder
{
    public function run()
    {
        Customer::create([
            'name' => 'Alice Johnson',
            'phone' => '123-456-7890',
            'email' => 'alice@example.com',
            'notes' => 'Regular customer, prefers morning appointments.',
            'consent_email' => true,
            'consent_sms' => true,
        ]);

        Customer::create([
            'name' => 'Bob Smith',
            'phone' => '234-567-8901',
            'email' => 'bob@example.com',
            'notes' => 'Occasional customer, prefers evening appointments.',
            'consent_email' => true,
            'consent_sms' => false,
        ]);

        Customer::create([
            'name' => 'Catherine Lee',
            'phone' => '345-678-9012',
            'email' => 'catherine@example.com',
            'notes' => 'VIP customer, enjoys all services.',
            'consent_email' => true,
            'consent_sms' => true,
        ]);

        Customer::create([
            'name' => 'David Brown',
            'phone' => '456-789-0123',
            'email' => 'david@example.com',
            'notes' => 'New customer, first appointment scheduled.',
            'consent_email' => false,
            'consent_sms' => true,
        ]);

        Customer::create([
            'name' => 'Eva Green',
            'phone' => '567-890-1234',
            'email' => 'eva@example.com',
            'notes' => 'Loyal customer, prefers hair services.',
            'consent_email' => true,
            'consent_sms' => true,
        ]);
    }
}