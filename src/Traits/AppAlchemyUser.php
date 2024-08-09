<?php

namespace AppAlchemy\Traits;

/**
 * @method static where(string $string, $token)
 */
trait AppAlchemyUser
{
    public function findForAppAlchemy($token)
    {
        return static::where('api_token', $token)->first();
    }
}
