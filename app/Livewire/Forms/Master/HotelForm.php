<?php

namespace App\Livewire\Forms\Master;

use Livewire\Form;
use Illuminate\Support\Facades\DB;

class HotelForm extends Form
{
    public ?int $hotelId = null;
    public string $name = '';
    public string $city = 'makkah';
    public ?int $stars = 4;
    public ?int $distance_to_haram = null;
    public string $map_url = '';
    public string $address = '';
    public bool $is_active = true;

    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'city'              => 'required|in:makkah,madinah,jeddah',
            'stars'             => 'nullable|integer|min:3|max:5',
            'distance_to_haram' => 'nullable|integer|min:0',
            'map_url'           => 'nullable|url|max:255',
            'address'           => 'nullable|string',
            'is_active'         => 'required|boolean',
        ];
    }

    /**
     * Menyusun Payload & Eksekusi Operasi via HotelService
     */
    public function store(\App\Services\Master\HotelService $hotelService): array
    {
        $this->validate();

        // 1. Menyusun payload internal sesuai skema tabel 'hotels'
        $payload = [
            'name'              => $this->name,
            'city'              => $this->city,
            'stars'             => $this->stars,
            'distance_to_haram' => $this->distance_to_haram,
            'map_url'           => $this->map_url ?: null,
            'address'           => $this->address ?: null,
            'is_active'         => $this->is_active,
            'updated_at'        => now()
        ];

        // 2. Eksekusi dependensi service layer berdasarkan konteks ID
        if ($this->hotelId) {
            $hotelService->updateHotel($this->hotelId, $payload);

            $actionResult = [
                'title'   => 'Sistem Diperbarui',
                'message' => "Informasi hotel {$payload['name']} sukses diperbarui."
            ];
        } else {
            // Pasang UUID murni untuk data baru
            $payload['uuid']       = (string) \Illuminate\Support\Str::uuid();
            $payload['created_at'] = now();

            $hotelService->createHotel($payload);

            $actionResult = [
                'title'   => 'Registrasi Berhasil',
                'message' => "Hotel {$payload['name']} telah berhasil didaftarkan ke sistem."
            ];
        }

        // 3. Reset state form agar bersih kembali
        $this->clear();

        return $actionResult;
    }

    public function setHotel(int $id): void
    {
        $hotel = DB::table('hotels')->where('id', $id)->whereNull('deleted_at')->first();

        if ($hotel) {
            $this->hotelId           = $hotel->id;
            $this->name              = $hotel->name;
            $this->city              = $hotel->city;
            $this->stars             = $hotel->stars ? (int) $hotel->stars : null;
            $this->distance_to_haram = $hotel->distance_to_haram ? (int) $hotel->distance_to_haram : null;
            $this->map_url           = $hotel->map_url ?? '';
            $this->address           = $hotel->address ?? '';
            $this->is_active         = (bool) $hotel->is_active;
        }
    }

    public function clear(): void
    {
        $this->reset(['hotelId', 'name', 'distance_to_haram', 'map_url', 'address']);
        $this->city      = 'makkah';
        $this->stars     = 4;
        $this->is_active = true;
    }
}
