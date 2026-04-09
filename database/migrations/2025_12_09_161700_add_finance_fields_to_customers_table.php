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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'customerName')) {
                $table->string('customerName')->nullable()->after('id');
            }
            if (!Schema::hasColumn('customers', 'customerPhone')) {
                $table->string('customerPhone')->nullable()->after('customerName');
            }
            if (!Schema::hasColumn('customers', 'customerEmail')) {
                $table->string('customerEmail')->nullable()->after('customerPhone');
            }
            if (!Schema::hasColumn('customers', 'customerAddress')) {
                $table->text('customerAddress')->nullable()->after('customerEmail');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['customerName', 'customerPhone', 'customerEmail', 'customerAddress']);
        });
    }
};
