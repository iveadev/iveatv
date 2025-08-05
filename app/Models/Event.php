<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    //
    protected $fillable = [
        'title',
        'file_id',
        'visible',
        'duration',
        'visibleFrom',
        'visibleTo',
        'visible',
        'sound',
    ];

    protected $with= [
        'file'
    ];

    protected $hidden= [
        'created_at',
        'updated_at'
    ];

    public function file(): BelongsTo {
         return $this->belongsTo(File::class);
    }

    public function programation(): HasMany {
         return $this->hasMany(Programation::class);
    }

    public function getDateRange() {
        $format = 'y-m-d';
        $from = date_create($this->visibleFrom);
        $dif = $diff=date_diff($from,date_create($this->visibleTo));
        $range = [date_format($from, $format)];
        for ($i=0; $i < $dif->days ; $i++) { 
            date_add($from, date_interval_create_from_date_string('1 day'));
            array_push($range,date_format($from, $format));
        }
        return $range;
    }

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
            'sound' => 'boolean',
            'duration' => 'string',
        ];
    }

    function destroyRelatedProgramation() {
        $ids = $this->programation->select('id')->flatten();
        programation::whereIn('id',$ids)->delete();
    }
}
