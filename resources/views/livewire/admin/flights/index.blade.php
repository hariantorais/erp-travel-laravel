<?php

use function Livewire\Volt\{state, title, layout, with, on};
use App\Services\Master\FlightService;

title('Master Penerbangan Komersial - ERP Umroh');
layout('components.layouts.app');

// 1. Hanya menyisakan state pencarian tabel saja sesuai PRD
state([
    'search' => '',
]);

// 2. Dependency Injection & Data Fetching dari Service Layer
with(
    fn(FlightService $flightService) => [
        'flights' => $flightService->getPaginatedFlights($this->search, 10),
    ],
);

// 3. Tangkap sinyal sukses dari komponen form anak untuk merender ulang tabel
on([
    'flight-updated' => function () {
        // Otomatis merender ulang tabel tanpa reload halaman
    },
]);

/**
 * Menghapus Rute Penerbangan dari Master Data
 */
$deleteFlight = function (int $id, FlightService $flightService) {
    // Proteksi: Cek paksa apakah penerbangan ini sudah mengunci logistik di M2.3
    $isUsed = DB::table('departure_flights')->where('flight_id', $id)->exists();
    if ($isUsed) {
        $this->dispatch('toast', type: 'danger', title: 'Akses Ditolak', message: 'Rute tidak bisa dihapus karena telah terikat di manifes jadwal keberangkatan.');
        return;
    }

    $flightService->deleteFlight($id);
    $this->dispatch('toast', type: 'warning', title: 'Data Dihapus', message: 'Rute penerbangan komersial berhasil dihapus dari sistem.');
};

?>

<div class="space-y-6">

    <flux:card class="p-6 bg-white shadow-sm border border-slate-200 rounded-2xl space-y-4">

        <x-ui.table-controls placeholder="Cari nomor pesawat, rute bandara..." buttonLabel="Tambah Penerbangan"
            modalTarget="modalFlightForm" />

        <div class="overflow-x-auto rounded-lg border border-slate-100">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead
                    class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4">No. Penerbangan</th>
                        <th class="p-4">Maskapai / Vendor</th>
                        <th class="p-4 text-center">Asal (ETD)</th>
                        <th class="p-4 text-center">Rute</th>
                        <th class="p-4 text-center">Tujuan (ETA)</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($flights as $f)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-mono font-black text-slate-900 tracking-tight">{{ $f->flight_no }}</td>
                            <td class="p-4 font-medium text-slate-800">
                                <span
                                    class="text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded mr-1.5 font-mono font-bold">{{ $f->airline_code }}</span>
                                {{ $f->airline_name }}
                            </td>
                            <td class="p-4 text-center font-bold tracking-wide text-slate-600">
                                {{ $f->departure_airport }}</td>
                            <td class="p-4 text-center text-slate-300 font-medium">➔</td>
                            <td class="p-4 text-center font-bold tracking-wide text-indigo-600">
                                {{ $f->arrival_airport }}</td>
                            <td class="p-4 text-right">
                                <x-ui.table-actions :id="$f->id" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-sm text-slate-400 italic">
                                Belum ada armada penerbangan komersial yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $flights->links() }}
        </div>
    </flux:card>

    <x-ui.modal name="modalFlightForm" title="Form Logistik Penerbangan Komersial" maxWidth="max-w-md">
        <x-slot:subtitle>Pastikan kode bandara menggunakan standar 3 huruf IATA (Contoh: CGK, JED, MED).</x-slot:subtitle>
        <livewire:admin.flights.form />
    </x-ui.modal>

    <x-ui.confirm-modal name="confirmDelete" title="Hapus Rute Penerbangan?" variant="danger">
        Data rute penerbangan komersial ini akan dihapus permanen dari sistem ERP utama. Apakah Anda yakin?
    </x-ui.confirm-modal>
</div>
