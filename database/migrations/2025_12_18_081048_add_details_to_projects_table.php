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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->enum('status', ['green', 'yellow', 'red'])->default('green'); // green=On Track, yellow=At Risk, red=Delayed
            $table->date('deadline')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['name', 'status', 'deadline', 'description']);
            $table->dropSoftDeletes();
        });
    }
};
