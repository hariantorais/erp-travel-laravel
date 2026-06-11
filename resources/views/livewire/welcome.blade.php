<?php

use function Livewire\Volt\{state, title};

title('Pengujian Komponen Flux UI');

// State untuk menguji reaktivitas data biding pada komponen Flux
state([
    'counter' => 0,
    'inputUji' => '',
    'sudahDiklik' => false,
]);

// Fungsi reaktif untuk dipanggil oleh tombol Flux
$increment = function () {
    $this->counter++;
    $this->sudahDiklik = true;
};

?>

<div class="space-y-6 max-w-2xl mx-auto">
    <flux:card class="p-6 bg-white shadow-sm border border-slate-200 rounded-xl space-y-4">
        <div class="flex justify-between items-start">
            <div>
                <flux:heading size="xl" level="1" class="font-bold text-slate-800">Laboratorium Pengujian Flux UI
                </flux:heading>
                <flux:subheading class="text-sm text-slate-500">Memastikan instalasi TALL Stack v4 berfungsi penuh
                </flux:subheading>
            </div>
            <flux:badge color="emerald" variant="solid" size="sm">Sistem Siap</flux:badge>
        </div>

        <hr class="border-slate-100" />

        <div class="p-4 bg-slate-50 rounded-lg space-y-3">
            <flux:heading level="2" size="md" class="font-semibold text-slate-700">Test 1: Reaktivitas &
                Tombol</flux:heading>
            <p class="text-xs text-slate-600">Klik tombol di bawah ini untuk menguji apakah jembatan Livewire dan
                javascript Flux berjalan:</p>

            <div class="flex items-center space-x-4">
                <flux:button wire:click="increment" variant="primary" icon="plus" size="sm">
                    Tambah Nilai
                </flux:button>

                <div class="text-sm">
                    Nilai Counter: <strong class="text-indigo-600 text-lg">{{ $counter }}</strong>
                </div>
            </div>

            @if ($sudahDiklik)
                <p class="text-[11px] text-emerald-600 font-medium flex items-center gap-1">
                    ✓ Livewire Volt Berhasil Mengeksekusi Fungsi Backend Tanpa Reload!
                </p>
            @endif
        </div>

        <div class="space-y-2 pt-2">
            <flux:heading level="2" size="md" class="font-semibold text-slate-700">Test 2: Komponen Input Form
            </flux:heading>

            <flux:input wire:model.live="inputUji" label="Ketik Sesuatu Di Sini:" placeholder="Contoh: Tes Koneksi"
                icon="magnifying-glass" size="sm" />

            @if ($inputUji)
                <div class="mt-2 p-2 bg-indigo-50 text-indigo-700 rounded-md text-xs">
                    <strong>Hasil Live Binding:</strong> {{ $inputUji }}
                </div>
            @endif
        </div>
    </flux:card>
</div>
