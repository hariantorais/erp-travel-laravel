<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AirlineSeeder extends Seeder
{
    public function run(): void
    {
        $airlines = [
            [
                'name' => 'Saudi Arabian Airlines',
                'code' => 'SV',
                'logo_url' => 'logos/saudia.png',
            ],
            [
                'name' => 'Garuda Indonesia',
                'code' => 'GA',
                'logo_url' => 'logos/garuda.png',
            ],
            [
                'name' => 'Lion Air',
                'code' => 'JT',
                'logo_url' => 'logos/lion.png',
            ],
            [
                'name' => 'Batik Air Malaysia',
                'code' => 'OD',
                'logo_url' => 'logos/batik-my.png',
            ],
            [
                'name' => 'Emirates',
                'code' => 'EK',
                'logo_url' => 'logos/emirates.png',
            ],
            [
                'name' => 'Qatar Airways',
                'code' => 'QR',
                'logo_url' => 'logos/qatar.png',
            ],
            [
                'name' => 'Oman Air',
                'code' => 'WY',
                'logo_url' => 'logos/oman.png',
            ],
            [
                'name' => 'Scoot',
                'code' => 'TR',
                'logo_url' => 'logos/scoot.png',
            ],
        ];

        foreach ($airlines as $airline) {
            DB::table('airlines')->insert([
                'uuid' => (string) Str::uuid(),
                'name' => $airline['name'],
                'code' => $airline['code'],
                'logo_url' => $airline['logo_url'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
