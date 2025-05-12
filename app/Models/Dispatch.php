<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispatch extends Model
{
    protected $fillable = [
        'name',
        'number_sacks',
        'original_number_sacks',
        'delivery_date',
        'client_id',
    ];


    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dispatch) {
            $lastDrying = Drying::latest('created_at')->first();

            if (!$lastDrying) {
                throw new \Exception('No se encontró un registro de secado.');
            }

            if ($dispatch->number_sacks > $lastDrying->available_sacks) {
                throw new \Exception('No hay suficientes sacos disponibles para despachar.');
            }

            // Guardar el número original de sacos disponibles
            $dispatch->original_number_sacks = $lastDrying->available_sacks;

            // Actualizar solo los sacos disponibles
            $lastDrying->available_sacks -= $dispatch->number_sacks;
            $lastDrying->save();
        });

        static::deleting(function ($dispatch) {
            $lastDrying = Drying::latest('created_at')->first();

            if ($lastDrying) {
                $lastDrying->available_sacks += $dispatch->number_sacks;
                $lastDrying->save();
            }
        });
    }

    /**
     * Método para obtener el número de sacos del último secado.
     */
    public function getLastDryingSacks()
    {
        $lastDrying = Drying::latest('created_at')->first();
        return $lastDrying?->available_sacks ?? 0;
    }

    

}
