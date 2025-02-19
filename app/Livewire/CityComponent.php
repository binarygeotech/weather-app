<?php

namespace App\Livewire;

use Livewire\Component;

class CityComponent extends Component
{
    public $cities;
    public $countries;

    // Form fields
    public $threshold = 6;
    public $selected_cities = [];

    public function __construct(
    ) {
        $this->cities = (new \App\Helpers\CitiesHelper())->getAllCities();
        $this->countries = (new \App\Helpers\CitiesHelper())->getCountries();
    }

    public function subscribe()
    {
        dd($this->selected_cities);

        session()->flash('status', 'Subscription successfully updated.');

        return $this->redirect('/weather-companion');
    }

    public function render()
    {
        return view('livewire.city');
    }
}
