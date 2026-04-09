<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteVisitor extends Model
{
    protected $fillable = ['page_visited', 'visitor_ip', 'date'];
}
