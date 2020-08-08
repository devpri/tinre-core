<?php

namespace  Devpri\Tinre\Policies;

use Devpri\Tinre\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Route;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['administrator'])) {
            return true;
        }

        return false;
    }

    public function view(User $user, User $userModel): bool
    {
        if (in_array($user->role, ['administrator'])) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if (in_array($user->role, ['administrator'])) {
            return true;
        }

        return false;
    }

    public function update(User $user, User $userModel): bool
    {
        if ($user->id === $userModel->id) {
            return false;
        }

        if (in_array($user->role, ['administrator'])) {
            return true;
        }

        return false;
    }

    public function updateOwn(User $user, User $userModel): bool
    {
        if ($user->id === $userModel->id) {
            return true;
        }

        return false;
    }

    public function changeEmail(User $user): bool
    {
        if (Route::has('email.change')) {
            return true;
        }

        return false;
    }

    public function delete(User $user, User $userModel): bool
    {
        if ($user->id === $userModel->id) {
            return false;
        }

        if (in_array($user->role, ['administrator'])) {
            return true;
        }

        return false;
    }
}
