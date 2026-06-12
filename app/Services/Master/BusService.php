<?php

namespace App\Services\Master;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class BusService
{
   /**
    * Mengambil data master bus dengan paginasi & pencarian
    */
   public function getPaginatedBuses(?string $search = null, int $perPage = 10): LengthAwarePaginator
   {
      return DB::table('buses')
         ->whereNull('deleted_at')
         ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
               $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('plate_number', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%");
            });
         })
         ->orderBy('name', 'asc')
         ->paginate($perPage);
   }

   /**
    * Membuat data bus baru
    */
   public function createBus(array $data): bool
   {
      return DB::table('buses')->insert($data);
   }

   /**
    * Memperbarui data bus
    */
   public function updateBus(int $id, array $data): int
   {
      return DB::table('buses')
         ->where('id', $id)
         ->whereNull('deleted_at')
         ->update($data);
   }

   /**
    * Melakukan Soft Delete pada data bus
    */
   public function deleteBus(int $id): int
   {
      return DB::table('buses')
         ->where('id', $id)
         ->update([
            'deleted_at' => now(),
            'updated_at' => now()
         ]);
   }
}
