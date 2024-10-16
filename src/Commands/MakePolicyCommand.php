<?php

namespace Gigcodes\LaravelDddSupport\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakePolicyCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new resource controller style policy';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Policy';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return App::path('Console/stubs/policy.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Policies';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base default import if we are already in the base namespace.
     *
     * @param  string  $name
     *
     * @throws FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $policyNamespace = $this->getNamespace($name);
        $modelClass = App::getNamespace().'Models\\'.($this->option('model') ?? str_replace('Policy', '', class_basename($name)));

        $replace = $this->buildReplacements($modelClass);

        $replace["use {$policyNamespace}\BasePolicy;\n"] = '';

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name) //Calls the buildClass method against the parents parent with the name parameter as an argument
        );
    }

    /**
     * Generates a list of values to replace in the stub.
     */
    protected function buildReplacements(string $modelClass): array
    {
        return [
            '{{ model }}' => class_basename($modelClass),
            '{{ namespacedModel }}' => $modelClass,
            '{{ modelVariableName }}' => Str::of(class_basename($modelClass))->camel()->singular(),
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Use or create the given model when creating the controller.'],
        ];
    }
}
