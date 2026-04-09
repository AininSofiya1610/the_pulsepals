<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'email', 
        'phone', 
        'company', 
        'status', 
        'created_from_lead',
        // For Finance module compatibility
        'customerName',
        'customerPhone',
        'customerEmail',
        'customerAddress',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'created_from_lead');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
