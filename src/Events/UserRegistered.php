<?php

namespace  Devpri\Tinre\Events;

use Devpri\Tinre\Models\User;

class UserRegistered
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
