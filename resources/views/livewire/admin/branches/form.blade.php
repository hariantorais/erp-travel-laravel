<?php

use function Livewire\Volt\{form, on};
use App\Services\Master\BranchService;
use App\Livewire\Forms\Master\BranchForm;

// 1. Injeksi Form Utility Class ke dalam Volt State
form(BranchForm::class);

// 2. KUNCI PERBAIKAN: Gunakan Action Assignment murni untuk menangkap data ID secara aman
on([
    'open-modal' => function ($id = null) {
        if ($id) {
            $this->form->setBranch($id);
        } else {
            $this->form->clear();
        }
    },
]);

// 3. Action Method: Persist Data Layer
$saveBranch = function (BranchService $branchService) {
    $this->validate();

    if ($this->form->branchId) {
        $branchService->updateBranch($this->form->branchId, $this->form->all());

        // PREMIUM TOAST EVENT DISPATCHED (Mode Edit)
        $this->dispatch('toast', type: 'success', title: 'Sistem Diperbarui', message: 'Data jaringan kantor cabang ' . $this->form->name . ' berhasil diperbarui!');
    } else {
        $branchService->createBranch($this->form->all());

        // PREMIUM TOAST EVENT DISPATCHED (Mode Tambah Baru)
        $this->dispatch('toast', type: 'success', title: 'Registrasi Berhasil', message: 'Kantor cabang pembantu baru telah sukses didaftarkan ke sistem ERP.');
    }

    $this->form->clear();

    $this->dispatch('close-modal', name: 'modalForm');
    $this->dispatch('branch-updated');
};

?>

<form wire:submit.prevent="saveBranch" class="space-y-4">
    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-1">
            <flux:input wire:model="form.code" label="Kode Cabang" placeholder="E.g. BTM01" required size="sm" />
        </div>
        <div class="col-span-2">
            <flux:input wire:model="form.name" label="Nama Lengkap Cabang" placeholder="E.g. Cabang Batam Center"
                required size="sm" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <flux:input wire:model="form.leader_name" label="Nama Kepala Pimpinan" placeholder="Nama Lengkap"
            size="sm" />
        <flux:input wire:model="form.phone" label="Nomor Telepon Kantor" placeholder="0778-xxxx" size="sm" />
    </div>

    <flux:input wire:model="form.address" label="Alamat Fisik Kantor" placeholder="Jalan, Kompleks, No Ruko..."
        size="sm" />

    <div class="p-3 bg-slate-50 rounded-xl">
        <flux:checkbox wire:model="form.is_main_office" label="Tetapkan Sebagai Kantor Pusat Utama (Headquarter)"
            class="text-xs text-slate-600" />
    </div>

    <x-ui.modal-footer />
</form>
