<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fermentation_planning extends Model
{
    protected $fillable = [
        "name",
        "F_date_start",
        "F_date_end",
        "worker_id",
    ];
    
    public function Fermentation(): HasMany
    {
        return $this->hasMany(Fermentation::class);
    }

    public function fermentation_tracking(): HasMany
    {
        return $this->hasMany(Fermentation_tracking::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }




//     public function setNameAttribute($value)
// {
//     $this->attributes['name'] = 'FERMENTACIÓN ' . $value;
// }

protected static function boot()
{
    parent::boot();
    
    static::creating(function ($model) {
        $lastfermnetation = static::latest()->first();
        $lastNumber = $lastfermnetation ? (int)str_replace('FERMENTACIÓN ', '', $lastfermnetation->name) : 0;
        $model->name = 'FERMENTACIÓN ' . ($lastNumber + 1);
    });
}


    
}
