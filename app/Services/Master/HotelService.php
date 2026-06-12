<?php

namespace App\Services\Master;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class HotelService
{
   /**
    * Mengambil data master hotel dengan sistem paginasi, filter kota, & pencarian
    */
   public function getPaginatedHotels(?string $search = null, ?string $city = null, int $perPage = 10): LengthAwarePaginator
   {
      return DB::table('hotels')
         ->whereNull('deleted_at')
         ->when($city, function ($query, $city) {
            $query->where('city', $city);
         })
         ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
               $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
         })
         ->orderBy('city', 'asc')
         ->orderBy('name', 'asc')
         ->paginate($perPage);
   }

   /**
    * Menyimpan data hotel baru
    */
   public function createHotel(array $data): bool
   {
      return DB::table('hotels')->insert($data);
   }

   /**
    * Memperbarui informasi hotel berdasarkan ID
    */
   public function updateHotel(int $id, array $data): int
   {
      return DB::table('hotels')
         ->where('id', $id)
         ->whereNull('deleted_at')
         ->update($data);
   }

   /**
    * Melakukan Soft Delete pada data hotel
    */
   public function deleteHotel(int $id): int
   {
      return DB::table('hotels')
         ->where('id', $id)
         ->update([
            'deleted_at' => now(),
            'updated_at' => now()
         ]);
   }
}
