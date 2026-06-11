<?php
use function Livewire\Volt\{state, title, layout, with, on};
use App\Services\Master\DepartureService;

title('Manajemen Jadwal Keberangkatan - ERP Umroh');
layout('components.layouts.app');

state(['search' => '']);

with(
    fn(DepartureService $service) => [
        'departures' => $service->getPaginatedDepartures($this->search, 10),
    ],
);

on(['departure-updated' => fn() => null]);

$deleteDeparture = function (int $id, DepartureService $service) {
    $service->deleteDeparture($id);
    $this->dispatch('toast', type: 'warning', title: 'Jadwal Dihapus', message: 'Jadwal keberangkatan berhasil diarsipkan.');
};
?>

<div class="space-y-6">
    <flux:card class="p-6 bg-white shadow-sm border border-slate-200 rounded-2xl space-y-4">
        <div class="flex justify-between items-center gap-3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari kode, paket, atau tanggal..."
                icon="magnifying-glass" size="sm" class="w-full max-w-md" />
            <flux:button x-on:click="$dispatch('open-modal', { name: 'modalForm' })" variant="primary" icon="plus"
                size="sm">Tambah Jadwal</flux:button>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-100">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text- tracking-wider border-b">
                    <tr>
                        <th class="p-4">Kode</th>
                        <th class="p-4">Paket & Cabang</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Kuota</th>
                        <th class="p-4">Harga Quad</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($departures as $d)
                        <tr class="border-b">
                            <td class="p-4">{{ $d->code }}</td>
                            <td class="p-4">{{ $d->package_name }}
                                {{ $d->branch_code }} - {{ $d->branch_name }}
                            </td>
                            <td class="p-4">{{ \Carbon\Carbon::parse($d->departure_date)->format('d M Y') }}</td>
                            <td class="p-4">{{ $d->quota_left }}/{{ $d->quota }}</td>

                            <td class="p-4 font-semibold">Rp {{ idr($d->quad_price) }}</td>
                            <td class="p-4">
                                <flux:badge :color="$d->status_color">{{ $d->status_name }}</flux:badge>
                            </td>
                            <td class="p-4">
                                <flux:button href="{{ route('admin.departures.show', $d->id) }}" icon="briefcase"
                                    size="xs" variant="ghost" class="text-indigo-600 hover:bg-indigo-50"
                                    inset="top bottom">
                                    Logistik
                                </flux:button>
                                <x-ui.table-actions :id="$d->id" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4 text-center text-gray-500">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pt-2">{{ $departures->links() }}</div>
    </flux:card>

    <x-ui.modal name="modalForm" title="Form Jadwal Keberangkatan" maxWidth="max-w-4xl">
        <x-slot:subtitle>Isi detail keberangkatan dan skema harga 4 tipe kamar.</x-slot:subtitle>
        <livewire:admin.departures.form />
    </x-ui.modal>

    <x-ui.confirm-modal name="confirmDelete" title="Arsipkan Jadwal?" variant="danger">
        Jadwal yang diarsipkan tidak bisa menerima booking baru.
    </x-ui.confirm-modal>
</div>
