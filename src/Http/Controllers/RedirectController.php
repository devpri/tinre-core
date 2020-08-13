<?php

namespace Devpri\Tinre\Http\Controllers;

use Carbon\Carbon;
use Devpri\Tinre\Jobs\ProcessClick;
use Devpri\Tinre\Models\Url;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function redirect(Request $request, $path)
    {
        $path = strtolower($request->path);

        $url = Url::whereRaw('lower(path) = (?)', [$path])->where(['active' => 1])->first();

        if (! $url) {
            return redirect('/');
        }

        $userAgent = $request->header('User-Agent');
        $ip = $request->ip;
        $createdAt = Carbon::now();
        $referer = $request->server('HTTP_REFERER');

        ProcessClick::dispatch($url, $createdAt, $ip, $userAgent, $referer);

        return redirect($url->long_url, config('tinre.redirect_type', 302));
    }
}
