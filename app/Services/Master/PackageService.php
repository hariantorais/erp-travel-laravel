<?php

namespace App\Services\Master;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PackageService
{
   /**
    * Mengambil data paket induk ber-paginasi dengan pencarian nama/kode.
    * Menggunakan LEFT JOIN standar untuk menarik nama maskapai acuan.
    */
   public function getPaginatedPackages(string $search = '', int $perPage = 10): LengthAwarePaginator
   {
      return DB::table('packages')
         ->leftJoin('airlines', 'packages.airline_id', '=', 'airlines.id') // PERBAIKAN: Menggunakan tanda titik (.) bukan (::)
         ->select('packages.*', 'airlines.name as airline_name')
         ->whereNull('packages.deleted_at')
         ->where(function ($query) use ($search) {
            $query->where('packages.name', 'like', "%{$search}%")
               ->orWhere('packages.code', 'like', "%{$search}%");
         })
         ->orderBy('packages.created_at', 'desc')
         ->paginate($perPage);
   }

   /**
    * Menyimpan data template induk paket baru (Dilengkapi UUID & Slug Otomatis)
    */
   public function createPackage(array $data): void
   {
      DB::transaction(function () use ($data) {
         // GENERATE KODE OTOMATIS: PKG-REG-9D
         $generatedCode = 'PKG-' . strtoupper($data['type']) . '-' . $data['duration_days'] . 'D';

         DB::table('packages')->insert([
            'uuid' => (string) Str::uuid(),
            'code' => $generatedCode, // <--- Kunci otomatis di sini
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . rand(100, 999),
            'type' => $data['type'] ?? 'umroh',
            'duration_days' => $data['duration_days'],
            'thumbnail' => $data['thumbnail'] ?? null,
            'description' => $data['description'] ?? null,
            'itinerary_summary' => $data['itinerary_summary'] ?? null,
            'estimated_schedule' => $data['estimated_schedule'] ?? null,
            'airline_id' => $data['airline_id'] ?? null,
            'hotel_madinah_id' => $data['hotel_madinah_id'] ?? null,
            'hotel_makkah_id' => $data['hotel_makkah_id'] ?? null,
            'is_featured' => $data['is_featured'] ?? false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
         ]);
      });
   }

   /**
    * Mengambil data tunggal paket berdasarkan ID.
    */
   public function findPackage($slug = null): ?object
   {
      return DB::table('packages')
         ->where('slug', $slug)
         ->whereNull('deleted_at')
         ->first();
   }

   /**
    * Memperbarui data template induk paket.
    */
   public function updatePackage(int $id, array $data): void
   {
      DB::transaction(function () use ($id, $data) {
         DB::table('packages')
            ->where('id', $id)
            ->update([
               'code' => strtoupper($data['code']),
               'name' => $data['name'],
               'slug' => Str::slug($data['name']),
               'type' => $data['type'] ?? 'umroh',
               'duration_days' => $data['duration_days'],
               'thumbnail' => $data['thumbnail'] ?? null,
               'description' => $data['description'] ?? null,
               'itinerary_summary' => $data['itinerary_summary'] ?? null,
               'estimated_schedule' => $data['estimated_schedule'] ?? null,

               'airline_id' => $data['airline_id'] ?? null,
               'hotel_madinah_id' => $data['hotel_madinah_id'] ?? null,
               'hotel_makkah_id' => $data['hotel_makkah_id'] ?? null,

               'is_featured' => $data['is_featured'] ?? false,
               'updated_at' => now(),
            ]);
      });
   }

   /**
    * Melakukan Soft Delete pada data paket induk.
    */

   public function deletePackage(int $id): void
   {
      DB::transaction(function () use ($id) {
         DB::table('packages')
            ->where('id', $id)
            ->update([
               'is_active' => false,
               'deleted_at' => now(),
               'updated_at' => now(),
            ]);
      }); // <-- PERBAIKAN: Menggunakan }); untuk menutup fungsi anonim dengan benar
   }
    /* =========================================================================
       SUB-DOMAIN LOGIK: PACKAGE ITINERARIES
       ========================================================================= */

   /**
    * Mengambil daftar itinerary bawaan paket berurutan hari.
    */
   public function getItinerariesByPackage(int $packageId): array
   {
      return DB::table('package_itineraries')
         ->where('package_id', $packageId)
         ->orderBy('day_no', 'asc')
         ->get()
         ->toArray();
   }

   /**
    * Mengambil data tunggal baris itinerary.
    */
   public function findItinerary(int $id): ?object
   {
      return DB::table('package_itineraries')->where('id', $id)->first();
   }

   /**
    * Menyimpan data itinerary harian baru.
    */
   /**
    * Menyimpan agenda itinerary baru untuk suatu paket program.
    * (Mematuhi Single Parameter Pattern & Server-Side Automation)
    */
   public function createItinerary(int $packageId, array $data): void
   {
      DB::transaction(function () use ($packageId, $data) {
         // Proteksi double-insert untuk kombinasi unik: package_id + day_no
         $exists = DB::table('package_itineraries')
            ->where('package_id', $packageId)
            ->where('day_no', $data['day_no'])
            ->exists();

         if ($exists) {
            throw new \Exception("Agenda untuk Hari Ke-{$data['day_no']} sudah terdaftar pada paket ini.");
         }

         DB::table('package_itineraries')->insert([
            'package_id' => $packageId,
            'day_no'     => (int) $data['day_no'],
            'city'       => $data['city'] ?? null,
            'title'      => $data['title'],
            'activity'   => $data['activity'],
            'meals'      => $data['meals'] ?? null, // KUNCI PERBAIKAN: Disinkronkan ke database
            'created_at' => now(),
            'updated_at' => now(),
         ]);
      });
   }

   /**
    * Memperbarui data rincian itinerary harian.
    * (Pola Pembaruan Non-Destruktif & Validasi Lintas Tenant)
    */
   public function updateItinerary(int $id, array $data): void
   {
      DB::transaction(function () use ($id, $data) {
         $current = DB::table('package_itineraries')->where('id', $id)->first();
         if (!$current) {
            throw new \Exception("Data rencana perjalanan tidak ditemukan atau telah dihapus.");
         }

         // Validasi pencegahan duplikasi nomor hari jika admin mengubah nomor harinya
         if ((int) $current['day_no'] !== (int) $data['day_no']) {
            $duplicateExists = DB::table('package_itineraries')
               ->where('package_id', $current['package_id'])
               ->where('day_no', $data['day_no'])
               ->where('id', '!=', $id)
               ->exists();

            if ($duplicateExists) {
               throw new \Exception("Gagal mengubah. Agenda Hari Ke-{$data['day_no']} sudah digunakan di baris lain.");
            }
         }

         DB::table('package_itineraries')->where('id', $id)->update([
            'day_no'     => (int) $data['day_no'],
            'city'       => $data['city'] ?? null,
            'title'      => $data['title'],
            'activity'   => $data['activity'],
            'meals'      => $data['meals'] ?? null, // KUNCI PERBAIKAN: Disinkronkan ke database
            'updated_at' => now(),
         ]);
      });
   }

   /**
    * Menghapus baris agenda itinerary dari paket terkait.
    */
   public function deleteItinerary(int $id): void
   {
      DB::transaction(function () use ($id) {
         $exists = DB::table('package_itineraries')->where('id', $id)->exists();
         if (!$exists) {
            throw new \Exception("Data agenda gagal dihapus karena tidak ditemukan di sistem.");
         }

         DB::table('package_itineraries')->where('id', $id)->delete();
      });
   }
}
