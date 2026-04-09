<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Changes unit_id foreign key from CASCADE to SET NULL
     * This ensures tickets are preserved even if a unit is hard-deleted.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['unit_id']);
            
            // Make column nullable
            $table->foreignId('unit_id')->nullable()->change();
        });

        Schema::table('tickets', function (Blueprint $table) {
            // Re-add foreign key with SET NULL behavior
            $table->foreign('unit_id')
                  ->references('id')
                  ->on('units')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('unit_id')
                  ->references('id')
                  ->on('units')
                  ->onDelete('cascade');
        });
    }
};
