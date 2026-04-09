<?php

namespace Database\Factories;

use App\Models\VendorFinance;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFinanceFactory extends Factory
{
    protected $model = VendorFinance::class;

    public function definition()
    {
        // Generate total bill amount between RM 1,000 and RM 50,000
        $totalAmount = $this->faker->numberBetween(1000, 50000);
        
        // Randomly determine payment status
        $paymentStatus = $this->faker->randomElement(['paid', 'partial', 'unpaid']);
        
        // Calculate paid amount based on status
        $paidAmount = match ($paymentStatus) {
            'paid' => $totalAmount,  // Fully paid
            'partial' => $this->faker->numberBetween(100, $totalAmount - 100),  // Partially paid
            'unpaid' => 0,  // Not paid yet
        };
        
        // Generate dates
        $invoiceDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $dueDate = $this->faker->dateTimeBetween($invoiceDate, '+30 days');
        
        return [
            'invoice_no' => 'VND-' . $this->faker->unique()->numerify('######'),
            'vendor_name' => $this->faker->company(),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'invoice' => $totalAmount,
            'paid_amount' => $paidAmount,
            'description' => $this->faker->sentence(),
        ];
    }
    
    /**
     * Create a fully paid transaction
     */
    public function paid()
    {
        return $this->state(function (array $attributes) {
            return [
                'paid_amount' => $attributes['invoice'],
            ];
        });
    }
    
    /**
     * Create a partially paid transaction
     */
    public function partial()
    {
        return $this->state(function (array $attributes) {
            return [
                'paid_amount' => $this->faker->numberBetween(100, $attributes['invoice'] - 100),
            ];
        });
    }
    
    /**
     * Create an unpaid transaction
     */
    public function unpaid()
    {
        return $this->state(function (array $attributes) {
            return [
                'paid_amount' => 0,
            ];
        });
    }
}
