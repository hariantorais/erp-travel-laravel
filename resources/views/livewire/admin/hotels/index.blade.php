<?php

use function Livewire\Volt\{state, title, layout, with, on};
use App\Services\Master\HotelService;

title('Manajemen Master Hotel - ERP Umroh');
layout('components.layouts.app');

// 1. Menyimpan state pencarian tabel dan filter kota
state([
    'search' => '',
    'city' => '',
]);

// 2. Data Fetching via Service Layer
with(
    fn(HotelService $hotelService) => [
        'hotels' => $hotelService->getPaginatedHotels($this->search, $this->city, 10),
    ],
);

// 3. Menangkap sinyal dari form anak untuk memperbarui isi tabel secara reaktif
on([
    'hotel-updated' => function () {
        // Otomatis menolak cache dan merender ulang tabel
    },
]);

/**
 * Menghapus Data Hotel (Soft Deletes murni via Service)
 */
$deleteHotel = function (int $id, HotelService $hotelService) {
    // Proteksi: Cek apakah hotel ini sudah dikunci oleh template paket aktif (M2.3)
    $isUsedInPackage = DB::table('packages')->where('hotel_madinah_id', $id)->orWhere('hotel_makkah_id', $id)->whereNull('deleted_at')->exists();

    if ($isUsedInPackage) {
        $this->dispatch('toast', type: 'danger', title: 'Akses Ditolak', message: 'Hotel tidak bisa dihapus karena terikat sebagai acuan default pada Paket Wisata.');
        return;
    }

    $hotelService->deleteHotel($id);
    $this->dispatch('toast', type: 'warning', title: 'Data Dihapus', message: 'Hotel tersebut telah berhasil dihapus dari sistem.');
};

?>

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Master Database Hotel</h2>
        <p class="text-xs text-slate-500 mt-1">Kelola seluruh jaringan akomodasi hotel di Makkah, Madinah, dan Jeddah
            untuk kebutuhan plotting paket serta logistik kloter.</p>
    </div>

    <flux:card class="p-6 bg-white shadow-sm border border-slate-200 rounded-2xl space-y-4">

        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <x-ui.table-controls placeholder="Cari nama hotel atau alamat..." buttonLabel="Tambah Hotel Baru"
                    modalTarget="modalForm" />
                <flux:select wire:model.live="city" size="sm" class="w-36 shrink-0" placeholder="Semua Kota">
                    <option value="makkah">Makkah</option>
                    <option value="madinah">Madinah</option>
                    <option value="jeddah">Jeddah</option>
                </flux:select>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-100">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead
                    class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4">Nama Hotel</th>
                        <th class="p-4">Kota</th>
                        <th class="p-4 text-center">Kelas Bintang</th>
                        <th class="p-4 text-center">Jarak ke Haram</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($hotels as $hotel)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-medium text-slate-800">
                                <div class="flex items-center gap-2">
                                    {{ $hotel->name }}
                                    @if ($hotel->map_url)
                                        <a href="{{ $hotel->map_url }}" target="_blank"
                                            class="text-indigo-500 hover:text-indigo-700">
                                            <flux:icon name="map-pin" variant="mini" class="w-3.5 h-3.5" />
                                        </a>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-400 font-normal mt-0.5">
                                    {{ Str::limit($hotel->address, 60, '...') ?? '-' }}</p>
                            </td>
                            <td class="p-4 uppercase font-bold text-xs tracking-wider text-slate-500">
                                {{ $hotel->city }}</td>
                            <td class="p-4 text-center font-medium text-amber-500">
                                {{ str_repeat('⭐', $hotel->stars ?? 3) }}
                            </td>
                            <td class="p-4 text-center font-mono text-xs font-bold text-slate-700">
                                {{ $hotel->distance_to_haram ? $hotel->distance_to_haram . ' M' : '-' }}
                            </td>
                            <td class="p-4 text-center">
                                @if ($hotel->is_active)
                                    <flux:badge color="emerald" size="sm">Aktif</flux:badge>
                                @else
                                    <flux:badge color="rose" size="sm">Non-Aktif</flux:badge>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <x-ui.table-actions :id="$hotel->id" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-sm text-slate-400 italic">
                                Tidak ada data akomodasi hotel yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $hotels->links() }}
        </div>
    </flux:card>

    <x-ui.modal name="modalForm" title="Form Registrasi Hotel Vendor" maxWidth="max-w-md">
        <x-slot:subtitle>Pastikan estimasi jarak ke halaman utama masjid akurat demi kredibilitas brosur
            paket.</x-slot:subtitle>
        <livewire:admin.hotels.form />
    </x-ui.modal>

    <x-ui.confirm-modal name="confirmDelete" title="Hapus Data Hotel Vendor" variant="danger">
        Akomodasi ini akan dihapus dari pilihan logistik paket keberangkatan. Apakah Anda yakin?
    </x-ui.confirm-modal>
</div>
