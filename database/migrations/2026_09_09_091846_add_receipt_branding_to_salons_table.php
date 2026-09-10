<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('vat_rate');
            $table->string('receipt_primary_color', 7)->nullable()->after('logo_path');
            $table->string('receipt_secondary_color', 7)->nullable()->after('receipt_primary_color');
            $table->string('receipt_template')->default('classic')->after('receipt_secondary_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'receipt_primary_color', 'receipt_secondary_color', 'receipt_template']);
        });
    }
};
