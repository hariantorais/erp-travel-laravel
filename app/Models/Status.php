<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    // Mengaktifkan mass assignment hanya untuk kolom yang diizinkan
    protected $fillable = ['group', 'code', 'name', 'color', 'order'];

    // Nonaktifkan timestamps karena tabel status bersifat statis (Master Data)
    public $timestamps = false;

    /**
     * Relasi ke tabel Booking (One to Many)
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
