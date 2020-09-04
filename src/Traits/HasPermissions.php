<?php

namespace Devpri\Tinre\Traits;

trait HasPermissions
{
    public function permissions()
    {
        if ($this->accessToken) {
            return $this->tokenPermissions($this->accessToken);
        }

        $permissions = config("tinre.role_permissions.{$this->role}", []);

        if (in_array('*', $permissions)) {
            return config('tinre.permissions');
        }

        return $permissions;
    }

    public function apiPermissions()
    {
        $permissions = config("tinre.role_permissions.{$this->role}", []);

        if (in_array('*', $permissions)) {
            $permissions = config('tinre.api_permissions');
        }

        return array_intersect($permissions, config('tinre.api_permissions'));
    }

    public function tokenPermissions($accessToken)
    {
        $tokenPermissions = $accessToken->permissions;

        if (! is_array($tokenPermissions)) {
            return [];
        }

        $apiPermissions = $this->apiPermissions();

        return array_intersect($tokenPermissions, $apiPermissions);
    }

    public function hasPermissionTo($permission)
    {
        $userPermissions = $this->permissions();

        if (in_array($permission, $userPermissions)) {
            return true;
        }

        return false;
    }

    public function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }
}
