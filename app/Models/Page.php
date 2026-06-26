<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['slug', 'title', 'content_json'];

    protected $casts = [
        'content_json' => 'array',
    ];
}
