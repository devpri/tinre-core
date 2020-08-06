<?php

namespace  Devpri\Tinre\Policies;

use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UrlPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['editor', 'administrator'])) {
            return true;
        }

        return false;
    }

    public function view(User $user, Url $url): bool
    {
        if ($user->id === (int) $url->user_id) {
            return true;
        }

        if (in_array($user->role, ['editor', 'administrator'])) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Url $url): bool
    {
        if ($user->id === (int) $url->user_id) {
            return true;
        }

        if (in_array($user->role, ['editor', 'administrator'])) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Url $url): bool
    {
        if ($user->id === (int) $url->user_id) {
            return true;
        }

        if (in_array($user->role, ['editor', 'administrator'])) {
            return true;
        }

        return false;
    }
}
