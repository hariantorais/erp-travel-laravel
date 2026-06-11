<?php

use function Livewire\Volt\{state, form, on, mount};
use App\Livewire\Forms\Master\DepartureFlightForm;
use App\Services\Master\DepartureService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

form(DepartureFlightForm::class);

state([
    'departureId' => null,
    'departure' => null,
    'assignedFlights' => [],
    'masterFlights' => [],
]);

/**
 * Mengambil data logistik penerbangan yang sudah dikunci untuk keberangkatan ini
 */
$refreshFlights = function () {
    $this->assignedFlights = DB::table('departure_flights as df')
        ->join('flights as f', 'f.id', '=', 'df.flight_id')
        ->join('airlines as a', 'a.id', '=', 'f.airline_id')
        ->where('df.departure_id', $this->departureId)
        ->select(['df.id', 'df.departure_id', 'df.type', 'df.etd', 'df.eta', 'df.pnr_code', 'f.flight_no', 'f.departure_airport', 'f.arrival_airport', 'a.name as airline_name', 'a.logo_url as airline_logo'])
        ->get();
};

/**
 * Inisialisasi Data Komponen
 */
mount(function ($departureId) {
    $this->departureId = $departureId;

    // Ambil data induk keberangkatan menggunakan DB murni
    $this->departure = DB::table('departures')->where('id', $departureId)->first();
    if (!$this->departure) {
        abort(404, 'Jadwal keberangkatan tidak ditemukan.');
    }

    // Ambil pilihan dropdown maskapai & rute master untuk kebutuhan form modal
    $this->masterFlights = DB::table('flights as f')
        ->join('airlines as a', 'a.id', '=', 'f.airline_id')
        ->select(['f.id', 'f.flight_no', 'f.departure_airport', 'f.arrival_airport', 'a.name as airline_name'])
        ->get();

    $this->refreshFlights();
});

/**
 * Membuka Modal Form Logistik Penerbangan
 */
$openFlightModal = function ($id = null, $type = 'berangkat') {
    $this->form->clear();
    $this->form->departure_id = $this->departureId;
    $this->form->type = $type;

    if ($id) {
        $df = DB::table('departure_flights')->where('id', $id)->first();
        if ($df) {
            // Mapping data stdClass objek ke form state
            $this->form->departureFlightId = $df->id;
            $this->form->flight_id = $df->flight_id;
            $this->form->type = $df->type;
            $this->form->etd = Carbon::parse($df->etd)->format('Y-m-d\TH:i');
            $this->form->eta = Carbon::parse($df->eta)->format('Y-m-d\TH:i');
            $this->form->pnr_code = $df->pnr_code;
        }
    }

    $this->dispatch('open-modal', name: 'modalFlightForm');
};

/**
 * Eksekusi Simpan Data Alokasi Tiket Pesawat Rombongan via DepartureService
 */
$saveAssignedFlight = function (DepartureService $departureService) {
    $this->form->validate();

    try {
        // Eksekusi logic transaksi murni di level service
        $departureService->assignDepartureFlight($this->form->all());

        $this->form->clear();
        $this->dispatch('close-modal', name: 'modalFlightForm');
        $this->refreshFlights();

        $this->dispatch('toast', type: 'success', title: 'Logistik Penerbangan Dikunci', message: 'Alokasi kursi penerbangan grup berhasil disimpan ke dalam manifes.');
    } catch (\Exception $e) {
        $this->dispatch('toast', type: 'danger', title: 'Gagal Menyimpan', message: $e->getMessage());
    }
};

/**
 * Mencabut Alokasi Penerbangan dari Jadwal Keberangkatan
 */
$removeAssignedFlight = function ($id) {
    try {
        DB::table('departure_flights')->where('id', $id)->delete();

        $this->refreshFlights();
        $this->dispatch('toast', type: 'warning', title: 'Alokasi Dicabut', message: 'Penerbangan berhasil dikeluarkan dari manifes jadwal keberangkatan.');
    } catch (\Exception $e) {
        $this->dispatch('toast', type: 'danger', title: 'Gagal Menghapus', message: $e->getMessage());
    }
};

?>

