<?php

namespace Gigcodes\LaravelDddSupport\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeInertiaViewCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:inertia-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new view for a given model using the inertia style';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'View';

    public function handle(): ?bool
    {
        $result = parent::handle();

        if ($this->getNameInput() === 'Create' || $this->getNameInput() === 'Edit') {
            $this->call('make:inertia-view', [
                'name'           => 'Form',
                '--model'        => $this->option('model'),
                '--admin'        => $this->option('admin'),
                '--route-prefix' => $this->option('route-prefix'),
            ]);
        }

        return $result;
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     */
    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        //These files are views so go into the resources
        $baseNamespace = (
            $this->option('admin')
                ? 'resources/js/Admin/Pages/'
                : 'resources/js/Frontend/Pages/'
        ).Str::plural($this->option('model'));

        if ($this->getNameInput() === 'Form') {
            $baseNamespace .= '/Partials';
        }

        //We need a vue file not a php file
        return App::basePath($baseNamespace.'/'.$name.'.vue');
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return App::path("Console/stubs/inertia-view.{$this->getNameInput()}.stub");
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base default import if we are already in the base namespace.
     *
     * @param string $name
     *
     * @throws FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $modelClass = App::getNamespace().'Models\\'.($this->option('model'));

        $replace = $this->buildReplacements($modelClass);

        $replace['{{ routePrefix }}'] = $this->option('route-prefix');

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name) //Calls the buildClass method against the parents parent with the name parameter as an argument
        );
    }

    /**
     * Generates a list of values to replace in the stub.
     */
    protected function buildReplacements(string $class): array
    {
        $class = Str::replace('/', '\\', $class);

        return [
            '{{ model }}'                                   => Str::of($class)->classBasename(),
            '{{ modelTitle }}'                              => Str::of($class)->classBasename()->singular()->headline(),
            '{{ modelPluralTitle }}'                        => Str::of($class)->classBasename()->plural()->headline(),
            '{{ modelVariableName }}'                       => Str::of($class)->classBasename()->camel()->singular(),
            '{{ modelPluralVariableName }}'                 => Str::of($class)->classBasename()->camel()->plural(),
            '{{ modelSnakedVariableName }}'                 => Str::of($class)->classBasename()->snake()->singular(),
            '{{ modelHyphenatedSnakedPluralVariableName }}' => Str::of($class)->classBasename()->snake('-')->plural(),
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Use or create the given model when creating the controller.'],
            ['route-prefix', 'rp', InputOption::VALUE_OPTIONAL, 'Use the given route prefix for the routes.'],
            ['admin', 'a', InputOption::VALUE_NEGATABLE, 'If the view is an admin view'],
        ];
    }
}
