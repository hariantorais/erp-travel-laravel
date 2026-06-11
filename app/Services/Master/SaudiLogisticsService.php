<?php

namespace App\Services\Master;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SaudiLogisticsService
{
   /* =========================================================================
     * AREA MANAJEMEN HOTEL SAUDI
     * ========================================================================= */

   public function getPaginatedHotels(string $search = '', int $perPage = 10): LengthAwarePaginator
   {
      return DB::table('saudi_hotels')
         ->whereNull('deleted_at')
         ->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
               ->orWhere('city', 'like', "%{$search}%");
         })
         ->orderBy('city', 'asc')
         ->orderBy('name', 'asc')
         ->paginate($perPage);
   }

   public function createHotel(array $data): void
   {
      DB::table('saudi_hotels')->insert([
         'name' => $data['name'],
         'city' => strtoupper($data['city']),
         'star_rating' => $data['star_rating'],
         'address' => $data['address'] ?? null,
         'contact_person' => $data['contact_person'] ?? null,
         'phone' => $data['phone'] ?? null,
         'created_at' => now(),
         'updated_at' => now(),
      ]);
   }

   /* =========================================================================
     * AREA MANAJEMEN BUS SAUDI
     * ========================================================================= */

   public function getPaginatedBuses(string $search = '', int $perPage = 10): LengthAwarePaginator
   {
      return DB::table('saudi_buses')
         ->whereNull('deleted_at')
         ->where(function ($query) use ($search) {
            $query->where('company_name', 'like', "%{$search}%")
               ->orWhere('fleet_number', 'like', "%{$search}%");
         })
         ->orderBy('company_name', 'asc')
         ->paginate($perPage);
   }

   public function createBus(array $data): void
   {
      DB::table('saudi_buses')->insert([
         'company_name' => $data['company_name'],
         'fleet_number' => strtoupper($data['fleet_number']),
         'capacity' => $data['capacity'],
         'driver_name' => $data['driver_name'] ?? null,
         'driver_phone' => $data['driver_phone'] ?? null,
         'created_at' => now(),
         'updated_at' => now(),
      ]);
   }
}
