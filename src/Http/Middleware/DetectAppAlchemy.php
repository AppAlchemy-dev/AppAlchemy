<?php

namespace AppAlchemy\Http\Middleware;

use AppAlchemy\AppAlchemy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class DetectAppAlchemy
{
    public function __construct(
        private AppAlchemy $appAlchemy
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->appAlchemy->isAppAlchemyApp()) {
            $request->attributes->set('is_appalchemy_app', true);

            // You can add more attributes or modify the request as needed
            // For example, setting a header that your views can check:
            $request->headers->set('X-AppAlchemy-App', 'true');
        }

        return $next($request);
    }
}
