<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('consent_viber')->default(false)->after('consent_sms');
            $table->string('viber_user_id')->nullable()->after('consent_viber');
            $table->string('viber_link_token')->nullable()->after('viber_user_id');
            $table->timestamp('viber_link_token_expires_at')->nullable()->after('viber_link_token');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['consent_viber', 'viber_user_id', 'viber_link_token', 'viber_link_token_expires_at']);
        });
    }
};
