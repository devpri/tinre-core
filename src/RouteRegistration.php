<?php

namespace Devpri\Tinre;

use Illuminate\Support\Facades\Route;

class RouteRegistration
{
    protected $registered = false;

    public function register(): void
    {
        $this->registered = true;

        Route::middleware('web')
            ->namespace('Devpri\Tinre\Http\Controllers\Web')
            ->prefix('/web')
            ->group(__DIR__ . '/../routes/web.php');

        Route::middleware(['web', 'auth'])
            ->prefix(Tinre::dashboardPath())
            ->get('{view?}', 'Devpri\Tinre\Http\Controllers\DashboardController@show')
            ->where('view', '.*')
            ->name('dashboard');
            
        Route::get('{path}', 'Devpri\Tinre\Http\Controllers\RedirectController@redirect')->name('url');
    }

    public function withHomeRoute($middleware = ['web']): RouteRegistration
    {
        Route::get('/', 'Devpri\Tinre\Http\Controllers\HomeController@show')->middleware($middleware);

        return $this;
    }

    public function withAuthenticationRoutes($middleware = ['web']): RouteRegistration
    {
        Route::group([
            'namespace' => 'Devpri\Tinre\Http\Controllers',
            'prefix' => Tinre::dashboardPath(),
            'middleware' => $middleware,
        ], function () {
            Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
            Route::post('login', 'Auth\LoginController@login');
            Route::post('logout', 'Auth\LoginController@logout')->name('logout');
        });

        return $this;
    }

    public function withPasswordResetRoutes($middleware = ['web']): RouteRegistration
    {
        Route::group([
            'namespace' => 'Devpri\Tinre\Http\Controllers',
            'prefix' => Tinre::dashboardPath(),
            'middleware' => $middleware,
        ], function () {
            Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
            Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
        });

        return $this;
    }

    public function withRegisterRoutes($middleware = ['web']): RouteRegistration
    {
        Route::group([
            'namespace' => 'Devpri\Tinre\Http\Controllers',
            'prefix' => Tinre::dashboardPath(),
            'middleware' => $middleware,
        ], function () {
            Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
            Route::post('register', 'Auth\RegisterController@register');
        });

        return $this;
    }

    public function withVerificationRoutes($middleware = ['web']): RouteRegistration
    {
        Route::group([
            'namespace' => 'Devpri\Tinre\Http\Controllers',
            'prefix' => Tinre::dashboardPath(),
            'middleware' => $middleware,
        ], function () {
            Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
            Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
            Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
        });

        return $this;
    }

    public function withEmailChangeRoutes($middleware = ['web']): RouteRegistration
    {
        Route::group([
            'namespace' => 'Devpri\Tinre\Http\Controllers',
            'prefix' => Tinre::dashboardPath(),
            'middleware' => $middleware,
        ], function () {
            Route::get('email/change/{token}', 'Auth\ChangeEmailController@change')->name('email.change');
            Route::post('email/change', 'Auth\ChangeEmailController@create')->name('email.request');
        });

        return $this;
    }

    public function __destruct()
    {
        if (!$this->registered) {
            $this->register();
        }
    }
}
