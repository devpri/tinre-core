<?php

namespace Devpri\Tinre\Tests\Console;

use Devpri\Tinre\Console\Kernel as KernelBase;
use Throwable;

class Kernel extends KernelBase
{
    /**
     * The bootstrap classes for the application.
     *
     * @return void
     */
    protected $bootstrappers = [];

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Throwable  $e
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function reportException(Throwable $e)
    {
        throw $e;
    }
}
