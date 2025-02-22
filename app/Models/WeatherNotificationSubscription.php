<?php

namespace App\Models;

use App\Helpers\CitiesHelper;
use Illuminate\Database\Eloquent\Model;

class WeatherNotificationSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'city',
        'threshold',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return (new CitiesHelper())->getCity($this->city)?->first();
    }
}
