<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DemoStaffSeeder::class,
            DemoServicesSeeder::class,
            DemoCustomersSeeder::class,
            DemoProductsSeeder::class,
        ]);
    }
}