<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $casts = ['parent_id' => 'array'];
    use HasFactory;

    protected $fillable = ['name','causer_id','parent_id'];

    function createdBy(){
        return $this->belongsTo(User::class,'causer_id');
    }

    function childTags(){
        return $this->hasMany(Tag::class,'parent_id');
    }

    function parent(){
        
    }



 

    

}
