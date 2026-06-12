<?php

use function Livewire\Volt\{state, title, layout, with, on};
use App\Services\Master\BusService;

title('Manajemen Armada Bus - ERP Umroh');
layout('components.layouts.app');

state([
    'search' => '',
]);

with(
    fn(BusService $busService) => [
        'buses' => $busService->getPaginatedBuses($this->search, 10),
    ],
);

on([
    'bus-updated' => function () {
        // Refresh tabel reaktif
    },
]);

$deleteBus = function (int $id, BusService $busService) {
    $busService->deleteBus($id);
    $this->dispatch('toast', type: 'warning', title: 'Data Dihapus', message: 'Data bus berhasil dihapus dari sistem.');
};

?>

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Master Transpotasi Bus</h2>
        <p class="text-xs text-slate-500 mt-1">Kelola data seluruh armada bus operasional, nomor kontak driver, serta
            pelat nomor untuk kebutuhan penjemputan jemaah di bandara maupun ziarah.</p>
    </div>

    <flux:card class="p-6 bg-white shadow-sm border border-slate-200 rounded-2xl space-y-4">

        <x-ui.table-controls placeholder="Cari kode, nama bus, pelat, atau vendor..." buttonLabel="Tambah Bus Baru"
            modalTarget="modalForm" />

        <div class="overflow-x-auto rounded-lg border border-slate-100">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead
                    class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4">Kode & Nama Bus</th>
                        <th class="p-4">Pelat Nomor</th>
                        <th class="p-4">Vendor / Syarikah</th>
                        <th class="p-4">Driver & Kontak</th>
                        <th class="p-4 text-center">Kapasitas</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($buses as $bus)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-medium text-slate-800">
                                <span
                                    class="font-mono text-xs font-bold bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded mr-1">{{ $bus->code }}</span>
                                {{ $bus->name }}
                            </td>
                            <td class="p-4 font-mono text-xs font-bold text-slate-600 uppercase">
                                {{ $bus->plate_number ?? '-' }}</td>
                            <td class="p-4 text-slate-700 font-medium">{{ $bus->vendor_name ?? '-' }}</td>
                            <td class="p-4 text-xs">
                                <div class="font-medium text-slate-800">{{ $bus->driver_name ?? '-' }}</div>
                                <div class="text-slate-400 font-mono mt-0.5">{{ $bus->driver_phone ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-center font-mono text-xs font-bold text-indigo-600">{{ $bus->capacity }}
                                Pax</td>
                            <td class="p-4 text-center">
                                @if ($bus->is_active)
                                    <flux:badge color="emerald" size="sm">Aktif</flux:badge>
                                @else
                                    <flux:badge color="rose" size="sm">Non-Aktif</flux:badge>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <x-ui.table-actions :id="$bus->id" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-sm text-slate-400 italic">
                                Belum ada data armada bus yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $buses->links() }}
        </div>
    </flux:card>

    <x-ui.modal name="modalForm" title="Form Inventaris Bus Operasional" maxWidth="max-w-md">
        <x-slot:subtitle>Pastikan nomor ponsel driver menggunakan format internasional aktif (Contoh:
            +966...).</x-slot:subtitle>
        <livewire:admin.buses.form />
    </x-ui.modal>

    <x-ui.confirm-modal name="confirmDelete" title="Hapus Data Bus?" variant="danger">
        Armada ini akan dinonaktifkan dari sistem logistik rombongan jemaah. Apakah Anda yakin?
    </x-ui.confirm-modal>
</div>
