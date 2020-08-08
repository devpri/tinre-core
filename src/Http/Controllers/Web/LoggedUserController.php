<?php

namespace Devpri\Tinre\Http\Controllers\Web;

use Devpri\Tinre\Http\Controllers\Controller;
use Devpri\Tinre\Http\Resources\Web\User as UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoggedUserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return new UserResource($user);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        if ($user->cant('updateOwn', $user)) {
            abort(401);
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'required_with:current_password', 'min:6'],
        ]);

        if ($validatedData['new_password'] && ! Hash::check($validatedData['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => [trans('The current password is invalid.')],
            ]);
        }

        if ($validatedData['new_password']) {
            $user->password = bcrypt($validatedData['new_password']);
        }

        $user->name = $validatedData['name'];

        $user->save();

        return (new UserResource($user))->additional(['message' => 'The user has been updated.']);
    }
}
