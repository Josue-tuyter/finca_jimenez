<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fermentation_tracking extends Model
{
    protected $fillable =[
        "name_of_tracking",
        "harvest_id",
        "weight",
        "humidity",
        "temperature",
        "B_weight",
        "total_weight",
        "location_id",
        //"fermentation_id",
    ]; 


    public function fermentation_planning(): BelongsTo
    {
        return $this->belongsTo(Fermentation_planning::class);
    }

    public function harvest()
    {
        return $this->belongsTo(Harvest::class, 'harvest_id');
    }

    public function fermentation(): HasMany
    {
        return $this->hasMany(Fermentation::class);
    }


    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function getSizeAttribute($value)
{
    return $value . ' Lb';
}






}
