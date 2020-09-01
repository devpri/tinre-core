<?php

namespace Devpri\Tinre\Http\Controllers\Web;

use Devpri\Tinre\Http\Controllers\Controller;
use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Services\StatsService;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    protected $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function clicks(Request $request, $id)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $user = $request->user();

        $url = Url::where('id', $id)->firstOrFail();

        if ($user->cant('view', $url)) {
            abort(401);
        }

        if (! $user->hasPermissionTo('stats:view')) {
            abort(401);
        }

        $data = $this->statsService->getClicks($id, $request->start_date, $request->end_date);

        return response()->json(['data' => $data]);
    }

    public function data(Request $request, $id, $column)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $user = $request->user();

        $url = Url::where('id', $id)->firstOrFail();

        if ($user->cant('view', $url)) {
            abort(401);
        }

        if (! $user->hasPermissionTo('stats:view')) {
            abort(401);
        }

        $data = $this->statsService->getData($column, $id, $request->start_date, $request->end_date);

        return response()->json($data);
    }
}
