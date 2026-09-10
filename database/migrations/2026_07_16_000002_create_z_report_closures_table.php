<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('z_report_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sequence_number');
            $table->date('report_date');
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->decimal('cash_revenue', 10, 2)->default(0);
            $table->decimal('card_revenue', 10, 2)->default(0);
            $table->decimal('retail_revenue', 10, 2)->default(0);
            $table->decimal('vat_collected', 10, 2)->default(0);
            $table->decimal('refunds_total', 10, 2)->default(0);
            $table->unsignedInteger('receipt_count')->default(0);
            $table->foreignId('closed_by')->constrained('users');
            $table->timestamp('closed_at');
            $table->timestamps();

            $table->unique(['salon_id', 'report_date']);
            $table->unique(['salon_id', 'sequence_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('z_report_closures');
    }
};
