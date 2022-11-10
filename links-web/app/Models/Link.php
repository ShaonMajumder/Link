<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;
    protected $guarded = ['_token'];
    protected $casts = [
        'tags' => 'array',
    ];
}