<div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-6">
    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Manifes & Alokasi Penerbangan Grup</h3>
            <p class="text-xs text-slate-500 mt-0.5">Plot nomor PNR, jam terbang riil, dan rute pendaratan imigrasi
                jemaah.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach (['berangkat' => 'Rute Pergi (Departure Flight)', 'pulang' => 'Rute Pulang (Return Flight)'] as $typeKey => $typeLabel)
            @php
                $assigned = $this->assignedFlights->where('type', $typeKey)->first();
            @endphp

            <div
                class="border rounded-xl p-4 transition-all {{ $assigned ? 'bg-slate-50/50 border-slate-200' : 'bg-slate-50/20 border-dashed border-slate-300' }}">
                <div class="flex justify-between items-center mb-3">
                    <span
                        class="text-xs font-bold tracking-wider uppercase {{ $typeKey === 'berangkat' ? 'text-indigo-600' : 'text-emerald-600' }}">
                        {{ $typeLabel }}
                    </span>

                    @if ($assigned)
                        <div class="flex items-center gap-1">
                            <flux:button variant="ghost" size="sm" icon="pencil-square"
                                class="text-slate-400 hover:text-indigo-600 p-1"
                                wire:click="openFlightModal({{ $assigned->id }}, '{{ $typeKey }}')" />
                            <flux:button variant="ghost" size="sm" icon="trash"
                                class="text-slate-400 hover:text-rose-600 p-1"
                                wire:click="removeAssignedFlight({{ $assigned->id }})" />
                        </div>
                    @endif
                </div>

                @if ($assigned)
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="font-mono font-black text-sm text-slate-800 bg-white px-2.5 py-1 rounded-lg border border-slate-200 shadow-3xs">
                                {{ $assigned->flight_no }}
                            </div>
                            <div>
                                <span
                                    class="text-xs font-bold text-slate-900 block">{{ $assigned->airline_name }}</span>
                                <span
                                    class="text-[10px] font-mono font-bold text-indigo-600 uppercase tracking-tight bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100/40">
                                    PNR: {{ $assigned->pnr_code ?? 'BELUM SET PNR' }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-100">
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">🛫 TINGGAL
                                    LANDAS</span>
                                <span class="font-bold text-slate-700 block">{{ $assigned->departure_airport }}</span>
                                <span
                                    class="text-[11px] text-slate-500 font-medium">{{ \Carbon\Carbon::parse($assigned->etd)->format('d M Y - H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">🛬 PENDARATAN</span>
                                <span class="font-bold text-slate-700 block">{{ $assigned->arrival_airport }}</span>
                                <span
                                    class="text-[11px] text-slate-500 font-medium">{{ \Carbon\Carbon::parse($assigned->eta)->format('d M Y - H:i') }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-6 text-center">
                        <span class="text-xs text-slate-400 italic mb-3">Belum ada alokasi pesawat...</span>
                        <flux:button variant="ghost" size="sm" icon="plus"
                            class="text-indigo-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg text-xs font-semibold px-3 shadow-3xs"
                            wire:click="openFlightModal(null, '{{ $typeKey }}')">
                            Plot Penerbangan
                        </flux:button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <x-ui.modal name="modalFlightForm" title="Konfigurasi Alokasi Tiket Penerbangan" maxWidth="max-w-md">
        <x-slot:subtitle>Mengikat armada pesawat komersial, kode PNR rombongan, serta jam terbang riil
            keberangkatan.</x-slot:subtitle>

        <form wire:submit.prevent="saveAssignedFlight" class="space-y-4">
            <flux:input wire:model="form.type" label="Kategori Penerbangan" readonly
                class="bg-slate-50 font-bold uppercase pointer-events-none text-slate-500" size="sm" />

            <flux:select wire:model="form.flight_id" label="Pilih Nomor Penerbangan Master"
                placeholder="-- Pilih Rute Sah --" required size="sm">
                @foreach ($masterFlights as $mFlight)
                    <flux:select.option value="{{ $mFlight->id }}">
                        {{ $mFlight->flight_no }} | {{ $mFlight->airline_name }} ({{ $mFlight->departure_airport }} ➔
                        {{ $mFlight->arrival_airport }})
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="form.etd" type="datetime-local" label="Waktu Terbang (ETD)" required
                    size="sm" />
                <flux:input wire:model="form.eta" type="datetime-local" label="Waktu Mendarat (ETA)" required
                    size="sm" />
            </div>

            <flux:input wire:model="form.pnr_code" label="Kode Booking / PNR Grup" placeholder="E.g. SV991K"
                size="sm" class="font-mono uppercase tracking-wider" />

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <flux:button type="button" @click="$dispatch('close-modal', { name: 'modalFlightForm' })"
                    variant="ghost" size="sm" class="rounded-xl font-semibold">Batal</flux:button>
                <flux:button type="submit" variant="filled" size="sm"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-sm px-4">Kunci
                    Logistik</flux:button>
            </div>
        </form>
    </x-ui.modal>
</div>
