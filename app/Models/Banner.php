<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    //
    protected $fillable= [
        'title',
        'type',
        'url',
        'duration',
        'visibleFrom',
        'visibleTo',
        'visible',
    ];
}
