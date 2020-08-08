<?php

namespace Devpri\Tinre\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        return view('tinre::layouts.dashboard');
    }
}
