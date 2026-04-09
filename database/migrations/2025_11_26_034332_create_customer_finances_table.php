<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customer_finances', function (Blueprint $table) {
            if (Schema::hasColumn('customer_finances', 'paid')) {
                $table->dropColumn(['paid']);
            }
            if (Schema::hasColumn('customer_finances', 'balance')) {
                $table->dropColumn(['balance']);
            }
            if (Schema::hasColumn('customer_finances', 'status')) {
                $table->dropColumn(['status']);
            }
            if (Schema::hasColumn('customer_finances', 'overdue')) {
                $table->dropColumn(['overdue']);
            }
        });
    }

    public function down()
    {
        Schema::table('customer_finances', function (Blueprint $table) {
            $table->decimal('paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('status')->default('Pending');
            $table->boolean('overdue')->default(false);
        });
    }
};