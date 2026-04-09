<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('vendors', function (Blueprint $table) {
        $table->decimal('total_paid', 10, 2)->default(0)->after('vendorAddress');
    });
}

public function down()
{
    Schema::table('vendors', function (Blueprint $table) {
        $table->dropColumn('total_paid');
    });
}
};
