<?php

namespace Devpri\Tinre;

use Illuminate\Session\SessionManager;

class Tinre
{
    public static $config = [];

    public static $styles = [];

    public static $scripts = [];

    public static $translations = [];

    public static function dashboardPath(): string
    {
        return config('tinre.dashboard_path', '/dashboard');
    }

    public static function routes(): RouteRegistration
    {
        return new RouteRegistration;
    }

    public static function config(): array
    {
        return static::$config;
    }

    public static function addToConfig(array $variables): Tinre
    {
        static::$config = array_merge(static::$config, $variables);

        return new static;
    }

    public static function addDashboardConfig()
    {
        static::addToConfig([
            'app_url' => rtrim(config('app.url', null), '/').'/',
            'dashboard_path' => static::dashboardPath(),
            'timezone' => config('app.timezone', 'UTC'),
            'date_format' => config('tinre.date_format', 'MM/DD/YYYY, h:mm:ss a'),
            'roles' => config('tinre.roles', []),
        ]);

        return new static;
    }

    public static function addUserToConfig($request): Tinre
    {
        static::addToConfig([
            'user_permissions' => $request->user()->permissions(),
            'user_api_access' => $request->user()->hasApiAccess(),
            'user_api_permissions' => $request->user()->apiPermissions(),
        ]);

        return new static;
    }

    public static function styles(): array
    {
        return static::$styles;
    }

    public static function addStyle($name, $path): Tinre
    {
        static::$styles[$name] = $path;

        return new static;
    }

    public static function scripts(): array
    {
        return static::$scripts;
    }

    public static function addScript($name, $path): Tinre
    {
        static::$scripts[$name] = $path;

        return new static;
    }

    public static function translations(): array
    {
        $json = [];

        foreach (static::$translations as $translation) {
            if (is_string($translation)) {
                if (is_readable($translation)) {
                    $translation = json_decode(file_get_contents($translation), true);

                    $json = array_merge($json, $translation);
                }
            }
        }

        return $json;
    }

    public static function addTranslation($path): Tinre
    {
        static::$translations[] = $path;

        return new static;
    }

    public static function messages(SessionManager $session): array
    {
        $messages = [];

        if ($session->has('status')) {
            $messages[] = [
                'type' => 'success',
                'text' => $session->get('status'),
            ];
        }

        if ($session->has('warning')) {
            $messages[] = [
                'type' => 'warning',
                'text' => $session->get('warning'),
            ];
        }

        return $messages;
    }
}
