<?php

namespace Devpri\Tinre\Services;

use Devpri\Tinre\Models\Url;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UrlService
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string'],
            'date' => ['nullable', 'array'],
            'limit' => ['nullable', 'number', 'min:1', 'max:100'],
            'active' => ['nullable', 'boolean'],
            'sort_by' => ['nullable', 'in:created_at,updated_at,clicks'],
            'sort_direction' => ['nullable', 'in:asc,desc'],
        ]);

        $user = $request->user();
        $search = $request->search;
        $date = $request->date;
        $limit = $request->limit ?? 30;
        $active = $request->active;
        $sortBy = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';

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

        if (isset($active)) {
            $query->where('active', $active);
        }

        return $query->orderBy($sortBy, $sortDirection)->with('user')->paginate($limit);
    }

    public function create(Request $request): Url
    {
        $this->validateUrl($request->long_url);

        $user = $request->user();

        if ($user && $user->cant('create', Url::class)) {
            abort(401);
        }

        $url = new Url();
        $url->long_url = $request->long_url;
        $url->user_id = $user ? $user->id : null;

        if ($request->path && ($user || config('tinre.guest_form_custom_path'))) {
            $url->path = $request->path;

            $this->validatePath($url->path);

            $url->save();

            return $url;
        }

        $maxGenerationAttempts = 5;

        while ($maxGenerationAttempts-- > 0) {
            try {
                $url->path = strtolower(Str::random(config('tinre.default_path_length')));

                $this->validatePath($url->path);

                $url->save();

                return $url;
            } catch (Exception $e) {
                if ($maxGenerationAttempts === 0) {
                    throw $e;
                }
            }
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'active' => ['required', 'boolean'],
        ]);

        $this->validateUrl($request->long_url);

        $url = Url::where('id', $id)->firstOrFail();

        if ($url->path != $request->path) {
            $this->validatePath($request->path);
        }

        $user = $request->user();

        if ($user->cant('update', $url)) {
            abort(401);
        }

        $url->active = $request->active;
        $url->long_url = $request->long_url;
        $url->path = $request->path;
        $url->save();

        return $url;
    }

    protected function validatePath($path): void
    {
        Validator::make([
            'path' => $path,
        ], [
            'path' => ['required', 'alpha_dash', 'min:'.config('tinre.min_path_length'), 'max:'.config('tinre.max_path_length')],
        ])->validate();

        $path = strtolower($path);

        $url = DB::table('urls')->whereRaw('lower(path) like (?)', ["%{$path}%"])->count();

        if ($url) {
            throw ValidationException::withMessages([
                'path' => [__('The path has already been taken.')],
            ]);
        }

        if (in_array($path, config('tinre.restricted_paths'))) {
            throw ValidationException::withMessages([
                'path' => [__('Restricted path.')],
            ]);
        }
    }

    protected function validateUrl($url): void
    {
        Validator::make([
            'long_url' => $url,
        ], [
            'long_url' => ['required', 'url', 'active_url'],
        ])->validate();

        if (in_array(parse_url($url, PHP_URL_HOST), config('tinre.restricted_domains'))) {
            throw ValidationException::withMessages([
                'long_url' => [__('Restricted domain.')],
            ]);
        }
    }
}
