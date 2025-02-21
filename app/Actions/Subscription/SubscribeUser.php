<?php

namespace App\Actions\Subscription;

use App\Models\User;

class SubscribeUser
{
    public function subscribe(User $user, array $cities, int $threshold)
    {
        $user->weatherNotificationSubscriptions()
            ->delete();

        foreach ($cities as $city) {
            $user->weatherNotificationSubscriptions()->create([
                'city' => $city->name,
                'threshold' => $threshold,
            ]);
        }

        $user->refresh();

        return $user;
    }
}
