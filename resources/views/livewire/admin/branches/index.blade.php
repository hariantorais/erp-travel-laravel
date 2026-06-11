<?php

use function Livewire\Volt\{state, title, layout, with, on};
use App\Services\Master\BranchService;

// Page Configuration
title('Manajemen Kantor Cabang - ERP Umroh');
layout('components.layouts.app');

// 1. Hanya menyisakan state pencarian tabel saja
state([
    'search' => '',
]);

// 2. Dependency Injection & Data Fetching
with(
    fn(BranchService $branchService) => [
        'branches' => $branchService->getPaginatedBranches($this->search, 5),
    ],
);

// 3. Menangkap sinyal dari form anak untuk memperbarui isi tabel secara reaktif
on([
    'branch-updated' => function () {
        // Otomatis menolak cache dan merender ulang tabel
    },
]);

$deleteBranch = function (int $id, BranchService $branchService) {
    $branchService->deleteBranch($id);

    // Kirim sinyal Premium Toast bertipe Warning/Error ke browser
    $this->dispatch('toast', type: 'warning', title: 'Data Dihapus', message: 'Kantor cabang tersebut telah berhasil dihapus dari sistem.');
};

?>

<div class="space-y-6">

    @if (session()->has('message'))
        <div
            class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 text-sm font-medium flex items-center gap-2 shadow-sm">
            ✓ {{ session('message') }}
        </div>
    @endif

    <flux:card class="p-6 bg-white shadow-sm border border-slate-200 rounded-2xl space-y-4">

        <x-ui.table-controls placeholder="Cari data..." buttonLabel="Tambah Cabang Baru" modalTarget="modalForm" />

        <div class="overflow-x-auto rounded-lg border border-slate-100">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead
                    class="bg-slate-50 text-slate-500 font-semibold uppercase text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4">Kode</th>
                        <th class="p-4">Nama Cabang</th>
                        <th class="p-4">Pimpinan</th>
                        <th class="p-4">No. Telepon</th>
                        <th class="p-4 text-center">Tipe Otoritas</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($branches as $branch)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-mono font-bold text-xs text-indigo-600">{{ $branch->code }}</td>
                            <td class="p-4 font-medium text-slate-800">
                                {{ $branch->name }}
                                <p class="text-[11px] text-slate-400 font-normal mt-0.5">
                                    {{ Str::limit($branch->address, 50) }}</p>
                            </td>
                            <td class="p-4 text-slate-700">{{ $branch->leader_name ?? '-' }}</td>
                            <td class="p-4 text-slate-500 text-xs">{{ $branch->phone ?? '-' }}</td>
                            <td class="p-4 text-center">
                                @if ($branch->is_main_office)
                                    <flux:badge color="indigo" variant="solid" size="sm"
                                        class="font-semibold text-[10px]">Pusat / HQ</flux:badge>
                                @else
                                    <flux:badge color="slate" size="sm" class="font-medium text-[10px]">Cabang
                                        Pembantu</flux:badge>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <x-ui.table-actions :id="$branch->id" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-sm text-slate-400">
                                Tidak ada data kantor cabang yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $branches->links() }}
        </div>
    </flux:card>

    <x-ui.modal name="modalForm" title="Form Registrasi Kantor Cabang" maxWidth="max-w-lg">
        <x-slot:subtitle>Pastikan kode internal cabang unik dan terstandardisasi.</x-slot:subtitle>

        <livewire:admin.branches.form />
    </x-ui.modal>

    <x-ui.confirm-modal name="confirmDelete" title="Hapus Data Kantor Cabang" variant="danger">
        Seluruh akses operasional untuk cabang ini akan dinonaktifkan sementara dari sistem ERP. Apakah Anda yakin?
    </x-ui.confirm-modal>
</div>
</div>
