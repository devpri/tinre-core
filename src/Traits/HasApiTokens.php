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

    public function createToken(string $name)
    {
        $plainTextToken = Str::random(100);

        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
        ]);

        $token->plain_text_token = $plainTextToken;

        return $token;
    }

    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
