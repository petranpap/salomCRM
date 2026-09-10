<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('salon_id')->nullable()->after('id')
                  ->constrained('salons')->nullOnDelete();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('salon_id')->nullable()->after('id')
                  ->constrained('salons')->nullOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('salon_id')->nullable()->after('id')
                  ->constrained('salons')->nullOnDelete();
        });

        // Backfill appointments.salon_id from their customer's salon_id. Written as a
        // per-row update rather than a raw UPDATE...JOIN so this migration runs on any
        // database engine (the JOIN syntax is MySQL-specific and breaks on SQLite,
        // which the test suite uses).
        DB::table('appointments')
            ->whereNull('salon_id')
            ->orderBy('id')
            ->chunkById(500, function ($appointments) {
                foreach ($appointments as $appointment) {
                    $customerSalonId = DB::table('customers')
                        ->where('id', $appointment->customer_id)
                        ->value('salon_id');

                    if ($customerSalonId !== null) {
                        DB::table('appointments')
                            ->where('id', $appointment->id)
                            ->update(['salon_id' => $customerSalonId]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('appointments', fn($t) => $t->dropForeign(['salon_id']));
        Schema::table('appointments', fn($t) => $t->dropColumn('salon_id'));

        Schema::table('services', fn($t) => $t->dropForeign(['salon_id']));
        Schema::table('services', fn($t) => $t->dropColumn('salon_id'));

        Schema::table('products', fn($t) => $t->dropForeign(['salon_id']));
        Schema::table('products', fn($t) => $t->dropColumn('salon_id'));
    }
};
