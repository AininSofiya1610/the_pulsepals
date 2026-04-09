<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFinance extends Model
{
    protected $fillable = [
        'invoice_no',
        'customer_name', 
        'invoice_date',
        'payment_date',
        'due_date',
        'amount',
        'received_amount',
        'type',
        'cogs',
        'description'
    ];

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }
}