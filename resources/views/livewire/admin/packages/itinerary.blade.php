<?php

use function Livewire\Volt\{state, title, layout, mount, form, on};
use App\Services\Master\PackageService;
use App\Livewire\Forms\Master\ItineraryForm;
use App\Models\Package;

title('Detail Rencana Perjalanan (Itinerary) - Multi-Tenant ERP');
layout('components.layouts.app');

// 1. Injeksi Form Object Itinerary Utility Class
form(ItineraryForm::class);

// 2. Deklarasi State Komponen Volt
state([
    'packageId' => null,
    'package' => null,
    'itineraries' => [],
]);

/**
 * Mengambil data itinerary dan memetakannya langsung berdasarkan indeks nomor hari database (day_no)
 */
$refreshTimeline = function (PackageService $packageService) {
    $rawItineraries = $packageService->getItinerariesByPackage($this->packageId);

    $this->itineraries = [];

    // KUNCI SINKRONISASI: Susun struktur data berdasarkan kolom riil 'day_no' database
    foreach ($rawItineraries as $item) {
        $this->itineraries[$item->day_no] = $item;
    }
};

/**
 * Siklus Awal Hidup Komponen Livewire Volt
 */
mount(function ($slug = null, PackageService $packageService) {
    $this->package = Package::with('airline')->firstWhere('slug', $slug);

    if (!$this->package) {
        return $this->redirect(route('admin.packages.index'), navigate: true);
    }

    $this->packageId = $this->package->id;
    $this->refreshTimeline($packageService);
});

/**
 * Menangkap sinyal global open-modal untuk persiapan data form anak
 * KUNCI PERBAIKAN: Menangkap parameter sebagai array $data tunggal agar sinkron dengan Livewire v3
 */
/**
 * Menangkap sinyal global open-modal untuk persiapan data form anak
 * KUNCI PERBAIKAN: Berikan default value array kosong (= []) agar tidak terbaca sebagai dependensi wajib
 */
on([
    'open-modal' => function ($id = null, $day_no = null) {
        if ($id) {
            $this->form->clear();

            $packageService = app(PackageService::class);

            $itinerary = $packageService->findItinerary((int) $id);
            if ($itinerary) {
                $this->form->fillFromData($itinerary);
            }
        } else {
            $this->form->clear();

            if (!empty($day_no)) {
                // Skenario B: Mode Tambah Baru
                $this->form->day_no = (int) $day_no;
            }
        }
    },
]);

/**
 * Menyimpan data agenda baik operasi Baru (Insert) maupun Ubah (Update) via Modal (Single Parameter Pattern)
 */
$saveItinerary = function (PackageService $packageService) {
    $this->form->validate();

    // Ambil payload array utuh yang strukturnya sudah sinkron 100% dengan database
    $payload = $this->form->all();

    try {
        if ($this->form->itineraryId) {
            $packageService->updateItinerary($this->form->itineraryId, $payload);
            $this->dispatch('toast', type: 'success', title: 'Agenda Diperbarui', message: 'Rencana aktivitas hari ke-' . $this->form->day_no . ' sukses diubah.');
        } else {
            $packageService->createItinerary($this->packageId, $payload);
            $this->dispatch('toast', type: 'success', title: 'Agenda Ditambahkan', message: 'Susunan acara hari ke-' . $this->form->day_no . ' berhasil dikunci.');
        }

        $this->form->clear();
        $this->dispatch('close-modal', name: 'modalForm');
        $this->refreshTimeline($packageService);
    } catch (\Exception $e) {
        $this->dispatch('toast', type: 'danger', title: 'Operasi Gagal', message: $e->getMessage());
    }
};

/**
 * Menghapus baris agenda tertentu dari database murni
 */
$deleteItinerary = function (int $id, PackageService $packageService) {
    try {
        $packageService->deleteItinerary($id);
        $this->dispatch('toast', type: 'warning', title: 'Agenda Dikosongkan', message: 'Baris rencana perjalanan berhasil dikeluarkan dari paket.');
        $this->refreshTimeline($packageService);
    } catch (\Exception $e) {
        $this->dispatch('toast', type: 'danger', title: 'Gagal Menghapus', message: $e->getMessage());
    }
};

?>

