<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpsellOpportunity extends Model
{
    protected $fillable = ['customer_name', 'item_bought', 'amount', 'date'];
}
