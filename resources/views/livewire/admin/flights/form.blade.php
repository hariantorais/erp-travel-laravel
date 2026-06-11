<?php

use function Livewire\Volt\{form, on, state, mount};
use App\Services\Master\FlightService;
use App\Livewire\Forms\Master\FlightForm;
use Illuminate\Support\Facades\DB;

// 1. Injeksi Form Utility Class ke dalam Volt State
form(FlightForm::class);

// State lokal khusus untuk merender opsi dropdown maskapai
state(['airlines' => []]);

mount(function () {
    $this->airlines = DB::table('airlines')->select('id', 'name', 'code')->orderBy('name', 'asc')->get();
});

// 2. Tangkap trigger aksi penekanan tombol tambah/edit dari komponen induk
on([
    'open-modal' => function ($id = null) {
        if ($id) {
            $this->form->setFlight($id);
        } else {
            $this->form->clear();
        }
    },
]);

// 3. Action Method: Persist Data Layer
$saveFlight = function (FlightService $flightService) {
    $this->validate();

    // Normalisasi inputan data menjadi huruf kapital murni
    $payload = [
        'airline_id' => $this->form->airline_id,
        'flight_no' => strtoupper($this->form->flight_no),
        'departure_airport' => strtoupper($this->form->departure_airport),
        'arrival_airport' => strtoupper($this->form->arrival_airport),
    ];

    try {
        if ($this->form->flightId) {
            $flightService->updateFlight($this->form->flightId, $payload);
            $this->dispatch('toast', type: 'success', title: 'Sistem Diperbarui', message: 'Rute penerbangan ' . $payload['flight_no'] . ' berhasil diperbarui!');
        } else {
            $flightService->createFlight($payload);
            $this->dispatch('toast', type: 'success', title: 'Registrasi Berhasil', message: 'Nomor penerbangan komersial baru berhasil didaftarkan ke ERP.');
        }

        $this->form->clear();
        $this->dispatch('close-modal', name: 'modalFlightForm');
        $this->dispatch('flight-updated'); // Beri sinyal ke index untuk merender ulang tabel
    } catch (\Exception $e) {
        $this->dispatch('toast', type: 'danger', title: 'Gagal Menyimpan', message: 'Terjadi kegagalan sistem atau kode penerbangan duplikat.');
    }
};

?>

<form wire:submit.prevent="saveFlight" class="space-y-4">

    <flux:select wire:model="form.airline_id" label="Perusahaan Maskapai (Vendor)" placeholder="-- Pilih Maskapai --"
        required size="sm">
        @foreach ($airlines as $air)
            <flux:select.option value="{{ $air->id }}">[{{ $air->code }}] {{ $air->name }}
            </flux:select.option>
        @endforeach
    </flux:select>

    <flux:input wire:model="form.flight_no" label="Nomor Penerbangan (Flight No.)" placeholder="E.g. SV817 atau GA980"
        required size="sm" class="font-mono uppercase tracking-wider" />

    <div class="grid grid-cols-2 gap-4">
        <flux:input wire:model="form.departure_airport" label="Kode Bandara Asal" placeholder="E.g. CGK" required
            size="sm" maxlength="3" class="font-mono uppercase text-center font-bold" />
        <flux:input wire:model="form.arrival_airport" label="Kode Bandara Tujuan" placeholder="E.g. MED" required
            size="sm" maxlength="3" class="font-mono uppercase text-center font-bold text-indigo-600" />
    </div>

    <x-ui.modal-footer />
</form>
