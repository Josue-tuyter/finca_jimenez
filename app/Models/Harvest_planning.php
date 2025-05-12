<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Harvest_planning extends Model
{
    protected $fillable = [
        'name',
        'date_start',
        'date_end',
        'parcel_id',
        'worker_id',
    ];

    //relacion de un seguimiento a muchas cosechas
    public function harvest(): HasMany{

        return $this ->hasMany(Harvest::class);
    }

    public function harvest_tracking()
    {
        return $this->hasMany(Harvest_tracking::class);
    }

    //realacion de  planificacion tarea a una parcela
    public function parcel(): BelongsTo
    {
        return $this->belongsTo(Parcel::class);
    }


    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    // public function setNameAttribute($value)
    // {
    //     $this->attributes['name'] = 'COSECHA ' . $value;
    // }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $lastharvest = static::latest()->first();
            $lastNumber = $lastharvest ? (int)str_replace('COSECHA ', '', $lastharvest->name) : 0;
            $model->name = 'COSECHA ' . ($lastNumber + 1);
        });
    }    


}
