<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Departure extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'closed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(fn($d) => $d->uuid = $d->uuid ?? Str::uuid());
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function pricings()
    {
        return $this->hasMany(DeparturePricing::class);
    }
}
