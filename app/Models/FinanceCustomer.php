<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * FinanceCustomer — used ONLY by the Finance module.
 *
 * This is the customer name list for billing/invoices.
 * Completely separate from CRM Customer (Lead conversions, Deals, Activities).
 *
 * Table: finance_customers
 */
class FinanceCustomer extends Model
{
    use HasFactory;

    protected $table = 'finance_customers';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];
}
