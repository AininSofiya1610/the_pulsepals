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
        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->string('guest_email')->nullable()->after('user_id');
            $table->boolean('is_staff')->default(false)->after('guest_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->dropColumn(['guest_email', 'is_staff']);
        });
    }
};
