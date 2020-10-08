<?php

namespace Devpri\Tinre\Http\Controllers\Web;

use Devpri\Tinre\Http\Controllers\Controller;
use Devpri\Tinre\Http\Resources\Web\Url as UrlResource;
use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Services\UrlService;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    protected $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'active' => ['nullable', 'boolean'],
            'user_id' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'in:created_at,updated_at,total_clicks'],
            'sort_direction' => ['nullable', 'in:asc,desc'],
        ]);

        $user = $request->user();

        $urls = $this->urlService->index($request->all(), $user);

        return UrlResource::collection($urls)->additional(['authorized_actions' => (new Url)->authorizedActions()]);
    }

    public function show(Request $request, $path): UrlResource
    {
        $user = $request->user();

        $url = Url::where('path', $path)->firstOrFail();

        if ($user->cant('view', $url)) {
            abort(401);
        }

        return new UrlResource($url);
    }

    public function create(Request $request): UrlResource
    {
        $user = $request->user();

        if ($user && $user->cant('create', Url::class)) {
            abort(401);
        }

        $url = $this->urlService->create($request->long_url, $request->path, $user);

        return (new UrlResource($url))->additional(['message' => 'The URL has been created.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'active' => ['required', 'boolean'],
        ]);

        $user = $request->user();

        $url = $this->urlService->update($id, $request->active, $request->long_url, $request->path, $user);

        return (new UrlResource($url))->additional(['message' => 'The URL has been updated.']);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();

        $url = Url::where('id', $id)->firstOrFail();

        if ($user->cant('delete', $url)) {
            abort(401);
        }

        $url->delete();

        return ['message' => 'The URL has been deleted.'];
    }
}
