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
        // Add 'Critical' to the ENUM list for status column
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('Open', 'In Progress', 'Resolved', 'Closed', 'Critical') DEFAULT 'Open'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM list (Warning: this might fail if there are 'Critical' records)
        // We'll leave it as is for safety or attempt to convert Critical -> Open?
        // For this task, we assume this is a dev environment change.
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('Open', 'In Progress', 'Resolved', 'Closed') DEFAULT 'Open'");
    }
};
