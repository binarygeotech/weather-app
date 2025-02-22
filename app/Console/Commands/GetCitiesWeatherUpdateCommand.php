<?php

namespace App\Console\Commands;

use App\Helpers\CitiesHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use App\Jobs\SendWeatherNotification;
use App\Models\WeatherNotificationSubscription;

class GetCitiesWeatherUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-cities-weather-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command loads subscribed cities and get the weather information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscribedCities = $this->getSubscribedCities();

        $subscribedCities->each(function ($city) {
            SendWeatherNotification::dispatch($city);
        });
    }

    /**
     * Get subscribed cities
     *
     * @return array
     */
    private function getSubscribedCities(): Collection
    {
        $subscribedCitiesData = WeatherNotificationSubscription::distinct('city')->get();

        return (new CitiesHelper())->getCities($subscribedCitiesData->pluck('city')->toArray());
    }
}
