<?php

namespace Devpri\Tinre\Http\Controllers;

use Devpri\Tinre\Models\Url;
use Illuminate\Http\Request;

class PreviewController extends Controller
{
    public function show(Request $request, $path)
    {
        $url = URL::select(['long_url', 'path', 'created_at'])->where(['path' => $path, 'active' => 1])->first();

        if (! $url) {
            return redirect('/');
        }

        return view('tinre::preview', ['url' => $url]);
    }
}
