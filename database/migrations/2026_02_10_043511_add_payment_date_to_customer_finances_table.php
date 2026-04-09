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
        Schema::table('customer_finances', function (Blueprint $table) {
            // Add payment_date to track when payment was actually received
            // This is separate from invoice_date (when invoice was issued)
            $table->date('payment_date')->nullable()->after('invoice_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_finances', function (Blueprint $table) {
            $table->dropColumn('payment_date');
        });
    }
};
