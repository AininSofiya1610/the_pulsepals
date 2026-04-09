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
        Schema::table('client_engagements', function (Blueprint $table) {
            if (!Schema::hasColumn('client_engagements', 'date')) {
                $table->date('date')->nullable();
            }
            // Make legacy columns nullable to avoid strict errors
            if (Schema::hasColumn('client_engagements', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->change();
            }
            if (Schema::hasColumn('client_engagements', 'activity_date')) {
                $table->date('activity_date')->nullable()->change();
            }
            if (Schema::hasColumn('client_engagements', 'activity_type')) {
                $table->string('activity_type')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_engagements', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'activity_type', 'date', 'notes']);
        });
    }
};
