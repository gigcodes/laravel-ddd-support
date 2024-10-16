<?php

namespace Gigcodes\LaravelDddSupport\Commands;

use Illuminate\Foundation\Console\ResourceMakeCommand;

class DomainResourceMakeCommand extends ResourceMakeCommand
{
    protected $name = 'make:domain-resource';

    protected function getDefaultNamespace($rootNamespace): string
    {
        $domainName = $this->argument('name');
        return $rootNamespace.'\Domain\\'.$domainName.'\Resources';
    }
}
