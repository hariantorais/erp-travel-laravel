<?php

namespace App\Livewire\Forms\Master;

use Livewire\Form;

class BusForm extends Form
{
    public ?int $busId = null;
    public string $company_name = '';
    public string $fleet_number = '';
    public int $capacity = 45; // Standar kapasitas default bus maktab/Saptco
    public ?string $driver_name = null;
    public ?string $driver_phone = null;

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:100',
            'fleet_number' => 'required|string|max:50|unique:saudi_buses,fleet_number,' . $this->busId,
            'capacity' => 'required|integer|min:15|max:60',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:50',
        ];
    }

    public function fillFromData(object $bus): void
    {
        $this->busId = $bus->id;
        $this->company_name = $bus->company_name;
        $this->fleet_number = $bus->fleet_number;
        $this->capacity = $bus->capacity;
        $this->driver_name = $bus->driver_name;
        $this->driver_phone = $bus->driver_phone;
    }

    public function clear(): void
    {
        $this->reset();
        $this->capacity = 45;
    }
}
