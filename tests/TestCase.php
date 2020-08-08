<?php

namespace Devpri\Tinre\Tests;

use Devpri\Tinre\TinreServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Torann\GeoIP\GeoIPServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->withFactories(__DIR__.'/../database/factories');

        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->withoutMix();
    }

    protected function getPackageProviders($app)
    {
        return [TinreServiceProvider::class, GeoIPServiceProvider::class];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app->get('config');

        $config->set('logging.default', 'errorlog');

        $config->set('database.default', 'testbench');

        $config->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $config->set('auth.providers.users.model', \Devpri\Tinre\Models\User::class);
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Console\Kernel', 'Devpri\Tinre\Tests\Console\Kernel');
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton(
            'Illuminate\Contracts\Http\Kernel',
            'Devpri\Tinre\Http\Kernel'
        );
    }

    /**
     * Resolve application HTTP exception handler.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton(
            'Illuminate\Contracts\Debug\ExceptionHandler',
            'Devpri\Tinre\Exceptions\Handler'
        );
    }
}
