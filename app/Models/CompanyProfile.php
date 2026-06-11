<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CompanyProfile extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'brand_name',
        'address',
        'phone',
        'email',
        'siskopatuh_piu_code',
        'siskopatuh_user',
        'siskopatuh_pass_encrypted',
        'travel_license_no',
    ];

    protected $hidden = ['siskopatuh_pass_encrypted'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('stamp')->singleFile();
    }
}
