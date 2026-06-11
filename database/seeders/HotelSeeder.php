<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [
            // ==================== COLA MAKKAH ====================
            [
                'name' => 'Pullman ZamZam Makkah',
                'city' => 'makkah',
                'stars' => 5,
                'distance_to_haram' => 50, // Di dalam kompleks Abraj Al Bait
                'address' => 'Abraj Al Bait Complex, Makkah',
            ],
            [
                'name' => 'Anjum Hotel Makkah',
                'city' => 'makkah',
                'stars' => 5,
                'distance_to_haram' => 250, // Sektor Jabal Kaaba
                'address' => 'Umm Al Qura Street, Jabal Kaaba, Makkah',
            ],
            [
                'name' => 'Swissôtel Makkah',
                'city' => 'makkah',
                'stars' => 5,
                'distance_to_haram' => 50,
                'address' => 'King Abdul Aziz Endowment, Ajyad Street, Makkah',
            ],
            [
                'name' => 'Le Méridien Towers Makkah',
                'city' => 'makkah',
                'stars' => 5,
                'distance_to_haram' => 1500, // Menggunakan Shuttle Bus Kudai
                'address' => 'Kudai Road, Makkah',
            ],
            [
                'name' => 'Elaf Kinda Hotel',
                'city' => 'makkah',
                'stars' => 4,
                'distance_to_haram' => 100,
                'address' => 'Al Mesial Street, Makkah',
            ],
            [
                'name' => 'Al Massa Hotel Makkah',
                'city' => 'makkah',
                'stars' => 4,
                'distance_to_haram' => 150,
                'address' => 'Ajyad Street, Makkah',
            ],
            [
                'name' => 'Fajr Al Badea 2',
                'city' => 'makkah',
                'stars' => 3,
                'distance_to_haram' => 650, // Standar Paket Ekonomi
                'address' => 'Ajyad Rey Bakhsh, Makkah',
            ],

            // ==================== KOTA MADINAH ====================
            [
                'name' => 'Anwar Al Madinah Mövenpick',
                'city' => 'madinah',
                'stars' => 5,
                'distance_to_haram' => 50, // Menempel di pelataran Utara
                'address' => 'Central Northern Area, Madinah',
            ],
            [
                'name' => 'Maden Hotel',
                'city' => 'madinah',
                'stars' => 5,
                'distance_to_haram' => 100,
                'address' => 'Central Northern Area, Madinah',
            ],
            [
                'name' => 'The Oberoi Madinah',
                'city' => 'madinah',
                'stars' => 5,
                'distance_to_haram' => 50,
                'address' => 'Abizar Road, Central Area, Madinah',
            ],
            [
                'name' => 'Elaf Grand Al Majeedi',
                'city' => 'madinah',
                'stars' => 4,
                'distance_to_haram' => 150,
                'address' => 'Northern Central Area, Madinah',
            ],
            [
                'name' => 'Al Haram Hotel Madinah',
                'city' => 'madinah',
                'stars' => 4,
                'distance_to_haram' => 200,
                'address' => 'Central Western Area, Madinah',
            ],
            [
                'name' => 'Arkan Al Manar Hotel',
                'city' => 'madinah',
                'stars' => 3,
                'distance_to_haram' => 350,
                'address' => 'Central Southern Area, Madinah',
            ],
        ];

        foreach ($hotels as $hotel) {
            DB::table('hotels')->insert([
                'uuid' => (string) Str::uuid(),
                'name' => $hotel['name'],
                'city' => $hotel['city'],
                'stars' => $hotel['stars'],
                'distance_to_haram' => $hotel['distance_to_haram'],
                'map_url' => 'https://maps.google.com/?q=' . urlencode($hotel['name']),
                'address' => $hotel['address'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
