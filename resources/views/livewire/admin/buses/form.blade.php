<?php

use function Livewire\Volt\{form, on};
use App\Livewire\Forms\Master\BusForm;
use App\Services\Master\BusService;

form(BusForm::class);

on([
    'open-modal' => function ($id = null) {
        if ($id) {
            $this->form->setBus($id);
        } else {
            $this->form->clear();
        }
    },
]);

/**
 * Simpan Data - Sangat Ramping & Bersih
 */
$saveBus = function (BusService $busService) {
    try {
        $result = $this->form->store($busService);

        $this->dispatch('toast', type: 'success', title: $result['title'], message: $result['message']);

        $this->dispatch('close-modal', name: 'modalForm');
        $this->dispatch('bus-updated');
    } catch (\Exception $e) {
        $this->dispatch('toast', type: 'danger', title: 'Operasi Gagal', message: 'Terjadi redundansi kode bus atau gangguan database internal.');
    }
};

?>

<form wire:submit.prevent="saveBus" class="space-y-4">

    <div class="grid grid-cols-3 gap-4">
        <div>
            <flux:input wire:model="form.code" label="Kode Bus" placeholder="E.g. BUS-01" required size="sm"
                class="font-mono uppercase" />
        </div>
        <div class="col-span-2">
            <flux:input wire:model="form.name" label="Nama Bus / Armada" placeholder="E.g. Bus Rombongan 1" required
                size="sm" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <flux:input wire:model="form.plate_number" label="Nomor Pelat" placeholder="E.g. B 1234 XYZ" size="sm"
                class="font-mono uppercase" />
        </div>
        <div>
            <flux:input wire:model="form.capacity" type="number" label="Kapasitas (Pax)" placeholder="45" required
                size="sm" />
        </div>
    </div>

    <flux:input wire:model="form.vendor_name" label="Nama Vendor / Syarikah" placeholder="E.g. Saptco Saudi"
        size="sm" />

    <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-3">
        <div>
            <flux:input wire:model="form.driver_name" label="Nama Supir" placeholder="E.g. Ahmad Abdullah"
                size="sm" />
        </div>
        <div>
            <flux:input wire:model="form.driver_phone" label="No Telepon Supir" placeholder="E.g. +9665xxxxx"
                size="sm" class="font-mono" />
        </div>
    </div>

    <div class="flex items-center gap-2 pt-2">
        <flux:checkbox wire:model="form.is_active" label="Armada siap jalan dan bisa digunakan dalam manifes" />
    </div>

    <x-ui.modal-footer />
</form>
