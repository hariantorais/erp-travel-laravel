<?php

namespace App\Services\Master;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class FlightService
{
   /**
    * Mengambil data master penerbangan dengan sistem paginasi & pencarian
    */
   public function getPaginatedFlights(?string $search = null, int $perPage = 10): LengthAwarePaginator
   {
      return DB::table('flights as f')
         ->join('airlines as a', 'a.id', '=', 'f.airline_id')
         ->when($search, function ($query, $search) {
            $query->where('f.flight_no', 'like', "%{$search}%")
               ->orWhere('f.departure_airport', 'like', "%{$search}%")
               ->orWhere('f.arrival_airport', 'like', "%{$search}%")
               ->orWhere('a.name', 'like', "%{$search}%");
         })
         ->select([
            'f.id',
            'f.uuid',
            'f.flight_no',
            'f.departure_airport',
            'f.arrival_airport',
            'a.name as airline_name',
            'a.code as airline_code'
         ])
         ->orderBy('f.flight_no')
         ->paginate($perPage);
   }

   /**
    * Menyimpan data rute penerbangan komersial baru
    */
   public function createFlight(array $data): void
   {
      DB::table('flights')->insert([
         'uuid'              => (string) Str::uuid(),
         'airline_id'        => (int) $data['airline_id'],
         'flight_no'         => strtoupper($data['flight_no']),
         'departure_airport' => strtoupper($data['departure_airport']),
         'arrival_airport'   => strtoupper($data['arrival_airport']),
         'created_at'        => now(),
         'updated_at'        => now(),
      ]);
   }

   /**
    * Memperbarui rute penerbangan komersial
    */
   public function updateFlight(int $id, array $data): void
   {
      DB::table('flights')->where('id', $id)->update([
         'airline_id'        => (int) $data['airline_id'],
         'flight_no'         => strtoupper($data['flight_no']),
         'departure_airport' => strtoupper($data['departure_airport']),
         'arrival_airport'   => strtoupper($data['arrival_airport']),
         'updated_at'        => now(),
      ]);
   }

   /**
    * Menghapus rute dari master data
    */
   public function deleteFlight(int $id): void
   {
      DB::table('flights')->where('id', $id)->delete();
   }
}
