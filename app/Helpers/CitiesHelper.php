<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class CitiesHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->cities = config('cities');
    }

    public function getAllCities(): Collection
    {
        return $this->cities;
    }

    public function getCitiesByCountry(string $country): Collection
    {
        return $this->cities->where('country', $country);
    }

    public function getCountries(): Collection
    {
        return $this->cities->pluck('country')->unique()->values()->sort()->flatten();
    }
}
