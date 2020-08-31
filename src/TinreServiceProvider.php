<?php

namespace Devpri\Tinre;

use Devpri\Tinre\Guards\TokenGuard;
use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class TinreServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'Devpri\Tinre\Models\User' => 'Devpri\Tinre\Policies\UserPolicy',
        'Devpri\Tinre\Models\Url' => 'Devpri\Tinre\Policies\UrlPolicy',
        'Devpri\Tinre\Models\AccessToken' => 'Devpri\Tinre\Policies\AccessTokenPolicy',
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();

        $this->registerPolicies();

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'tinre');

        $this->loadJsonTranslationsFrom(resource_path('lang/vendor/tinre'));

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tinre');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->listenEvents();

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tinre.php', 'tinre');

        $this->registerTokenGuard();

        Tinre::addTranslation(
            resource_path('lang/vendor/tinre/'.app()->getLocale().'.json')
        );
    }

    protected function registerRoutes()
    {
        Tinre::routes()
            ->withHomeRoute()
            ->withAuthenticationRoutes()
            ->withRegisterRoutes()
            ->withPasswordResetRoutes()
            ->withVerificationRoutes()
            ->withEmailChangeRoutes();
    }

    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    protected function listenEvents()
    {
        \Illuminate\Support\Facades\Event::listen(
            \Devpri\Tinre\Events\EmailChangeCreated::class,
            \Devpri\Tinre\Listeners\SendEmailChangeNotification::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \Devpri\Tinre\Events\UserRegistered::class,
            \Devpri\Tinre\Listeners\SendEmailVerificationNotification::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['tinre'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/tinre.php' => config_path('tinre.php'),
        ], 'tinre-config');

        $this->publishes([
            __DIR__.'/../stubs/TinreServiceProvider.stub' => app_path('Providers/TinreServiceProvider.php'),
        ], 'tinre-provider');

        // Publishing the views.
        $this->publishes([
            __DIR__.'/../resources/views/partials' => base_path('resources/views/vendor/tinre/partials'),
        ], 'tinre-views');

        // Publishing assets.
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/tinre'),
        ], 'tinre-assets');

        // Publishing the translation files.
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/tinre'),
        ], 'tinre-lang');
    }

    protected function registerTokenGuard()
    {
        Auth::resolved(function ($auth) {
            $auth->extend('token', function ($app, $name, array $config) {
                return tap($this->makeTokenGuard(), function ($guard) {
                    $this->app->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    protected function makeTokenGuard()
    {
        return new RequestGuard(function ($request) {
            return (new TokenGuard(
                $request
            ))->user();
        }, $this->app['request']);
    }
}
