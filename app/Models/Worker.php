<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Worker extends Model
{
    public function harvest_planning(): HasMany
    {
        return $this->hasMany(Harvest_planning::class);
    }


    public function fermentation_planning(): HasMany
    {
        return $this->hasMany(Fermentation_planning::class);
    }


    public function drying_planning(): HasMany
    {
        return $this->hasMany(Drying_planning::class);
    }

    public function isOccupied(): bool
    {
        $today = now()->toDateString();
    
        $isOccupiedInHarvest = $this->harvest_planning()->where('date_end', '>=', $today)->exists();
        $isOccupiedInFermentation = $this->fermentation_planning()->where('F_date_end', '>=', $today)->exists();
        $isOccupiedInDrying = $this->drying_planning()->where('D_date_end', '>=', $today)->exists();
    
        return $isOccupiedInHarvest || $isOccupiedInFermentation || $isOccupiedInDrying;
    }



    protected $fillable = [
        "email",
        "name",
        "phone",
        "cedula",
    ];
}
