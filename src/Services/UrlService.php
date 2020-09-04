<?php

namespace Devpri\Tinre\Services;

use Devpri\Tinre\Models\Url;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UrlService
{
    public function index($data, $user)
    {
        $search = $data['search'] ?? null;
        $userId = $data['user_id'] ?? null;
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;
        $limit = $data['limit'] ?? 25;
        $active = $data['active'] ?? null;
        $sortBy = $data['sort_by'] ?? 'created_at';
        $sortDirection = $data['sort_direction'] ?? 'desc';

        $query = Url::query();

        $query->when($user->cant('viewAny', Url::class), function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        });

        $query->when($userId, function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });

        $query->when($search, function ($query) use ($search) {
            return $query->where(function (Builder $query) use ($search) {
                return $query->where('path', 'LIKE', "%{$search}%")
                    ->orWhere('long_url', 'LIKE', "%{$search}%");
            });
        });

        $query->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        });

        $query->when(isset($active), function ($query) use ($active) {
            return $query->where('active', $active);
        });

        return $query->orderBy($sortBy, $sortDirection)->with('user')->paginate($limit);
    }

    public function create($longUrl, $path = null, $user = null): Url
    {
        $this->validateUrl($longUrl);

        $url = new Url();
        $url->long_url = $longUrl;
        $url->user_id = $user ? $user->id : null;

        $path = $user || config('tinre.guest_form_custom_path') ? $path : null;

        if ($path) {
            $this->validatePath($path);

            $url->path = $path;
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

    public function update($id, $active, $longUrl, $path, $user): Url
    {
        $this->validateUrl($longUrl);

        $url = Url::where('id', $id)->firstOrFail();

        if ($url->path != $path) {
            $this->validatePath($path);
        }

        if ($user->cant('update', $url)) {
            abort(401);
        }

        $url->active = $active;
        $url->long_url = $longUrl;
        $url->path = $path;
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
