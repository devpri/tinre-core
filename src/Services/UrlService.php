<?php

namespace Devpri\Tinre\Services;

use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UrlService
{
    public function create(array $data, User $user = null): Url
    {
        $this->validateUrl($data['long_url']);

        $url = new Url();
        $url->long_url = $data['long_url'];
        $url->user_id = $user ? $user->id : null;

        if (isset($data['path'])) {
            $url->path = $data['path'];

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

    protected function validatePath($path)
    {
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

    protected function validateUrl($url)
    {
        if (in_array(parse_url($url, PHP_URL_HOST), config('tinre.restricted_domains'))) {
            throw ValidationException::withMessages([
                'long_url' => [__('Restricted domain.')],
            ]);
        }
    }
}
