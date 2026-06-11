<?php

use function Livewire\Volt\{state, title, layout, with, on, usesPagination};
use App\Services\Master\PackageService;

// Page Configuration
title('Manajemen Cetak Biru Paket - ERP Sahara Travel');
layout('components.layouts.app');

usesPagination();

// 1. Global State untuk kebutuhan filter pencarian tabel master
state([
    'search' => '',
    'perPage' => 10,
]);

// 2. Data Fetching Layer melalui Service Master
with(
    fn(PackageService $packageService) => [
        'packages' => $packageService->getPaginatedPackages($this->search, $this->perPage),
    ],
);

// Mendengarkan emisi reaktif dari komponen form anak untuk menyegarkan tabel secara asinkron
on(['package-updated' => '$refresh']);

/**
 * Eksekusi Soft-Delete pada portofolio produk paket
 */
$deletePackage = function (int $id, PackageService $packageService) {
    $packageService->deletePackage($id);
    $this->dispatch('toast', type: 'warning', title: 'Portofolio Diarsipkan', message: 'Cetak biru produk program umroh berhasil dinonaktifkan.');
};

?>

<div class="space-y-6">
    <flux:card class="p-6 bg-white shadow-xs border border-slate-200 rounded-2xl space-y-4">

        <x-ui.table-controls placeholder="Cari berdasarkan kode SKU paket atau nama program..."
            buttonLabel="Buat Template Paket Baru" modalTarget="modalPackageForm" />

        <div class="overflow-x-auto rounded-xl border border-slate-200/60">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead
                    class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-200/60">
                    <tr>
                        <th class="p-4 w-1/3">Cetak Biru & Nama Program</th>
                        <th class="p-4">Jenis Kategori</th>
                        <th class="p-4">Durasi Acuan</th>
                        <th class="p-4">Rekomendasi Maskapai</th>
                        <th class="p-4">Estimasi Jadwal Brosur</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($packages as $package)
                        <tr class="hover:bg-slate-50/40 transition-colors">

                            <td class="p-4">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span
                                        class="font-mono font-bold text-indigo-600 tracking-wider text-[11px] bg-indigo-50 px-1.5 py-0.5 rounded-md">
                                        {{ $package->code }}
                                    </span>
                                    @if ($package->is_featured)
                                        <span
                                            class="bg-amber-50 text-amber-700 font-extrabold px-1.5 py-0.5 rounded text-[9px] uppercase tracking-wide border border-amber-200/50">
                                            ★ Highlight
                                        </span>
                                    @endif
                                </div>
                                <span class="font-bold text-slate-800 text-sm block mt-1">{{ $package->name }}</span>
                            </td>

                            <td class="p-4 font-semibold uppercase tracking-wider text-[10px]">
                                <span
                                    class="px-2 py-1 rounded-full {{ $package->type === 'haji' ? 'bg-purple-50 text-purple-700' : ($package->type === 'tour' ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700') }}">
                                    {{ $package->type }}
                                </span>
                            </td>

                            <td class="p-4 font-medium text-slate-700">
                                <span class="font-bold text-slate-900 text-sm">{{ $package->duration_days }}</span> Hari
                            </td>

                            <td class="p-4 text-slate-600 font-medium">
                                <span class="inline-flex items-center gap-1 text-slate-700 text-xs">
                                    ✈️ {{ $package->airline_name ?? 'Belum Ditentukan' }}
                                </span>
                                @if ($package->itinerary_summary)
                                    <p class="text-[10px] text-slate-400 font-normal mt-0.5 truncate max-w-xs">
                                        Rute: {{ $package->itinerary_summary }}
                                    </p>
                                @endif
                            </td>

                            <td class="p-4 text-slate-500 font-medium">
                                {{ $package->estimated_schedule ?? 'Kapan Saja (Fleksibel)' }}
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1">

                                    <flux:button href="{{ route('admin.packages.itinerary', $package->slug) }}"
                                        wire:navigate variant="ghost" size="sm" icon="map"
                                        class="text-slate-400 hover:text-indigo-600 rounded-lg"
                                        title="Kelola Susunan Itinerary Per Hari" />

                                    <x-ui.table-actions :id="$package->id" :showEdit="true" editModal="modalPackageForm"
                                        deleteModal="confirmDeletePackage" deleteAction="deletePackage" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-sm text-slate-400 font-medium">
                                Portofolio cetak biru program paket umroh belum terdaftar di dalam database master.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($packages->hasPages())
            <div class="pt-2">
                {{ $packages->links() }}
            </div>
        @endif
    </flux:card>

    <x-ui.modal name="modalPackageForm" title="Konfigurasi Master Brosur Paket Travel" maxWidth="max-w-2xl">
        <livewire:admin.packages.form />
    </x-ui.modal>

    <x-ui.confirm-modal name="confirmDeletePackage" title="Arsipkan Master Cetak Biru Paket" variant="danger">
        Tindakan ini akan memindahkan data cetak biru program paket umroh ke dalam folder arsip sistem induk
        (soft-delete).
        Seluruh jadwal operasional yang bergantung pada paket acuan ini akan dinonaktifkan. Apakah Anda yakin?
    </x-ui.confirm-modal>
</div>
