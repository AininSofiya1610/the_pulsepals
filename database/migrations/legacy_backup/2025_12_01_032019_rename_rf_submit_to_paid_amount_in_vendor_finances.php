<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Schema::table('vendor_finances', function (Blueprint $table) {
        //     $table->renameColumn('rf_submit', 'paid_amount');
        // });
    }

    public function down()
    {
        Schema::table('vendor_finances', function (Blueprint $table) {
            $table->renameColumn('paid_amount', 'rf_submit');
        });
    }
};
