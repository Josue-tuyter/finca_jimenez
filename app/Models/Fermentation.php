<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fermentation extends Model
{
    protected $fillable = [
        "F_name",
        "humidity",
        "fermentation_tracking_id",
        "F_total_weight",
    ] ;


    public function fermentation_planning(): BelongsTo
    {
        return $this->belongsTo(Fermentation_planning::class);
    }

    
    public function fermentation_tracking(): HasMany
    {
        return $this->hasMany(Fermentation_tracking::class);
    }

    public function dryingTrackings()
    {
        return $this->belongsToMany(Drying_tracking::class, 'drying_tracking_fermentation');
    }

    

}
