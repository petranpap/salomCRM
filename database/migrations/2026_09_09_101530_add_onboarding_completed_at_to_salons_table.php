<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->timestamp('onboarding_completed_at')->nullable()->after('receipt_template');
        });

        // Existing salons predate the onboarding wizard — mark them already onboarded so
        // it only ever triggers for salons created from this point on, not retroactively.
        DB::table('salons')->update(['onboarding_completed_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->dropColumn('onboarding_completed_at');
        });
    }
};
