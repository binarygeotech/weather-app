<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use App\Contracts\WeatherServiceContract;

class WeatherApiService implements WeatherServiceContract
{
    /**
     * Raw weather data
     *
     * @var [type]
     */
    private $data;

    /**
     * Make API request to get weather data
     *
     * @param string $api_key
     * @param integer $latitude
     * @param integer $longitude
     * @return self
     */
    public function callApi(string $api_key, int $latitude, int $longitude): self
    {
        $url = "http://api.weatherapi.com/v1/current.json?key=${api_key}&q=${latitude},${longitude}";

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get($url);

        $response->throwUnlessStatus(200);

        $this->data = $response->json();

        return $this;
    }

    /**
     * Get weather data
     *
     * @return array
     */
    public function getData(): array
    {
        $uvIndex = Arr::get($this->data, 'current.uv');
        $precipitation = Arr::get($this->data, 'current.precip_mm');

        return [
            'uvIndex' => $uvIndex,
            'precipitation' => $precipitation,
        ];
    }
}
