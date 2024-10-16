<?php

namespace Gigcodes\LaravelDddSupport;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelDddSupportServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-ddd-support')
            ->hasCommands([

            ]);
    }
}
