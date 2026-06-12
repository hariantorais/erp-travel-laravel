<?php

use function Livewire\Volt\{state, title, layout, with, on};
use Illuminate\Support\Facades\DB;

title('Daftar Maskapai - ERP Umroh');
layout('components.layouts.app');

// 1. Menyimpan state pencarian tabel saja
state([
    'search' => '',
]);

// 2. Data Fetching menggunakan Query Builder SQL murni (Mengakomodasi SoftDeletes)
with(function () {
    $airlines = DB::table('airlines')
        ->whereNull('deleted_at')
        ->when($this->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
            });
        })
        ->orderBy('name', 'asc')
        ->paginate(10);

    return [
        'airlines' => $airlines,
    ];
});

// 3. Menangkap sinyal reaktivitas dari form anak untuk me-refresh tabel
on([
    'airline-updated' => function () {
        // Otomatis merender ulang tabel maskapai
    },
]);

/**
 * Menghapus Data Maskapai (Soft Deletes murni via Query Builder)
 */
$deleteAirline = function (int $id) {
    // Proteksi: Jangan hapus maskapai jika sudah dikunci oleh rute penerbangan aktif
    $hasFlights = DB::table('flights')->where('airline_id', $id)->whereNull('deleted_at')->exists();
    if ($hasFlights) {
        $this->dispatch('toast', type: 'danger', title: 'Akses Ditolak', message: 'Maskapai ini tidak bisa dihapus karena memiliki rute penerbangan yang aktif.');
        return;
    }

    DB::table('airlines')
        ->where('id', $id)
        ->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

    $this->dispatch('toast', type: 'warning', title: 'Data Dihapus', message: 'Maskapai tersebut telah berhasil dihapus (Soft Delete).');
};

?>

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Daftar Maskapai Vendor</h2>
        <p class="text-xs text-slate-500 mt-1">Kelola vendor perusahaan penerbangan komersial utama yang bekerja sama
            dengan Sahara Travel.</p>
    </div>

    <flux:card class="p-6 bg-white shadow-sm border border-slate-200 rounded-2xl space-y-4">

        <!-- Reusable Controls Sahara UI -->
        <x-ui.table-controls placeholder="Cari nama maskapai atau kode..." buttonLabel="Tambah Maskapai Baru"
            modalTarget="modalForm" />

        <div class="overflow-x-auto rounded-lg border border-slate-100">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead
                    class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4 w-20">Logo</th>
                        <th class="p-4">Kode IATA</th>
                        <th class="p-4">Nama Perusahaan Maskapai</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($airlines as $air)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4">
                                @if ($air->logo_url)
                                    <img src="{{ $air->logo_url }}" alt="Logo"
                                        class="w-8 h-8 object-contain rounded">
                                @else
                                    <div
                                        class="w-8 h-8 bg-slate-100 rounded flex items-center justify-center text-slate-400 font-bold text-xs font-mono">
                                        {{ $air->code }}
                                    </div>
                                @endif
                            </td>
                            <td class="p-4 font-mono font-black text-indigo-600 text-xs tracking-wider">
                                {{ $air->code }}</td>
                            <td class="p-4 font-medium text-slate-800">{{ $air->name }}</td>
                            <td class="p-4 text-right">
                                <x-ui.table-actions :id="$air->id" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-sm text-slate-400 italic">
                                Belum ada perusahaan maskapai yang terdaftar di dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $airlines->links() }}
        </div>
    </flux:card>

    <!-- Modal Form Injector -->
    <x-ui.modal name="modalForm" title="Form Registrasi Maskapai Vendor" maxWidth="max-w-md">
        <x-slot:subtitle>Pastikan kode unik IATA maskapai sesuai standar penerbangan internasional.</x-slot:subtitle>
        <livewire:admin.airlines.form />
    </x-ui.modal>

    <x-ui.confirm-modal name="confirmDelete" title="Hapus Maskapai Vendor?" variant="danger">
        Data maskapai ini akan dinonaktifkan dari master logistik penerbangan. Apakah Anda yakin?
    </x-ui.confirm-modal>
</div>
