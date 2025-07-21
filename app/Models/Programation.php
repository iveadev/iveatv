<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Programation extends Model
{
    //

    protected $fillable = [
        'date',
        'event_id',
        'duration',
        'visible',
        'order',
    ];
    protected $with = [
        'event',
    ];

    function event() : BelongsTo {
        return $this->belongsTo(Event::class);
    }
}
