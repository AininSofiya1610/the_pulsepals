<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    protected $fillable = [
        'vendor_finance_id',
        'payment_date',
        'amount'
    ];

    public function vendorFinance()
    {
        return $this->belongsTo(VendorFinance::class, 'vendor_finance_id');
    }
}