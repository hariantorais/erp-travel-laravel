<?php

namespace App\Livewire\Forms\Master;

use Livewire\Form;

class ItineraryForm extends Form
{
    public ?int $itineraryId = null;
    public ?int $package_id = null;
    public int $day_no = 1; // Disinkronkan dari day_number -> day_no
    public ?string $city = null;
    public string $title = '';
    public string $activity = ''; // Disinkronkan dari description -> activity
    public string $meals = 'B, L, D';

    public function rules(): array
    {
        return [
            'day_no' => 'required|integer|min:1',
            'city' => 'nullable|string|max:100',
            'title' => 'required|string|max:255',
            'activity' => 'required|string',
            'meals' => 'required|string|max:100',
        ];
    }

    public function fillFromData(object $itinerary): void
    {
        $this->itineraryId = $itinerary->id;
        $this->package_id = $itinerary->package_id;
        $this->day_no = (int) $itinerary->day_no;
        $this->city = $itinerary->city;
        $this->title = $itinerary->title;
        $this->activity = $itinerary->activity;
        $this->meals = $itinerary->meals;
    }

    public function clear(): void
    {
        $this->reset();
        // Berikan nilai default perusahaan untuk konsumsi jemaah
        $this->meals = 'B, L, D';
        $this->day_no = 1;
    }
}
