<?php

namespace Devpri\Tinre\Services;

use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UrlService
{
    public function create(array $data, User $user = null): Url
    {
        $url = new Url();
        $url->long_url = $data['long_url'];
        $url->user_id = $user ? $user->id : null;

        if (isset($data['path'])) {
            $url->path = $data['path'];

            $this->validatePath($url->path );

            $url->save();

            return $url;
        }

        $maxGenerationAttempts = 5;

        while ($maxGenerationAttempts-- > 0) {
            try {
                $url->path = Str::random(config('tinre.default_path_length'));
                
                $this->validatePath($url->path );
                
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
        if(in_array($path, config('tinre.restricted_paths'))) {
            throw ValidationException::withMessages([
                'path' => [__('Restricted path.')],
            ]);
        }
    }
}
