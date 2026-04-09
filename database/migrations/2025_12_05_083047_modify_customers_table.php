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
            // Check if customerName exists, if so we need to manually handle the migration
            // For now, just add the new columns
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('customers', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('customers', 'company')) {
                $table->string('company')->nullable();
            }
            if (!Schema::hasColumn('customers', 'status')) {
                $table->string('status')->default('active');
            }
            if (!Schema::hasColumn('customers', 'created_from_lead')) {
                $table->foreignId('created_from_lead')->nullable()->constrained('leads')->onDelete('set null');
            }
        });
        
        // Rename customerName to name if it exists (requires doctrine/dbal)
        if (Schema::hasColumn('customers', 'customerName') && !Schema::hasColumn('customers', 'name')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->renameColumn('customerName', 'name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'created_from_lead')) {
                $table->dropForeign(['created_from_lead']);
                $table->dropColumn('created_from_lead');
            }
            if (Schema::hasColumn('customers', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('customers', 'company')) {
                $table->dropColumn('company');
            }
            if (Schema::hasColumn('customers', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('customers', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
