<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change ENUM columns to STRING to allow dynamic values
        DB::statement("ALTER TABLE tickets MODIFY priority VARCHAR(255) DEFAULT 'Medium'");
        DB::statement("ALTER TABLE tickets MODIFY ticket_type VARCHAR(255) DEFAULT 'CM'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM (data loss may occur if non-enum values exist)
        DB::statement("ALTER TABLE tickets MODIFY priority ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium'");
        DB::statement("ALTER TABLE tickets MODIFY ticket_type ENUM('CM', 'PM') DEFAULT 'CM'");
    }
};
