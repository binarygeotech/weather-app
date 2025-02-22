<?php

namespace App\Actions\Subscription;

use App\Models\User;

class NotifyUser
{
    public function notify(User $user, array $data)
    {
        \Log::debug($data);

        return $user;
    }
}
