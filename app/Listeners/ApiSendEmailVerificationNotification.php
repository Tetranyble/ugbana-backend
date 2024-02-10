<?php

namespace App\Listeners;

use App\Events\ApiRegistered;
use App\Notifications\ApiEmailVerificationNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ApiSendEmailVerificationNotification
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(ApiRegistered $event)
    {
        if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
            $event->user->phoneVerification();
            $event->user->notify(new ApiEmailVerificationNotification($event->user));
        }
    }
}
