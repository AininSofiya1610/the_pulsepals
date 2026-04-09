<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientEngagement extends Model
{
    protected $fillable = ['customer_name', 'activity_type', 'date', 'notes'];
}
