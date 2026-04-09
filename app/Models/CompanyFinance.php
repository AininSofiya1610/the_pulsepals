<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyFinance extends Model
{
    use HasFactory;

    protected $fillable = [
        'mbb_balance',
        'rhb_balance',
        'net_pay',
        'record_date',
    ];

    protected $casts = [
        'record_date' => 'date',
        'mbb_balance' => 'decimal:2',
        'rhb_balance' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];
}
