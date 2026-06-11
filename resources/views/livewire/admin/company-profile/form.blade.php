<?php

use function Livewire\Volt\{form, on, uses, state};
use App\Services\Master\CompanyProfileService;
use App\Livewire\Forms\CompanyProfileForm;
use Livewire\WithFileUploads;

uses([WithFileUploads::class]);
form(CompanyProfileForm::class);

state([
    'logo' => null,
    'stamp' => null,
]);

on([
    'open-modal' => function () {
        $this->form->setProfile();
        $this->reset('logo', 'stamp');
    },
]);

$saveProfile = function (CompanyProfileService $service) {
    $this->validate();

    // 1. Simpan data teks dulu
    $service->updateProfile($this->form->all());

    // 2. Simpan file pakai MediaLibrary
    $profileModel = $service->getProfileModel();
    if ($this->logo) {
        $profileModel->addMedia($this->logo)->toMediaCollection('logo');
    }
    if ($this->stamp) {
        $profileModel->addMedia($this->stamp)->toMediaCollection('stamp');
    }

    $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Profil perusahaan diperbarui!');
    $this->dispatch('close-modal', name: 'modalForm');
    $this->dispatch('refresh'); // Refresh index
};

?>

<form wire:submit.prevent="saveProfile" class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <flux:input wire:model="form.name" label="Nama PT Sesuai Akta" required />
        <flux:input wire:model="form.brand_name" label="Nama Brand" />
    </div>

    <div class="grid grid-cols-2 gap-4">
        <flux:input wire:model="form.travel_license_no" label="No. Izin PPIU" />
        <flux:input wire:model="form.siskopatuh_piu_code" label="Kode PIU Siskopatuh" required />
    </div>

    <flux:separator text="Kredensial Siskopatuh" />
    <div class="grid grid-cols-2 gap-4">
        <flux:input wire:model="form.siskopatuh_user" label="Username Siskopatuh" />
        <flux:input wire:model="form.siskopatuh_password" type="password" label="Password Siskopatuh"
            placeholder="Isi jika ingin ganti" />
    </div>

    <flux:textarea wire:model="form.address" label="Alamat Kantor Pusat" required rows="2" />

    <div class="grid grid-cols-2 gap-4">
        <flux:input wire:model="form.phone" label="Telepon" required />
        <flux:input wire:model="form.email" type="email" label="Email" />
    </div>

    <flux:separator text="File Pendukung" />
    <div class="grid grid-cols-2 gap-4">
        <flux:input wire:model="logo" type="file" label="Logo Biro" accept="image/*" />
        <flux:input wire:model="stamp" type="file" label="Stempel Digital" accept="image/*" />
    </div>

    <div class="flex justify-end gap-2 pt-2">
        <flux:button x-on:click="isOpen = false" type="button" variant="ghost">Batal</flux:button>
        <flux:button type="submit" variant="primary">Simpan Profil</flux:button>
    </div>
</form>
