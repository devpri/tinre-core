<?php

namespace Devpri\Tinre\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user && config('tinre.redirect_user_to_dashboard')) {
            return redirect(config('tinre.dashboard_path'));
        }

        return view('tinre::home');
    }
}
