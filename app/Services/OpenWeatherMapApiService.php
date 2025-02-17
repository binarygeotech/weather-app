<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Contracts\WeatherServiceContract;

class OpenWeatherMapApiService implements WeatherServiceContract
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
    public function callApi(string $api_key, int $latitude, int $longitude)
    {
        $url = "https://api.openweathermap.org/data/2.5/onecall?lat={$latitude}&lon={$longitude}&exclude=hourly,daily&appid={$api_key}";

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
    public function getData()
    {
        $uvIndex = Arr::get($this->data, 'current.uvi');
        $precipitation = Arr::get($this->data, 'current.rain.1h', 0);

        return [
            'uvIndex' => $uvIndex,
            'precipitation' => $precipitation,
        ];
    }
}
