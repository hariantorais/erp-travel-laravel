<?php

namespace App\Livewire\Forms\Master;

use Livewire\Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BusForm extends Form
{
    public ?int $busId = null;
    public string $code = '';
    public string $name = '';
    public string $plate_number = '';
    public int $capacity = 45;
    public string $vendor_name = '';
    public string $driver_name = '';
    public string $driver_phone = '';
    public bool $is_active = true;

    public function rules(): array
    {
        return [
            'code'         => 'required|string|max:20|unique:buses,code,' . $this->busId,
            'name'         => 'required|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'capacity'     => 'required|integer|min:1',
            'vendor_name'  => 'nullable|string|max:255',
            'driver_name'  => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:20',
            'is_active'    => 'required|boolean',
        ];
    }

    /**
     * Menyusun Payload & Mengeksekusi Operasi Via Service Layer
     */
    public function store(\App\Services\Master\BusService $busService): array
    {
        $this->validate();

        // 1. Menyusun payload internal secara aman di dalam Form Object
        $payload = [
            'code'         => strtoupper($this->code),
            'name'         => $this->name,
            'plate_number' => $this->plate_number ? strtoupper($this->plate_number) : null,
            'capacity'     => $this->capacity,
            'vendor_name'  => $this->vendor_name ?: null,
            'driver_name'  => $this->driver_name ?: null,
            'driver_phone' => $this->driver_phone ?: null,
            'is_active'    => $this->is_active,
            'updated_at'   => now(),
            'updated_by'   => Auth::id()
        ];

        // 2. Eksekusi ke Service Layer tergantung status ID (Create / Update)
        if ($this->busId) {
            $busService->updateBus($this->busId, $payload);

            $actionResult = [
                'title'   => 'Sistem Diperbarui',
                'message' => "Data armada bus {$payload['name']} berhasil diperbarui."
            ];
        } else {
            $payload['created_at'] = now();
            $payload['created_by'] = Auth::id();

            $busService->createBus($payload);

            $actionResult = [
                'title'   => 'Registrasi Berhasil',
                'message' => "Armada bus baru {$payload['name']} sukses ditambahkan ke logistik."
            ];
        }

        // 3. Bersihkan state setelah berhasil disimpan
        $this->clear();

        // Kembalikan metadata info untuk kebutuhan teks notifikasi Toast di komponen Volt
        return $actionResult;
    }

    public function setBus(int $id): void
    {
        $bus = DB::table('buses')->where('id', $id)->whereNull('deleted_at')->first();
        if ($bus) {
            $this->busId        = $bus->id;
            $this->code         = $bus->code;
            $this->name         = $bus->name;
            $this->plate_number = $bus->plate_number ?? '';
            $this->capacity     = (int) $bus->capacity;
            $this->vendor_name  = $bus->vendor_name ?? '';
            $this->driver_name  = $bus->driver_name ?? '';
            $this->driver_phone = $bus->driver_phone ?? '';
            $this->is_active    = (bool) $bus->is_active;
        }
    }

    public function clear(): void
    {
        $this->reset(['busId', 'code', 'name', 'plate_number', 'capacity', 'vendor_name', 'driver_name', 'driver_phone']);
        $this->is_active = true;
    }
}
