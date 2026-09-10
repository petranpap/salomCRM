<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class MakeSuperAdmin extends Command
{
    protected $signature   = 'make:superadmin';
    protected $description = 'Create or promote a user to platform super admin (no salon restriction)';

    public function handle(): int
    {
        $this->info('--- Platform Super Admin Setup ---');

        $email = $this->ask('Email address');

        $existing = User::where('email', $email)->first();

        if ($existing) {
            if ($existing->isSuperAdmin()) {
                $this->warn("{$email} is already a super admin.");
                return self::SUCCESS;
            }

            if ($this->confirm("User {$existing->name} exists (role: {$existing->role}). Promote to super_admin?")) {
                $existing->update(['role' => 'super_admin', 'salon_id' => null]);
                $this->info("Done. {$existing->email} is now a super admin.");
            }

            return self::SUCCESS;
        }

        $name     = $this->ask('Full name');
        $password = $this->secret('Password');

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => ['required', Password::defaults()]]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        User::create([
            'name'      => $name,
            'email'     => $email,
            'password'  => Hash::make($password),
            'role'      => 'super_admin',
            'salon_id'  => null,
        ]);

        $this->info("Super admin account created for {$email}.");
        $this->info('This account has full access to all salons and users.');

        return self::SUCCESS;
    }
}
