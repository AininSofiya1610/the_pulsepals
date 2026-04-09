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
        Schema::table('leads', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('leads', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('leads', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('leads', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('leads', 'source')) {
                $table->string('source')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'assigned_to')) {
                $table->dropForeign(['assigned_to']);
                $table->dropColumn('assigned_to');
            }
            if (Schema::hasColumn('leads', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('leads', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('leads', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
