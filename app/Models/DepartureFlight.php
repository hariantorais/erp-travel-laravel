<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartureFlight extends Model
{
    protected $fillable = ['departure_id', 'flight_id', 'type', 'etd', 'eta', 'pnr_code', 'created_by'];

    protected $casts = [
        'etd' => 'datetime',
        'eta' => 'datetime',
    ];

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }
}
