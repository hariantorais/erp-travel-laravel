<?php

use function Livewire\Volt\{form, on};
use App\Livewire\Forms\Master\HotelForm;
use App\Services\Master\HotelService;

form(HotelForm::class);

on([
    'open-modal' => function ($id = null) {
        if ($id) {
            $this->form->setHotel($id);
        } else {
            $this->form->clear();
        }
    },
]);

/**
 * Aksi Simpan - Pendelegasian Penuh ke Form Object
 */
$saveHotel = function (HotelService $hotelService) {
    try {
        // Eksekusi data handling internal dan tangkap metadata teksnya
        $result = $this->form->store($hotelService);

        // Render toast sukses secara dinamis
        $this->dispatch('toast', type: 'success', title: $result['title'], message: $result['message']);

        $this->dispatch('close-modal', name: 'modalForm');
        $this->dispatch('hotel-updated');
    } catch (\Exception $e) {
        $this->dispatch('toast', type: 'danger', title: 'Operasi Gagal', message: 'Terjadi kegagalan sistem atau gangguan database internal.');
    }
};

?>

<form wire:submit.prevent="saveHotel" class="space-y-4">

    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2">
            <flux:input wire:model="form.name" label="Nama Hotel" placeholder="E.g. Pullman Zamzam" required
                size="sm" />
        </div>
        <div>
            <flux:select wire:model="form.city" label="Kota" required size="sm">
                <option value="makkah">Makkah</option>
                <option value="madinah">Madinah</option>
                <option value="jeddah">Jeddah</option>
            </flux:select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <flux:select wire:model="form.stars" label="Kelas Bintang" size="sm">
                <option value="3">⭐ 3 (Reguler)</option>
                <option value="4">⭐⭐ 4 (Bisnis)</option>
                <option value="5">⭐⭐⭐ 5 (VIP)</option>
            </flux:select>
        </div>
        <div>
            <flux:input wire:model="form.distance_to_haram" type="number" label="Jarak ke Masjid (Meter)"
                placeholder="E.g. 150" size="sm" />
        </div>
    </div>

    <flux:input wire:model="form.map_url" label="Link Google Maps URL" placeholder="http://maps.google.com/..."
        size="sm" class="text-xs" />

    <flux:textarea wire:model="form.address" label="Alamat Fisik Lengkap"
        placeholder="E.g. Ibrahim Al Khalil Street, Makkah" size="sm" rows="2" />

    <div class="flex items-center gap-2 pt-2">
        <flux:checkbox wire:model="form.is_active" label="Hotel ini Aktif & Bisa Digunakan untuk Logistik Paket" />
    </div>

    <x-ui.modal-footer />
</form>
