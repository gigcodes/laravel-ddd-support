<?php

namespace Gigcodes\LaravelDddSupport\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SetupModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:model {name} {--namespace=} {--admin} {--domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the scaffolding for a new model type';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $namespace = $this->option('namespace') ? "{$this->option('namespace')}/" : null;
        $domainNamespace = false;
        if ($this->option('admin')) {
            $namespace = "Admin/{$namespace}";
        }

        if ($this->option('domain')) {
            $domainNamespace = true;
        }

        $namespacedName = "{$namespace}{$name}";

        //Create the model and migration
        $this->call('make:model', ['name' => $name, '--migration' => true]);

        //Create the request
        $this->call('make:model-request', ['name' => "{$namespacedName}Request", '--model' => $name]);

        //Create the resource
        $this->call('make:resource', ['name' => "{$name}Resource"]);

        //Create the collection resource
        $this->call('make:resource', ['name' => "{$name}CollectionResource", '--collection' => true]);

        //Create the action
        $this->call('make:action', ['name' => "{$namespace}Persist{$name}Action", '--request' => "{$namespacedName}Request", '--model' => $name]);

        //Create the policy
        $this->call('make:policy', ['name' => "{$name}Policy", '--model' => $name]);

        $this->call('setup:views', [
            'name' => $name,
            '--admin' => $this->option('admin'),
        ]);

        //Create the controller
        $this->call('make:inertia-controller', [
            'name' => "{$namespacedName}Controller",
            '--model' => $name,
            '--resource' => "{$name}Resource",
            '--request' => "{$namespacedName}Request",
            '--action' => "{$namespace}Persist{$name}Action",
            '--route-prefix' => $this->option('admin') ? 'admin::' : '',
            '--component-prefix' => $this->option('admin') ? 'Admin::' : '',
        ]);

        return 0;
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['namespace', 'n', InputOption::VALUE_OPTIONAL, 'Gets the namespace to use for requests, actions and controllers'],
            ['admin', 'a', InputOption::VALUE_OPTIONAL, 'If the model is admin based'],
        ];
    }
}
