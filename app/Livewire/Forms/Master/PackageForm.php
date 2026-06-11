<?php

namespace App\Livewire\Forms\Master;

use Livewire\Form;

class PackageForm extends Form
{
    public ?int $packageId = null;
    public string $code = '';
    public string $name = '';
    public string $type = 'umroh';
    public int $duration_days = 9;
    public ?string $thumbnail = null;
    public ?string $description = null;
    public ?string $itinerary_summary = null;
    public ?string $estimated_schedule = null;

    // Menggunakan ID Referensi dari tabel Master Logistik masing-masing
    public ?int $airline_id = null;
    public ?int $hotel_madinah_id = null;
    public ?int $hotel_makkah_id = null;

    public bool $is_featured = false;

    /**
     * Aturan validasi yang disesuaikan dengan skema template Master Paket terbaru.
     */
    public function rules(): array
    {
        return [
            'code' => 'string|max:50|unique:packages,code,' . $this->packageId,
            'name' => 'required|string|max:255',
            'type' => 'required|in:umroh,haji,tour',
            'duration_days' => 'required|integer|min:1',
            'thumbnail' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'itinerary_summary' => 'nullable|string',
            'estimated_schedule' => 'nullable|string|max:100',

            // Validasi foreign key opsional (bisa diisi untuk brosur default)
            'airline_id' => 'nullable|integer|exists:airlines,id',
            'hotel_madinah_id' => 'nullable|integer|exists:hotels,id',
            'hotel_makkah_id' => 'nullable|integer|exists:hotels,id',

            'is_featured' => 'required|boolean',
        ];
    }

    /**
     * Memetakan data objek database ke dalam state Form Livewire saat mode Edit.
     */
    public function fillFromData(object $package): void
    {
        $this->packageId = $package->id;
        $this->code = $package->code;
        $this->name = $package->name;
        $this->type = $package->type;
        $this->duration_days = $package->duration_days;
        $this->thumbnail = $package->thumbnail;
        $this->description = $package->description;
        $this->itinerary_summary = $package->itinerary_summary;
        $this->estimated_schedule = $package->estimated_schedule;

        $this->airline_id = $package->airline_id ? (int) $package->airline_id : null;
        $this->hotel_madinah_id = $package->hotel_madinah_id ? (int) $package->hotel_madinah_id : null;
        $this->hotel_makkah_id = $package->hotel_makkah_id ? (int) $package->hotel_makkah_id : null;

        $this->is_featured = (bool) $package->is_featured;
    }

    /**
     * Membersihkan state formulir kembali ke nilai default awal.
     */
    public function clear(): void
    {
        $this->reset();
        $this->type = 'umroh';
        $this->duration_days = 9;
        $this->is_featured = false;
    }
}
