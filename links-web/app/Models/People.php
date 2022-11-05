<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Ui\Presets\Vue;

class People extends Model
{
    use HasFactory;
    protected $guarded = ['_token'];

    public function values(){
        return $this->hasMany(Value::class);
    }
}
