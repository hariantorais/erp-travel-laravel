<?php

use function Livewire\Volt\{form, on, with};
use App\Services\Master\PackageService;
use App\Livewire\Forms\Master\PackageForm;
use Illuminate\Support\Facades\DB;

// 1. Injeksi Package Form Utility Class ke dalam Volt State
form(PackageForm::class);

/**
 * Menyediakan data referensi logistik eksternal secara dinamis untuk komponen select form
 */
with(
    fn() => [
        'airlines' => DB::table('airlines')->whereNull('deleted_at')->orderBy('name', 'asc')->get(),
        'hotelsMadinah' => DB::table('hotels')->where('city', 'madinah')->where('is_active', true)->whereNull('deleted_at')->orderBy('name', 'asc')->get(),
        'hotelsMakkah' => DB::table('hotels')->where('city', 'makkah')->where('is_active', true)->whereNull('deleted_at')->orderBy('name', 'asc')->get(),
    ],
);

// 2. Event Listener: Menangkap data ID saat modal dipicu terbuka
on([
    'open-modal' => function ($name, $id = null) {
        if ($name === 'modalPackageForm') {
            if ($id) {
                $packageService = app(PackageService::class);
                $package = $packageService->findPackage($id);
                if ($package) {
                    $this->form->fillFromData($package);
                }
            } else {
                $this->form->clear();
            }
        }
    },
]);

/**
 * Aksi eksekusi penyimpanan data brosur induk paket
 */
$savePackage = function (PackageService $packageService) {
    // Jalankan aturan validasi yang sudah disesuaikan di PackageForm
    $validatedData = $this->form->validate();

    // Pastikan casting tipe data manual untuk checkbox is_featured
    $validatedData['is_featured'] = (bool) $this->form->is_featured;

    // Eksekusi data template via Service Layer
    if ($this->form->packageId) {
        $packageService->updatePackage($this->form->packageId, $validatedData);
        $this->dispatch('toast', type: 'success', title: 'Template Diperbarui', message: 'Spesifikasi master paket ' . $this->form->name . ' sukses diubah.');
    } else {
        $packageService->createPackage($validatedData);
        $this->dispatch('toast', type: 'success', title: 'Template Ditambahkan', message: 'Cetak biru program paket umroh baru berhasil disimpan ke sistem master.');
    }

    $this->form->clear();

    // Tutup jendela modal reaktif dan segarkan baris tabel list utama
    $this->dispatch('close-modal', name: 'modalPackageForm');
    $this->dispatch('package-updated');
};

?>

<form wire:submit.prevent="savePackage" class="space-y-5">

    <div class="space-y-3">
        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Paket</h4>

        <div class="grid grid-cols-4 gap-4">
            <div class="col-span-1">
                <flux:input wire:model="form.code" label="Kode SKU Paket" :placeholder="$this->form->packageId ? '' : '-'"
                    disabled readonly size="sm"
                    class="bg-slate-50 font-mono font-bold tracking-wider text-slate-500" />
            </div>
            <div class="col-span-3">
                <flux:input wire:model="form.name" label="Nama Program Brosur"
                    placeholder="E.g. Umroh Reguler Istiqomah 9 Hari" required size="sm" />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <flux:select wire:model="form.type" label="Jenis Kategori" size="sm" required>
                    <option value="umroh">Umroh Program</option>
                    <option value="haji">Haji Khusus</option>
                    <option value="tour">Halal Holiday Tour</option>
                </flux:select>
            </div>
            <div>
                <flux:input wire:model="form.duration_days" type="number" min="1" label="Durasi Standar (Hari)"
                    required size="sm" />
            </div>
            <div>
                <flux:input wire:model="form.estimated_schedule" label="Estimasi Jadwal Brosur"
                    placeholder="E.g. Setiap Minggu Ke-3" size="sm" />
            </div>
        </div>
    </div>

    <flux:separator class="my-1" />

    <div class="space-y-3">
        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rekomendasi Acuan Logistik Saudi</h4>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <flux:select wire:model="form.airline_id" label="Maskapai" size="sm">
                    <option value="">-- Pilih --</option>
                    @foreach ($airlines as $airline)
                        <option value="{{ $airline->id }}">{{ $airline->name }} ({{ $airline->code }})</option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <flux:select wire:model="form.hotel_madinah_id" label="Hotel Madinah" size="sm">
                    <option value="">-- Pilih --</option>
                    @foreach ($hotelsMadinah as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }} [★{{ $hotel->stars ?? '3' }}]</option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <flux:select wire:model="form.hotel_makkah_id" label="Hotel Mekkah" size="sm">
                    <option value="">-- Pilih --</option>
                    @foreach ($hotelsMakkah as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }} [★{{ $hotel->stars ?? '3' }}]</option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </div>

    <flux:separator class="my-1" />

    <div class="space-y-4">
        <div class="grid grid-cols-3 gap-4 items-end">
            <div class="col-span-2">
                <flux:input wire:model="form.itinerary_summary" label="Rute Perjalanan"
                    placeholder="E.g. Jakarta - JED - Madinah - Makkah - CGK" size="sm" />
            </div>
            <div class="col-span-1 pb-1.5 flex justify-start pl-2">
                <flux:checkbox wire:model="form.is_featured" label="Tampilkan di Highlight Beranda" size="sm"
                    class="font-semibold text-slate-700" />
            </div>
        </div>

        <div>
            <flux:textarea wire:model="form.description" label="Deskripsi"
                placeholder="Tuliskan syarat, ketentuan, cakupan harga, maupun informasi penting lainnya..."
                rows="4" />
        </div>
    </div>

    <x-ui.modal-footer target="modalPackageForm" />
</form>
