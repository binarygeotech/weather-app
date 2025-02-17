<?php

use App\Helpers\CitiesHelper;
use App\Services\WeatherApiService;
use App\Services\OpenMeteoApiService;

test('can call open meteo api service', function () {
    $cities = (new CitiesHelper())->getAllCities()->random(1);

    $latitude = $cities->first()->latitude;
    $longitude = $cities->first()->longitude;

    $response = (new OpenMeteoApiService())->callApi('', $latitude, $longitude);

    expect($response->getData())->toBeArray()
        ->toHaveKey('uvIndex')
        ->toHaveKey('precipitation');
})->skip(function () {
    return ! config('services.weather.open_weather_map_api_key');
});

test('can call weather api service', function () {
    $city = (new CitiesHelper())->getAllCities()->random(1)->first();

    $api_key = config('services.weather.weather_api_key');
    $latitude = $city->latitude;
    $longitude = $city->longitude;

    $response = (new WeatherApiService())->callApi($api_key, $latitude, $longitude);

    expect($response->getData())->toBeArray()
        ->toHaveKey('uvIndex')
        ->toHaveKey('precipitation');
})->skip(function () {
    return ! config('services.weather.weather_api_key');
});
