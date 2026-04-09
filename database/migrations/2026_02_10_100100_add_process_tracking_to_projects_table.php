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
            // Stage 1: Order from Vendor
            $table->date('order_date')->nullable()->after('description');
            $table->string('vendor_name')->nullable()->after('order_date');
            $table->string('po_number')->nullable()->after('vendor_name');
            
            // Stage 2: Delivery to Microlab
            $table->date('delivery_date')->nullable()->after('po_number');
            $table->string('received_by')->nullable()->after('delivery_date');
            
            // Stage 3: Installation/Customer Delivery
            $table->date('installation_date')->nullable()->after('received_by');
            $table->string('installed_by')->nullable()->after('installation_date');
            
            // Stage 4: Closing
            $table->date('closing_date')->nullable()->after('installed_by');
            $table->text('closing_notes')->nullable()->after('closing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'order_date', 'vendor_name', 'po_number',
                'delivery_date', 'received_by',
                'installation_date', 'installed_by',
                'closing_date', 'closing_notes'
            ]);
        });
    }
};
