<?php

use App\Services\Master\DepartureService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // 1. Amankan aktor pengguna yang sedang login
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // 2. Insert data Cabang
    $branchId = DB::table('branches')->insertGetId([
        'code'       => 'BTH',
        'name'       => 'Batam Central',
        'address'    => 'Jl. Raya Batam Center No. 123',
        'phone'      => '08123456789',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 3. Insert data Status sesuai skema grup & code
    $statusId = DB::table('statuses')->insertGetId([
        'group'      => 'departure',
        'code'       => 'active',
        'name'       => 'Active / Available',
        'color'      => '#10b981',
        'order'      => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 4. KUNCI FORMAT: Lengkap sesuai dengan skema Blueprint packages asli kamu
    $packageId = DB::table('packages')->insertGetId([
        'uuid'          => (string) Str::uuid(),
        'code'          => 'PKG-UMR9',
        'name'          => 'Umroh Premium 9 Hari',
        'slug'          => 'umroh-premium-9-hari-' . Str::random(5), // Mengisi kolom slug wajib
        'type'          => 'umroh',
        'duration_days' => 9,
        'is_featured'   => 0,
        'is_active'     => 1,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    // 5. Insert jadwal Keberangkatan Induk
    $this->departureId = DB::table('departures')->insertGetId([
        'uuid'           => (string) Str::uuid(),
        'package_id'     => $packageId,
        'branch_id'      => $branchId,
        'code'           => 'UMR9-25DES26',
        'departure_date' => '2026-12-25',
        'return_date'    => '2027-01-03',
        'quota'          => 45,
        'quota_left'     => 45,
        'status_id'      => $statusId,
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);

    // 6. Insert Master Maskapai
    $airlineId = DB::table('airlines')->insertGetId([
        'uuid'       => (string) Str::uuid(),
        'name'       => 'Saudi Arabian Airlines',
        'code'       => 'SV',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 7. Insert Master Rute Penerbangan Komersial
    $this->flightId = DB::table('flights')->insertGetId([
        'uuid'              => (string) Str::uuid(),
        'airline_id'        => $airlineId,
        'flight_no'         => 'SV-817',
        'departure_airport' => 'CGK',
        'arrival_airport'   => 'MED',
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);
});

/**
 * UJI KASUS 1: Menyimpan data penerbangan yang valid
 */
test('it can assign valid departure flight data', function () {
    $departureService = app(DepartureService::class);

    $payload = [
        'departure_id' => $this->departureId,
        'flight_id'    => $this->flightId,
        'type'         => 'berangkat',
        'etd'          => '2026-12-25 08:00:00',
        'eta'          => '2026-12-25 16:00:00',
        'pnr_code'     => 'SV991K'
    ];

    $departureService->assignDepartureFlight($payload);

    $this->assertDatabaseHas('departure_flights', [
        'departure_id' => $this->departureId,
        'type'         => 'berangkat',
        'pnr_code'     => 'SV991K',
        'created_by'   => $this->user->id
    ]);
});

/**
 * UJI KASUS 2: Proteksi jika jam mendarat kacau/terbalik
 */
test('it throws exception if eta is before or equal to etd', function () {
    $departureService = app(DepartureService::class);

    $invalidPayload = [
        'departure_id' => $this->departureId,
        'flight_id'    => $this->flightId,
        'type'         => 'berangkat',
        'etd'          => '2026-12-25 08:00:00',
        'eta'          => '2026-12-25 06:00:00',
        'pnr_code'     => 'SV991K'
    ];

    $departureService->assignDepartureFlight($invalidPayload);
})->throws(\Exception::class, "Waktu pendaratan (ETA) tidak boleh mendahului atau sama dengan waktu terbang (ETD).");
