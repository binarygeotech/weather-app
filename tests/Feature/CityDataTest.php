<?php

test('cities data exists', function () {
    $cities = config('cities');

    expect($cities)->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($cities->count())->toBeGreaterThan(0);
});

test('cities data has countries', function () {
    $citiesHelper = new \App\Helpers\CitiesHelper();

    expect($citiesHelper->getCountries())->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($citiesHelper->getCountries()->count())->toBeGreaterThan(0);
    expect($citiesHelper->getCountries()->where(
        function ($val) {
            return $val === 'United States';
        }
    )->count())->toBeGreaterThan(0);
});

test('can get cities by country', function () {
    $citiesHelper = new \App\Helpers\CitiesHelper();

    expect($citiesHelper->getCitiesByCountry('United States'))->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($citiesHelper->getCitiesByCountry('United States')->count())->toBeGreaterThan(0);
});
