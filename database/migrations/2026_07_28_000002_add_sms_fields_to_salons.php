<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Each salon picks its own SMS gateway and supplies its own account details from
// Settings — see SalonSettingsController / App\Services\Sms\SmsManager. Credentials
// are stored encrypted (Salon::$casts — encrypted:array) since sms_credentials can
// hold a real gateway secret key.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->string('sms_driver')->nullable()->after('opening_hours');
            $table->text('sms_credentials')->nullable()->after('sms_driver');
        });
    }

    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            $table->dropColumn(['sms_driver', 'sms_credentials']);
        });
    }
};
