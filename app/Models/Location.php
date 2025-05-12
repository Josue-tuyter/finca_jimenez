<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        "name",
        "description",
    ];


    public function fermentation_tracking(): HasMany
    {
        return $this->Hasmany(Fermentation_tracking::class);
    }
}