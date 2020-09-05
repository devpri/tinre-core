<?php

namespace Devpri\Tinre\Http\Controllers\Api\V1;

use Devpri\Tinre\Http\Controllers\Controller;
use Devpri\Tinre\Http\Resources\Api\V1\User as UserResource;
use Devpri\Tinre\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->cant('viewAny', User::class)) {
            abort(401);
        }

        $search = $request->search;

        $query = User::query();

        $query->when($search, function ($query) use ($search) {
            return $query->where(function (Builder $query) use ($search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        });

        $users = $query->orderBy('created_at', 'DESC')->paginate(30);

        return UserResource::collection($users)->additional(['authorized_actions' => (new User)->authorizedActions()]);
    }

    public function show(Request $request, $id)
    {
        $authUser = $request->user();

        $user = User::where('id', $id)->firstOrFail();

        if ($authUser->cant('view', $user)) {
            abort(401);
        }

        return new UserResource($user);
    }
}
