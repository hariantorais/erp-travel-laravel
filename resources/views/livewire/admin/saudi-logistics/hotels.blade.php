<?php

use function Livewire\Volt\{state, title, layout, with, form, on};
use App\Services\Master\SaudiLogisticsService;
use App\Livewire\Forms\Master\HotelForm;

title('Master Akomodasi Hotel Saudi - ERP Sahara Travel');
layout('components.layouts.app');

// Injeksi Form Object Hotel
form(HotelForm::class);

state([
    'search' => '',
    'perPage' => 10,
]);

/**
 * Mengirimkan data paginasi hotel secara reaktif ke view
 */
with(
    fn(SaudiLogisticsService $logisticsService) => [
        'hotels' => $logisticsService->getPaginatedHotels($this->search, $this->perPage),
    ],
);

/**
 * Menangkap event pembukaan modal form
 */
on([
    'open-modal' => function ($name, $id = null) {
        if ($name === 'modalHotelForm') {
            if ($id) {
                $logisticsService = app(SaudiLogisticsService::class);
                // Kita buat fungsi pencarian single record di service layer nanti jika diperlukan
                $hotel = DB::table('saudi_hotels')->where('id', $id)->first();
                if ($hotel) {
                    $this->form->fillFromData($hotel);
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
$saveHotel = function (SaudiLogisticsService $logisticsService) {
    $validated = $this->form->validate();

    if ($this->form->hotelId) {
        // Mode Update data murni
        DB::table('saudi_hotels')
            ->where('id', $this->form->hotelId)
            ->update([
                'name' => $validated['name'],
                'city' => $validated['city'],
                'star_rating' => $validated['star_rating'],
                'address' => $validated['address'],
                'contact_person' => $validated['contact_person'],
                'phone' => $validated['phone'],
                'updated_at' => now(),
            ]);

        $this->dispatch('toast', type: 'success', title: 'Data Diperbarui', message: 'Spesifikasi akomodasi hotel ' . $this->form->name . ' sukses diubah.');
    } else {
        // Mode Create data baru via Service Layer
        $logisticsService->createHotel($validated);

        $this->dispatch('toast', type: 'success', title: 'Hotel Didaftarkan', message: 'Mitra akomodasi hotel baru berhasil disimpan ke sistem.');
    }

    $this->form->clear();
    $this->dispatch('close-modal', name: 'modalHotelForm');
};

/**
 * Penghapusan Data Mitra Hotel (Soft Delete)
 */
$deleteHotel = function (int $id) {
    DB::table('saudi_hotels')
        ->where('id', $id)
        ->update([
            'deleted_at' => now(),
        ]);

    $this->dispatch('toast', type: 'warning', title: 'Hotel Dinonaktifkan', message: 'Data mitra hotel berhasil dikeluarkan dari daftar aktif.');
};

?>

<div class="space-y-5">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Master Hotel Arab Saudi</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh portofolio kemitraan akomodasi hotel jemaah di kota
                Makkah dan Madinah.</p>
        </div>
        <flux:button x-on:click="$dispatch('open-modal', { name: 'modalHotelForm' })" variant="primary" icon="plus"
            size="sm" class="rounded-xl font-semibold shadow-sm shrink-0">
            Tambah Mitra Hotel
        </flux:button>
    </div>

    <div class="flex justify-between items-center bg-white p-3 border border-slate-200/80 rounded-xl gap-4 shadow-sm">
        <div class="w-full max-w-xs relative">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama hotel atau kota..."
                size="sm" icon="magnifying-glass" class="rounded-lg" />
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[11px] text-slate-400 font-medium whitespace-nowrap">Baris per halaman:</span>
            <select wire:model.live="perPage"
                class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 p-1.5 rounded-lg focus:outline-none">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="p-4">Nama Hotel & Klasifikasi</th>
                    <th class="p-4">Kota Logistik</th>
                    <th class="p-4">Kontak Person (PJ)</th>
                    <th class="p-4">Alamat Singkat</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-600">
                @forelse($hotels as $hotel)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-4">
                            <div class="space-y-0.5">
                                <span class="text-sm font-bold text-slate-900 block">{{ $hotel->name }}</span>
                                <div class="flex items-center gap-0.5 text-amber-400">
                                    @for ($i = 1; $i <= $hotel->star_rating; $i++)
                                        <flux:icon name="star" variant="solid" class="w-3 h-3" />
                                    @endfor
                                    <span class="text-[10px] text-slate-400 ml-1 font-mono">Bintang
                                        {{ $hotel->star_rating }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            @if ($hotel->city === 'MAKKAH')
                                <span
                                    class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-2 py-0.5 rounded-md font-mono font-bold text-[10px]">🕋
                                    MAKKAH</span>
                            @else
                                <span
                                    class="bg-indigo-50 text-indigo-700 border border-indigo-100 px-2 py-0.5 rounded-md font-mono font-bold text-[10px]">🕌
                                    MADINAH</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="space-y-0.5">
                                <span class="text-slate-800 block">{{ $hotel->contact_person ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $hotel->phone ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="p-4 max-w-xs truncate text-slate-500">
                            {{ $hotel->address ?? '-' }}
                        </td>
                        <td class="p-4 text-right">
                            <x-ui.table-actions :id="$hotel->id" editModal="modalHotelForm"
                                deleteModal="confirmDeleteHotel" deleteAction="deleteHotel" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 italic">
                            Belum ada records data hotel Saudi yang sesuai dengan kriteria pencarian Anda.
                        </td>
                    </tr>
                @endempty
        </tbody>
    </table>

    @if ($hotels->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $hotels->links() }}
        </div>
    @endif
</div>

<x-ui.modal name="modalHotelForm" title="Formulir Kemitraan Hotel Saudi" maxWidth="max-w-md">
    <x-slot:subtitle>Isi data spesifikasi hotel secara akurat untuk mempermudah alokasi kamar paket
        umroh.</x-slot:subtitle>

    <form wire:submit.prevent="saveHotel" class="space-y-4">

        <flux:input wire:model="form.name" label="Nama Lengkap Hotel" placeholder="E.g. Pullman Zamzam Makkah"
            required size="sm" />

        <div class="grid grid-cols-2 gap-4">
            <div>
                <flux:select wire:model="form.city" label="Kota Penempatan" size="sm" required>
                    <option value="MAKKAH">MAKKAH</option>
                    <option value="MADINAH">MADINAH</option>
                </flux:select>
            </div>
            <div>
                <flux:select wire:model="form.star_rating" type="number" label="Klasifikasi Bintang" size="sm"
                    required>
                    <option value="3">Bintang 3</option>
                    <option value="4">Bintang 4</option>
                    <option value="5">Bintang 5</option>
                </flux:select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <flux:input wire:model="form.contact_person" label="Nama Contact Person (PJ)"
                    placeholder="E.g. Syeikh Ahmad" size="sm" />
            </div>
            <div>
                <flux:input wire:model="form.phone" label="Nomor Telepon/WhatsApp" placeholder="E.g. +9665xxxxx"
                    size="sm" />
            </div>
        </div>

        <flux:textarea wire:model="form.address" label="Alamat / Koordinat Lokasi"
            placeholder="E.g. Abraj Al Bait, Ring Road 1, Makkah..." rows="3" />

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
            <flux:button x-on:click="$dispatch('close-modal', { name: 'modalHotelForm' })" type="button"
                variant="ghost" size="sm">Batal</flux:button>
            <flux:button type="submit" variant="primary" size="sm" class="shadow-sm font-semibold px-4">Simpan
                Data Hotel</flux:button>
        </div>
    </form>
</x-ui.modal>

<x-ui.confirm-modal name="confirmDeleteHotel" title="Nonaktifkan Kemitraan Hotel" variant="danger">
    Hotel yang dinonaktifkan tidak akan muncul pada pilihan paket keberangkatan baru. Apakah Anda yakin ingin
    melanjutkan?
</x-ui.confirm-modal>
</div>
