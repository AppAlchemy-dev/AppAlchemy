<?php

namespace AppAlchemy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AppAlchemy\AppAlchemy
 */
class AppAlchemy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AppAlchemy\AppAlchemy::class;
    }
}
