<?php

namespace App\Livewire\Forms\Master; // ⚠️ Pastikan nama folder/namespace ini tepat

use Livewire\Form; // ◄ KUNCI UTAMA: Wajib mengimpor class ini!

class DepartureFlightForm extends Form // ◄ KUNCI UTAMA: Wajib extends Form
{
    public ?int $departureFlightId = null;
    public ?int $departure_id = null;
    public ?int $flight_id = null;
    public string $type = 'berangkat';
    public ?string $etd = null;
    public ?string $eta = null;
    public ?string $pnr_code = null;

    /**
     * Rule Validasi sesuai PRD M2.3
     */
    public function rules(): array
    {
        return [
            'departure_id' => 'required|integer',
            'flight_id'    => 'required|integer',
            'type'         => 'required|in:berangkat,pulang',
            'etd'          => 'required',
            'eta'          => 'required',
            'pnr_code'     => 'nullable|string|max:50',
        ];
    }

    /**
     * Bersihkan Form State saat modal ditutup
     */
    public function clear(): void
    {
        $this->departureFlightId = null;
        $this->flight_id = null;
        $this->etd = null;
        $this->eta = null;
        $this->pnr_code = null;
    }
}
