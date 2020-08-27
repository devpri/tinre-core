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
        $urls = $this->urlService->index($request);

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
        $url = $this->urlService->create($request);

        return (new UrlResource($url))->additional(['message' => 'The URL has been created.']);
    }

    public function update(Request $request, $id)
    {
        $url = $this->urlService->update($request, $id);

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
