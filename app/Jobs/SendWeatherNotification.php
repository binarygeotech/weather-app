<?php

namespace App\Jobs;

use Illuminate\Support\Collection;
use App\Services\WeatherApiService;
use App\Services\OpenMeteoApiService;
use App\Notifications\WeatherNotification;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\WeatherNotificationSubscription;

class SendWeatherNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected object $city,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $latitude = $this->city->latitude;
        $longitude = $this->city->longitude;

        $weatherData = $this->getAverageWeatherData($latitude, $longitude);
        $subscribers = $this->getSubscribers();

        $subscribers->each(function ($subscriber) use ($weatherData) {
            if ($weatherData['uvIndex'] >= $subscriber->threshold) {
                $subscriber->user->notify(new WeatherNotification($this->city, $weatherData));
            }
        });
    }

    /**
     * Get subscribers
     *
     * @return array
     */
    private function getSubscribers(): Collection
    {
        return WeatherNotificationSubscription::with('user')
            ->where('city', $this->city->name)
            ->get();
    }

    /**
     * Get weather data
     *
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    private function getAverageWeatherData(float $latitude, float $longitude): array
    {
        $meteo_response = [];
        $weather_response = [];

        // Call openmethod to get weather data
        $meteo_response = (new OpenMeteoApiService())->callApi('', $latitude, $longitude);


        // Call weather API to get weather data
        $weather_api_key = config('services.weather.weather_api_key');

        if (isset($weather_api_key)) {
            $weather_response = (new WeatherApiService())->callApi($weather_api_key, $latitude, $longitude);
        }

        if (!empty($weather_response) && !empty($meteo_response)) {
            $meteoData = $meteo_response->getData();
            $wApiData = $weather_response->getData();

            $weatherData = [
                'uvIndex' => ($wApiData['uvIndex'] + $meteoData['uvIndex']) / 2,
                'precipitation' => ($wApiData['precipitation'] + $meteoData['precipitation']) / 2,
            ];
        } elseif (!empty($weather_response) && empty($meteo_response)) {
            $weatherData = $weather_response->getData();
        } elseif (!empty($meteo_response) && empty($weather_response)) {
            $weatherData = $meteo_response->getData();
        } else {
            $weatherData = [
                'uvIndex' => null,
                'precipitation' => null,
            ];
        }

        return $weatherData;
    }
}
