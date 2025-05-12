<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Drying_tracking extends Model
{
    protected $fillable = [
        "name",
        "drying_method_id",
        "humidity",
        "color", 
        "textura",
        "moho",

    ];

    public function drying(): HasMany
    {
        return $this->hasMany(Drying::class);
    }


    public function drying_planning(): BelongsTo
    {
        return $this->belongsTo(Drying_planning::class);
    }



    public function drying_method(): BelongsTo
    {
        return $this->belongsTo(Drying_method::class);
    }



    public function fermentations()
    {
        return $this->belongsToMany(Fermentation::class, 'fermentation_drying', 'drying_id', 'fermentation_id');
    }
    



}
