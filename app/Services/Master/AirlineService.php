<?php

namespace App\Services\Master;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class AirlineService
{
   /**
    * Mengambil data master maskapai (Abaikan Soft Deletes)
    */
   public function getPaginatedAirlines(?string $search = null, int $perPage = 10): LengthAwarePaginator
   {
      return DB::table('airlines')
         ->whereNull('deleted_at')
         ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
               $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
         })
         ->orderBy('name', 'asc')
         ->paginate($perPage);
   }

   /**
    * Membuat data maskapai baru beserta penanganan aset logo
    */
   public function createAirline(array $data, $logoFile = null): bool
   {
      if ($logoFile) {
         $path = $logoFile->store('airlines/logos', 'public');
         $data['logo_url'] = Storage::url($path);
      } else {
         $data['logo_url'] = null;
      }

      return DB::table('airlines')->insert($data);
   }

   /**
    * Memperbarui data maskapai dan manajemen siklus hidup logo lama
    */
   public function updateAirline(int $id, array $data, $logoFile = null): int
   {
      if ($logoFile) {
         // Ambil data maskapai lama untuk mengecek logo yang sudah ada
         $oldAirline = DB::table('airlines')->where('id', $id)->first();

         if ($oldAirline && $oldAirline->logo_url) {
            $oldPath = str_replace('/storage/', '', $oldAirline->logo_url);
            Storage::disk('public')->delete($oldPath);
         }

         // Simpan file baru
         $path = $logoFile->store('airlines/logos', 'public');
         $data['logo_url'] = Storage::url($path);
      }

      return DB::table('airlines')
         ->where('id', $id)
         ->whereNull('deleted_at')
         ->update($data);
   }

   /**
    * Soft Delete data maskapai
    */
   public function deleteAirline(int $id): int
   {
      return DB::table('airlines')
         ->where('id', $id)
         ->update([
            'deleted_at' => now(),
            'updated_at' => now()
         ]);
   }
}
