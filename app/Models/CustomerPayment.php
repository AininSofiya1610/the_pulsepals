<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    protected $fillable = [
        'customer_finance_id',
        'payment_date',
        'amount',
    ];

    public function customerFinance()
    {
        return $this->belongsTo(CustomerFinance::class);
    }
}