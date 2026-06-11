<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        CompanyProfile::create([
            'name' => 'PT Travel Barokah Makmur',
            'brand_name' => 'Barokah Umroh',
            'address' => 'Jl. Mekah No. 1, Jakarta Pusat',
            'phone' => '021-1234567',
            'email' => 'info@barokahumroh.com',
            'siskopatuh_piu_code' => '12345',
            'travel_license_no' => 'SK.123/PPIU/2020',
        ]);
    }
}
