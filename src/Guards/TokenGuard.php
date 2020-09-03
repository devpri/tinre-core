<?php

namespace Devpri\Tinre\Guards;

use Devpri\Tinre\Models\AccessToken;
use Illuminate\Http\Request;

class TokenGuard
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function user()
    {
        if ($token = $this->request->bearerToken()) {
            $accessToken = AccessToken::findToken($token);

            if (! $accessToken) {
                return;
            }

            $user = $accessToken->user;

            if (! $user->active) {
                return;
            }

            if (! $user->hasApiAccess()) {
                return;
            }

            return $user->withAccessToken(
                tap($accessToken->forceFill(['last_used_at' => now()]))->save()
            );
        }
    }
}
