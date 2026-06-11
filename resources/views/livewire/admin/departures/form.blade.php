<?php

use function Livewire\Volt\{form, on, with};
use App\Services\Master\DepartureService;
use App\Livewire\Forms\Master\DepartureForm;
use Illuminate\Support\Facades\DB;

// 1. Injeksi Form Object Utility Class
form(DepartureForm::class);

// 2. Ambil data master acuan untuk komponen dropdown select secara efisien
with(
    fn() => [
        'packages' => DB::table('packages')->whereNull('deleted_at')->where('is_active', true)->orderBy('name', 'asc')->get(),
        'branches' => DB::table('branches')->whereNull('deleted_at')->orderBy('name', 'asc')->get(),
    ],
);

// 3. Event Listener: Menangkap parameter array asosiatif id dari tabel secara aman
on([
    'open-modal' => function ($name, $id = null) {
        // Pastikan target event ditujukan murni untuk modal ini
        if ($name === 'modalForm') {
            if ($id) {
                $this->form->setDeparture((int) $id);
            } else {
                $this->form->clear();
            }
        }
    },
]);

/**
 * Aksi Eksekusi Penyimpanan Operasional Keberangkatan (Single Parameter Pattern)
 */
$saveDeparture = function (DepartureService $service) {
    // KUNCI SUKSES: Panggil fungsi sanitasi kustom baru kita
    $this->form->validateAndSanitize();

    // Sekarang, $payload dipastikan berisi array integer bersih bebas titik!
    $payload = $this->form->all();

    try {
        if ($this->form->departureId) {
            $service->updateDeparture($this->form->departureId, $payload);
            $this->dispatch('toast', type: 'success', title: 'Berhasil Diperbarui', message: 'Alokasi logistik dan komponen harga berhasil diubah.');
        } else {
            $service->createDeparture($payload);
            $this->dispatch('toast', type: 'success', title: 'Berhasil Dibuat', message: 'Slot jadwal keberangkatan baru sukses diaktifkan.');
        }

        $this->form->clear();
        $this->dispatch('close-modal', name: 'modalForm');
        $this->dispatch('departure-updated');
    } catch (\Exception $e) {
        $this->dispatch('toast', type: 'danger', title: 'Operasi Gagal', message: $e->getMessage());
    }
};

?>

<form wire:submit.prevent="saveDeparture" class="space-y-5">
    <div class="grid grid-cols-2 gap-4">
        <flux:select wire:model="form.package_id" label="Program Paket Acuan (Brosur)"
            :disabled="$this->form->departureId !== null" required size="sm"
            class="{{ $this->form->departureId ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : '' }}">
            <option value="">-- Pilih Paket Program --</option>
            @foreach ($packages as $p)
                <option value="{{ $p->id }}">
                    {{ "$p->name ($p->code)" }}
                </option>
            @endforeach
        </flux:select>

        <flux:select wire:model="form.branch_id" label="Kantor Cabang Pelaksana" required size="sm">
            <option value="">-- Pilih Cabang Kontrol --</option>
            @foreach ($branches as $b)
                <option value="{{ $b->id }}">
                    {{ "$b->name ($b->code)" }}
                </option>
            @endforeach
        </flux:select>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <flux:input type="date" wire:model="form.departure_date" label="Tanggal Keberangkatan" required
            size="sm" />
        <flux:input type="date" wire:model="form.return_date" label="Tanggal Kepulangan" required size="sm" />
        <flux:input type="number" wire:model="form.quota" min="1" label="Batas Total Kuota (Pax)" required
            size="sm" />
    </div>

    <flux:separator class="my-1" />

    <div class="space-y-3">
        <div>
            <h4 class="text-sm font-bold text-slate-800">Komponen Matriks Keuangan Kamar</h4>
            <p class="text-[11px] text-slate-400">Tentukan penyesuaian nilai tarif dasar berdasarkan skema pilihan
                akomodasi kamar jemaah.</p>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50/50">
            <table class="w-full text-left text-xs ">
                <thead>
                    <tr
                        class="bg-slate-100/80 border-b border-slate-200 text-slate-600 font-bold tracking-wide uppercase text-[10px]">
                        <th class="p-3 w-24">Tipe</th>
                        <th class="p-3">Harga Jual (IDR)</th>
                        <th class="p-3">Harga Agen (IDR)</th>
                        <th class="p-3">Anak (IDR)</th>
                        <th class="p-3">Infant (IDR)</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 bg-white">
                    @foreach ($form->pricings as $i => $p)
                        <tr wire:key="pricing-row-{{ $i }}" class="hover:bg-slate-50/50 transition-colors">

                            <td class="p-2.5 font-mono font-bold text-slate-700 align-middle">
                                <span
                                    class="bg-slate-100 text-slate-800 px-2 py-1 rounded-md tracking-wider block text-center text-[11px]">
                                    {{ strtoupper($p['room_type']) }}
                                </span>
                            </td>

                            <td class="p-2.5">
                                <flux:input wire:model="form.pricings.{{ $i }}.price" x-data
                                    x-mask:dynamic="$money($input, '.')" type="text" prefix="Rp" placeholder="0"
                                    size="sm" class="rounded-lg shadow-xs" />
                            </td>

                            <td class="p-2.5">
                                <flux:input wire:model="form.pricings.{{ $i }}.agent_price" x-data
                                    x-mask:dynamic="$money($input, '.')" type="text" prefix="Rp" placeholder="0"
                                    size="sm" class="rounded-lg shadow-xs" />
                            </td>

                            <td class="p-2.5">
                                <flux:input wire:model="form.pricings.{{ $i }}.child_price" x-data
                                    x-mask:dynamic="$money($input, '.')" type="text" prefix="Rp" placeholder="0"
                                    size="sm" class="rounded-lg shadow-xs" />
                            </td>

                            <td class="p-2.5">
                                <flux:input wire:model="form.pricings.{{ $i }}.infant_price" x-data
                                    x-mask:dynamic="$money($input, '.')" type="text" prefix="Rp" placeholder="0"
                                    size="sm" class="rounded-lg shadow-xs" />
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <x-ui.modal-footer target="modalForm" :submitLabel="$this->form->departureId ? 'Simpan Perubahan Jadwal' : 'Buka Slot Keberangkatan'" />
</form>
