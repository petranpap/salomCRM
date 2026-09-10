<?php

namespace App\Console\Commands;

use App\Models\Salon;
use App\Models\User;
use Illuminate\Console\Command;

class SetupSalon extends Command
{
    protected $signature   = 'salon:setup {--name=My Salon}';
    protected $description = 'Create the first salon and assign all existing users to it';

    public function handle(): int
    {
        $name = $this->option('name');

        $salon = Salon::firstOrCreate(
            ['name' => $name],
            ['timezone' => 'Europe/Nicosia', 'is_active' => true]
        );

        $updated = User::whereNull('salon_id')->update(['salon_id' => $salon->id]);

        // Make the first user admin
        $firstUser = User::where('salon_id', $salon->id)->orderBy('id')->first();
        if ($firstUser && $firstUser->role !== 'owner') {
            $firstUser->update(['role' => 'owner']);
            $this->info("Set {$firstUser->email} as owner.");
        }

        $this->info("Salon \"{$salon->name}\" ready (ID: {$salon->id}). Assigned {$updated} users.");

        return self::SUCCESS;
    }
}
