<?php

namespace Devpri\Tinre\Http\Controllers\Web;

use Auth;
use Devpri\Tinre\Http\Controllers\Controller;
use Devpri\Tinre\Http\Resources\Web\Url as UrlResource;
use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Services\UrlService;
use Illuminate\Database\Eloquent\Builder;
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
        $user = $request->user();

        $search = $request->search;
        $date = $request->date;

        $query = Url::query();

        if ($user->cant('viewAny', Url::class)) {
            $query->where('user_id', $user->id);
        }

        if ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('path', 'LIKE', "%{$search}%")
                    ->orWhere('long_url', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($date) {
            $query->whereBetween('created_at', $date);
        }

        $urls = $query->where('active', $request->active ? $request->active : 1)->orderBy('created_at', 'DESC')->with('user')->paginate(30);

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
        
        $validationRules = ['long_url' => ['required', 'url', 'active_url']];

        if($user || config('tinre.guest_form_custom_path')) {
            $validationRules['path'] = ['nullable', 'alpha_dash', 'unique:urls', "min:" . config('tinre.min_path_length'), "max:" . config('tinre.max_path_length')];
        }

        $validatedData = $request->validate($validationRules);

        $url = $this->urlService->create($validatedData, $user);

        return (new UrlResource($url))->additional(['message' => 'The URL has been created.']);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'active' => ['required', 'boolean'],
            'long_url' => ['required', 'url', 'active_url'],
            'path' => ['required', "unique:urls,path,{$id},id", "min:" . config('tinre.min_path_length'), "max:" . config('tinre.max_path_length')],
        ]);

        $user = $request->user();

        $url = Url::where('id', $id)->firstOrFail();

        if ($user->cant('update', $url)) {
            abort(401);
        }

        $url->active = $validatedData['active'];
        $url->long_url = $validatedData['long_url'];
        $url->path = $validatedData['path'];
        $url->save();

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
