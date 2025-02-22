<?php

use App\Models\User;
use Livewire\Livewire;
use App\Helpers\CitiesHelper;
use Illuminate\Support\Collection;
use App\Notifications\WeatherNotification;
use App\Livewire\WeatherCompanionComponent;
use Illuminate\Support\Facades\Notification;
use App\Models\WeatherNotificationSubscription;

test('user can load subscribed weather notification', function () {
    $cities = (new CitiesHelper())->getAllCities()->random(5);
    $user = User::factory()->create();

    $this->actingAs($user);
    $threshold = round(rand(1, 10), 1);

    $state = [
            'threshold' => $threshold,
            'selectedCities' => $cities->random(3),
    ];

    $component = Livewire::test(WeatherCompanionComponent::class, ['state' => $state])
        ->set('state', $state)
        ->assertSet('state', $state);

    expect($component->state['threshold'])->toEqual($threshold);
    expect(count($component->state['selectedCities']))->toEqual(3);
});

test('user can subscribe to weather notification', function () {
    $cities = (new CitiesHelper())->getAllCities()->random(5);
    $user = User::factory()->create();

    $this->actingAs($user);
    $threshold = round(rand(1, 10), 1);
    $state = [
        'threshold' => $threshold,
        'selectedCities' => $cities->random(3),
    ];

    Livewire::test(WeatherCompanionComponent::class)
        ->set('state', $state)
        ->call('subscribe')
        ->assertStatus(200);

    $this->assertDatabaseHas('weather_notification_subscriptions', [
        'user_id' => $user->id,
        'threshold' => $threshold,
        'city' => $state['selectedCities'][0]->name,
    ]);
});

test('user can unsubscribe to weather notification', function () {
    $cities = (new CitiesHelper())->getAllCities()->random(5);
    $user = User::factory()->create();

    $this->actingAs($user);
    $threshold = round(rand(1, 10), 1);
    $state = [
        'threshold' => $threshold,
        'selectedCities' => $cities->random(3),
    ];

    Livewire::test(WeatherCompanionComponent::class)
        ->set('state', $state)
        ->call('subscribe')
        ->assertStatus(200);

    $this->assertDatabaseHas('weather_notification_subscriptions', [
        'user_id' => $user->id,
        'threshold' => $threshold,
        'city' => $state['selectedCities'][0]->name,
    ]);

    Livewire::test(WeatherCompanionComponent::class)
        ->set('state', [
            'threshold' => 1,
            'selectedCities' => [],
        ])
        ->call('subscribe')
        ->assertStatus(200);

    $this->assertDatabaseMissing('weather_notification_subscriptions', [
        'user_id' => $user->id,
        'threshold' => $threshold,
        'city' => $state['selectedCities'][0]->name,
    ]);
});

test('user can be notified to weather', function () {
    Notification::fake();

    $citiesCount = 2;
    $cities = (new CitiesHelper())->getAllCities()->random(5);
    $user = User::factory()->create();

    Collection::macro('newWeatherData', function (int $count) {
        $coll = [];
        for ($i = 0; $i < $count; $i++) {
            $coll[] = [
                'precipitation' => round(rand(0, 5), 1),
                'uvIndex' => round(rand(0, 15), 1),
            ];
        }

        $this->items = $coll;
        return $this;
    });

    $weatherApi = Mockery::mock('WeatherAPI');
    $weatherApi->shouldReceive('callApi')
        ->andReturn($weatherApi)
        ->shouldReceive('getData')
        ->times($citiesCount)
        ->andReturn(collect()->newWeatherData(1));

    $this->actingAs($user);
    $threshold = round(rand(1, 10), 1);
    $state = [
        'threshold' => $threshold,
        'selectedCities' => $cities->random($citiesCount),
    ];

    Livewire::test(WeatherCompanionComponent::class)
        ->set('state', $state)
        ->call('subscribe')
        ->assertStatus(200);

    $this->assertDatabaseHas('weather_notification_subscriptions', [
        'user_id' => $user->id,
        'threshold' => $threshold,
        'city' => $state['selectedCities'][0]->name,
    ]);

    $cities->each(function ($city) use ($weatherApi) {
        $subscribers = WeatherNotificationSubscription::with('user')
            ->where('city', $city->name)
            ->get();

        $subscribers->each(function ($subscriber) use ($weatherApi, $city) {
            $weatherData = $weatherApi->callApi()->getData()->first();

            if ($weatherData['uvIndex'] >= $subscriber->threshold) {
                $subscriber->user->notify(new WeatherNotification($city, $weatherData));

                Notification::assertSentTo($subscriber->user, WeatherNotification::class);
            } else {
                Notification::assertNotSentTo($subscriber->user, WeatherNotification::class);
            }
        });
    });
});