<div class="space-y-6 max-w-4xl mx-auto">

    <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
        <a href="{{ route('admin.packages.index') }}" wire:navigate class="hover:text-indigo-600 transition-colors">Master
            Paket</a>
        <flux:icon name="chevron-right" variant="micro" class="w-3 h-3" />
        <span class="text-slate-600">Rencana Perjalanan (Itinerary)</span>
    </div>

    <div
        class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span
                class="font-mono font-bold text-xs text-indigo-600 uppercase tracking-tight block mb-0.5">{{ $package->code }}</span>
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">{{ $package->name }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Durasi Program: <span class="font-bold text-slate-700">{{ $package->duration_days }} Hari</span>
                <span class="mx-1.5 text-slate-300">|</span>
                Maskapai Penerbangan: <span class="font-bold text-slate-700">{{ $package->airline->name ?? '-' }}</span>
            </p>
        </div>

        <flux:button href="{{ route('admin.packages.index') }}" wire:navigate variant="ghost" icon="arrow-left"
            size="sm"
            class="rounded-xl font-semibold text-slate-600 border border-slate-200 bg-slate-50/50 hover:bg-white shrink-0">
            Kembali Ke Master
        </flux:button>
    </div>

    <div class="relative border-l-2 border-slate-200 ml-4 space-y-4">

        @for ($day = 1; $day <= $package->duration_days; $day++)
            @php
                $agenda = $this->itineraries[$day] ?? null;
            @endphp

            <div class="relative pl-6 group">
                <div
                    class="absolute -left-[7px] top-1.5 w-3 h-3 rounded-full border-2 border-white shadow-xs ring-4 ring-slate-100 transition-all group-hover:scale-125 {{ $agenda ? 'bg-indigo-600' : 'bg-slate-300' }}">
                </div>

                @if ($agenda)
                    <div
                        class="bg-white border border-slate-100 rounded-xl p-4 shadow-xs hover:border-slate-200 transition-all flex justify-between items-start gap-4">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span
                                    class="bg-indigo-50 text-indigo-700 font-mono font-black text-[10px] px-2 py-0.5 rounded border border-indigo-100/60 shrink-0">HARI
                                    {{ $day }}</span>
                                @if ($agenda->city)
                                    <span
                                        class="bg-slate-100 text-slate-700 font-bold text-[10px] px-1.5 py-0.5 rounded shrink-0">📍
                                        {{ strtoupper($agenda->city) }}</span>
                                @endif
                                <h3 class="text-sm font-bold text-slate-900 tracking-tight">{{ $agenda->title }}</h3>

                                @if ($agenda->meals)
                                    <span
                                        class="text-[10px] text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100/60 font-semibold shrink-0">🍴
                                        Layanan Makan: {{ $agenda->meals }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 font-medium leading-relaxed whitespace-pre-line">
                                {{ $agenda->activity }}
                            </p>
                        </div>

                        <div
                            class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                            <flux:button
                                x-on:click="$dispatch('open-modal', { name: 'modalForm', id: {{ $agenda->id }}, day_no: {{ $day }} })"
                                variant="ghost" size="sm" icon="pencil-square"
                                class="text-slate-400 hover:text-indigo-600 p-1.5" title="Ubah Agenda" />
                            <flux:button
                                x-on:click="$dispatch('open-confirm', { name: 'confirmDeleteItinerary', action: 'deleteItinerary', payload: {{ $agenda->id }} })"
                                variant="ghost" size="sm" icon="trash"
                                class="text-slate-400 hover:text-rose-600 p-1.5" title="Kosongkan Agenda Hari Ini" />
                        </div>
                    </div>
                @else
                    <div
                        class="bg-slate-50/50 border border-dashed border-slate-200 rounded-xl p-3 flex justify-between items-center gap-4 transition-all hover:bg-slate-50 hover:border-slate-300">
                        <div class="flex items-center gap-3">
                            <span
                                class="bg-slate-200 text-slate-500 font-mono font-bold text-[10px] px-2 py-0.5 rounded border border-slate-300/40 shrink-0">HARI
                                {{ $day }}</span>
                            <span class="text-xs text-slate-400 font-medium italic">Belum ada rincian agenda program
                                perjalanan...</span>
                        </div>

                        <flux:button
                            x-on:click="$dispatch('open-modal', { name: 'modalForm', day_no: '{{ $day }}'})"
                            variant="ghost" size="sm" icon="plus"
                            class="text-slate-400 hover:text-indigo-600 hover:bg-white border border-transparent hover:border-slate-200 rounded-lg text-[11px] font-semibold tracking-tight shadow-none px-3">
                            Tambah Agenda
                        </flux:button>
                    </div>
                @endif
            </div>
        @endfor
    </div>

    <x-ui.modal name="modalForm" title="Tata Kelola Agenda Perjalanan" maxWidth="max-w-lg">
        <x-slot:subtitle>Masukkan rincian kegiatan ibadah, lokasi ziarah, rute transportasi, serta katering konsumsi
            jemaah.</x-slot:subtitle>

        <form wire:submit.prevent="saveItinerary" class="space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1">
                    <flux:input wire:model="form.day_no" type="number" label="Hari Ke-" readonly
                        class="bg-slate-50 text-slate-700 font-bold select-none pointer-events-none" size="sm" />
                </div>
                <div class="col-span-2">
                    <flux:input wire:model="form.meals" label="Katering Makan (Meals)"
                        placeholder="E.g. B, L, D (Breakfast, Lunch, Dinner)" required size="sm" />
                </div>
            </div>

            <flux:input wire:model="form.city" label="Kota Lokasi Aktivitas (Opsional)"
                placeholder="E.g. Makkah, Madinah, Jeddah, Istanbul" size="sm" />

            <flux:input wire:model="form.title" label="Judul Aktivitas / Agenda Utama"
                placeholder="E.g. Manasik Umroh Mandiri & Mengambil Miqat di Dzulhulaifah" required size="sm" />

            <flux:textarea wire:model="form.activity" label="Deskripsi Detail Alur Perjalanan & Estimasi Jam"
                placeholder="Tulis instruksi berkumpul di lobby, jadwal bus pembawa jemaah, kunjungan Raudah, dll..."
                rows="5" required />

            <x-ui.modal-footer target="modalForm" />
        </form>
    </x-ui.modal>

    <x-ui.confirm-modal name="confirmDeleteItinerary" title="Kosongkan Agenda Harian" variant="danger">
        Seluruh teks judul, detail agenda, dan data katering untuk hari terpilih ini akan dihapus permanen dari produk
        paket travel. Apakah Anda yakin?
    </x-ui.confirm-modal>
</div>
