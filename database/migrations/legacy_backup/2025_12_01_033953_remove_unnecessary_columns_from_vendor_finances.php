<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendor_finances', function (Blueprint $table) {
            $table->dropColumn(['paid', 'balance', 'overdue', 'status']);
        });
    }

    public function down()
    {
        Schema::table('vendor_finances', function (Blueprint $table) {
            $table->decimal('paid', 10, 2)->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->boolean('overdue')->default(false);
            $table->string('status')->nullable();
        });
    }
};
