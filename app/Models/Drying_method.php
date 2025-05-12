<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drying_method extends Model
{
    protected $fillable = [
        "name",
        "description",
    ];

    public function drying (): HasMany
    {
        return $this->hasMany(Drying::class);
    }


}
