<?php

namespace Gigcodes\LaravelDddSupport\Commands;

use Illuminate\Console\Command;

class SetupInertiaViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:views {name} {{--route-prefix=}} {{--admin}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates all views for the given model';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');

        $this->call('make:inertia-view', [
            'name' => 'Index',
            '--admin' => $this->option('admin'),
            '--model' => $name,
            '--route-prefix' => $this->option('admin') ? 'admin::' : '',
        ]);

        $this->call('make:inertia-view', [
            'name' => 'Create',
            '--admin' => $this->option('admin'),
            '--model' => $name,
            '--route-prefix' => $this->option('admin') ? 'admin::' : '',
        ]);

        $this->call('make:inertia-view', [
            'name' => 'Edit',
            '--admin' => $this->option('admin'),
            '--model' => $name,
            '--route-prefix' => $this->option('admin') ? 'admin::' : '',
        ]);

        return 0;
    }
}
