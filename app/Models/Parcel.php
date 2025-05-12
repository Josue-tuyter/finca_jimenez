<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parcel extends Model
{
    public function harvest(): HasMany
    {
        return $this->hasMany(Harvest::class);
    }


    protected $fillable = [
        "name",
        "description",
        "length",
    ];
}
