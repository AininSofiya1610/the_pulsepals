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
        Schema::create('company_finances', function (Blueprint $table) {
            $table->id();
            $table->decimal('mbb_balance', 15, 2)->default(0);
            $table->decimal('rhb_balance', 15, 2)->default(0);
            $table->decimal('net_pay', 15, 2)->default(0); // For staff salary
            $table->date('record_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_finances');
    }
};
