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
        Schema::create('vendor_finances', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no');
            $table->string('vendor_name');
            $table->text('description')->nullable();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('invoice', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_finances');
    }
};