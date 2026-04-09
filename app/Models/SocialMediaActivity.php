<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialMediaActivity extends Model
{
    protected $fillable = ['platform', 'activity_type', 'likes', 'comments', 'shares', 'date'];
}
