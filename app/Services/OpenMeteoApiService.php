<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use App\Contracts\WeatherServiceContract;

class OpenMeteoApiService implements WeatherServiceContract
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
        $url = "https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&daily=uv_index_max,precipitation_sum&timezone=auto";

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
        $uvIndex = Arr::get($this->data, 'daily.uv_index_max.0');
        $precipitation = Arr::get($this->data, 'daily.precipitation_sum.0');

        return [
            'uvIndex' => $uvIndex,
            'precipitation' => $precipitation,
        ];
    }
}
