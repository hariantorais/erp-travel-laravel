<?php

namespace App\Services\Master;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BranchService
{
   /**
    * Mengambil data cabang ber-paginasi dengan pencarian.
    */
   public function getPaginatedBranches(string $search = '', int $perPage = 10): LengthAwarePaginator
   {
      return DB::table('branches')
         ->whereNull('deleted_at') // Dukungan Soft Deletes sesuai PRD
         ->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
               ->orWhere('code', 'like', "%{$search}%")
               ->orWhere('leader_name', 'like', "%{$search}%");
         })
         ->orderBy('is_main_office', 'desc') // Kantor pusat selalu di atas
         ->orderBy('created_at', 'desc')
         ->paginate($perPage);
   }

   /**
    * Menyimpan data cabang baru ke database.
    */
   public function createBranch(array $data): void
   {
      DB::transaction(function () use ($data) {
         // Jika cabang baru ditandai sebagai Kantor Pusat, matikan status kantor pusat cabang lain
         if (!empty($data['is_main_office']) && $data['is_main_office'] == true) {
            DB::table('branches')->update(['is_main_office' => false]);
         }

         DB::table('branches')->insert([
            'code' => strtoupper($data['code']),
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'leader_name' => $data['leader_name'] ?? null,
            'is_main_office' => $data['is_main_office'] ?? false,
            'is_active' => $data['is_active'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
         ]);
      });
   }

   /**
    * KUNCI PELENGKAP: Memperbarui data cabang yang sudah ada menggunakan Query Builder.
    */
   public function updateBranch(int $id, array $data): void
   {
      DB::transaction(function () use ($id, $data) {
         // Jika cabang ini diperbarui menjadi Kantor Pusat, matikan status kantor pusat cabang lain (Kecuali dirinya sendiri)
         if (!empty($data['is_main_office']) && $data['is_main_office'] == true) {
            DB::table('branches')
               ->where('id', '!=', $id)
               ->update(['is_main_office' => false]);
         }

         // Eksekusi update data spesifik berdasarkan ID kantor cabang
         DB::table('branches')
            ->where('id', $id)
            ->update([
               'code' => strtoupper($data['code']),
               'name' => $data['name'],
               'address' => $data['address'] ?? null,
               'phone' => $data['phone'] ?? null,
               'leader_name' => $data['leader_name'] ?? null,
               'is_main_office' => $data['is_main_office'] ?? false,
               'updated_at' => now(), // Mencatat rekam jejak waktu modifikasi
            ]);
      });
   }

   public function deleteBranch(int $id): void
   {
      DB::transaction(function () use ($id) {
         DB::table('branches')
            ->where('id', $id)
            ->update([
               'deleted_at' => now(),
               'is_active' => false, // Nonaktifkan juga status operasionalnya
            ]);
      });
   }
}
