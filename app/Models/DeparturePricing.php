<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeparturePricing extends Model
{
    protected $guarded = ['id'];
    public function departure()
    {
        return $this->belongsTo(Departure::class);
    }
}
