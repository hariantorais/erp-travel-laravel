<?php

namespace App\Livewire\Forms\Master;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Facades\DB;

class DepartureForm extends Form
{
    public ?int $departureId = null;
    public ?string $code = null;

    #[Validate('required', message: 'Paket wajib dipilih')]
    public ?int $package_id = null;

    #[Validate('required', message: 'Cabang wajib dipilih')]
    public ?int $branch_id = null;

    #[Validate('required|date')]
    public string $departure_date = '';

    #[Validate('required|date|after_or_equal:departure_date')]
    public string $return_date = '';

    #[Validate('required|integer|min:1')]
    public ?int $quota = null;

    public array $pricings = [];

    /**
     * Aturan validasi internal untuk array pricings
     */
    public function rules(): array
    {
        return [
            'pricings.*.price' => 'required|integer|min:0',
            'pricings.*.agent_price' => 'required|integer|min:0',
            'pricings.*.child_price' => 'nullable|integer|min:0',
            'pricings.*.infant_price' => 'nullable|integer|min:0',
        ];
    }

    /**
     * KUNCI PERBAIKAN: Fungsi Validasi Kustom untuk Mensanitasi Properti Secara Langsung
     * BEST PRACTICE: Memaksa pembersihan data masker UI langsung pada state properti Form Object
     */
    public function validateAndSanitize(): array
    {
        // 1. Lakukan pembersihan paksa (Sanitasi) langsung ke properti internal $this->pricings
        if (!empty($this->pricings)) {
            foreach ($this->pricings as $i => $pricing) {
                $this->pricings[$i]['price'] = (int) preg_replace('/[^0-9]/', '', $pricing['price'] ?? 0);
                $this->pricings[$i]['agent_price'] = (int) preg_replace('/[^0-9]/', '', $pricing['agent_price'] ?? 0);
                $this->pricings[$i]['child_price'] = (int) preg_replace('/[^0-9]/', '', $pricing['child_price'] ?? 0);
                $this->pricings[$i]['infant_price'] = (int) preg_replace('/[^0-9]/', '', $pricing['infant_price'] ?? 0);
            }
        }

        // 2. Jalankan fungsi validasi asli milik Livewire setelah data properti dipastikan bersih murni
        return $this->validate();
    }

    public function setDeparture(int $id): void
    {
        $d = DB::table('departures')->where('id', $id)->first();
        if (!$d) return;

        $this->departureId = $d->id;
        $this->code = $d->code;
        $this->package_id = $d->package_id;
        $this->branch_id = $d->branch_id;
        $this->departure_date = $d->departure_date;
        $this->return_date = $d->return_date;
        $this->quota = $d->quota;

        $this->pricings = DB::table('departure_pricings')
            ->where('departure_id', $id)
            ->get(['room_type', 'price', 'agent_price', 'child_price', 'infant_price'])
            ->map(fn($p) => (array) $p)
            ->toArray();
    }

    public function clear(): void
    {
        $this->reset();

        // Default value standar operasional
        $this->departure_date = now()->addMonth()->format('Y-m-d');
        $this->return_date = now()->addMonth()->addDays(9)->format('Y-m-d');
        $this->quota = 45;

        $this->pricings = [
            ['room_type' => 'quad', 'price' => 0, 'agent_price' => 0, 'child_price' => 0, 'infant_price' => 0],
            ['room_type' => 'triple', 'price' => 0, 'agent_price' => 0, 'child_price' => 0, 'infant_price' => 0],
            ['room_type' => 'double', 'price' => 0, 'agent_price' => 0, 'child_price' => 0, 'infant_price' => 0],
            ['room_type' => 'single', 'price' => 0, 'agent_price' => 0, 'child_price' => 0, 'infant_price' => 0],
        ];
    }
}
