<?php

namespace Devpri\Tinre\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \Devpri\Tinre\Console\Commands\Publish::class,
        \Devpri\Tinre\Console\Commands\CreateUser::class,
    ];
}
