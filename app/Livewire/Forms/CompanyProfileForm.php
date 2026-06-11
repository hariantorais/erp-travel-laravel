<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Illuminate\Support\Facades\DB;

class CompanyProfileForm extends Form
{
    public ?int $profileId = null;
    public string $name = '';
    public ?string $brand_name = null;
    public string $address = '';
    public string $phone = '';
    public ?string $email = null;
    public string $siskopatuh_piu_code = '';
    public ?string $siskopatuh_user = null;
    public ?string $siskopatuh_password = null; // Plain, nanti di-encrypt di Service
    public ?string $travel_license_no = null;

    // File pakai MediaLibrary, jadi tidak masuk Form Object. Upload langsung di Volt.

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'siskopatuh_piu_code' => 'required|string|max:50',
            'siskopatuh_user' => 'nullable|string|max:255',
            'siskopatuh_password' => 'nullable|string',
            'travel_license_no' => 'nullable|string|max:255',
        ];
    }

    public function setProfile(): void
    {
        // company_profiles cuma 1 row, ambil row pertama
        $profile = DB::table('company_profiles')->first();

        if ($profile) {
            $this->profileId = $profile->id;
            $this->name = $profile->name;
            $this->brand_name = $profile->brand_name;
            $this->address = $profile->address;
            $this->phone = $profile->phone;
            $this->email = $profile->email;
            $this->siskopatuh_piu_code = $profile->siskopatuh_piu_code;
            $this->siskopatuh_user = $profile->siskopatuh_user;
            $this->travel_license_no = $profile->travel_license_no;
            // Password tidak di-load demi keamanan
        }
    }

    public function clear(): void
    {
        $this->reset(['siskopatuh_password']); // Hanya reset password saat buka modal
    }
}
