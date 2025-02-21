<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Helpers\CitiesHelper;
use App\Actions\Subscription\SubscribeUser;

class WeatherCompanionComponent extends Component
{
    public $cities;
    public $countries;

    /**
     * Current weather subscription data
     *
     * @var array
     */
    public $state = [
        'threshold' => 6,
        'selectedCities' => [],
    ];

    /**
     * On Component mount
     *
     * @return void
     */
    public function mount(
        CitiesHelper $citiesHelper,
    ) {
        $this->cities = $citiesHelper->getAllCities();
        $this->countries = $citiesHelper->getCountries();

        $subcriptions =  $this->currentUser()->weatherNotificationSubscriptions;
        $threshold = $subcriptions->first()?->threshold;

        $subscribedCities = $subcriptions->transform(function ($subscription) use ($citiesHelper) {
            return $citiesHelper->getCity($subscription->city);
        })->flatten()->toArray();

        $this->state = [
            'threshold' => $threshold ?? 6,
            'selectedCities' => $subscribedCities,
        ];
    }

    public function subscribe(
        SubscribeUser $subscribeUser,
    ) {
        $this->state['selectedCities'] = is_array($this->state['selectedCities']) ?
                $this->state['selectedCities'] :
                json_decode($this->state['selectedCities']);

        $this->validate([
            'state.selectedCities' => 'sometimes|array',
            'state.threshold' => 'required|integer|min:0',
        ], [
            'state.threshold.required' => 'Please enter a threshold value.',
            'state.threshold.integer' => 'Threshold value must be an integer.',
            'state.threshold.min' => 'Threshold value must be at least 0.',
            'state.selectedCities.array' => 'Selected city is not valid.',
        ]);

        $subscribeUser->subscribe(
            $this->currentUser(),
            $this->state['selectedCities'],
            $this->state['threshold'],
        );

        session()->flash('message', 'Subscription updated successfully!');

        $this->dispatch('subscriptionUpdated');
    }

    public function reloadState(CitiesHelper $citiesHelper)
    {
        $subcriptions =  $this->currentUser()->weatherNotificationSubscriptions;
        $threshold = $subcriptions->first()?->threshold;

        $subscribedCities = $subcriptions->transform(function ($subscription) use ($citiesHelper) {
            return $citiesHelper->getCity($subscription->city);
        })->flatten()->toArray();

        $this->state = [
            'threshold' => $threshold,
            'selectedCities' => $subscribedCities,
        ];
    }

    private function currentUser(): User | null
    {
        return auth()->user();
    }

    public function render()
    {
        return view('livewire.weather_companion');
    }
}
