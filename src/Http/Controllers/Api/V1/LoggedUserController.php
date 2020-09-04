<?php

namespace Devpri\Tinre\Http\Controllers\Api\V1;

use Devpri\Tinre\Http\Controllers\Controller;
use Devpri\Tinre\Http\Resources\Api\V1\User as UserResource;
use Illuminate\Http\Request;

class LoggedUserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->cant('view', $user)) {
            abort(401);
        }

        return new UserResource($user);
    }
}
