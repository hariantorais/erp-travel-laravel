<?php

namespace App\Livewire\Forms\Master;

use Livewire\Form;
use Illuminate\Support\Facades\DB;

class AirlineForm extends Form
{
    public ?int $airlineId = null;
    public string $name = '';
    public string $code = '';
    public string $logo_url = '';

    // Properti baru untuk menampung berkas upload fisik temporer
    public $logo;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airlines,code,' . $this->airlineId,
            // Validasi file logo: Maksimal 2MB, format gambar murni
            'logo' => 'nullable|image|max:2048',
        ];
    }

    public function setAirline(int $id): void
    {
        $airline = DB::table('airlines')->where('id', $id)->whereNull('deleted_at')->first();

        if ($airline) {
            $this->airlineId = $airline->id;
            $this->name      = $airline->name;
            $this->code      = $airline->code;
            $this->logo_url  = $airline->logo_url ?? '';
        }
    }

    public function clear(): void
    {
        $this->airlineId = null;
        $this->name      = '';
        $this->code      = '';
        $this->logo_url  = '';
        $this->logo      = null; // Reset file input
    }
}
