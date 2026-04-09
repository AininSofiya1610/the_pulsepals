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
        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'vendorPhone')) {
                $table->string('vendorPhone')->nullable()->after('vendorName');
            }
            if (!Schema::hasColumn('vendors', 'vendorEmail')) {
                $table->string('vendorEmail')->nullable()->after('vendorPhone');
            }
            if (!Schema::hasColumn('vendors', 'vendorAddress')) {
                $table->text('vendorAddress')->nullable()->after('vendorEmail');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['vendorPhone', 'vendorEmail', 'vendorAddress']);
        });
    }
};
