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
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('public_token', 64)->nullable()->after('ticket_id');
            $table->index('public_token');
        });
        
        // Generate tokens for existing tickets
        $tickets = \App\Models\Ticket::whereNull('public_token')->get();
        foreach ($tickets as $ticket) {
            $ticket->update(['public_token' => \Illuminate\Support\Str::random(32)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
