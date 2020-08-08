<?php

namespace Devpri\Tinre\Traits;

use Illuminate\Support\Facades\Auth;

trait AuthorizedActions
{
    public function authorizedActions(): array
    {
        $authorizedActions = [];

        $user = Auth::user();

        if (! $user) {
            return $authorizedActions;
        }

        foreach ($this->actions as $action) {
            if ($user->can($action, $this)) {
                $authorizedActions[] = $action;
            }
        }

        return $authorizedActions;
    }
}
