<?php

namespace  Devpri\Tinre\Listeners;

use Devpri\Tinre\Events\EmailChangeCreated;
use Devpri\Tinre\Notifications\ChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class SendEmailChangeNotification
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(EmailChangeCreated $event)
    {
        Notification::route('mail', $event->emailChange['email'])->notify(new ChangeEmailNotification($event->emailChange['token']));
    }
}
