<?php

namespace App\Livewire\Forms\Master;

use Livewire\Form;

class HotelForm extends Form
{
    public ?int $hotelId = null;
    public string $name = '';
    public string $city = 'MAKKAH'; // Default kota pertama
    public int $star_rating = 4; // Default rekomendasi bintang 4
    public ?string $address = null;
    public ?string $contact_person = null;
    public ?string $phone = null;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'city' => 'required|in:MAKKAH,MADINAH',
            'star_rating' => 'required|integer|min:3|max:5',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
        ];
    }

    public function fillFromData(object $hotel): void
    {
        $this->hotelId = $hotel->id;
        $this->name = $hotel->name;
        $this->city = $hotel->city;
        $this->star_rating = $hotel->star_rating;
        $this->address = $hotel->address;
        $this->contact_person = $hotel->contact_person;
        $this->phone = $hotel->phone;
    }

    public function clear(): void
    {
        $this->reset();
        $this->city = 'MAKKAH';
        $this->star_rating = 4;
    }
}
