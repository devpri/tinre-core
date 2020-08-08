<?php

namespace Devpri\Tinre\Http\Controllers\Auth;

use Carbon\Carbon;
use Devpri\Tinre\Events\EmailChangeCreated;
use Devpri\Tinre\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ChangeEmailController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Change Email Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles user email changes.
    |
    */

    public function __construct()
    {
        $this->middleware('auth')->only('create');
    }

    /**
     * Where to redirect users after verification.
     */
    public function redirectTo()
    {
        return route('dashboard');
    }

    public function create(Request $request)
    {
        $this->validateData($request);

        $user = $request->user();

        if (! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => __('Invalid Password')], 403);
        }

        $maxGenerationAttempts = 5;

        while ($maxGenerationAttempts-- > 0) {
            try {
                $data = [
                    'email' => $request->email,
                    'token' => Str::random(60),
                    'created_at' => Carbon::now(),
                ];

                DB::table('email_changes')->updateOrInsert(['user_id' => $user->id], $data);
            } catch (Exception $e) {
                if ($maxGenerationAttempts === 0) {
                    throw $e;
                }
            }
        }

        event(new EmailChangeCreated($data));

        return response()->json(['message' => __('Please verify your email.')]);
    }

    public function change(Request $request, $token)
    {
        $email = DB::table('email_changes')->where('token', $token)->first();

        if (! $email) {
            abort(404);
        }

        DB::table('users')->where('id', $email->user_id)->update([
            'email' => $email->email,
            'email_verified_at' => Carbon::now(),
        ]);

        DB::table('email_changes')->where('token', $token)->delete();

        return redirect($this->redirectTo())->with('status', __('Email changed.'));
    }

    protected function validateData(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required'],
        ]);
    }
}
