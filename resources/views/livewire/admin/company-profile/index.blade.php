<?php

use function Livewire\Volt\{state, title, layout, with, on};
use App\Services\Master\CompanyProfileService;

title('Profil Perusahaan - ERP Umroh');
layout('components.layouts.app');

on(['refresh']);

with(
    fn(CompanyProfileService $service) => [
        'profile' => $service->getProfile(),
        'profileModel' => $service->getProfileModel(), // Untuk ambil URL logo
    ],
);

?>

<div class="space-y-6">
    <flux:card class="p-6 bg-white shadow-sm border border-slate-200 rounded-2xl space-y-4">
        <div class="flex justify-between items-center">
            <flux:heading size="lg">{{ $profile?->name ?? 'Belum diisi' }}</flux:heading>
            <flux:button x-on:click="$dispatch('open-modal', { name: 'modalForm' })" variant="primary" icon="pencil"
                size="sm">
                Edit Profil
            </flux:button>
        </div>

        <div class="grid grid-cols-3 gap-6 text-sm">
            <div>
                <p class="text-slate-500">Nama Brand</p>
                <p class="font-medium text-slate-800">{{ $profile?->brand_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500">No. Izin PPIU</p>
                <p class="font-medium text-slate-800">{{ $profile?->travel_license_no ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Kode PIU Siskopatuh</p>
                <p class="font-medium text-slate-800">{{ $profile?->siskopatuh_piu_code ?? '-' }}</p>
            </div>
            <div class="col-span-3">
                <p class="text-slate-500">Alamat</p>
                <p class="font-medium text-slate-800">{{ $profile?->address ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Telepon</p>
                <p class="font-medium text-slate-800">{{ $profile?->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Email</p>
                <p class="font-medium text-slate-800">{{ $profile?->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Logo</p>
                @if ($profileModel->getFirstMediaUrl('logo'))
                    <img src="{{ $profileModel->getFirstMediaUrl('logo') }}" class="h-12 mt-1">
                @else
                    <p class="font-medium text-slate-800">-</p>
                @endif
            </div>
        </div>
    </flux:card>

    <x-ui.modal name="modalForm" title="Edit Profil Perusahaan" maxWidth="max-w-2xl">
        <livewire:admin.company-profile.form />
    </x-ui.modal>
</div>
