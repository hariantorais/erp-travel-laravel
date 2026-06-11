<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Bus extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'plate_number',
        'capacity',
        'vendor_name',
        'driver_name',
        'driver_phone',
        'is_active'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty();
    }

    protected static function booted()
    {
        static::creating(fn($model) => $model->created_by = auth()->guard('web')->id());
        static::updating(fn($model) => $model->updated_by = auth()->guard('web')->id());
    }
}
