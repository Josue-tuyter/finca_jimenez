<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drying_planning extends Model
{
    protected $fillable =[
        "name",
        "D_date_start",
        "D_date_end",
        "worker_id",
    ];

    public function drying (): HasMany
    {
        return $this->hasMany(Drying::class);
    }

    public function drying_trackings (): HasMany
    {
        return $this->hasMany(Drying_tracking::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }



    
//     public function setNameAttribute($value)
// {
//     $this->attributes['name'] = 'SECADO ' . $value;
// }
protected static function boot()
{
    parent::boot();
    
    static::creating(function ($model) {
        $lastdrying = static::latest()->first();
        $lastNumber = $lastdrying ? (int)str_replace('SECADO ', '', $lastdrying->name) : 0;
        $model->name = 'SECADO ' . ($lastNumber + 1);
    });
}


}
