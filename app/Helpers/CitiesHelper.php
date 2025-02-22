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

    public function getCity(string $name): object
    {
        return $this->cities->where('name', $name)->first();
    }

    public function getCities(array $names): object
    {
        return $this->cities->whereIn('name', $names);
    }

    public function getCitiesByCountry(string $country): Collection
    {
        return $this->cities->where('country', $country)->flatten();
    }

    public function getCountries(): Collection
    {
        return $this->cities->pluck('country')->unique()->values()->sort()->flatten();
    }
}
