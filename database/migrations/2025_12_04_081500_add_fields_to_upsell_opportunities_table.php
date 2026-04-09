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
        Schema::table('upsell_opportunities', function (Blueprint $table) {
            if (!Schema::hasColumn('upsell_opportunities', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn('upsell_opportunities', 'item_bought')) {
                $table->string('item_bought')->nullable();
            }
            if (!Schema::hasColumn('upsell_opportunities', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('upsell_opportunities', 'date')) {
                $table->date('date')->nullable();
            }

            // Make legacy columns nullable
            if (Schema::hasColumn('upsell_opportunities', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->change();
            }
            if (Schema::hasColumn('upsell_opportunities', 'opportunity_name')) {
                $table->string('opportunity_name')->nullable()->change();
            }
            if (Schema::hasColumn('upsell_opportunities', 'revenue_amount')) {
                $table->decimal('revenue_amount', 10, 2)->nullable()->change();
            }
            if (Schema::hasColumn('upsell_opportunities', 'status')) {
                $table->string('status')->nullable()->change();
            }
            if (Schema::hasColumn('upsell_opportunities', 'close_date')) {
                $table->date('close_date')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upsell_opportunities', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'item_bought', 'amount', 'date']);
        });
    }
};
