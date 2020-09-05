<?php

namespace Devpri\Tinre\Http\Controllers\Web;

use Devpri\Tinre\Http\Controllers\Controller;
use Devpri\Tinre\Http\Resources\Web\User as UserResource;
use Devpri\Tinre\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function create(Request $request)
    {
        $authUser = $request->user();

        if ($authUser->cant('create', User::class)) {
            abort(401);
        }

        $validatedData = $request->validate([
            'active' => ['required', 'boolean'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = new User;
        $user->active = $validatedData['active'];
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'];
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return (new UserResource($user))->additional(['message' => 'The user has been created.']);
    }

    public function update(Request $request, $id)
    {
        $authUser = $request->user();

        $user = User::where('id', $id)->firstOrFail();

        if ($authUser->cant('update', $user)) {
            abort(401);
        }

        $validatedData = $request->validate([
            'active' => ['required', 'boolean'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'role' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->active = $validatedData['active'];
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'];

        if (isset($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return (new UserResource($user))->additional(['message' => 'The user has been updated.']);
    }

    public function delete(Request $request, $id)
    {
        $authUser = $request->user();

        $user = User::where('id', $id)->firstOrFail();

        if ($authUser->cant('delete', $user)) {
            abort(401);
        }

        $user->delete();

        return ['message' => 'The user has been deleted.'];
    }
}
