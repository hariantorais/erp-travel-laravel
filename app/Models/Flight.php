<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Flight extends Model
{
    protected $fillable = ['airline_id', 'flight_no', 'departure_airport', 'arrival_airport'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class);
    }
}
