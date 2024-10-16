<?php

namespace Gigcodes\LaravelDddSupport\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeModelRequestCommand extends RequestMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:model-request';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return App::path('Console/stubs/model-request.stub');
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     *
     * @throws FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $baseName = Str::of($name)->classBasename()->replace('Request', '');
        $modelClass = App::getNamespace().'Models\\'.($this->option('model') ?? $baseName);

        $content = parent::buildClass($name);

        $content = str_replace(
            '{{ namespacedModel }}',
            $modelClass,
            $content
        );

        return str_replace(
            '{{ model }}',
            Str::of($modelClass)->classBasename(),
            $content
        );
    }

    protected function getOptions(): array
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, "The model we're generating the request for"],
            ...parent::getOptions(),
        ];
    }
}
