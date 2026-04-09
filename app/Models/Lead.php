<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

  protected $fillable = [
    'name',
    'email',
    'phone',
    'source',
    'status',
    'assigned_to'

    ];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'created_from_lead');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
