<?php

namespace App\Contracts;

interface WeatherServiceContract
{
    /**
     * Make API request to get weather data
     *
     * @param string $api_key
     * @param integer $latitude
     * @param integer $longitude
     * @return self
     */
    public function callApi(string $api_key, int $latitude, int $longitude);

    /**
     * Get weather data
     *
     * @return array
     */
    public function getData();
}
