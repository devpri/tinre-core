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
        if ($user->hasPermissionTo('url:view:any')) {
            return true;
        }

        return false;
    }

    public function view(User $user, Url $url): bool
    {
        if ($user->hasPermissionTo('url:view:any')) {
            return true;
        }

        if (! $user->hasPermissionTo('url:view')) {
            return false;
        }

        if ($user->id === $url->user_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('url:create')) {
            return true;
        }

        return false;
    }

    public function update(User $user, Url $url): bool
    {
        if ($user->hasPermissionTo('url:update:any')) {
            return true;
        }

        if (! $user->hasPermissionTo('url:update')) {
            return true;
        }

        if ($user->id === $url->user_id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Url $url): bool
    {
        if ($user->hasPermissionTo('url:delete:any')) {
            return true;
        }

        if (! $user->hasPermissionTo('url:delete')) {
            return true;
        }

        if ($user->id === $url->user_id) {
            return true;
        }

        return false;
    }
}
