<?php

namespace App\Models;

use App\Http\Controllers\LinkController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;
    protected $guarded = ['_token'];
    protected $casts = [
        'tags' => 'array',
    ];
    protected $appends = ['tag_label'];

    public function getTagLabelAttribute()
    {
        return $this->attributes['tag_label'] = (new LinkController())->getTags($this->tags) ; //some logic to return numbers
    }

    
}
