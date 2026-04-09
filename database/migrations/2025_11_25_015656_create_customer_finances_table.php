<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::create('customer_finances', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_no');
        $table->string('customer_name');
        $table->string('type');
        $table->text('description')->nullable();
        $table->date('invoice_date');
        $table->date('due_date');
        $table->decimal('amount', 10, 2);
        $table->decimal('cogs', 10, 2);
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('customer_finances');
    }
};