<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename existing 'admin' role to 'owner' (salon-scoped admin)
        DB::table('users')->where('role', 'admin')->update(['role' => 'owner']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'owner')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
    }
};
