<?php

namespace  Devpri\Tinre\Policies;

use Devpri\Tinre\Models\AccessToken;
use Devpri\Tinre\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccessTokenPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('access_token:view:any')) {
            return true;
        }

        return false;
    }

    public function view(User $user, AccessToken $accessToken): bool
    {
        if ($user->hasPermissionTo('access_token:view:any')) {
            return true;
        }

        if (! $user->hasPermissionTo('access_token:view')) {
            return false;
        }

        if ($user->id === $accessToken->user_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('access_token:create')) {
            return true;
        }

        return false;
    }

    public function update(User $user, AccessToken $accessToken): bool
    {
        if ($user->hasPermissionTo('access_token:update:any')) {
            return true;
        }

        if (! $user->hasPermissionTo('access_token:update')) {
            return false;
        }

        if ($user->id === $accessToken->user_id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, AccessToken $accessToken): bool
    {
        if ($user->hasPermissionTo('access_token:delete:any')) {
            return true;
        }

        if (! $user->hasPermissionTo('access_token:delete')) {
            return false;
        }

        if ($user->id === $accessToken->user_id) {
            return true;
        }

        return false;
    }
}
