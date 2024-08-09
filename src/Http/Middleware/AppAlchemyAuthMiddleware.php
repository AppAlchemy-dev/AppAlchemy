<?php

namespace AppAlchemy\Http\Middleware;

use AppAlchemy\AppAlchemy;
use Closure;
use Illuminate\Http\Request;

readonly class AppAlchemyAuthMiddleware
{
    public function __construct(private AppAlchemy $appAlchemy) {}

    public function handle(Request $request, Closure $next)
    {
        if ($this->appAlchemy->isAppAlchemyApp()) {
            $token = $this->appAlchemy->getAuthToken();

            if ($token && $this->appAlchemy->authenticateToken($token)) {
                return $next($request);
            }

            return response('Unauthorized', 401);
        }

        return $next($request);
    }
}
