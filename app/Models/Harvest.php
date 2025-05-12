<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Harvest_planning;


class Harvest extends Model
{
    protected $fillable = [
        "name",
        "number_buckests",
        "weight",
    ];

    //relacio de muchas cosechas a un trabajador
    // public function worker(): BelongsTo
    // {
    //     return $this->belongsTo(Worker::class);
    // }

    //relacion de muchas cosehcas a una parcela
    // public function parcel(): BelongsTo
    // {
    //     return $this->belongsTo(Parcel::class);
    // }

    // //relalcion de muchas cosehca a una planificacion de cosecha
    public function harvest_plannings(): BelongsTo
    {
        return $this->belongsTo(Harvest_planning::class);
    }

    //relacion de muchas cosechas a un seguimiento
    public function harvest_trackings(): BelongsTo
    {
        return $this->belongsTo(Harvest_tracking::class);
    }

    public function fermentation_trackings(): HasMany
    {
        return $this->hasMany(Fermentation_tracking::class);
    }



    public function getSizeAttribute($value)
{
    return $value . ' Lb';
}




}
