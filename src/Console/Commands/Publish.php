<?php

namespace Devpri\Tinre\Console\Commands;

use Illuminate\Console\Command;

class Publish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tinre:publish {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish Tinre resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'tinre-config',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'tinre-assets',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'tinre-lang',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'tinre-views',
            '--force' => $this->option('force'),
        ]);

        $this->call('view:clear');
    }
}
