<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Drying extends Model
{
    protected $fillable = [
        "name",
        "number_sacks",
        "available_sacks",
        "weight",
    ];

    public function drying_planning(): BelongsTo
    {
        return $this->belongsTo(Drying_planning::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($drying) {
            $drying->available_sacks = $drying->number_sacks;
        });
    }



}
