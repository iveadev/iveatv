<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    //
    protected $fillable = [
        'filename',
        'type',
        'name',
        'avalible',
        'url',
    ];

    protected $appends = [
        'color',
    ];

    protected function casts(): array
    {
        return [
            'avalible' => 'boolean',
        ];
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtolower(substr($value,0,5)),
        );
    }

    public function getColorAttribute() : object{
      return (object)[
        'text-lime-500'=> $this->avalible && $this->type == 'image', 
        'text-emerald-500' => $this->avalible && $this->type == 'video',
        'text-gray-400' => !$this->avalible,
      ];
    }
}
