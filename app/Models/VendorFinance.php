<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorFinance extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'invoice_no',
        'vendor_name',
        'description',
        'invoice_date',
        'due_date',
        'invoice',
        'paid_amount'
    ];

    public function payments()
    {
        return $this->hasMany(VendorPayment::class, 'vendor_finance_id');
    }
}
