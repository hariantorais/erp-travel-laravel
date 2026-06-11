<?php

use function Livewire\Volt\{state, title, layout, with, form, on};
use App\Services\Master\SaudiLogisticsService;
use App\Livewire\Forms\Master\BusForm;

// Mengatur title halaman yang otomatis dirender oleh layout induk
title('Master Armada Bus Saudi - ERP Sahara Travel');
layout('components.layouts.app');

// Injeksi Form Object Bus
form(BusForm::class);

state([
    'search' => '',
    'perPage' => 10,
]);

/**
 * Menyediakan data paginasi bus secara reaktif ke tabel
 */
with(
    fn(SaudiLogisticsService $logisticsService) => [
        'buses' => $logisticsService->getPaginatedBuses($this->search, $this->perPage),
    ],
);

/**
 * Event Listener pembukaan modal form
 */
on([
    'open-modal' => function ($name, $id = null) {
        if ($name === 'modalBusForm') {
            if ($id) {
                $bus = DB::table('saudi_buses')->where('id', $id)->first();
                if ($bus) {
                    $this->form->fillFromData($bus);
                }
            } else {
                $this->form->clear();
            }
        }
    },
]);

/**
 * Proses Simpan Data (Aksi Ganda: Tambah Baru / Perbarui)
 */
$saveBus = function (SaudiLogisticsService $logisticsService) {
    $validated = $this->form->validate();

    if ($this->form->busId) {
        DB::table('saudi_buses')
            ->where('id', $this->form->busId)
            ->update([
                'company_name' => $validated['company_name'],
                'fleet_number' => strtoupper($validated['fleet_number']),
                'capacity' => $validated['capacity'],
                'driver_name' => $validated['driver_name'],
                'driver_phone' => $validated['driver_phone'],
                'updated_at' => now(),
            ]);

        $this->dispatch('toast', type: 'success', title: 'Bus Diperbarui', message: 'Spesifikasi armada bus nomor lambung ' . $this->form->fleet_number . ' sukses diubah.');
    } else {
        $logisticsService->createBus($validated);

        $this->dispatch('toast', type: 'success', title: 'Bus Didaftarkan', message: 'Unit armada bus baru berhasil dimasukkan ke dalam daftar logistik.');
    }

    $this->form->clear();
    $this->dispatch('close-modal', name: 'modalBusForm');
};

/**
 * Penghapusan Data Bus (Soft Delete)
 */
$deleteBus = function (int $id) {
    DB::table('saudi_buses')
        ->where('id', $id)
        ->update([
            'deleted_at' => now(),
        ]);

    $this->dispatch('toast', type: 'warning', title: 'Bus Dinonaktifkan', message: 'Unit bus berhasil dinonaktifkan dari sistem operasional.');
};

?>

<div class="space-y-4">

    <x-ui.table-controls placeholder="Cari perusahaan bus atau nomor lambung..." buttonLabel="Tambah Mitra Bus"
        modalTarget="modalBusForm" />

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="p-4">Perusahaan / Vendor Bus</th>
                    <th class="p-4">No. Lambung / Plat</th>
                    <th class="p-4">Kapasitas Kursi</th>
                    <th class="p-4">Sopir / Driver</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-600">
                @forelse($buses as $bus)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-4">
                            <span class="text-sm font-bold text-slate-900 block">{{ $bus->company_name }}</span>
                        </td>
                        <td class="p-4">
                            <span
                                class="bg-slate-100 text-slate-800 border border-slate-200/80 px-2 py-0.5 rounded-md font-mono font-bold text-[10px]">
                                🚌 {{ $bus->fleet_number }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-indigo-600">{{ $bus->capacity }}</span> <span
                                class="text-slate-400">Pax / Seats</span>
                        </td>
                        <td class="p-4">
                            <div class="space-y-0.5">
                                <span class="text-slate-800 block">{{ $bus->driver_name ?? 'Belum Diplot' }}</span>
                                <span
                                    class="text-[10px] text-slate-400 font-mono">{{ $bus->driver_phone ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="p-4 text-right">
                            <x-ui.table-actions :id="$bus->id" editModal="modalBusForm"
                                deleteModal="confirmDeleteBus" deleteAction="deleteBus" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 italic">
                            Belum ada records data armada bus yang sesuai dengan kriteria pencarian Anda.
                        </td>
                    </tr>
                @endempty
        </tbody>
    </table>

    <x-ui.table-footer :data="$buses" />
</div>

<x-ui.modal name="modalBusForm" title="Formulir Inventaris Bus Saudi" maxWidth="max-w-md">
    <x-slot:subtitle>Daftarkan spesifikasi armada transportasi lokal Saudi untuk pengaturan manifest rombongan
        jemaah.</x-slot:subtitle>

    <form wire:submit.prevent="saveBus" class="space-y-4">

        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <flux:input wire:model="form.company_name" label="Nama Perusahaan Bus"
                    placeholder="E.g. Saptco Vip / Rawahel" required size="sm" />
            </div>
            <div class="col-span-1">
                <flux:input wire:model="form.capacity" type="number" min="15" max="60"
                    label="Kapasitas Kursi" required size="sm" />
            </div>
        </div>

        <flux:input wire:model="form.fleet_number" label="Nomor Plat / Kode Lambung Bus"
            placeholder="E.g. SPT-4402-ARA" required size="sm" />

        <flux:separator class="my-1" />

        <div class="grid grid-cols-2 gap-4">
            <div>
                <flux:input wire:model="form.driver_name" label="Nama Pengemudi / Driver"
                    placeholder="E.g. Ahmed Al-Ghamdi" size="sm" />
            </div>
            <div>
                <flux:input wire:model="form.driver_phone" label="Kontor Telepon Driver"
                    placeholder="E.g. +9665xxxxx" size="sm" />
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
            <flux:button x-on:click="$dispatch('close-modal', { name: 'modalBusForm' })" type="button"
                variant="ghost" size="sm">Batal</flux:button>
            <flux:button type="submit" variant="primary" size="sm" class="shadow-sm font-semibold px-4">Simpan
                Data Bus</flux:button>
        </div>
    </form>
</x-ui.modal>

<x-ui.confirm-modal name="confirmDeleteBus" title="Nonaktifkan Unit Bus" variant="danger">
    Armada bus yang dinonaktifkan tidak dapat ditunjuk untuk melayani rombongan kelompok terbang baru. Apakah Anda
    yakin?
</x-ui.confirm-modal>
</div>
