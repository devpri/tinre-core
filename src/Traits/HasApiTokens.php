<?php

namespace Devpri\Tinre\Traits;

use Illuminate\Support\Str;

trait HasApiTokens
{
    public $accessToken;

    public function tokens()
    {
        return $this->hasMany('Devpri\Tinre\Models\AccessToken');
    }

    public function createToken(string $name, $permissions = [])
    {
        $plainTextToken = Str::random(100);

        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'permissions' => is_array($permissions) ? array_intersect($permissions, $this->apiPermissions()) : null,
        ]);

        $token->plain_text_token = $plainTextToken;

        return $token;
    }

    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function hasApiAccess()
    {
        if (in_array($this->role, config('tinre.api_roles', []))) {
            return true;
        }

        return false;
    }
}
