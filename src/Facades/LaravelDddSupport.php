<?php

namespace Gigcodes\LaravelDddSupport\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gigcodes\LaravelDddSupport\LaravelDddSupport
 */
class LaravelDddSupport extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Gigcodes\LaravelDddSupport\LaravelDddSupport::class;
    }
}
