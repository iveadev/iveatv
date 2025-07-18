<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    //
    protected $fillable = [
        'filename',
        'title',
        'avalible',
        'url',
    ];
    protected function casts(): array
    {
        return [
            'avalible' => 'boolean',
        ];
}
}
