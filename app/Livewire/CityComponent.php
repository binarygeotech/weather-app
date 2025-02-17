<?php

namespace App\Livewire;

use Livewire\Component;

class CityComponent extends Component
{
    public $cities;
    public $countries;

    public function __construct(
    ) {
        $this->cities = (new \App\Helpers\CitiesHelper())->getAllCities();
        $this->countries = (new \App\Helpers\CitiesHelper())->getCountries();
    }

    public function render()
    {
        return view('livewire.city');
    }
}
