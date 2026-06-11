<?php

namespace App\Services\Master;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DepartureService
{
   /**
    * Ambil data departures untuk tabel index + search + pagination
    * AUDIT FINAL: Menjamin seluruh properti cabang dan status ter-select sempurna.
    */
   public function getPaginatedDepartures(string $search = '', int $perPage = 10): LengthAwarePaginator
   {
      return DB::table('departures as d')
         ->leftJoin('packages as p', 'p.id', '=', 'd.package_id')
         ->leftJoin('branches as b', 'b.id', '=', 'd.branch_id')
         ->leftJoin('statuses as s', 's.id', '=', 'd.status_id')
         ->leftJoin('departure_pricings as dp', function ($join) {
            $join->on('dp.departure_id', '=', 'd.id')
               ->where('dp.room_type', '=', 'quad');
         })
         ->whereNull('d.deleted_at')
         ->where(function ($q) use ($search) {
            $q->where('d.code', 'like', "%{$search}%")
               ->orWhere('p.name', 'like', "%{$search}%")
               ->orWhere('b.name', 'like', "%{$search}%");
         })
         ->select([
            'd.id',
            'd.uuid',
            'd.code',
            'd.departure_date',
            'd.return_date',
            'd.quota',
            'd.quota_left',
            'p.name as package_name',
            'b.code as branch_code',
            'b.name as branch_name',
            's.name as status_name',
            's.color as status_color', // <-- KUNCI PERBAIKAN: Wajib ditarik agar lencana Flux/Tailwind tidak crash
            'dp.price as quad_price'
         ])
         ->orderBy('d.departure_date', 'asc')
         ->paginate($perPage);
   }

