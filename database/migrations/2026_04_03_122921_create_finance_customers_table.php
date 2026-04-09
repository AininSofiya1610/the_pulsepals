<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Create the finance_customers table and copy existing customer data into it.
     *
     * This separates Finance customers from CRM customers:
     *   - `customers` table → CRM only (Lead conversions, Deals, Activities)
     *   - `finance_customers` table → Finance only (billing name list, Excel import)
     */
    public function up(): void
    {
        Schema::create('finance_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Copy existing customers into finance_customers
        // Handles both old (customerName) and new (name) column naming
        $customers = DB::table('customers')->get();
        $hasCustomerName    = Schema::hasColumn('customers', 'customerName');
        $hasCustomerPhone   = Schema::hasColumn('customers', 'customerPhone');
        $hasCustomerEmail   = Schema::hasColumn('customers', 'customerEmail');
        $hasCustomerAddress = Schema::hasColumn('customers', 'customerAddress');

        foreach ($customers as $customer) {
            $name = $customer->name ?? 'Unknown';
            if ($hasCustomerName && !empty($customer->customerName)) {
                $name = $customer->customerName;
            }

            DB::table('finance_customers')->insert([
                'name'       => $name,
                'phone'      => ($hasCustomerPhone ? ($customer->customerPhone ?? null) : null) ?: ($customer->phone ?? null),
                'email'      => ($hasCustomerEmail ? ($customer->customerEmail ?? null) : null) ?: ($customer->email ?? null),
                'address'    => $hasCustomerAddress ? ($customer->customerAddress ?? null) : null,
                'created_at' => $customer->created_at,
                'updated_at' => $customer->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_customers');
    }
};
