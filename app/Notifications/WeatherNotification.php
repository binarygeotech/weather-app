<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeatherNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected object $city,
        protected array $weatherData,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        //         Warning:
        // - The average UV index is 7.35. It's considered harmful, please take necessary precautions!
        // - The average precipitation is 2.5 mm. Please be prepared for potential rain.

        // Stay safe and plan accordingly!


        return (new MailMessage())
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('The weather update for ' . $this->city->name . ', ' . $this->city->country . ' is as follows:')
                    ->line($this->getPrecipitationLineMessage($this->weatherData['precipitation']))
                    ->line($this->getUvLineMessage($this->weatherData['uvIndex']))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the precipitation line message
     *
     * @param float $precipitation
     * @return string
     */
    protected function getPrecipitationLineMessage(float $precipitation): string
    {
        if ($precipitation < 2.5) {
            return 'The average precipitation is low. Please be prepared for potential rain.';
        }

        if ($precipitation < 7.5) {
            return 'The average precipitation is moderate. Please be prepared for potential rain.';
        }

        if ($precipitation < 15) {
            return 'The average precipitation is high. Please be prepared for potential rain.';
        }

        return 'The average precipitation is very high. Please be prepared for potential rain.';
    }

    /**
     * Get the UV line message
     *
     * @param float $uvIndex
     * @return string
     */
    protected function getUvLineMessage(float $uvIndex): string
    {
        if ($uvIndex < 3) {
            return 'The average UV index is low. It\'s safe to go outside.';
        }

        if ($uvIndex < 6) {
            return 'The average UV index is moderate. It\'s safe to go outside.';
        }

        if ($uvIndex < 8) {
            return 'The average UV index is high. Please take necessary precautions.';
        }

        if ($uvIndex < 11) {
            return 'The average UV index is very high. Please take necessary precautions.';
        }

        return 'The average UV index is extreme. Please take necessary precautions.';
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
