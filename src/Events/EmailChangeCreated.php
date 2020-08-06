<?php

namespace  Devpri\Tinre\Events;

class EmailChangeCreated
{
    public $emailChange;

    public function __construct($emailChange)
    {
        $this->emailChange = $emailChange;
    }
}
