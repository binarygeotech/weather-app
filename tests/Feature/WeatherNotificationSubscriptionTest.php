<?php

use App\Models\User;
use Livewire\Livewire;
use App\Helpers\CitiesHelper;
use App\Livewire\WeatherCompanionComponent;

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
})->only();

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
})->only();

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
})->only();
