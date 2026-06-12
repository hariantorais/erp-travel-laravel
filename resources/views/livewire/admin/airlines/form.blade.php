<?php

use function Livewire\Volt\{form, on, usesFileUploads};
use App\Livewire\Forms\Master\AirlineForm;
use App\Services\Master\AirlineService;
use Illuminate\Support\Str;

usesFileUploads();

form(AirlineForm::class);

on([
    'open-modal' => function ($id = null) {
        if ($id) {
            $this->form->setAirline($id);
        } else {
            $this->form->clear();
        }
    },
]);

/**
 * Simpan Data Maskapai - Bersih dengan Delegasi Penuh ke Service Layer
 */
$saveAirline = function (AirlineService $airlineService) {
    $this->validate();

    $payload = [
        'name' => $this->form->name,
        'code' => strtoupper($this->form->code),
        'updated_at' => now(),
    ];

    try {
        if ($this->form->airlineId) {
            // Mode Update: Kirim file logo mentah sebagai argumen ketiga
            $airlineService->updateAirline($this->form->airlineId, $payload, $this->form->logo);
            $this->dispatch('toast', type: 'success', title: 'Sistem Diperbarui', message: 'Data vendor maskapai ' . $payload['name'] . ' berhasil disimpan!');
        } else {
            // Mode Create: Inject UUID dan kirim file logo mentah
            $payload['uuid'] = (string) Str::uuid();
            $payload['created_at'] = now();

            $airlineService->createAirline($payload, $this->form->logo);
            $this->dispatch('toast', type: 'success', title: 'Registrasi Berhasil', message: 'Maskapai penerbangan baru sukses ditambahkan ke basis data.');
        }

        $this->form->clear();
        $this->dispatch('close-modal', name: 'modalForm');
        $this->dispatch('airline-updated');
    } catch (\Exception $e) {
        $this->dispatch('toast', type: 'danger', title: 'Operasi Gagal', message: 'Terjadi redundansi data atau kesalahan database internal.');
    }
};

?>

<form wire:submit.prevent="saveAirline" class="space-y-4">

    <div class="grid grid-cols-4 gap-4">
        <div class="col-span-1">
            <flux:input wire:model="form.code" label="Kode IATA" placeholder="E.g. SV" required size="sm"
                class="font-mono uppercase text-center tracking-wider font-bold" maxlength="10" />
        </div>
        <div class="col-span-3">
            <flux:input wire:model="form.name" label="Nama Lengkap Maskapai" placeholder="E.g. Saudi Arabian Airlines"
                required size="sm" />
        </div>
    </div>

    <!-- UI INPUT FILE LOGO -->
    <div class="space-y-2">
        <label class="text-sm font-medium text-slate-700 block">Logo Maskapai</label>

        <div class="flex items-center gap-4 p-3 bg-slate-50 border border-slate-200 rounded-xl">
            <!-- Preview Section -->
            <div
                class="w-12 h-12 bg-white rounded-lg border border-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                @if ($this->form->logo)
                    <img src="{{ $this->form->logo->temporaryUrl() }}" class="w-full h-full object-contain">
                @elseif ($this->form->logo_url)
                    <img src="{{ $this->form->logo_url }}" class="w-full h-full object-contain">
                @else
                    <flux:icon name="photo" variant="mini" class="w-5 h-5 text-slate-300" />
                @endif
            </div>

            <!-- Input File -->
            <div class="flex-1 min-w-0">
                <input type="file" wire:model="form.logo" accept="image/*"
                    class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer w-full" />
                <p class="text-[10px] text-slate-400 mt-1">Format: PNG, JPG, WebP. Maksimal ukuran file 2MB.</p>
            </div>
        </div>
        @error('form.logo')
            <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
        @enderror
    </div>

    <x-ui.modal-footer />
</form>