   /**
    * Simpan departure baru (Menerima 1 array payload utuh dari form)
    */
   public function createDeparture(array $data): void
   {
      DB::transaction(function () use ($data) {
         // 1. Ambil data master paket untuk otomatisasi penomoran kode SKU
         $package = DB::table('packages')->where('id', $data['package_id'])->first();
         if (!$package) {
            throw new \Exception("Master template paket acuan tidak ditemukan.");
         }

         // 2. Generate kode otomatis: UMR9-25DES26
         $dateObj = Carbon::parse($data['departure_date']);
         $dateFormatted = strtoupper($dateObj->isoFormat('DDMMMGG'));
         $prefix = str_replace('PKG-', '', $package->code);
         $generatedCode = $prefix . '-' . $dateFormatted;

         // 3. Otomatisasi kalkulasi tanggal pulang berdasarkan durasi hari paket master
         $calculatedReturnDate = $dateObj->copy()->addDays($package->duration_days)->toDateString();

         // 4. Insert data induk ke tabel departures
         $departureId = DB::table('departures')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'package_id' => $data['package_id'],
            'branch_id' => $data['branch_id'] ?? 1,
            'code' => $generatedCode,
            'departure_date' => $data['departure_date'],
            'return_date' => $calculatedReturnDate,
            'quota' => (int) $data['quota'],
            'quota_left' => (int) $data['quota'],
            'status_id' => 1, // Status awal: Active / Available
            'created_by' => auth()->guard('web')->id(),
            'created_at' => now(),
            'updated_at' => now(),
         ]);

         // 5. Looping data pricings berdasarkan format array koleksi dari form
         if (!empty($data['pricings'])) {
            $pricingPayload = [];
            foreach ($data['pricings'] as $p) {
               // Proteksi pencegahan tipe kamar single (hanya quad, triple, double sesuai spesifikasi awal M2.2)
               if (in_array($p['room_type'], ['quad', 'triple', 'double'])) {
                  $pricingPayload[] = [
                     'departure_id' => $departureId,
                     'room_type' => $p['room_type'],
                     'currency' => 'IDR',
                     'price' => (int) $p['price'],
                     'agent_price' => (int) $p['agent_price'],
                     'child_price' => (int) ($p['child_price'] ?? 0),
                     'infant_price' => (int) ($p['infant_price'] ?? 0),
                     'created_at' => now(),
                     'updated_at' => now(),
                  ];
               }
            }

            if (!empty($pricingPayload)) {
               DB::table('departure_pricings')->insert($pricingPayload);
            }
         }
      });
   }
   /**
    * Update data departure (Universal & Anti-Stuck Upsert Pattern)
    */
   public function updateDeparture(int $id, array $data): void
   {
      DB::transaction(function () use ($id, $data) {
         $currentDeparture = DB::table('departures')->where('id', $id)->first();
         if (!$currentDeparture) {
            throw new \Exception("Data jadwal keberangkatan tidak ditemukan.");
         }

         $package = DB::table('packages')->where('id', $data['package_id'])->first();
         if (!$package) {
            throw new \Exception("Paket acuan tidak ditemukan.");
         }

         $dateObj = Carbon::parse($data['departure_date']);
         $calculatedReturnDate = $dateObj->copy()->addDays($package->duration_days)->toDateString();

         // Kalkulasi selisih kuota secara aman
         $quotaDelta = (int) $data['quota'] - (int) $currentDeparture->quota;
         $newQuotaLeft = (int) $currentDeparture->quota_left + $quotaDelta;

         if ($newQuotaLeft < 0) {
            throw new \Exception("Jumlah kuota baru terlalu kecil, kursi yang dipesan jemaah sudah melebihi batas.");
         }

         // Update tabel induk
         DB::table('departures')->where('id', $id)->update([
            'package_id' => $data['package_id'],
            'branch_id' => $data['branch_id'],
            'departure_date' => $data['departure_date'],
            'return_date' => $calculatedReturnDate,
            'quota' => (int) $data['quota'],
            'quota_left' => $newQuotaLeft,
            'updated_at' => now(),
         ]);

         // KUNCI PERBAIKAN: Gunakan updateOrInsert dengan sanitasi regex murni
         if (!empty($data['pricings'])) {
            foreach ($data['pricings'] as $p) {
               // Sanitasi paksa: bersihkan semua karakter non-angka (menghapus titik/Rp jika ada)
               $cleanPrice = (int) preg_replace('/[^0-9]/', '', $p['price']);
               $cleanAgentPrice = (int) preg_replace('/[^0-9]/', '', $p['agent_price']);
               $cleanChildPrice = (int) preg_replace('/[^0-9]/', '', $p['child_price'] ?? 0);
               $cleanInfantPrice = (int) preg_replace('/[^0-9]/', '', $p['infant_price'] ?? 0);

               DB::table('departure_pricings')->updateOrInsert(
                  [
                     'departure_id' => $id,
                     'room_type' => $p['room_type']
                  ],
                  [
                     'currency' => 'IDR',
                     'price' => $cleanPrice,
                     'agent_price' => $cleanAgentPrice,
                     'child_price' => $cleanChildPrice,
                     'infant_price' => $cleanInfantPrice,
                     'updated_at' => now(),
                  ]
               );
            }
         }
      });
   }

   /**
    * Ambil detail data untuk edit (Eager loading manual via keyBy)
    */
   public function findDepartureWithPricings(int $id): ?object
   {
      $departure = DB::table('departures')->where('id', $id)->whereNull('deleted_at')->first();
      if (!$departure) return null;

      $departure->pricings = DB::table('departure_pricings')
         ->where('departure_id', $id)
         ->get()
         ->keyBy('room_type')
         ->toArray();

      return $departure;
   }

   /**
    * Soft delete
    */
   public function deleteDeparture(int $id): void
   {
      DB::table('departures')->where('id', $id)->update([
         'deleted_at' => now(),
         'updated_at' => now(),
      ]);
   }

   /**
    * Menyematkan manifes alokasi tiket penerbangan rombongan (M2.3)
    * AUDIT FINAL: Menggunakan DB murni agar konsisten dengan arsitektur internal kelas.
    */
   public function assignDepartureFlight(array $data): void
   {
      DB::transaction(function () use ($data) {
         // Validasi logika bisnis dasar di level service
         $etd = Carbon::parse($data['etd']);
         $eta = Carbon::parse($data['eta']);

         if ($eta->lessThanOrEqualTo($etd)) {
            throw new \Exception("Waktu pendaratan (ETA) tidak boleh mendahului atau sama dengan waktu terbang (ETD).");
         }

         // Gunakan updateOrInsert sebagai pola Upsert yang aman dari duplikasi data rute per jenis
         DB::table('departure_flights')->updateOrInsert(
            [
               'departure_id' => (int) $data['departure_id'],
               'type'         => $data['type'], // 'berangkat' atau 'pulang'
            ],
            [
               'flight_id'  => (int) $data['flight_id'],
               'etd'        => $data['etd'],
               'eta'        => $data['eta'],
               'pnr_code'   => !empty($data['pnr_code']) ? strtoupper(trim($data['pnr_code'])) : null,
               'created_by' => auth()->guard('web')->id(),
               'created_at' => now(),
               'updated_at' => now(),
            ]
         );
      });
   }
}
