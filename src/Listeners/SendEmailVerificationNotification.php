<?php

namespace  Devpri\Tinre\Listeners;

use Devpri\Tinre\Events\UserRegistered;
use Devpri\Tinre\Notifications\EmailVerificationNotification;

class SendEmailVerificationNotification
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $event->user->notify(new EmailVerificationNotification);
    }
}
