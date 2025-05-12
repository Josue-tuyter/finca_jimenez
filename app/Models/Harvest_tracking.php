<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Harvest_tracking extends Model
{
    protected $fillable = [
        "size",
        "humidity",
        "disease",

    ];

    public function harvest(): HasMany
    {
        return $this->hasMany(Harvest::class);
    }

    public function harvest_plannings(): BelongsTo
    {
        return $this->belongsTo(Harvest_planning::class);
    }


    public function parcel(): BelongsTo
    {
        return $this->belongsTo(Parcel::class);
    }




}
