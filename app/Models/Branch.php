<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'is_main_office' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: 1 cabang punya banyak user
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi: 1 cabang punya banyak booking
     */
}
