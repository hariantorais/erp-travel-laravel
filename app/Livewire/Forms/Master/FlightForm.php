<?php

namespace App\Livewire\Forms\Master;

use Livewire\Form;
use Illuminate\Support\Facades\DB;

class FlightForm extends Form
{
    public ?int $flightId = null;
    public string $airline_id = '';
    public string $flight_no = '';
    public string $departure_airport = '';
    public string $arrival_airport = '';

    /**
     * Aturan Validasi Master Flight
     */
    public function rules(): array
    {
        return [
            'airline_id'        => 'required|integer',
            'flight_no'         => 'required|string|max:20',
            'departure_airport' => 'required|string|max:3|min:3',
            'arrival_airport'   => 'required|string|max:3|min:3',
        ];
    }

    /**
     * Memetakan data dari database ke properti form saat mode Edit
     */
    public function setFlight(int $id): void
    {
        $flight = DB::table('flights')->where('id', $id)->first();

        if ($flight) {
            $this->flightId          = $flight->id;
            $this->airline_id        = (string) $flight->airline_id;
            $this->flight_no         = $flight->flight_no;
            $this->departure_airport = $flight->departure_airport;
            $this->arrival_airport   = $flight->arrival_airport;
        }
    }

    /**
     * Bersihkan State Form (Mode Tambah Baru)
     */
    public function clear(): void
    {
        $this->flightId          = null;
        $this->airline_id        = '';
        $this->flight_no         = '';
        $this->departure_airport = '';
        $this->arrival_airport   = '';
    }
}
