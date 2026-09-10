<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('working_hours');
            $table->string('specialty', 100)->nullable()->after('color');
            $table->boolean('is_active')->default(true)->after('specialty');
        });
    }

    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropColumn(['color', 'specialty', 'is_active']);
        });
    }
};
