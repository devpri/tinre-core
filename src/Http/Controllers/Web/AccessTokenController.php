<?php

namespace Devpri\Tinre\Http\Controllers\Web;

use Devpri\Tinre\Http\Controllers\Controller;
use Devpri\Tinre\Http\Resources\Web\AccessToken as AccessTokenResource;
use Devpri\Tinre\Models\AccessToken;
use Illuminate\Http\Request;

class AccessTokenController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->hasAnyPermission(['access_token:view', 'access_token:view:any'])) {
            abort(401);
        }

        $query = AccessToken::query();

        if ($user->cant('viewAny', AccessToken::class)) {
            $query->where('user_id', $user->id);
        }

        if ($user->can('viewAny', AccessToken::class)) {
            $query->with('user');
        }

        $accessTokens = $query->orderBy('created_at', 'desc')->paginate(20);

        return AccessTokenResource::collection($accessTokens)->additional(['authorized_actions' => (new AccessToken)->authorizedActions()]);
    }

    public function show(Request $request, $id)
    {
        $accessToken = AccessToken::where('id', $id)->firstOrFail();

        $user = $request->user();

        if ($user->cant('view', $accessToken)) {
            abort(401);
        }

        if ($user->can('viewAny', AccessToken::class)) {
            $accessToken->load('user');
        }

        return new AccessTokenResource($accessToken);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
        ]);

        $user = $request->user();

        if ($user->cant('create', AccessToken::class)) {
            abort(401);
        }

        $permissions = $request->permissions ? array_intersect($user->apiPermissions(), $request->permissions) : null;

        $accessToken = $user->createToken($request->name, $permissions);

        return (new AccessTokenResource($accessToken))->additional(['message' => __('The access token has been created.')]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
        ]);

        $user = $request->user();

        $accessToken = AccessToken::where('id', $id)->firstOrFail();

        if ($user->cant('update', $accessToken)) {
            abort(401);
        }

        if ($request->permissions) {
            $request->permissions = array_intersect(config('tinre.api_permissions', []), $request->permissions);
        }

        $permissions = $request->permissions ? array_intersect($user->apiPermissions(), $request->permissions) : null;

        $accessToken->update([
            'name' => $request->name,
            'permissions' => $permissions,
        ]);

        return (new AccessTokenResource($accessToken))->additional(['message' => __('The access token has been updated.')]);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();

        $accessToken = AccessToken::where('id', $id)->firstOrFail();

        if ($user->cant('delete', $accessToken)) {
            abort(401);
        }

        $accessToken->delete();

        return ['message' => __('The access token has been deleted.')];
    }
}
